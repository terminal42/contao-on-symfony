<?php

namespace Contao\LegacyBundle\EventListener;

use Contao\LegacyBundle\Event\LoadDataContainerEvent;

class LegacyModuleListener extends \Controller
{
    protected $module;
    protected $rootDir;

    public function __construct($module, $rootDir)
    {
        $this->module = $module;
        $this->rootDir = $rootDir;
    }

    public function onLoadDataContainerEvent(LoadDataContainerEvent $event)
    {
        $file = 'system/modules/' . $this->module . '/dca/' . $event->getDataContainer() . '.php';

        if (file_exists(TL_ROOT . '/' . $file)) {
            include TL_ROOT . '/' . $file;
        }
    }
}
