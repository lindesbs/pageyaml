<?php

namespace lindesbs\pageyaml\Classes;

use Contao\Backend;

class BackendDCAClasses extends Backend
{

    public function __construct()
    {
        $GLOBALS['TL_CSS']['pageYaml'] = 'bundles/pageyaml/pageyaml.css';
    }

    public function renderGlobalOperationsLink(
        string $href,
        string $label,
        string $title,
        ?string $class,
        ?string $attr,
        array|string $ids
    ): string
    {
        return '
<div class="pageyaml_dropdown">
  <button class="pageyaml_dropbtn">PageYAML</button>
  <div class="pageyaml_dropdown-content">
    <a href="contao?do=page&'.$href.'&import">Import</a>
    <a href="#">Export</a>
  </div>
</div> 
';
    }

}