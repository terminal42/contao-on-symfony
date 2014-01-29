<?php

namespace Contao\LegacyBundle\Translation;

use Symfony\Component\Translation\Loader\ArrayLoader;

class LegacyPhpFileLoader extends ArrayLoader
{
    public function load($resource, $locale, $domain = 'messages')
    {
        unset($GLOBALS['TL_LANG']);
        include $resource;

        $catalogue = parent::load($GLOBALS['TL_LANG'], $locale, $domain);

        unset($GLOBALS['TL_LANG']);

        return $catalogue;
    }
}
