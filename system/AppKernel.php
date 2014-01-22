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

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Contao\Bundle\LegacyBundle\LegacyBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * Let the ContaoLegacyBundle set charset from localconfig.php
     *
     * @return  string
     */
    public function getCharset()
    {
        return '';
    }
}
