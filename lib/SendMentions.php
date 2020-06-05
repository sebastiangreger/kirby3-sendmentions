<?php

namespace sgkirby\SendMentions;

use Kirby\Data\Data;
use Kirby\Toolkit\F;
use Kirby\Toolkit\V;

class SendMentions
{
    private static $triggered = [];
    private static $logfile = null;
    private static $log = null;

    /*
     * Resends pings to a single target
     */
    public static function resend($page, $pingdata)
    {
        // do not send webmentions, unless page has been published
        if ($page->status() != 'listed') {
            return false;
        }

        // load the array of previously sent pings
        static::$log = Storage::read($page, 'sendmentions');

        $source = $page->url();
        $target = $pingdata['target'];

        // empty the log file and start with the source URL
        static::logger('>>> ' . $source, false);

        if ($pingdata['type'] === 'archive.org') {
            $return = static::pingArchive($target);
        } else {
            $return = static::sendMention($source, $target);
        }

        // save sent pings' info in pings.json
        Storage::write($page, static::$log, 'sendmentions');

        return $return;
    }

    /*
     * Sends all pings for a page
     */
    public static function send($newPage, $oldPage = null, $force = false)
    {
        // trigger a check whether old data has to be migrated
        if (!F::exists(kirby()->root('site') . '/logs/sendmentions/sendmentions.log')) {
            Migration::run();
        }

        // do not send webmentions, unless page has been published
        if ($newPage->status() != 'listed') {
            // remove the queue if it exists
            Storage::delete($newPage, 'sendmentionqueue');
            return;
        }

        // do not send webmentions, unless "pings on publish/update" are active
        if (
            $oldPage != null
            && (
                ($newPage->status() == $oldPage->status() && !static::pageSettings($newPage, 'pingOnUpdate'))
                || ($newPage->status() != $oldPage->status() && !static::pageSettings($newPage, 'pingOnPublish'))
            )
        ) {
            // remove the queue if it exists
            return Storage::delete($newPage, 'sendmentionqueue');
        }

        // only proceed for selected templates
        if (!in_array($newPage->intendedTemplate()->name(), option('sgkirby.sendmentions.templates'))) {
            return;
        }

        // if asynchronous mode is active, create queue job instead
        if (!$force && option('sgkirby.sendmentions.synchronous') === false) {
            Storage::write($newPage, [time()], 'sendmentionqueue');
            return;
        }

        // load the array of previously sent pings
        static::$log = Storage::read($newPage, 'sendmentions');

        $source = $newPage->url();

        // empty the log file and start with the source URL
        static::logger('>>> ' . $source, false);

        // loop through all URLs in the post content
        foreach (static::parseUrls($newPage) as $target) {

            // do not process invalid URL
            if (! V::url($target)) {
                static::logger('Not a valild URL: ' . $target);
                continue;
            }

            // do not process same-site URLs
            $sourceDomain = parse_url($source, PHP_URL_HOST);
            if (strpos($target, $sourceDomain) !== false) {
                static::logger('Same-site URL: ' . $target);
                continue;
            }

            // do not process URLs already pinged before
            if (isset(static::$log[ $target ])) {
                static::logger('URL already pinged earlier: ' . $target);
                continue;
            }

            // do not process URLs already processed in this run
            if (in_array($target, static::$triggered)) {
                static::logger('URL duplicate within page: ' . $target);
                continue;
            }

            // send webmentions/pingbacks
            static::sendMention($source, $target);

            // if set in config, ping archive.org for all external links as well (default = off)
            if (in_array($newPage->intendedTemplate()->name(), option('sgkirby.sendmentions.archiveorg'))) {
                static::pingArchive($target);
            }

            // mark this URL as processed (independent of success/failure), to avoid double pings
            static::$triggered[] = $target;
        }

        // save sent pings' info in pings.json
        Storage::write($newPage, static::$log, 'sendmentions');

        // delete (potentially existing) queue file when done
        return Storage::delete($newPage, 'sendmentionqueue');
    }

    public static function updateLog($target, $type, $entry = [])
    {

        // add timestamp to the data array
        $entry['timestamp'] = time();

        // store webmention response in array
        static::$log[$target][$type] = $entry;
    }

    public static function sendMention($source, $target)
    {

        // load Indieweb MentionClient and Mf2 Parser
        require_once dirname(__DIR__) . DS . 'vendor' . DS . 'IndieWeb' . DS . 'MentionClient.php';
        if (class_exists('Mf2\\Parser') === false) {
            require_once dirname(__DIR__) . DS . 'vendor' . DS . 'Mf2' . DS . 'Parser.php';
        }
        $client = new \IndieWeb\MentionClient();

        // check for webmention endpoint
        if ($endpoint = $client->discoverWebmentionEndpoint($target)) {

            // not sending webmentions to localhost (W3C spec 4.3)
            if (strpos($endpoint, '//localhost') === true || strpos($endpoint, '//127.0.0') === true) {
                return false;
            }

            // send webmention
            $response = $client->sendWebmention($source, $target);

            // store webmention response in log
            $data = [
                'type' => 'webmention',
                'endpoint' => $endpoint,
                'response' => $response['code'],
            ];
            static::updateLog($target, 'mention', $data);
            static::logger('Webmention sent: ' . $target . ' (' . $response['code'] . ';' . $endpoint . ')');
        }
        // as a fallback, try to send a pingback
        elseif ($response = $client->sendPingback($source, $target)) {
            // if successful, store pingback info in log
            $data = [
                'type' => 'pingback',
            ];
            static::updateLog($target, 'mention', $data);
            static::logger('Pingback sent: ' . $target);
        } else {
            $data = [
                'type' => 'none',
            ];
            static::updateLog($target, 'mention', $data);
            static::logger('No endpoint found: ' . $target);
        }
        return $data;
    }

    public static function pingArchive($target)
    {
        $archiveCheckURL = 'https://web.archive.org/wayback/available?url=' . $target;
        $archiveSubmitURL = 'https://web.archive.org/save/' . $target;

        // if URL has been saved to archive.org within the last 24h, store that URL (no need to spam archive.org with identical copies)
        $archiveInfo = json_decode(file_get_contents($archiveCheckURL), true);
        if (array_key_exists('closest', $archiveInfo['archived_snapshots']) && time() - strtotime($archiveInfo['archived_snapshots']['closest']['timestamp']) < 86400) {

            // if successful, store archive URL in log
            static::updateLog($target, 'archive.org', [
                'url' => (string)$archiveInfo['archived_snapshots']['closest']['url'],
            ]);
            static::logger('Already exists on archive.org: ' . $target);

        // otherwise send a save request
        } else {

            try {
                $archiveResponse = Remote::get(
                    $archiveSubmitURL,
                    [
                        'timeout' => 30,
                        'agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0'
                    ]
                );
            } catch (Exception $e) {
                static::logger('Error polling archive.org: ' . $target . ' (' . $e . ')');
            }
            $archiveResponseHeaders = $archiveResponse->headers();
            if (!empty($archiveResponseHeaders)) {
                // if successful, store archive URL in log
                if (!empty($archiveResponseHeaders['Content-Location'])) {
                    static::updateLog($target, 'archive.org', [
                        'url' => (string)'https://web.archive.org' . $archiveResponseHeaders['Content-Location'],
                    ]);
                    static::logger('Saved to archive.org: ' . $target);
                } elseif (! empty($archiveResponseHeaders['X-Archive-Wayback-Runtime-Error'])) {
                    static::logger('Error from archive.org: ' . $target . ' (' . $archiveResponseHeaders['X-Archive-Wayback-Runtime-Error'] . ')');
                } else {
                    // TODO: use $archiveResponse['errorMessage'] instead (not stable)
                    static::logger('Error from archive.org: ' . $target . ' (unspecified)');
                }
            } else {
                static::logger('No response from archive.org: ' . $target);
            }

        }
    }

    public static function parseUrls($newPage)
    {
        $regexUrlPattern = "#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS";

        if (preg_match_all($regexUrlPattern, (string)$newPage->content()->text()->kirbytext(), $allUrlsInContent)) {
            return $allUrlsInContent[0];
        } else {
            return [];
        }
    }

    public static function pageSettings($page, string $key = null)
    {
        $stored = Storage::read($page, 'pagesettings');
        $defaults = [
            'pingOnPublish' => option('sgkirby.sendmentions.pingOnPublish'),
            'pingOnUpdate' => option('sgkirby.sendmentions.pingOnUpdate'),
        ];
        foreach ($defaults as $k => $v) {
            $settings[$k] = $stored[$k] ?? $v;
        }

        // if a specific key is given, return the value for that key
        if ($key !== null) {
            return $settings[$key] ?? null;
        }
        // otherwise return associative array
        return $settings;
    }

    // Log file writer
    public static function logger($body, $append = true)
    {
        F::write(
            kirby()->root('site') . '/logs/sendmentions/sendmentions.log',
            date('Y-m-d H:i:s') . ' ' . $body . PHP_EOL,
            $append
        );
    }
}
