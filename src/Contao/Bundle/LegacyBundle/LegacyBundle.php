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

    /**
     * Initialize Contao legacy config
     */
    public function boot()
    {
        $GLOBALS['TL_CONFIG'] = $this->container->getParameter('contao_legacy.config');
    }

    /**
     * Pass legacy Contao config to DIC before building the cache
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('kernel.trusted_proxies', trimsplit(',', $GLOBALS['TL_CONFIG']['proxyServerIps']));

        // @todo check if secret is not publicly visible that way (because of CSRF protection)
        $container->setParameter('kernel.secret', $GLOBALS['TL_CONFIG']['encryptionKey']);

        if ($container->getParameter('kernel.charset') === '') {
            $container->setParameter('kernel.charset', strtoupper($GLOBALS['TL_CONFIG']['characterSet']));
        }

        // Cache config config in DIC so it can be set on boot
        $container->setParameter('contao_legacy.config', $GLOBALS['TL_CONFIG']);
    }
}
