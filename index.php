<?php

namespace sgkirby\SendMentions;

/**
 * Kirby 3 Sendmentions Plugin
 *
 * @version   1.0.0-beta.2
 * @author    Sebastian Greger <msg@sebastiangreger.net>
 * @copyright Sebastian Greger <msg@sebastiangreger.net>
 * @link      https://github.com/sebastiangreger/kirby3-sendmentions
 * @license   MIT
 */

load([
    'sgkirby\\SendMentions\\SendMentions' => 'lib/SendMentions.php',
    'sgkirby\\SendMentions\\Storage'      => 'lib/Storage.php',
    'sgkirby\\SendMentions\\Cron'         => 'lib/Cron.php',
    'sgkirby\\SendMentions\\Migration'    => 'lib/Migration.php'
], __DIR__);

\Kirby::plugin('sgkirby/sendmentions', [

    'options' => [
        'pingOnPublish'       => false,
        'pingOnUpdate'        => false,
        'synchronous'         => false,
        'templates'           => [],
        'archiveorg'          => [],
    ],

    'api' => require __DIR__ . '/config/api.php',

    'routes' => require __DIR__ . '/config/routes.php',

    'sections' => [
        'pings' => require __DIR__ . '/sections/pings.php',
        'sendmentions' => require __DIR__ . '/sections/sendmentions.php',
    ],

    'hooks'       => require  __DIR__ . '/config/hooks.php',

]);
