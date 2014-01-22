<?php

namespace Contao\Bundle\LegacyBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class FrontendLoader extends Loader
{
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        // prepare a new route
        $pattern = '/{alias}';
        $defaults = array(
            '_controller' => 'LegacyBundle:Frontend:index',
        );
        $requirements = array(
            'alias' => '.*',
        );
        $route = new Route($pattern, $defaults, $requirements);

        // add the new route to the route collection:
        $routeName = 'contao_legacy_frontend';
        $routes->add($routeName, $route);

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'contao_legacy_frontend' === $type;
    }
}
