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

namespace Contao\Bundle\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('kernel.trusted_proxies', array('localhost'));
        $container->setParameter('kernel.secret', 'verysecret');

        // Set Contao config to container
        foreach ($GLOBALS['TL_CONFIG'] as $k => $v) {
            $container->setParameter('contao.config.' . strtolower($k), $v);
        }
    }
}
