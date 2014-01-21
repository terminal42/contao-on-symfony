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

define('TL_MODE', 'FE');
require_once 'system/initialize.php';

$response->send();
$kernel->terminate($request, $response);
