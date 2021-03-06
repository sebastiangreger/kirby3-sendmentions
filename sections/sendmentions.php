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
                Migration::run();
            }

            $errors = [];

            if (!in_array($this->model()->intendedTemplate()->name(), option('sgkirby.sendmentions.templates'))) {
                $errors['template-not-active'] = [
                    'id'      => 'template-not-active',
                    'message' => '<strong>Setup issue:</strong> The Commentions plugin is not configured as active for this template. Check the <a href="https://github.com/sebastiangreger/kirby3-sendmentions" target="_blank">Readme</a> for the required setup.',
                    'theme'   => 'negative',
                ];
            }

            // for now, always return an empty error array
            return $errors;
        },

        'pageId' => function () {
            return $this->model()->id();
        },

        'queued' => function () {
            return F::exists($this->model()->root() . '/_sendmentions/sendmentionqueue.yml');
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
            $verb = strlen(option('sgkirby.sendmentions.secret')) >= 10 ? 'Enqueue' : 'Send';
            foreach ([$action] as $k) {
                $id = 'pingOn' . ucfirst($action);
                $disabled = false;
                $settings[$k] = [
                    'id' => $id,
                    'text' => [
                        $verb . " pings on " . $action,
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
