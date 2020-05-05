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

    ]

];
