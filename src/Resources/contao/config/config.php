<?php

declare(strict_types=1);

use lindesbs\pageyaml\Controller\BackendImportController;

$GLOBALS['BE_MOD']['design']['page']['pageyaml'] = [BackendImportController::class,'renderForm'];
