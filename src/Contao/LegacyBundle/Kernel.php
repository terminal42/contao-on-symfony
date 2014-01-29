<?php

namespace Contao\LegacyBundle;

use Contao\Framework\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel
{

    public function registerBundles()
    {
        $bundles = parent::registerBundles();
        $rootDir = dirname($this->getRootDir());

        foreach (\ModuleLoader::getActive() as $module) {
            $bundles[] = new ContaoLegacyModule($module, $rootDir);
        }

        return $bundles;
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
