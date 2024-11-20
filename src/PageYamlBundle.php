<?php

declare(strict_types=1);

namespace lindesbs\pageyaml;

use lindesbs\pageyaml\DependencyInjection\PageYamlExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PageYamlBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PageYamlExtension();
    }
}
