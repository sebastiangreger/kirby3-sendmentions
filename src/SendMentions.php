<?php

namespace sgkirby\SendMentions;

use Kirby\Data\Data;
use Kirby\Toolkit\F;
use Kirby\Toolkit\V;

class SendMentions {
	
	private static $triggered = array();
	private static $logfile = null;
	private static $log = null;

    public static function loadLog( $newPage ) {
		
		// load the array of pings sent earlier
		static::$logfile = $newPage->root() . DS . '.sendmentions.json';
		if ( F::exists( static::$logfile ) )
			return Data::read( static::$logfile, 'json' );
		else
			return [];

	}

    public static function updateLog( $target, $type, $entry = [] ) {
	
		// add timestamp to the data array
		$entry['timestamp'] = time();
		
		// store webmention response in array
		static::$log[ (string) $target ][$type] = $entry;
	
	}
		
    public static function sendMention( $source, $target ) {

		// load Indieweb MentionClient and Mf2 Parser
		require_once( dirname(__DIR__) . DS . 'vendor' . DS . 'IndieWeb' . DS . 'MentionClient.php' );
		require_once( dirname(__DIR__) . DS . 'vendor' . DS . 'Mf2' . DS . 'Parser.php' );
		$client = new \IndieWeb\MentionClient();

		// check for webmention endpoint
		if ( $endpoint = $client->discoverWebmentionEndpoint( $target ) ) :

			// not sending webmentions to localhost (W3C spec 4.3)
			if ( strpos( $endpoint, '//localhost' ) === TRUE || strpos( $endpoint, '//127.0.0' ) === TRUE )
				return;

			// send webmention
			$response = $client->sendWebmention( $source, $target );

			// store webmention response in log
			static::updateLog ( $target, 'webmention', [
				'endpoint' => $endpoint,
				'response' => $response['code'],
			]);

		// as a fallback, try to send a pingback
		elseif ( $response = $client->sendPingback( $source, $target ) ) :

			// if successful, store pingback info in log
			static::updateLog ( $target, 'pingback' );

		endif;
		
	}
	
    public static function pingArchive( $target ) {

		$archiveCheckURL = 'https://web.archive.org/wayback/available?url=' . $target;
		$archiveSubmitURL = 'https://web.archive.org/save/' . $target;
		
		// if URL has been saved to archive.org within the last 24h, store that URL (no need to spam archive.org with identical copies)
		$archiveInfo = json_decode( file_get_contents( $archiveCheckURL ), true );
		if ( array_key_exists( 'closest', $archiveInfo['archived_snapshots'] ) && time() - strtotime( $archiveInfo['archived_snapshots']['closest']['timestamp'] ) < 86400 ) :

			// if successful, store archive URL in log
			static::updateLog ( $target, 'archive.org', [
				'url' => (string) $archiveInfo['archived_snapshots']['closest']['url'],
			]);

		// otherwise send a save request
		elseif ( $archiveResponse = F::read( $archiveSubmitURL ) ) :
		
			// if successful, store archive URL in log
			static::updateLog ( $target, 'archive.org', [
				'url' => (string) 'https://web.archive.org' . $archiveResponse['headers']['content-location'],
			]);

		endif;
			
	}

    public static function parseUrls( $newPage ) {
		
		$regexUrlPattern = "#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS";

		if ( preg_match_all( $regexUrlPattern, (string) $newPage->content()->text()->kirbytext(), $allUrlsInContent ) )
			return $allUrlsInContent[0];
		else
			return array();
		
	}

    public static function send( $newPage, $oldPage ) {

		// do not send webmentions, unless page has been published
		if ( $newPage->status() != 'listed' )
			return;

		// if an array of applicable templates is given, only proceed for selected templates
		if ( option( 'sgkirby.sendmentions.templates', [] ) != [] && ! in_array( $newPage->template()->name(), option( 'sgkirby.sendmentions.templates', [] ) ) )
			return;

		// load the array of previously sent pings
		static::$log = static::loadLog( $newPage );

		$source = $newPage->url();

		// loop through all URLs in the post content
		foreach( static::parseUrls( $newPage ) as $target ) :

			// do not process invalid URL
			if( ! V::url( $target ) )
				continue;

			// do not process local URLs
			$sourceDomain = parse_url( $source, PHP_URL_HOST );
			if( strpos( $target, $sourceDomain ) !== false )
				continue;

			// do not process URLs already pinged before
			if( isset( static::$log[ $target ] ) )
				continue;

			// do not process URLs already processed in this run
			if( in_array( $target, static::$triggered ) )
				continue;

			// send webmentions/pingbacks
			static::sendMention( $source, $target );

			// if set in config, ping archive.org for all external links as well (default = off)
			if ( is_array( option( 'sgkirby.sendmentions.archiveorg', false ) ) )
				static::pingArchive( $target );

			// mark this URL as processed (independent of success/failure), to avoid double pings
			static::$triggered[] = $target;

		endforeach;

		// save sent pings' info in pings.json
		Data::write( static::$logfile, static::$log );

    }
}
