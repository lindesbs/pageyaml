<?php

namespace lindesbs\pageyaml\Classes;

use Contao\Backend;
use Contao\System;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

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


        $container = System::getContainer();
        $twig = $container->get('twig');

        return $twig->render('@PageYaml\Backend\GlobalOperationsLink.html.twig',
            [
                'href' => $href
            ]
        );
    }

}