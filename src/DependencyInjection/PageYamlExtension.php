<?php

declare(strict_types=1);

namespace lindesbs\pageyaml\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PageYamlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        (new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config')))
            ->load('services.yml');
    }
}
