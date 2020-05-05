<?php

namespace sgkirby\SendMentions;

/**
 * Kirby 3 Sendmentions Plugin
 *
 * @version   1.0.0-alpha.1
 * @author    Sebastian Greger <msg@sebastiangreger.net>
 * @copyright Sebastian Greger <msg@sebastiangreger.net>
 * @link      https://github.com/sebastiangreger/kirby3-sendmentions
 * @license   MIT
 */

load([
    'sgkirby\\SendMentions\\SendMentions' => 'lib/SendMentions.php',
    'sgkirby\\SendMentions\\Storage' => 'lib/Storage.php'
], __DIR__);

\Kirby::plugin('sgkirby/sendmentions', [

    'api' => require __DIR__ . '/config/api.php',

    'sections' => [
        'pings' => require __DIR__ . '/sections/pings.php',
        'sendmentions' => require __DIR__ . '/sections/sendmentions.php',
    ],

    'hooks'       => require  __DIR__ . '/config/hooks.php',

]);
