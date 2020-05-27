<?php

namespace sgkirby\SendMentions;

use Kirby\Toolkit\F;

return [

	'props' => [

		'headline' => function ($message = "Pings") {
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

        'pageId' => function () {
            return $this->model()->id();
        },

		'sendmentions' => function () {
			$page = $this->model();
			$logfile = Storage::read($page, 'sendmentions');

            $i = 0;
            foreach ($logfile as $url => $pings) {
                if (!is_array($pings)) {
                    $return[] = [
                        'uid' => $i,
                        'pageid' => $page->id(),
                        'target' => $url,
                        'type' => 'notsent',
                        'data' =>  [],
                    ];
                    $i++;
                } else {
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
            }

            return $return ?? [];
        },

        'pageSettings' => function () {
            // retrieve saved values (if exist) and loop to create the array for vue
            $stored = SendMentions::pageSettings($this->model());
            if ($this->model()->status() === 'listed') {
                $action = 'update';
            } else {
                $action = 'publish';
            }
            foreach ([$action] as $k) {
                $id = 'pingOn' . ucfirst($action);
                $disabled = false;
                $settings[$k] = [
                    'id' => $id,
                    'text' => [
                        "Send pings on " . $action,
                        "No pings on " . $action,
                    ],
                    'value' => $disabled ? false : $stored[$id],
                    'disabled' => $disabled,
                    'debug' => $stored,
                ];
            }
            return $settings;
        },

	],

];
