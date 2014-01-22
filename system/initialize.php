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

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/AppKernel.php';

$kernel = new AppKernel('legacy', false);
