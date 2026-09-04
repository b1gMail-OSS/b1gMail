<?php
/*
 * b1gMail public (NLI/LI) URL routing
 *
 * NLI (index.php): /login, /signup, /deref, /blog/…, /kma/…, /faq/…, …
 * LI (*.php):      /mail/inbox → email.php, /settings/mfa → prefs.php, /start/blog → start.php?action=blog
 * Plugins:        RegisterRoutes() via BMRoute() + fallback /{action} → index.php?action={action}
 */

require_once __DIR__ . '/route.registry.inc.php';

/** @var array<int, array{path: string, script: string, params: array<string, string>}>|null */
$routePublicPluginRoutes = null;

/**
 * @return bool
 */
function PublicRoutingActive()
{
	return UrlRoutingEnabled() || RoutePublicNeedsFrontController();
}

/**
 * @return bool
 */
function RoutePublicNeedsFrontController()
{
	if(defined('BM_ROUTE_FRONT') && BM_ROUTE_FRONT)
		return true;

	$script = isset($_SERVER['SCRIPT_NAME']) ? (string)$_SERVER['SCRIPT_NAME'] : '';

	return $script !== '' && basename($script) === 'app.php';
}

/**
 * Host from the current request (no port).
 *
 * @return string
 */
function RouteRequestHost()
{
	$host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
	$host = strtolower(preg_replace('/:\d+$/', '', $host));
	return $host;
}

/**
 * True if two hosts are the same or a www / non-www pair.
 *
 * @param string $a
 * @param string $b
 * @return bool
 */
function RouteHostsAreWwwAliases($a, $b)
{
	$a = strtolower((string)$a);
	$b = strtolower((string)$b);
	if($a === '' || $b === '')
		return false;
	if($a === $b)
		return true;

	return $a === 'www.' . $b || $b === 'www.' . $a;
}

/**
 * Replace the host of an absolute URL.
 *
 * @param string $url
 * @param string $host
 * @return string
 */
function RouteUrlWithHost($url, $host)
{
	$parts = parse_url((string)$url);
	if(!is_array($parts) || empty($parts['host']) || $host === '')
		return $url;

	$scheme = isset($parts['scheme']) ? $parts['scheme'] : 'https';
	$port = isset($parts['port']) ? ':' . $parts['port'] : '';
	$path = isset($parts['path']) ? $parts['path'] : '/';
	$query = isset($parts['query']) ? '?' . $parts['query'] : '';

	return $scheme . '://' . $host . $port . $path . $query;
}

/**
 * Install root URL (trailing slash), honouring HTTPS / ssl_url (reverse proxy safe).
 *
 * www and the bare domain are treated as the same site: asset and link URLs
 * follow the host of the current request so the two origins cannot diverge.
 *
 * @return string
 */
function RouteInstallRootUrl()
{
	global $bm_prefs;

	$url = trim((string)($bm_prefs['selfurl'] ?? ''));

	if($url !== '' && !preg_match('#^https?://#i', $url))
		$url = 'https://' . ltrim($url, '/');

	if(function_exists('SessionRequestIsHttps') && SessionRequestIsHttps())
	{
		if(!empty($bm_prefs['ssl_url']))
			$url = $bm_prefs['ssl_url'];
		else if($url !== '')
			$url = preg_replace('#^http://#i', 'https://', $url);
	}

	if($url === '' && !empty($_SERVER['HTTP_HOST']))
	{
		$scheme = (function_exists('SessionRequestIsHttps') && SessionRequestIsHttps()) ? 'https' : 'http';
		$url = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
	}

	$url = rtrim($url, '/') . '/';

	$reqHost = RouteRequestHost();
	$cfgHost = parse_url($url, PHP_URL_HOST);
	if($reqHost !== '' && is_string($cfgHost) && RouteHostsAreWwwAliases($reqHost, $cfgHost))
		$url = rtrim(RouteUrlWithHost($url, $reqHost), '/') . '/';

	return $url;
}

/**
 * FQDN install root (trailing slash).
 *
 * @return string
 */
function PublicFqdnSelfUrl()
{
	return RouteInstallRootUrl();
}

/**
 * Ensure a public URL has a scheme and host (e.g. for e-mails).
 *
 * @param string $url
 * @return string
 */
function PublicEnsureAbsoluteUrl($url)
{
	if(!is_string($url) || $url === '')
		return $url;

	if(preg_match('#^https?://#i', $url))
		return $url;

	$base = rtrim(PublicFqdnSelfUrl(), '/');
	if($base !== '' && preg_match('#^https?://#i', $base))
		return $base . '/' . ltrim($url, '/');

	if(!empty($_SERVER['HTTP_HOST']))
	{
		$scheme = (function_exists('SessionRequestIsHttps') && SessionRequestIsHttps()) ? 'https' : 'http';
		return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($url, '/');
	}

	return $url;
}

/**
 * @param string $path Relative path (no leading slash)
 * @return string
 */
function PublicPublicUrl($path)
{
	if(!is_string($path) || $path === '')
		return $path;

	if(preg_match('#^https?://#i', $path))
		return $path;

	if(PublicRoutingActive())
		$path = PublicFqdnSelfUrl() . ltrim($path, '/');

	return PublicEnsureAbsoluteUrl($path);
}

/**
 * Redirect direct index.php GET requests to the matching pretty URL.
 */
function RouteRedirectLegacyPublicIndexIfNeeded()
{
	$script = isset($_SERVER['SCRIPT_NAME']) ? basename((string)$_SERVER['SCRIPT_NAME']) : '';
	$legacyIndex = ($script === 'index.php');

	if(!$legacyIndex)
	{
		if(defined('BM_ROUTE_FRONT') && BM_ROUTE_FRONT)
		{
			$publicPath = RouteParsePublicPathFromRequest();
			if($publicPath === 'index.php')
				$legacyIndex = true;
		}

		if(!$legacyIndex && !empty($_SERVER['REQUEST_URI']))
		{
			$uriPath = parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH);
			if(is_string($uriPath) && preg_match('#/index\.php$#i', $uriPath))
				$legacyIndex = true;
		}
	}

	if(!$legacyIndex)
		return;

	$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string)$_SERVER['REQUEST_METHOD']) : 'GET';
	if($method !== 'GET' && $method !== 'HEAD')
		return;

	$noRedirect = array(
		'resetpassword',
		'activateaccount',
		'confirmalias',
		'readcertmail',
		'completeaddressbookentry',
		'checkaddressavailability',
		'showaddresssugestions',
		'codegen',
		'initiatesession',
	);
	$action = isset($_GET['action']) ? RouteNormalizePathSegment((string)$_GET['action']) : 'login';
	if(in_array($action, $noRedirect, true))
		return;

	$params = $_GET;
	unset($params['sid']);

	$target = PublicBuildPath('index.php', $params);
	if(!is_string($target) || $target === '' || $target === 'index.php')
		return;

	if(function_exists('PublicNavRelativePath'))
		$target = PublicNavRelativePath($target);
	if($target !== '' && $target[0] !== '/')
		$target = '/' . $target;

	header('Location: ' . $target, true, 301);
	exit();
}

/**
 * When SSL login is required, redirect HTTP NLI GET to ssl_url (keeps CSRF cookies consistent).
 */
function RouteRedirectLegacyPublicLoginHttpsIfNeeded()
{
	global $bm_prefs;

	if(!isset($bm_prefs['ssl_login_enable']) || $bm_prefs['ssl_login_enable'] !== 'yes')
		return;

	if(empty($bm_prefs['ssl_url']))
		return;

	if(function_exists('SessionRequestIsHttps') && SessionRequestIsHttps())
		return;

	$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string)$_SERVER['REQUEST_METHOD']) : 'GET';
	if($method !== 'GET' && $method !== 'HEAD')
		return;

	$ssl = rtrim((string)$bm_prefs['ssl_url'], '/');
	$uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/';
	if($uri === '')
		$uri = '/';

	header('Location: ' . $ssl . $uri, true, 302);
	exit();
}

/**
 * Parse path relative to install root (lowercase segments).
 *
 * @return string
 */
function RouteParsePublicPathFromRequest()
{
	$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$path = parse_url($uri, PHP_URL_PATH);
	if(!is_string($path) || $path === '')
		return '';

	$path = rawurldecode($path);
	$prefix = RouteAdminUrlPrefix();
	if($prefix !== '' && strpos($path, $prefix) === 0)
		$path = substr($path, strlen($prefix));
	$path = trim($path, '/');

	return RouteNormalizeAdminPath($path);
}

/**
 * Path segments from REQUEST_URI without lowercasing (for case-sensitive tokens).
 *
 * @return array<int, string>
 */
function RouteParsePublicRawSegmentsFromRequest()
{
	$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$path = parse_url($uri, PHP_URL_PATH);
	if(!is_string($path) || $path === '')
		return array();

	$path = rawurldecode($path);
	$prefix = RouteAdminUrlPrefix();
	if($prefix !== '' && strpos($path, $prefix) === 0)
		$path = substr($path, strlen($prefix));
	$path = trim($path, '/');
	if($path === '')
		return array();

	$segments = explode('/', $path);
	$out = array();
	foreach($segments as $segment)
	{
		if($segment !== '')
			$out[] = $segment;
	}

	return $out;
}

/**
 * @param string $path
 * @param string $script
 * @param array<string, string> $params
 */
function RouteRegisterPublicRoute($path, $script, array $params = array())
{
	global $routePublicPluginRoutes;

	if($routePublicPluginRoutes === null)
		$routePublicPluginRoutes = array();

	$routePublicPluginRoutes[] = array(
		'path'   => RouteNormalizeAdminPath($path),
		'script' => $script,
		'params' => $params,
	);
}

/**
 * Collect optional RegisterRoutes() from plugins (after init).
 */
function RouteInitPluginRoutes()
{
	global $routePublicPluginRoutes;

	if($routePublicPluginRoutes === null)
		$routePublicPluginRoutes = array();

	ModuleFunction('RegisterRoutes');
}

/**
 * @return array<string, string>
 */
function RouteGetNliStaticRoutes()
{
	return array(
		''              => array('script' => 'index.php', 'params' => array()),
		'login'         => array('script' => 'index.php', 'params' => array()),
		'index.php'     => array('script' => 'index.php', 'params' => array()),
		'signup'        => array('script' => 'index.php', 'params' => array('action' => 'signup')),
		'register'      => array('script' => 'index.php', 'params' => array('action' => 'signup')),
		'tos'           => array('script' => 'index.php', 'params' => array('action' => 'tos')),
		'imprint'       => array('script' => 'index.php', 'params' => array('action' => 'imprint')),
		'codegen'       => array('script' => 'index.php', 'params' => array('action' => 'codegen')),
		'faq'           => array('script' => 'index.php', 'params' => array('action' => 'faq')),
		'lost-password' => array('script' => 'index.php', 'params' => array('action' => 'lostPassword')),
		'password'      => array('script' => 'index.php', 'params' => array('action' => 'lostPassword')),
		'mfa-verify'    => array('script' => 'index.php', 'params' => array('action' => 'mfaVerify')),
		'switchlanguage'=> array('script' => 'index.php', 'params' => array('action' => 'switchLanguage')),
		'checkaddressavailability' => array('script' => 'index.php', 'params' => array('action' => 'checkAddressAvailability')),
		'showaddresssugestions'    => array('script' => 'index.php', 'params' => array('action' => 'showAddressSugestions')),
		'deref'         => array('script' => 'deref.php', 'params' => array()),
	);
}

/**
 * @return array<string, string> path key => script filename
 */
function RouteGetLiScriptMap()
{
	static $map = null;

	if($map !== null)
		return $map;

	$map = array(
		'home'     => 'start.php',
		'start'    => 'start.php',
		'mail'     => 'email.php',
		'inbox'    => 'email.php',
		'settings' => 'prefs.php',
		'prefs'    => 'prefs.php',
	);

	foreach(glob(RouteRootDir() . '*.php') as $file)
	{
		$base = basename($file);
		if(in_array($base, array('index.php', 'app.php', 'deref.php'), true))
			continue;

		if(preg_match('/^([^.]+)\.([^.]+)\.php$/', $base, $m))
			$map[strtolower($m[1] . '/' . $m[2])] = $base;
		else
			$map[strtolower(pathinfo($base, PATHINFO_FILENAME))] = $base;
	}

	return $map;
}

/**
 * @param string $module calendar|todo|addressbook|notes
 * @return array<string, string> legacy action => URL segment
 */
function RouteOrganizerActionAliases($module)
{
	static $aliases = array(
		'calendar' => array(
			'editDate'   => 'edit',
			'addDate'    => 'add',
			'createDate' => 'create',
			'saveDate'   => 'save',
			'deleteDate' => 'delete',
			'showDate'   => 'show',
			'dayView'    => 'dayview',
			'groups'     => 'groups',
			'action'     => 'action',
		),
		'todo' => array(
			'editTask'   => 'edit',
			'addTask'    => 'add',
			'createTask' => 'create',
			'saveTask'   => 'save',
			'deleteTask' => 'delete',
			'getLists'   => 'getlists',
			'addList'    => 'addlist',
			'deleteList' => 'deletelist',
			'action'     => 'action',
		),
		'addressbook' => array(
			'editContact'        => 'edit',
			'addContact'         => 'add',
			'createContact'      => 'create',
			'saveContact'        => 'save',
			'deleteContact'      => 'delete',
			'showContact'        => 'show',
			'groups'             => 'groups',
			'exportDialog'       => 'exportdialog',
			'importDialogStart'  => 'importdialogstart',
			'importDialog'       => 'importdialog',
			'groupAction'        => 'groupaction',
			'deleteGroup'        => 'deletegroup',
			'editGroup'          => 'editgroup',
			'saveGroup'          => 'savegroup',
			'userPictureDialog'  => 'userpicturedialog',
			'vcfImportDialog'    => 'vcfimportdialog',
			'attendeePopup'      => 'attendeepopup',
			'addressbookPicture' => 'addressbookpicture',
			'quickAdd'           => 'quickadd',
			'selfComplete'       => 'selfcomplete',
			'sendSelfComplete'   => 'sendselfcomplete',
			'action'             => 'action',
		),
		'notes' => array(
			'editNote'     => 'edit',
			'addNote'      => 'add',
			'createNote'   => 'create',
			'saveNote'     => 'save',
			'deleteNote'   => 'delete',
			'getNoteText'  => 'getnotetext',
			'action'       => 'action',
		),
	);

	return isset($aliases[$module]) ? $aliases[$module] : array();
}

/**
 * @param string $module
 * @param string $action
 * @return string
 */
function RouteOrganizerActionToSegment($module, $action)
{
	$aliases = RouteOrganizerActionAliases($module);
	if(isset($aliases[$action]))
		return $aliases[$action];

	return RouteNormalizePathSegment($action);
}

/**
 * @param string $module
 * @param string $segment
 * @return string
 */
function RouteOrganizerSegmentToAction($module, $segment)
{
	$segment = RouteNormalizePathSegment($segment);
	$aliases = RouteOrganizerActionAliases($module);
	foreach($aliases as $action => $alias)
	{
		if($alias === $segment)
			return $action;
	}

	return RouteRestoreLegacyAction($segment);
}

/**
 * @param string $module
 * @param array<int, string> $rest
 * @return array<string, string>
 */
function RouteOrganizerParamsFromRest($module, array $rest)
{
	$params = array();
	if(empty($rest))
		return $params;

	// Relative "organizer.notes.php" under /organizer/notes/ must not overwrite ?action=
	if(preg_match('/\.php$/i', (string)$rest[0]))
		return $params;

	$params['action'] = RouteOrganizerSegmentToAction($module, $rest[0]);
	if(isset($rest[1]) && $rest[1] !== '')
		$params['id'] = $rest[1];

	return $params;
}

/**
 * @param string $pathKey e.g. organizer/calendar
 * @param array<string, mixed> $params
 * @return array{path: string, extra: array<string, string>}
 */
function RoutePublicPathFromOrganizer($pathKey, array $params)
{
	$module = substr($pathKey, strlen('organizer/'));
	$path = $pathKey;
	$exclude = array();

	$action = isset($params['action']) ? (string)$params['action'] : '';
	if($action !== '' && $action !== 'start')
	{
		$path .= '/' . RouteOrganizerActionToSegment($module, $action);
		$exclude[] = 'action';
	}

	if(isset($params['id']) && (string)$params['id'] !== '')
	{
		$path .= '/' . rawurlencode((string)$params['id']);
		$exclude[] = 'id';
	}

	return array(
		'path'  => $path,
		'extra' => RoutePublicFilterExtraParams($params, $exclude),
	);
}

/**
 * @param string $action
 * @param array<int, string> $rest
 * @return array<string, string>
 */
function RoutePrefsParamsFromRest($action, array $rest)
{
	$params = array('action' => RouteRestoreLegacyAction($action));
	if(empty($rest))
		return $params;

	$params['do'] = RouteRestoreLegacyDo($rest[0]);
	if(isset($rest[1]) && $rest[1] !== '')
		$params['id'] = $rest[1];

	return $params;
}

/**
 * @param string $action
 * @param array<string, mixed> $params
 * @return array{path: string, extra: array<string, string>}
 */
function RoutePublicPathFromPrefs($action, array $params)
{
	$path = 'settings/' . RouteNormalizePathSegment($action);
	$exclude = array('action');

	$do = isset($params['do']) ? RouteNormalizePathSegment((string)$params['do']) : '';
	if($do !== '')
	{
		$path .= '/' . $do;
		$exclude[] = 'do';
		if(isset($params['id']) && (string)$params['id'] !== '')
		{
			$path .= '/' . rawurlencode((string)$params['id']);
			$exclude[] = 'id';
		}
	}

	return array(
		'path'  => $path,
		'extra' => RoutePublicFilterExtraParams($params, $exclude),
	);
}

/**
 * @param array<int, string> $segments
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchLiPath(array $segments)
{
	$map = RouteGetLiScriptMap();

	if(empty($segments))
		return array('script' => 'start.php', 'params' => array());

	if($segments[0] === 'email' && isset($segments[1]) && $segments[1] === 'read'
		&& isset($segments[2]) && ctype_digit((string)$segments[2]))
	{
		return array('script' => 'email.read.php', 'params' => array('id' => $segments[2]));
	}

	if($segments[0] === 'mail')
	{
		if(isset($segments[1]) && ctype_digit((string)$segments[1]))
		{
			return array('script' => 'email.read.php', 'params' => array('id' => $segments[1]));
		}

		$params = array();
		if(isset($segments[1]) && $segments[1] === 'inbox')
			$params['folder'] = '0';
		else if(isset($segments[1]) && $segments[1] === 'folder' && isset($segments[2]))
			$params['folder'] = $segments[2];

		return array('script' => 'email.php', 'params' => $params);
	}

	if($segments[0] === 'settings' || $segments[0] === 'prefs')
	{
		if(!isset($segments[1]) || $segments[1] === '')
			return array('script' => 'prefs.php', 'params' => array());

		return array(
			'script' => 'prefs.php',
			'params' => RoutePrefsParamsFromRest($segments[1], array_slice($segments, 2)),
		);
	}

	if($segments[0] === 'start' && isset($segments[1]) && $segments[1] !== '')
	{
		return array(
			'script' => 'start.php',
			'params' => array('action' => RouteRestoreLegacyAction($segments[1])),
		);
	}

	$count = count($segments);
	for($len = $count; $len >= 1; $len--)
	{
		$key = implode('/', array_slice($segments, 0, $len));
		if(!isset($map[$key]))
			continue;

		$params = array();
		$rest = array_slice($segments, $len);
		if($key === 'email.php' || $key === 'email')
		{
			if(isset($rest[0]) && $rest[0] === 'inbox')
				$params['folder'] = '0';
			else if(isset($rest[0]) && $rest[0] === 'folder' && isset($rest[1]))
				$params['folder'] = $rest[1];
		}
		else if(strpos($key, 'organizer/') === 0 && !empty($rest))
			$params = array_merge($params, RouteOrganizerParamsFromRest(substr($key, strlen('organizer/')), $rest));
		else if(!empty($rest) && strpos($map[$key], 'prefs.') === 0)
			$params['action'] = $rest[0];

		return array('script' => $map[$key], 'params' => $params);
	}

	return null;
}

/**
 * @param array<int, string> $segments
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchRegisteredPluginRoutes(array $segments)
{
	global $routePublicPluginRoutes;

	if(!is_array($routePublicPluginRoutes) || empty($routePublicPluginRoutes))
		return null;

	$path = implode('/', $segments);

	$sorted = $routePublicPluginRoutes;
	usort($sorted, function($a, $b) {
		return strlen($b['path']) - strlen($a['path']);
	});

	foreach($sorted as $route)
	{
		if($route['path'] === $path)
			return array('script' => $route['script'], 'params' => $route['params']);
	}

	return null;
}

/**
 * Fallback: /{action} → index.php?action={action} (plugins FileHandler).
 *
 * @param array<int, string> $segments
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchPluginActionFallback(array $segments)
{
	static $reserved = array(
		'admin', 'interface', 'clientlib', 'plugins', 'templates', 'serverlib',
		'temp', 'languages', 'plz', 'mail', 'settings', 'prefs', 'start', 'home', 'inbox',
		'deref',
	);

	if(count($segments) !== 1)
		return null;

	$action = $segments[0];
	if($action === '' || in_array($action, $reserved, true))
		return null;

	if(!preg_match('/^[a-z][a-z0-9_-]*$/', $action))
		return null;

	return array(
		'script' => 'index.php',
		'params' => array('action' => $action),
	);
}

/**
 * @param string $publicPath
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchPublicPath($publicPath)
{
	$publicPath = RouteNormalizeAdminPath($publicPath);
	$segments = ($publicPath === '') ? array() : explode('/', $publicPath);

	if(isset($segments[0]) && $segments[0] === 'reset-password')
	{
		$raw = RouteParsePublicRawSegmentsFromRequest();
		$match = RouteMatchResetPassword(!empty($raw) ? $raw : $segments);
		if($match !== null)
			return $match;

		$key = '';
		if(isset($raw[1]))
			$key = trim((string)$raw[1]);
		else if(isset($segments[1]))
			$key = trim((string)$segments[1]);

		return array(
			'script' => 'index.php',
			'params' => array(
				'action' => 'resetPassword',
				'key'    => $key,
			),
		);
	}

	$nli = RouteGetNliStaticRoutes();
	if(isset($nli[$publicPath]))
		return $nli[$publicPath];

	$match = RouteRunMatchers($segments);
	if($match !== null)
		return $match;

	$match = RouteMatchLiPath($segments);
	if($match !== null)
		return $match;

	$match = RouteMatchRegisteredPluginRoutes($segments);
	if($match !== null)
		return $match;

	return RouteMatchPluginActionFallback($segments);
}

/**
 * Build pretty path + query from script and params.
 *
 * @param string $script
 * @param array<string, mixed> $params
 * @param bool $trailingAmp
 * @return string
 */
function PublicBuildPath($script, array $params = array(), $trailingAmp = false)
{
	$publicPath = RoutePublicPathFromLegacy($script, $params);
	if($publicPath === null)
	{
		$url = $script;
		if(!empty($params))
			$url .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
		if($trailingAmp && strpos($url, '?') === false)
			$url .= '?';
		else if($trailingAmp)
			$url .= '&';

		return PublicPublicUrl($url);
	}

	$extra = $publicPath['extra'];
	$url = $publicPath['path'];
	if(!empty($extra))
		$url .= '?' . http_build_query($extra, '', '&', PHP_QUERY_RFC3986);
	if($trailingAmp)
	{
		if(strpos($url, '?') !== false)
			$url .= '&';
		else
			$url .= '?';
	}

	return PublicPublicUrl($url);
}

/**
 * @param string $script
 * @param array<string, mixed> $params
 * @return array{path: string, extra: array<string, string>}|null
 */
function RoutePublicPathFromLegacy($script, array $params)
{
	$script = basename($script);

	// Avatar is served as a binary endpoint; keep legacy avatar.php (direct script, single bootstrap).
	if($script === 'avatar.php')
		return null;

	if($script === 'deref.php')
		return array('path' => 'deref', 'extra' => RoutePublicFilterExtraParams($params, array()));

	// Build pretty path from legacy script + query (inverse of RouteMatchPublicPath).
	if($script === 'index.php')
	{
		$action = isset($params['action']) ? RouteNormalizePathSegment((string)$params['action']) : '';

		if($action === '' || $action === 'login')
			return array('path' => 'login', 'extra' => RoutePublicFilterExtraParams($params, array('action')));

		$actionAliases = array(
			'lostpassword'  => 'lost-password',
			'mfaverify'     => 'mfa-verify',
			'forgetcookie'  => 'forget-cookie',
		);
		if(isset($actionAliases[$action]))
			return array('path' => $actionAliases[$action], 'extra' => RoutePublicFilterExtraParams($params, array('action')));

		if($action === 'newsplugin')
			return array('path' => 'news', 'extra' => RoutePublicFilterExtraParams($params, array('action')));

		$pluginLegacy = RouteRunLegacyConverters($script, $params);
		if($pluginLegacy !== null)
			return $pluginLegacy;

		if($action !== '')
			return array('path' => $action, 'extra' => RoutePublicFilterExtraParams($params, array('action')));
	}

	if($script === 'start.php')
	{
		$pluginLegacy = RouteRunLegacyConverters($script, $params);
		if($pluginLegacy !== null)
			return $pluginLegacy;

		$action = isset($params['action']) ? RouteNormalizePathSegment((string)$params['action']) : '';
		if($action === 'faxplugin')
			return array('path' => 'start/fax', 'extra' => RoutePublicFilterExtraParams($params, array('action')));
		if($action === '')
			return array('path' => 'start', 'extra' => RoutePublicFilterExtraParams($params, array('action')));
		return array('path' => 'start/' . $action, 'extra' => RoutePublicFilterExtraParams($params, array('action')));
	}

	if($script === 'email.php')
	{
		$folder = isset($params['folder']) ? (string)$params['folder'] : '';
		if($folder === '0' || $folder === '')
			return array('path' => 'mail/inbox', 'extra' => RoutePublicFilterExtraParams($params, array('folder')));
		return array('path' => 'mail/folder/' . rawurlencode($folder), 'extra' => RoutePublicFilterExtraParams($params, array('folder')));
	}

	if($script === 'email.read.php')
	{
		$action = isset($params['action']) ? (string)$params['action'] : '';
		if($action !== '' && $action !== 'read')
			return null;

		if(isset($params['id']) && (string)$params['id'] !== '')
		{
			return array(
				'path'  => 'email/read/' . rawurlencode((string)$params['id']),
				'extra' => RoutePublicFilterExtraParams($params, array('id')),
			);
		}
	}

	if($script === 'prefs.php')
	{
		$pluginLegacy = RouteRunLegacyConverters($script, $params);
		if($pluginLegacy !== null)
			return $pluginLegacy;

		$action = isset($params['action']) ? (string)$params['action'] : '';
		if($action === '')
			return array('path' => 'settings', 'extra' => RoutePublicFilterExtraParams($params, array('action')));

		return RoutePublicPathFromPrefs($action, $params);
	}

	$map = RouteGetLiScriptMap();
	$key = null;
	if(preg_match('/^([^.]+)\.([^.]+)\.php$/', $script, $m))
		$key = strtolower($m[1] . '/' . $m[2]);
	else
	{
		$stem = strtolower(pathinfo($script, PATHINFO_FILENAME));
		if(isset($map[$stem]))
			$key = $stem;
	}

	if($key !== null && isset($map[$key]))
	{
		if(strpos($key, 'organizer/') === 0)
			return RoutePublicPathFromOrganizer($key, $params);

		return array('path' => $key, 'extra' => RoutePublicFilterExtraParams($params, array()));
	}

	return null;
}

/**
 * @param array<string, mixed> $params
 * @param array<int, string> $exclude
 * @return array<string, string>
 */
function RoutePublicFilterExtraParams(array $params, array $exclude)
{
	$extra = array();
	foreach($params as $k => $v)
	{
		if(in_array($k, $exclude, true))
			continue;
		if(is_scalar($v))
			$extra[$k] = (string)$v;
	}
	return $extra;
}

/**
 * @param string $script
 * @param array<string, mixed> $params
 * @param bool $trailingAmp
 * @return string
 */
function NliUrl($script = 'index.php', array $params = array(), $trailingAmp = false)
{
	return PublicBuildPath($script, $params, $trailingAmp);
}

/**
 * @param string $script
 * @param array<string, mixed> $params
 * @param bool $trailingAmp
 * @return string
 */
function LiUrl($script, array $params = array(), $trailingAmp = false)
{
	return PublicBuildPath($script, $params, $trailingAmp);
}

/**
 * Convert legacy user/NLI URL when routing is active.
 *
 * @param string $url
 * @return string|null
 */
function PublicConvertLegacyUrl($url)
{
	if(!is_string($url) || $url === '')
		return null;

	if(strpos($url, '../') !== false || strpos($url, './') === 0)
		return null;

	if(preg_match('#^https?://#i', $url))
	{
		$path = parse_url($url, PHP_URL_PATH);
		if(!is_string($path) || stripos($path, '/admin/') !== false)
			return null;
		if(stripos($path, '.php') === false)
			return $url;
		$query = parse_url($url, PHP_URL_QUERY);
		$url = basename($path) . ($query !== null && $query !== '' ? '?' . $query : '');
	}

	if(stripos($url, '.php') === false)
		return null;

	$trailingAmp = false;
	if(preg_match('/[&?]$/', $url))
	{
		$trailingAmp = substr($url, -1) === '&';
		$url = rtrim($url, '?&');
	}

	$script = $url;
	$query = '';
	if(($pos = strpos($url, '?')) !== false)
	{
		$script = substr($url, 0, $pos);
		$query = substr($url, $pos + 1);
	}

	$params = array();
	if($query !== '')
		parse_str($query, $params);

	$publicPath = RoutePublicPathFromLegacy(basename($script), $params);
	if($publicPath === null)
		return null;

	if(PublicRoutingActive())
		return PublicBuildPath(basename($script), $params, $trailingAmp);

	global $bm_prefs;
	$rel = $publicPath['path'];
	if(!empty($publicPath['extra']))
		$rel .= '?' . http_build_query($publicPath['extra'], '', '&', PHP_QUERY_RFC3986);
	if($trailingAmp)
		$rel .= (strpos($rel, '?') !== false) ? '&' : '?';

	return rtrim($bm_prefs['selfurl'], '/') . '/' . ltrim($rel, '/');
}

/**
 * Strip install origin from a nav URL (forms/links stay on the current host/scheme).
 *
 * @param string $url
 * @return string
 */
function PublicNavRelativePath($url)
{
	if(!is_string($url) || $url === '')
		return $url;

	if(!preg_match('#^https?://#i', $url))
		return $url;

	$path = parse_url($url, PHP_URL_PATH);
	$query = parse_url($url, PHP_URL_QUERY);
	$rel = (is_string($path) && $path !== '') ? $path : '/';
	if(is_string($query) && $query !== '')
		$rel .= '?' . $query;

	return $rel;
}

/**
 * NLI nav URL with optional SSL base (signup/login).
 *
 * @param string $legacyUrl
 * @param string|null $sslPref bm_prefs key, e.g. ssl_signup_enable
 * @return string
 */
function PublicNavUrl($legacyUrl, $sslPref = null)
{
	global $bm_prefs;

	$url = SessionUrl($legacyUrl);
	if($sslPref !== null && isset($bm_prefs[$sslPref]) && $bm_prefs[$sslPref] === 'yes' && !empty($bm_prefs['ssl_url']))
	{
		$self = rtrim($bm_prefs['selfurl'], '/');
		if(strpos($url, $self) === 0)
			$url = rtrim($bm_prefs['ssl_url'], '/') . substr($url, strlen($self));
	}

	return PublicNavRelativePath($url);
}

/**
 * Replace relative index.php links in NLI HTML messages (e.g. after pretty-URL actions).
 *
 * @param string $html
 * @return string
 */
function NliResolveMessageLinks($html)
{
	global $bm_prefs;

	if(!is_string($html) || $html === '')
		return $html;

	$homeUrl = PublicRoutingActive()
		? PublicFqdnSelfUrl()
		: rtrim($bm_prefs['selfurl'], '/') . '/index.php';

	return str_replace(
		array(
			'href="index.php"',
			'href="index.php?action=imprint"',
		),
		array(
			'href="' . $homeUrl . '"',
			'href="' . PublicNavUrl('index.php?action=imprint') . '"',
		),
		$html
	);
}

/**
 * @param Template $tpl
 */
function AssignTemplatePublicNavUrls($tpl)
{
	if(defined('ADMIN_MODE') && ADMIN_MODE)
		return;

	$tpl->assign('nliUrlHome', PublicNavUrl('index.php'));
	$loginUrl = PublicNavUrl('index.php?action=login', 'ssl_login_enable');
	if($loginUrl === '' || stripos($loginUrl, '.php') !== false)
		$loginUrl = '/login';
	$tpl->assign('nliUrlLogin', $loginUrl);
	$tpl->assign('nliUrlSignup', PublicNavUrl('index.php?action=signup'));
	$tpl->assign('nliUrlSignupSsl', PublicNavUrl('index.php?action=signup', 'ssl_signup_enable'));
	$tpl->assign('nliUrlFaq', PublicNavUrl('index.php?action=faq'));
	$tpl->assign('nliUrlTos', PublicNavUrl('index.php?action=tos'));
	$tpl->assign('nliUrlImprint', PublicNavUrl('index.php?action=imprint'));
	$tpl->assign('nliUrlForgetCookie', PublicNavUrl('index.php?action=forgetCookie'));
	$tpl->assign('nliUrlLostPassword', PublicNavUrl('index.php?action=lostPassword', 'ssl_login_enable'));
	$tpl->assign('nliUrlMfaVerify', PublicNavUrl('index.php?action=mfaVerify'));
}

/**
 * Basename of the script handling this request (legacy entry or app.php route target).
 *
 * @return string
 */
function RouteRequestScriptBasename()
{
	if(defined('BM_ROUTE_TARGET_SCRIPT'))
		return basename(BM_ROUTE_TARGET_SCRIPT);

	return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

/**
 * Current request URL for forms/links (pretty path or legacy script), without session id.
 *
 * @return string
 */
function RouteRequestSelfUrl()
{
	if(PublicRoutingActive())
	{
		$script = RouteRequestScriptBasename();
		$params = $_GET;
		unset($params['sid']);
		$publicPath = RoutePublicPathFromLegacy($script, $params);
		if($publicPath !== null)
		{
			$path = $publicPath['path'];
			if(!empty($publicPath['extra']))
				$path .= '?' . http_build_query($publicPath['extra'], '', '&', PHP_QUERY_RFC3986);

			return PublicPublicUrl($path);
		}

		$path = RouteParsePublicPathFromRequest();
		if($path === 'app.php' || $path === '')
		{
			$path = $script;
			if(!empty($params))
				$path .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

			return PublicPublicUrl($path);
		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$query = parse_url($uri, PHP_URL_QUERY);
		if(is_string($query) && $query !== '')
		{
			parse_str($query, $params);
			unset($params['sid']);
			if(!empty($params))
				$path .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
		}

		return PublicPublicUrl($path);
	}

	$self = isset($_SERVER['PHP_SELF']) ? (string)$_SERVER['PHP_SELF'] : basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
	$self = ltrim($self, '/');

	$query = isset($_SERVER['QUERY_STRING']) ? (string)$_SERVER['QUERY_STRING'] : '';
	if($query !== '')
	{
		parse_str($query, $params);
		unset($params['sid']);
		if(!empty($params))
			$self .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
	}

	return $self;
}

/**
 * @return string Absolute path to target script
 */
function RouteResolvePublicScript()
{
	$publicPath = RouteParsePublicPathFromRequest();
	$match = RouteMatchPublicPath($publicPath);

	if($match === null)
	{
		header('HTTP/1.0 404 Not Found');
		echo 'Route not found.';
		exit();
	}

	if(!defined('BM_ROUTE_TARGET_SCRIPT'))
		define('BM_ROUTE_TARGET_SCRIPT', $match['script']);

	RouteApplyRequestParams($match['params']);

	$script = RouteRootDir() . $match['script'];
	if(!is_file($script))
	{
		header('HTTP/1.0 404 Not Found');
		echo 'Script not found.';
		exit();
	}

	return $script;
}

/**
 * @param array<int, string> $segments
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchResetPassword(array $segments)
{
	if(!isset($segments[0]) || $segments[0] !== 'reset-password')
		return null;

	if(count($segments) === 2)
	{
		$key = trim($segments[1]);
		if(strlen($key) !== 64 || !ctype_xdigit($key))
			return null;

		return array(
			'script' => 'index.php',
			'params' => array(
				'action' => 'resetPassword',
				'key'    => $key,
			),
		);
	}

	if(count($segments) === 3)
	{
		$userID = (int)$segments[1];
		$key = trim($segments[2]);

		if($userID <= 0 || strlen($key) !== 32 || !ctype_xdigit($key))
			return null;

		return array(
			'script' => 'index.php',
			'params' => array(
				'action' => 'resetPassword',
				'user'   => (string)$userID,
				'key'    => $key,
			),
		);
	}

	return null;
}

/**
 * @param string $script
 * @param array<string, mixed> $params
 * @return array{path: string, extra: array<string, string>}|null
 */
function RouteLegacyResetPassword($script, array $params)
{
	if($script !== 'index.php')
		return null;

	$action = isset($params['action']) ? RouteNormalizePathSegment((string)$params['action']) : '';
	if($action !== 'resetpassword')
		return null;

	$key = isset($params['key']) ? trim((string)$params['key']) : '';
	if($key === '')
		return null;

	if(strlen($key) === 64 && ctype_xdigit($key))
	{
		return array(
			'path'  => 'reset-password/' . rawurlencode($key),
			'extra' => array(),
		);
	}

	$userID = isset($params['user']) ? (int)$params['user'] : 0;
	if($userID <= 0)
		return null;

	return array(
		'path'  => 'reset-password/' . $userID . '/' . rawurlencode($key),
		'extra' => array(),
	);
}

/**
 * @param Template $tpl
 */
function AssignTemplatePublicRouteVars($tpl)
{
	$routing = PublicRoutingActive();
	$tpl->assign('publicRoutingEnabled', $routing);

	if(!$routing || ADMIN_MODE)
		return;

	global $bm_prefs;

	$theme = $tpl->getTemplateVars('_tplname');
	if(!is_string($theme) || $theme === '')
		$theme = $bm_prefs['template'];

	$tpl->assign('tpldir', PublicFqdnSelfUrl() . 'templates/' . $theme . '/');
}

RouteRegisterMatcher('RouteMatchResetPassword', 'RouteLegacyResetPassword', 30);
