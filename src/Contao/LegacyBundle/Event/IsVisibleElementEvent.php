<?php

namespace Contao\LegacyBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class IsVisibleElementEvent extends Event
{
    private $element;
    private $isVisible;

    public function __construct(\Model $element, $isVisible)
    {
        $this->element = $element;
        $this->isVisible = $isVisible;
    }

    public function setIsVisible($value)
    {
        $this->isVisible = (bool) $value;
    }

    public function isVisible()
    {
        return (bool) $this->isVisible;
    }

    public function getElement()
    {
        return $this->element;
    }
}
