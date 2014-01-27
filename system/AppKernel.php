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

use Symfony\Component\Config\Loader\LoaderInterface;
use Contao\Framework\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Contao\LegacyBundle\ContaoLegacyBundle(dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('calendar', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('comments', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('core', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('devtools', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('faq', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('listing', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('news', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('newsletter', dirname($this->getRootDir())),
            new Contao\LegacyBundle\ContaoLegacyModule('repository', dirname($this->getRootDir())),
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
