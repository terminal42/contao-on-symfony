<?php

namespace Contao\LegacyBundle;

class ClassLoader
{
    private static $loader;

    /**
     * A map of classes that should automatically get an alias
     *
     * Example:
     * 'Environment'   => 'Contao\LegacyBundle\Module\Core\Library\Environment'
     *
     * @var array
     */
    private $classMap = array(
        'Environment' => 'Contao\LegacyBundle\Module\Core\Library\Environment'
    );

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public static function register($prepend = false)
    {
        if (null === static::$loader) {
            static::$loader = new static();
        }

        spl_autoload_register(array(static::$loader, 'loadClass'), true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public static function unregister()
    {
        if (null === static::$loader) {
            return;
        }

        spl_autoload_unregister(array(static::$loader, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if (isset($this->classMap[$class])) {
            class_alias($this->classMap[$class], $class);

            return true;
        }
    }
}
