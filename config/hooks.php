<?php

namespace sgkirby\SendMentions;

return[

    'page.update:after' => function( $newPage, $oldPage ) {
        SendMentions::send( $newPage, $oldPage );
    },

    'page.changeStatus:after' => function( $newPage, $oldPage ) {
        SendMentions::send( $newPage, $oldPage );
    },

];
