<?php

namespace Contao\LegacyBundle\EventListener;

use Contao\LegacyBundle\Event\IsVisibleElementEvent;
use Contao\LegacyBundle\Event\LoadDataContainerEvent;

class LegacyHookListener extends \Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function onLoadDataContainerEvent(LoadDataContainerEvent $event)
    {
        // HOOK: allow to load custom settings
        if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer']))
        {
            foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback)
            {
                $this->import($callback[0]);
                $this->$callback[0]->$callback[1]($event->getDataContainer());
            }
        }
    }

    public function onIsVisibleElementEvent(IsVisibleElementEvent $event)
    {
        if (isset($GLOBALS['TL_HOOKS']['isVisibleElement']) && is_array($GLOBALS['TL_HOOKS']['isVisibleElement']))
        {
            $blnReturn = $event->isVisible();
            $objElement = $event->getElement();

            foreach ($GLOBALS['TL_HOOKS']['isVisibleElement'] as $callback)
            {
                $blnReturn = static::importStatic($callback[0])->$callback[1]($objElement, $blnReturn);
            }

            $event->setIsVisible($blnReturn);
        }
    }
}
