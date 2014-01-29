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
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Contao\Framework\DependentBundleInterface;
use Contao\LegacyBundle\DependencyInjection\ContaoLegacyModuleExtension;

class ContaoLegacyModule extends Bundle implements DependentBundleInterface
{
    protected $module;
    protected $rootDir;

    public function __construct($module, $rootDir)
    {
        $this->module = $module;
        $this->rootDir = $rootDir;
        $this->name = 'ContaoLegacy' . Container::camelize($module) . 'Module';
        $this->extension = new ContaoLegacyModuleExtension($module, $rootDir);
    }

    public function boot()
    {
        $path = $this->rootDir . '/system/modules/' . $this->module;
        $strFile = $path . '/config/config.php';

        if (file_exists($strFile)) {
            include $strFile;
        }

        // Find and register language files
        $finder = new Finder();
        $translator = $this->container->get('translator');

        $finder->files()->in($path.'/languages');

        foreach ($finder as $file) {
            $locale = basename(dirname($file->getRealPath()));
            $extension = $file->getExtension();
            $domain = $file->getBasename('.'.$extension);

            if ($domain == 'default') {
                $domain = 'messages';
            }

            if ($extension == 'php') {
                $translator->addResource('contao_legacy_php', $file->getRealPath(), $locale, $domain);
            } elseif ($extension == 'xlf') {
                $translator->addResource('contao_legacy_xlf', $file->getRealPath(), $locale, $domain);
            }
        }
    }

    public function getDependencies()
    {
        if ($this->module == 'core') {
            return array('FrameworkBundle');
        }

        $dependencies = array('ContaoLegacyCoreModule');
        $file = $this->rootDir . '/system/modules/' . $this->module . '/config/autoload.ini';

        // Read the autoload.ini if any
        if (file_exists($file)) {
            $config = parse_ini_file($file, true);

            if (!empty($config['requires'])) {
                foreach ($config['requires'] as $module) {
                    if ($module == 'core') {
                        continue;
                    }

                    $dependencies[] = 'ContaoLegacy' . Container::camelize($module) . 'Module';
                }
            }
        }

        return $dependencies;
    }
}
