<?php

namespace Contao\Bundle\LegacyBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class FrontendLoader extends Loader
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        $config = $this->container->getParameter('contao_legacy.config');

        $pattern = '/{alias}';
        $defaults = array(
            '_controller' => 'LegacyBundle:Frontend:index',
        );
        $requirements = array(
            'alias' => '.*',
        );

        if ($config['urlSuffix'] != '') {
            $pattern .= '.{_format}';
            $requirements['_format'] = substr($config['urlSuffix'], 1);
            $defaults['_format'] = substr($config['urlSuffix'], 1);
        }

        if ($config['addLanguageToUrl']) {
            $pattern = '/{_locale}' . $pattern;
        }

        $routes = new RouteCollection();
        $route = new Route($pattern, $defaults, $requirements);
        $routes->add('contao_legacy_frontend', $route);

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'contao_legacy_frontend' === $type;
    }
}
