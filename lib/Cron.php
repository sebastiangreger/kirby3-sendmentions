<?php

namespace sgkirby\SendMentions;

use Exception;
use Kirby\Http\Response;
use Kirby\Toolkit\F;

class Cron
{
    /**
     * Called by the router, verifies the validity of the cron request and triggers the processing
     *
     * @return \Kirby\Http\Response
     */
    public static function route()
    {
        $secret = option('sgkirby.sendmentions.secret');

        // validation with actionable error messages
        if (!get('token')) {
            return new Response('<p>Error: Token attribute missing from URL or empty.</p>', 'text/html', 403);
        }
        if ($secret == '') {
            return new Response('<p>Error: No token configured in config file.</p>', 'text/html', 500);
        }
        if (strlen($secret) < 10) {
            return new Response('<p>Error: Token set in config is too short.</p>', 'text/html', 500);
        }
        if (preg_match('/[&%#+]/i', $secret)) {
            return new Response('<p>Error: Token set in config contains invalid characters.</p>', 'text/html', 500);
        }
        if (get('token') != $secret) {
            return new Response('<p>Error: Incorrect token in URL attribute.</p>', 'text/html', 403);
        }

        // trigger the processing
        try {
            Cron::processQueue();
            return new Response('<p>Success.</p>', 'text/html', 200);
        } catch (Exception $e) {
            return new Response('<p>Error: ' . $e->getMessage() . '</p>', 'text/html', 400);
        }
    }

    /**
     * Handles the asynchronous processing of pings
     *
     * @return bool True if successful
     */
    public static function processQueue()
    {
        // limit to one process by only proceeding if no (or an expired left over) lockfile exists
        $logfolder = kirby()->root('site') . DS . 'logs' . DS . 'sendmentions';
        $lockfile = $logfolder . DS . 'queuelock.log';
        if (F::exists($lockfile) && F::modified($lockfile) > (time() - 120)) {
            throw new Exception('A queue process is already running.');
        } elseif (F::exists($lockfile)) {
            F::remove($lockfile);
        }

        // loop through all pages in the index
        foreach (site()->index() as $page) {
            // if page is queued for sending pings, rund the regular send routine
            if (F::exists(Storage::file($page, 'sendmentionqueue'))) {
                SendMentions::send($page);
            }
        }

        // remove the lockfile, if exists
        if (F::exists($lockfile)) {
            F::remove($lockfile);
        }

        // create/update the timestamped log file
        $logfile = $logfolder . DS . 'lastcron.log';
        F::write($logfile, time());

        return true;
    }
}
