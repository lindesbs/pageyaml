<?php

declare(strict_types=1);

use lindesbs\pageyaml\Classes\BackendDCAClasses;


$GLOBALS['TL_DCA']['tl_page']['list']['global_operations']['pageYaml'] = [
    'href'              => 'key=pageyaml',
    'attributes'        => 'onclick="Backend.getScrollOffset()"',
    'button_callback'   => [BackendDCAClasses::class, 'renderGlobalOperationsLink' ]
];
