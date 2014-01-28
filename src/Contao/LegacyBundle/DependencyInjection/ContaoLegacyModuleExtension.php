<?php

namespace Contao\LegacyBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ContaoLegacyModuleExtension extends Extension
{
    protected $module;
    protected $rootDir;

    public function __construct($module, $rootDir)
    {
        $this->module = $module;
        $this->rootDir = $rootDir;
    }

    public function getAlias()
    {
        return 'ContaoLegacy' . Container::camelize($this->module) . 'Module';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $definition= new Definition('Contao\LegacyBundle\EventListener\LegacyModuleListener', array($this->module, $this->rootDir));
        $definition->addTag('kernel.event_listener', array('event'=>'contao_legacy.load_data_container', 'method'=>'onLoadDataContainerEvent'));
        $container->setDefinition('contao_legacy.'.$this->module.'_module_listener', $definition);
    }
}
