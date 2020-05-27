<?php

namespace sgkirby\SendMentions;

return [

    'routes' => [
        [
            'pattern' => 'sendmentions/(:any)',
            'method'  => 'PATCH',
            'action'  => function (string $pageid) {
                return SendMentions::resend($this->page($pageid), $this->requestBody());
            }
        ],
        [
            'pattern' => 'sendmentions/pagesettings/(:any)',
            'method'  => 'PATCH',
            'action'  => function (string $pageid) {
                $data = $this->requestBody();
                $settings = Storage::read($this->page($pageid), 'pagesettings');
                $settings[$data['key']] = $data['value'];
                return Storage::write($this->page($pageid), $settings, 'pagesettings');
            }
        ],

    ]

];
