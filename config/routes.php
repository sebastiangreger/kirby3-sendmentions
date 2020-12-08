<?php

namespace sgkirby\SendMentions;

return [

    [
        'pattern' => 'sendmentions-processqueue',
        'action'  => function () {
            return Cron::route();
        }
    ]

];
