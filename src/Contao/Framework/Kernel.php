<?php

namespace Contao\Framework;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Finder\Finder;
use Contao\Framework\Exception\UnresolvableDependenciesException;

abstract class Kernel extends BaseKernel
{

    public function registerBundles()
    {
        $bundles = array();

        $root = dirname($this->getRootDir());
        $finder = new Finder();
        $finder->files()->name('*Bundle.php')->in(array($root.'/src', $root.'/vendor'))->exclude('Tests');

        foreach ($finder as $file) {
            $class = str_replace(array($root.'/src/', $root.'/vendor/', '.php'), '', $file->getRealPath());
            $class = str_replace('/', '\\', $class);

            while (strpos($class, '\\') !== false) {
                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    if (!$reflection->isAbstract()) {
                        $bundles[] = new $class($this);
                    }

                    continue(2);
                }
                $class = substr($class, strpos($class, '\\')+1);
            }
        }

        return $bundles;
    }

    public function initializeBundles()
    {
        parent::initializeBundles();

        $dependencies = array();
        $active = array();

        foreach ($this->bundles as $bundle) {
            $dependencies[$bundle->getName()] = ($bundle instanceof DependentBundleInterface) ? $bundle->getDependencies() : array();
        }

        // Resolve the dependencies
        while (!empty($dependencies)) {
            $matched = false;

            foreach ($dependencies as $name => $requires) {
                if (empty($requires)) {
                    $matched = true;
                } else {
                    $matched = count(array_diff($requires, $active)) === 0;
                }

                if ($matched === true) {
                    unset($dependencies[$name]);
                    $active[] = $name;
                    continue(2);
                }
            }

            // The dependencies cannot be resolved
            if ($matched === false) {
                throw new UnresolvableDependenciesException("The module dependencies could not be resolved.\n".print_r($dependencies, true));
            }
        }

        $bundles = $this->bundles;
        $this->bundles = array();

        foreach ($active as $name) {
            $this->bundles[$name] = $bundles[$name];
        }

        $this->bundleMap = array_intersect_key($this->bundleMap, array_flip($active));
    }
}
