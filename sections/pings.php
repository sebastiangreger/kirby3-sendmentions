<?php

namespace sgkirby\SendMentions;

return [

	'props' => [

		'headline' => function ($message = "Pings sent") {
			return $message;
		},

	],

	'computed' => [

        'sendmentionsSystemErrors' => function () {

            $errors[] = [
                'id' => 'missing-dependencies',
                'message' => '<strong>Action required!</strong> As of version 1.0 of the Sendmentions plugin, the section name "pings" has been deprecated and replaced with "sendmentions". Also some of the setting options and defaults have changed - check the <a href="https://github.com/sebastiangreger/kirby3-sendmentions" target="_blank">Readme</a> for details.',
                'theme' => 'info',
            ];

            return $errors;
        },

	],

];
