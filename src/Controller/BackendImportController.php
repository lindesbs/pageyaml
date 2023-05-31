<?php

namespace lindesbs\pageyaml\Controller;

use Contao\FileUpload;
use Contao\Input;

class BackendImportController
{
    public function renderForm(): string
    {
        if (Input::get('key') != 'pageyaml') {
            return '';
        }

        $objUploader = new FileUpload();



        return 'Jeppa';
    }
}