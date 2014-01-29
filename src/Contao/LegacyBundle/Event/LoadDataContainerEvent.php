<?php

namespace Contao\LegacyBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class LoadDataContainerEvent extends Event
{
    private $dataContainer;

    public function __construct($name)
    {
        $this->dataContainer = $name;
    }

    public function getDataContainer()
    {
        return $this->dataContainer;
    }
}
