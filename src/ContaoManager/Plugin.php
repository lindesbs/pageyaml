<?php

namespace lindesbs\pageyaml\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use lindesbs\pageyaml\PageYamlBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PageYamlBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}