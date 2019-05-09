<?php

namespace sgkirby\SendMentions;

/**
 * Kirby 3 Sendmentions Plugin
 *
 * @version   0.1.0
 * @author    Sebastian Greger <msg@sebastiangreger.net>
 * @copyright Sebastian Greger <msg@sebastiangreger.net>
 * @link      https://github.com/sebastiangreger/kirby3-sendmentions
 * @license   MIT
 */

load([
    'sgkirby\\SendMentions\\SendMentions' => 'src/SendMentions.php'
], __DIR__);

\Kirby::plugin('sgkirby/sendmentions', [

    'sections' => [
        'pings' => require __DIR__ . '/sections/pings.php'
    ],

    'hooks'       => require 'config/hooks.php',

]);
