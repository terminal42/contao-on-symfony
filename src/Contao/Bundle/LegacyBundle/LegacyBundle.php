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

namespace Contao\Bundle\LegacyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LegacyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('kernel.trusted_proxies', trimsplit(',', $GLOBALS['TL_CONFIG']['proxyServerIps']));
        $container->setParameter('kernel.secret', $GLOBALS['TL_CONFIG']['encryptionKey']);

        if ($container->getParameter('kernel.charset') === '') {
            $container->setParameter('kernel.charset', strtoupper($GLOBALS['TL_CONFIG']['characterSet']));
        }

        // Set Contao config to container
        foreach ($GLOBALS['TL_CONFIG'] as $k => $v) {

            $configKey = preg_replace_callback('/([A-Z])/', function($c) {
                return "_" . strtolower($c[1]);
            }, $k);

            $container->setParameter('contao.config.' . strtolower($configKey), $v);
        }
    }
}
