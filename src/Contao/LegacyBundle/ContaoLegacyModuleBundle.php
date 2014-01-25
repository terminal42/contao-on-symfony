<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Contao\LegacyBundle;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoLegacyModuleBundle extends Bundle
{
    protected $module;
    protected $rootDir;

    public function __construct($module, $rootDir)
    {
        $this->module = $module;
        $this->rootDir = $rootDir;
        $this->name = 'ContaoLegacy' . Container::camelize($module) . 'ModuleBundle';
    }

    public function boot()
    {
    }

    public function build(ContainerBuilder $container)
    {
    }
}
