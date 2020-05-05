<?php

namespace sgkirby\SendMentions;

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

            $errors[] = [
                'id' => 'missing-dependencies',
                'message' => '<strong>Action required!</strong> As of version 1.0 of the <a href="https://github.com/sebastiangreger/kirby3-sendmentions" target="_blank">Sendmentions plugin</a>, the section name "pings" has been deprecated and replaced with "sendmentions". While this still works at the moment, you should update your blueprint to avoid errors once this legacy fallback is removed in the future.',
                'theme' => 'info',
            ];

            return $errors;
        },

		'sendmentions' => function () {
			$page = $this->model();
			$logfile = Storage::read($page, 'sendmentions');

            $i = 0;
            foreach ($logfile as $url => $pings) {
                foreach ($pings as $type => $data) {
                    $return[] = [
                        'uid' => $i,
                        'pageid' => $page->id(),
                        'target' => $url,
                        'type' => ($type === 'mention' ? $data['type'] : $type),
                        'data' => $data,
                    ];
                    $i++;
                }
            }

            return $return;
        },

	],

];
