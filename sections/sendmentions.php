<?php

namespace sgkirby\SendMentions;

use Kirby\Toolkit\F;

return [

	'props' => [

		'headline' => function ($message = "Pings sent") {
			return $message;
		},

        'empty' => function ($empty = null) {
            return 'No pings sent, yet';
        },

	],

	'computed' => [

        'sendmentionsSystemErrors' => function () {

            // run a check whether there is unmigrated data, but do not output anything
            if (!F::exists(kirby()->root('site') . '/logs/sendmentions/sendmentions.log')) {
                SendMentions::migration();
            }

            // for now, always return an empty error array
            return [];
        },

		'sendmentions' => function () {
			$page = $this->model();
			$logfile = Storage::read($page, 'sendmentions');

            $i = 0;
            foreach ($logfile as $url => $pings) {
                foreach ($pings as $type => $data) {
                    if ($type == 'mention') {
                        if ($data['type'] == 'webmention') {
                            $display = '<strong>Webmention (' . $data['response'] . '):</strong> ' . $url;
                        } elseif ($data['type'] == 'pingback') {
                            $display = '<strong>Pingback:</strong> ' . $url;
                        } else {
                            $display = '<strong>No endpoint:</strong> ' . $url;
                        }
                    } elseif ($type == 'archive.org') {
                        $display = '<strong>Archive.org:</strong> ' . $url;
                    } else {
                        $display = $url;
                    }
                    $return[] = [
                        'uid' => $i,
                        'pageid' => $page->id(),
                        'target' => $url,
                        'display' => $display,
                        'type' => ($type === 'mention' ? $data['type'] : $type),
                        'data' => $data,
                    ];
                    $i++;
                }
            }

            return $return ?? [];
        },

	],

];
