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
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Contao\LegacyBundle\ContaoLegacyBundle(),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('calendar', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('comments', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('core', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('devtools', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('faq', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('listing', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('news', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('newsletter', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModuleBundle('repository', dirname($this->getRootDir())),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
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
