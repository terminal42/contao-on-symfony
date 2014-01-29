<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Library
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Contao;

use Symfony\Component\HttpFoundation\Request;


/**
 * Reads the environment variables
 *
 * The class returns the environment variables (which are stored in the PHP
 * $_SERVER array) independent of the operating system.
 *
 * Usage:
 *
 *     echo Environment::get('scriptName');
 *     echo Environment::get('requestUri');
 *
 * @package   Library
 * @author    Leo Feyer <https://github.com/leofeyer>
 * @copyright Leo Feyer 2005-2014
 */
class Environment
{

	/**
	 * Object instance (Singleton)
	 * @var \Environment
	 */
	protected static $objInstance;

	/**
	 * Cache
	 * @var array
	 */
	protected static $arrCache = array();


	/**
	 * Return an environment variable
	 *
	 * @param string $strKey The variable name
	 *
	 * @return mixed The variable value
	 */
	public static function get($strKey)
	{
		if (isset(static::$arrCache[$strKey]))
		{
			return static::$arrCache[$strKey];
		}

		if (in_array($strKey, get_class_methods(get_class())))
		{
			return static::$strKey();
		}
		else
		{
			$arrChunks = preg_split('/([A-Z][a-z]*)/', $strKey, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			$strServerKey = strtoupper(implode('_', $arrChunks));

			return static::getRequest()->server->get($strServerKey);
		}
	}


	/**
	 * Set an environment variable
	 *
	 * @param string $strKey   The variable name
	 * @param mixed  $varValue The variable value
	 */
	public static function set($strKey, $varValue)
	{
		static::$arrCache[$strKey] = $varValue;
	}


	/**
	 * Return the absolute path to the script (e.g. /home/www/html/website/index.php)
	 *
	 * @return string The absolute path to the script
	 */
	protected static function scriptFilename()
	{
		return str_replace('//', '/', str_replace('\\', '/', (PHP_SAPI == 'cgi' || PHP_SAPI == 'isapi' || PHP_SAPI == 'cgi-fcgi' || PHP_SAPI == 'fpm-fcgi') && (static::get('OrigPathTranslated') ?: static::get('PathTranslated')) ? (static::get('OrigPathTranslated') ?: static::get('PathTranslated')) : (static::get('OrigScriptFilename') ?: static::get('ScriptFilename'))));
	}


	/**
	 * Return the relative path to the script (e.g. /website/index.php)
	 *
	 * @return string The relative path to the script
	 */
	protected static function scriptName()
	{
		return (PHP_SAPI == 'cgi' || PHP_SAPI == 'isapi' || PHP_SAPI == 'cgi-fcgi' || PHP_SAPI == 'fpm-fcgi') && (static::get('OrigPathInfo') ?: static::get('PathInfo')) ? (static::get('OrigPathInfo') ?: static::get('PathInfo')) : (static::get('OrigScriptName') ?: static::get('ScriptName'));
	}


	/**
	 * Alias for scriptName()
	 *
	 * @return string The script name
	 */
	protected static function phpSelf()
	{
		return static::scriptName();
	}


	/**
	 * Return the document root (e.g. /home/www/user/)
	 *
	 * Calculated as SCRIPT_FILENAME minus SCRIPT_NAME as some CGI versions
	 * and mod-rewrite rules might return an incorrect DOCUMENT_ROOT.
	 *
	 * @return string The document root
	 */
	protected static function documentRoot()
	{
		$strDocumentRoot = '';
		$arrUriSegments = array();
		$scriptName = static::get('scriptName');
		$scriptFilename = static::get('scriptFilename');

		// Fallback to DOCUMENT_ROOT if SCRIPT_FILENAME and SCRIPT_NAME point to different files
		if (basename($scriptName) != basename($scriptFilename))
		{
			return str_replace('//', '/', str_replace('\\', '/', realpath(static::get('DocumentRoot'))));
		}

		if (substr($scriptFilename, 0, 1) == '/')
		{
			$strDocumentRoot = '/';
		}

		$arrSnSegments = explode('/', strrev($scriptName));
		$arrSfnSegments = explode('/', strrev($scriptFilename));

		foreach ($arrSfnSegments as $k=>$v)
		{
			if ($arrSnSegments[$k] != $v)
			{
				$arrUriSegments[] = $v;
			}
		}

		$strDocumentRoot .= strrev(implode('/', $arrUriSegments));

		if (strlen($strDocumentRoot) < 2)
		{
			$strDocumentRoot = substr($scriptFilename, 0, -(strlen($strDocumentRoot) + 1));
		}

		return str_replace('//', '/', str_replace('\\', '/', realpath($strDocumentRoot)));
	}


	/**
	 * Return the request URI [path]?[query] (e.g. /contao/index.php?id=2)
	 *
	 * @return string The request URI
	 */
	protected static function requestUri()
	{
		if (static::get('RequestUri') != '')
		{
			return static::get('RequestUri');
		}
		else
		{
			return '/' . preg_replace('/^\//', '', static::get('scriptName')) . (static::get('QueryString') != '' ? '?' . static::get('QueryString') : '');
		}
	}


	/**
	 * Return the first eight accepted languages as array
	 *
	 * @return array The languages array
	 *
	 * @author Leo Unglaub <https://github.com/LeoUnglaub>
	 */
	protected static function httpAcceptLanguage()
	{
		$arrAccepted = array();
		$arrLanguages = array();

		// The implementation differs from the original implementation and also works with .jp browsers
		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', static::get('HttpAcceptLanguage'), $arrAccepted);

		// Remove all invalid locales
		foreach ($arrAccepted[1] as $v)
		{
			$chunks = explode('-', $v);

			// Language plus dialect, e.g. en-US or fr-FR
			if (isset($chunks[1]))
			{
				$locale = $chunks[0] . '-' . strtoupper($chunks[1]);

				if (preg_match('/^[a-z]{2}(\-[A-Z]{2})?$/', $locale))
				{
					$arrLanguages[] = $locale;
				}
			}

			$locale = $chunks[0];

			// Language only, e.g. en or fr (see #29)
			if (preg_match('/^[a-z]{2}$/', $locale))
			{
				$arrLanguages[] = $locale;
			}
		}

		return array_slice(array_unique($arrLanguages), 0, 8);
	}


	/**
	 * Return accepted encoding types as array
	 *
	 * @return array The encoding types array
	 */
	protected static function httpAcceptEncoding()
	{
		return array_values(array_unique(explode(',', strtolower(static::get('HttpAcceptEncoding')))));
	}


	/**
	 * Return the user agent as string
	 *
	 * @return string The user agent string
	 */
	protected static function httpUserAgent()
	{
		$ua = strip_tags(static::get('HttpUserAgent'));
		$ua = preg_replace('/javascript|vbscri?pt|script|applet|alert|document|write|cookie/i', '', $ua);

		return substr($ua, 0, 255);
	}


	/**
	 * Return the HTTP Host
	 *
	 * @return string The host name
	 */
	protected static function httpHost()
	{
		if (static::get('HttpHost') != '')
		{
			$host = static::get('HttpHost');
		}
		else
		{
			$host = static::get('ServerName');

			if (static::get('ServerPort') != 80)
			{
				$host .= ':' . static::get('ServerPort');
			}
		}

		return preg_replace('/[^A-Za-z0-9\[\]\.:-]/', '', $host);
	}


	/**
	 * Return the HTTP X-Forwarded-Host
	 *
	 * @return string The name of the X-Forwarded-Host
	 */
	protected static function httpXForwardedHost()
	{
		return preg_replace('/[^A-Za-z0-9\[\]\.:-]/', '', static::get('HttpXForwardedHost'));
	}


	/**
	 * Return true if the current page was requested via an SSL connection
	 *
	 * @return boolean True if SSL is enabled
	 */
	protected static function ssl()
	{
		return (static::get('SslSessionId') || static::get('Https') == 'on' || static::get('Https') == 1);
	}


	/**
	 * Return the current URL without path or query string
	 *
	 * @return string The URL
	 */
	protected static function url()
	{
		$host = static::get('httpHost');
		$xhost = static::get('httpXForwardedHost');

		// SSL proxy
		if ($xhost != '' && $xhost == $GLOBALS['TL_CONFIG']['sslProxyDomain'])
		{
			return 'https://' .  $xhost . '/' . $host;
		}

		return (static::get('ssl') ? 'https://' : 'http://') . $host;
	}


	/**
	 * Return the current URL with path or query string
	 *
	 * @return string The URL
	 */
	protected static function uri()
	{
		return static::get('url') . static::get('requestUri');
	}


	/**
	 * Return the real REMOTE_ADDR even if a proxy server is used
	 *
	 * @return string The IP address of the client
	 */
	protected static function ip()
	{
		// No X-Forwarded-For IP
		if (static::get('HttpXForwardedFor') != '' || !preg_match('/^[A-Fa-f0-9, \.\:]+$/', static::get('HttpXForwardedFor')))
		{
			return substr(static::get('RemoteAddr'), 0, 64);
		}

		$strXip = static::get('HttpXForwardedFor');
		$arrTrusted = trimsplit(',', $GLOBALS['TL_CONFIG']['proxyServerIps']);

		// Generate an array of X-Forwarded-For IPs
		if (strpos($strXip, ',') !== false)
		{
			$arrIps = trimsplit(',', $strXip);
		}
		else
		{
			$arrIps = array($strXip);
		}

		$arrIps = array_reverse($arrIps);

		// Return the first untrusted IP address (see #5830)
		foreach ($arrIps as $strIp)
		{
			if (!in_array($strIp, $arrTrusted))
			{
				return substr($strIp, 0, 64);
			}
		}

		// If all X-Forward-For IPs are trusted, return the remote address
		return substr(static::get('RemoteAddr'), 0, 64);
	}


	/**
	 * Return the SERVER_ADDR
	 *
	 * @return string The IP address of the server
	 */
	protected static function server()
	{
		$strServer = static::get('ServerAddr') ?: static::get('LocalAddr');

		// Special workaround for Strato users
		if (empty($strServer))
		{
			$strServer = @gethostbyname(static::get('ServerName'));
		}

		return $strServer;
	}


	/**
	 * Return the relative path to the base directory (e.g. /path)
	 *
	 * @return string The relative path to the installation
	 */
	protected static function path()
	{
		return TL_PATH;
	}


	/**
	 * Return the relativ path to the script (e.g. index.php)
	 *
	 * @return string The relative path to the script
	 */
	protected static function script()
	{
		return preg_replace('/^' . preg_quote(TL_PATH, '/') . '\/?/', '', static::get('scriptName'));
	}


	/**
	 * Return the relativ path to the script and include the request (e.g. index.php?id=2)
	 *
	 * @return string The relative path to the script including the request string
	 */
	protected static function request()
	{
		$strRequest = preg_replace('/^' . preg_quote(TL_PATH, '/') . '\/?/', '', static::get('requestUri'));

		// From version 2.9, do not fallback to $this->script
		// anymore if the request string is empty (see #1844).

		// IE security fix (thanks to Michiel Leideman)
		$strRequest = str_replace(array('<', '>', '"'), array('%3C', '%3E', '%22'), $strRequest);

		// Do not urldecode() here (thanks to Russ McRee)!
		return $strRequest;
	}


	/**
	 * Return the request string without the index.php fragment
	 *
	 * @return string The request string without the index.php fragment
	 */
	protected static function indexFreeRequest()
	{
		$strRequest = static::get('request');

		if ($strRequest == 'index.php')
		{
			return '';
		}

		return $strRequest;
	}


	/**
	 * Return the URL and path that can be used in a <base> tag
	 *
	 * @return string The base URL
	 */
	protected static function base()
	{
		return static::get('url') . TL_PATH . '/';
	}


	/**
	 * Return the host name
	 *
	 * @return string The host name
	 */
	protected static function host()
	{
		return static::get('httpHost');
	}


	/**
	 * Return true on Ajax requests
	 *
	 * @return boolean True if it is an Ajax request
	 */
	protected static function isAjaxRequest()
	{
		return static::getRequest()->isXmlHttpRequest();
	}


	/**
	 * Return the operating system and the browser name and version
	 *
	 * @return object The agent information
	 */
	protected static function agent()
	{
		$ua = static::get('httpUserAgent');

		$return = new \stdClass();
		$return->string = $ua;

		$os = 'unknown';
		$mobile = false;
		$browser = 'other';
		$shorty = '';
		$version = '';
		$engine = '';

		// Operating system
		foreach ($GLOBALS['TL_CONFIG']['os'] as $k=>$v)
		{
			if (stripos($ua, $k) !== false)
			{
				$os = $v['os'];
				$mobile = $v['mobile'];
				break;
			}
		}

		// Android tablets are not mobile (see #4150)
		if ($os == 'android' && stripos($ua, 'mobile') === false)
		{
			$mobile = false;
		}

		$return->os = $os;

		// Browser and version
		foreach ($GLOBALS['TL_CONFIG']['browser'] as $k=>$v)
		{
			if (stripos($ua, $k) !== false)
			{
				$browser = $v['browser'];
				$shorty  = $v['shorty'];
				$version = preg_replace($v['version'], '$1', $ua);
				$engine  = $v['engine'];
				break;
			}
		}

		$versions = explode('.', $version);
		$version  = $versions[0];

		$return->class = $os . ' ' . $browser . ' ' . $engine;

		// Add the version number if available
		if ($version != '')
		{
			$return->class .= ' ' . $shorty . $version;
		}

		// Mark mobile devices
		if ($mobile)
		{
			$return->class .= ' mobile';
		}

		$return->browser  = $browser;
		$return->shorty   = $shorty;
		$return->version  = $version;
		$return->engine   = $engine;
		$return->versions = $versions;
		$return->mobile   = $mobile;

		return $return;
	}


	/**
	 * Prevent direct instantiation (Singleton)
	 *
	 * @deprecated Environment is now a static class
	 */
	protected function __construct() {}


	/**
	 * Prevent cloning of the object (Singleton)
	 *
	 * @deprecated Environment is now a static class
	 */
	final public function __clone() {}


	/**
	 * Return an environment variable
	 *
	 * @param string $strKey The variable name
	 *
	 * @return string The variable value
	 *
	 * @deprecated Use Environment::get() instead
	 */
	public function __get($strKey)
	{
		return static::get($strKey);
	}


	/**
	 * Set an environment variable
	 *
	 * @param string $strKey   The variable name
	 * @param mixed  $varValue The variable value
	 *
	 * @deprecated Use Environment::set() instead
	 */
	public function __set($strKey, $varValue)
	{
		static::set($strKey, $varValue);
	}


	/**
	 * Return the object instance (Singleton)
	 *
	 * @return \Environment The object instance
	 *
	 * @deprecated Environment is now a static class
	 */
	public static function getInstance()
	{
		if (static::$objInstance === null)
		{
			static::$objInstance = new static();
		}

		return static::$objInstance;
	}


	/**
	 * Return Symfony Request from DIC or create from globals
	 * @return  Request
	 */
	public static function getRequest()
	{
        static $globalRequest;
    	global $container;

        if (null === $container || ($request = $container->get('request_stack')->getCurrentRequest()) === null) {
            if (null === $globalRequest) {
                $globalRequest = Request::createFromGlobals();
            }

            $request = $globalRequest;
        }

        return $request;
	}
}
