<?php
/*
 * b1gMail URL routing
 *
 * ACP (Admin) — pretty URLs via /admin/app.php front controller (legacy *.php entry still resolves):
 *   /admin/                                    → welcome.php
 *   /admin/{script}                            → users.php, groups.php, …
 *   /admin/{bereich}/{modul}                   → prefs.common.php (default action)
 *   /admin/{bereich}/{modul}/{action}          → prefs.common.php?action=…
 *   /admin/plugin/{pluginname}                          → plugin.page.php?plugin=… (name case-insensitive)
 *   /admin/plugin/{pluginname}/{do}                   → plugin.page.php?plugin=…&do=…
 *   /admin/plugin/{pluginname}/{do}/{id}               → plugin.page.php?plugin=…&do=…&id=… (numeric id)
 *   /admin/plugin/{pluginname}/{do}/{id}/{action}      → plugin.page.php?plugin=…&do=…&id=…&action=…
 *   All path segments are lowercase in URLs and on incoming requests.
 *   Query string: ?save=true&do1=mail (extra params, not path segments)
 *
 * LI / NLI — see route-public.inc.php (app.php front controller).
 */

if(!function_exists('PublicRoutingActive'))
	require_once __DIR__ . '/route-public.inc.php';

/**
 * Absolute path to b1gMail root (src/), without relying on B1GMAIL_DIR constant.
 *
 * @return string Trailing slash
 */
function RouteRootDir()
{
	return dirname(__DIR__) . '/';
}

/**
 * Ensure url_routing preference column exists.
 */
function EnsureUrlRoutingPrefColumns()
{
	global $db;

	$res = $db->Query('SHOW COLUMNS FROM {pre}prefs LIKE ?', 'url_routing');
	$exists = $res->RowCount() > 0;
	$res->Free();

	if(!$exists)
		$db->Query("ALTER TABLE {pre}prefs ADD COLUMN `url_routing` enum('yes','no') NOT NULL DEFAULT 'no'");
}

/**
 * Apply defaults for url_routing pref (legacy column; always treated as enabled).
 */
function UrlRoutingApplyPrefDefaults()
{
	global $bm_prefs;

	$bm_prefs['url_routing'] = 'yes';
}

/**
 * Pretty URL generation is always enabled (ACP, LI, NLI).
 *
 * @return bool
 */
function UrlRoutingEnabled()
{
	return true;
}

/**
 * Generate and resolve pretty admin URLs (pref or current request via front controller).
 *
 * @return bool
 */
function AdminRoutingActive()
{
	return UrlRoutingEnabled() || RouteAdminNeedsAbsoluteUrls();
}

/**
 * Use FQDN for admin assets and links (pretty routing active).
 *
 * @return bool
 */
function AdminUseFqdnUrls()
{
	return AdminRoutingActive();
}

/**
 * Prepend admin FQDN base to a relative admin path.
 *
 * @param string $url
 * @return string
 */
function AdminPublicUrl($url)
{
	if(!is_string($url) || $url === '')
		return $url;

	if(preg_match('#^https?://#i', $url))
		return $url;

	if(!AdminUseFqdnUrls())
		return $url;

	return AdminFqdnBaseUrl() . ltrim($url, '/');
}

/**
 * Convert legacy admin URL (script.php?query) to pretty path when routing is active.
 *
 * @param string $url
 * @return string|null Null if not a convertible admin script URL
 */
function AdminConvertLegacyUrl($url)
{
	if(!AdminRoutingActive())
		return null;

	if(!is_string($url) || $url === '')
		return null;

	if(strpos($url, '../') !== false || strpos($url, './') === 0)
		return null;

	if(preg_match('#^https?://#i', $url))
	{
		$path = parse_url($url, PHP_URL_PATH);
		if(!is_string($path) || stripos($path, '/admin/') === false)
			return null;
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

	$script = basename($script);
	if(!preg_match('/\.php$/i', $script))
		return null;

	// Binary avatar endpoint: keep legacy avatar.php (stable on nested pretty-URL pages).
	if(strcasecmp($script, 'avatar.php') === 0)
		return null;

	$params = array();
	if($query !== '')
		parse_str($query, $params);

	return AdminUrl($script, $params, $trailingAmp);
}

/**
 * Normalize one URL path segment to lowercase (pretty URLs are always lower case).
 *
 * @param string $segment
 * @return string
 */
function RouteNormalizePathSegment($segment)
{
	return strtolower(rawurldecode((string)$segment));
}

/**
 * Restore legacy action names from lowercase pretty-URL segments.
 *
 * @param string $action
 * @return string
 */
function RouteRestoreLegacyAction($action)
{
	$action = (string)$action;
	static $map = array(
		'sessionlock'          => 'sessionLock',
		'sessionlocknow'       => 'sessionLockNow',
		'sessionunlock'        => 'sessionUnlock',
		'sessionkeepalive'     => 'sessionKeepAlive',
		'sessionstatus'        => 'sessionStatus',
		'mfasetup'             => 'mfaSetup',
		'mfaverify'            => 'mfaVerify',
		'resetpassword'        => 'resetPassword',
		'reset-password'       => 'resetPassword',
		'showwidgetprefs'      => 'showWidgetPrefs',
		'savecustomize'        => 'saveCustomize',
		'getnotifications'     => 'getNotifications',
		'getnotificationcount' => 'getNotificationCount',
		'testpush'             => 'testPush',
		'savewidgetorder'      => 'saveWidgetOrder',
		'checksafecode'             => 'checkSafeCode',
		'checkaddressavailability'  => 'checkAddressAvailability',
		'showaddresssugestions'     => 'showAddressSugestions',
		'forgetcookie'              => 'forgetCookie',
		'confirmalias'              => 'confirmAlias',
		'readcertmail'              => 'readCertMail',
		'completeaddressbookentry'  => 'completeAddressBookEntry',
		'activateaccount'           => 'activateAccount',
		'initiatesession'           => 'initiateSession',
		'switchlanguage'            => 'switchLanguage',
		'generatevapid'             => 'generateVapid',
		'faxplugin'                 => 'faxPlugin',
		'fax'                       => 'faxPlugin',
	);

	$lower = strtolower($action);
	if(isset($map[$lower]))
		return $map[$lower];

	return $action;
}

/**
 * Restore legacy prefs.php do= values from lowercase pretty-URL segments.
 *
 * @param string $do
 * @return string
 */
function RouteRestoreLegacyDo($do)
{
	$do = (string)$do;
	static $map = array(
		'save'                      => 'save',
		'redeem'                    => 'redeem',
		'edit'                      => 'edit',
		'add'                       => 'add',
		'delete'                    => 'delete',
		'action'                    => 'action',
		'create'                    => 'create',
		'update'                    => 'update',
		'download'                  => 'download',
		'savefilter'                => 'saveFilter',
		'editconditions'            => 'editConditions',
		'editactions'               => 'editActions',
		'createfilter'              => 'createFilter',
		'resetdb'                   => 'resetDB',
		'createsignature'           => 'createSignature',
		'savesignature'             => 'saveSignature',
		'createaccount'             => 'createAccount',
		'saveaccount'               => 'saveAccount',
		'changepw'                  => 'changePW',
		'chargeaccount'             => 'chargeAccount',
		'cancelaccount'             => 'cancelAccount',
		'reallycancelaccount'       => 'reallyCancelAccount',
		'showinvoice'               => 'showInvoice',
		'initiatepayment'           => 'initiatePayment',
		'deleteorder'               => 'deleteOrder',
		'importprivatecertificate'  => 'importPrivateCertificate',
		'uploadprivatecertificate'  => 'uploadPrivateCertificate',
		'importpubliccertificate'   => 'importPublicCertificate',
		'uploadpubliccertificate'   => 'uploadPublicCertificate',
		'issueprivatecertificate'   => 'issuePrivateCertificate',
		'showcertificate'           => 'showCertificate',
		'downloadcertificate'       => 'downloadCertificate',
		'exportprivatecertificate'  => 'exportPrivateCertificate',
		'downloadprivatecertificate'=> 'downloadPrivateCertificate',
		'getprivatecertificate'     => 'getPrivateCertificate',
		'mfasave'                   => 'mfaSave',
		'placeorder'                => 'placeOrder',
		'cancelaccountwarning'      => 'cancelAccountWarning',
	);

	$lower = strtolower($do);
	if(isset($map[$lower]))
		return $map[$lower];

	return $do;
}

/**
 * Normalize admin path after /admin/ (lowercase segments).
 *
 * @param string $adminPath
 * @return string
 */
function RouteNormalizeAdminPath($adminPath)
{
	$adminPath = trim((string)$adminPath, '/');
	if($adminPath === '')
		return '';

	$segments = explode('/', $adminPath);
	$normalized = array();
	foreach($segments as $segment)
	{
		if($segment === '')
			continue;
		$normalized[] = RouteNormalizePathSegment($segment);
	}

	return implode('/', $normalized);
}

/**
 * Pretty-URL slug for an admin plugin page (/admin/plugin/{slug}/).
 *
 * @param string $internalName Plugin class / module name (e.g. SignaturePlugin)
 * @return string
 */
function RoutePluginAdminSlug($internalName)
{
	global $plugins;

	$internalName = (string)$internalName;
	if($internalName === '')
		return '';

	if(isset($plugins) && is_object($plugins)
		&& isset($plugins->_plugins[$internalName]['instance']))
	{
		$inst = $plugins->_plugins[$internalName]['instance'];
		if(isset($inst->admin_route_slug) && trim((string)$inst->admin_route_slug) !== '')
			return RouteNormalizePathSegment((string)$inst->admin_route_slug);
	}

	return RouteNormalizePathSegment($internalName);
}

/**
 * Resolve plugin internal name from a lowercase URL slug.
 *
 * @param string $slug
 * @return string
 */
function RouteResolvePluginInternalName($slug)
{
	global $plugins;

	$slug = RouteNormalizePathSegment($slug);
	if($slug === '')
		return '';

	if(!isset($plugins) || !is_object($plugins))
		return $slug;

	foreach(array('_plugins', '_inactivePlugins') as $bucket)
	{
		if(!isset($plugins->$bucket) || !is_array($plugins->$bucket))
			continue;

		foreach($plugins->$bucket as $name => $info)
		{
			if(strcasecmp((string)$name, $slug) === 0)
				return (string)$name;

			if(isset($info['instance']) && is_object($info['instance'])
				&& isset($info['instance']->admin_route_slug)
				&& trim((string)$info['instance']->admin_route_slug) !== ''
				&& RouteNormalizePathSegment((string)$info['instance']->admin_route_slug) === $slug)
				return (string)$name;

			if(RouteNormalizePathSegment((string)$name) === $slug)
				return (string)$name;
		}
	}

	return $slug;
}

/**
 * Path prefix before /admin/ (e.g. /mail for installs in a subdirectory).
 *
 * @return string Leading slash, no trailing slash; empty string at docroot.
 */
function RouteAdminUrlPrefix()
{
	$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$path = parse_url($uri, PHP_URL_PATH);
	if(!is_string($path) || $path === '')
		$path = '/';

	$pos = stripos($path, '/admin');
	if($pos === false)
		return '';

	return $pos > 0 ? substr($path, 0, $pos) : '';
}

/**
 * Admin path segments after /admin/ (no leading/trailing slashes).
 *
 * @return string
 */
function RouteParseAdminPathFromRequest()
{
	$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$path = parse_url($uri, PHP_URL_PATH);
	if(!is_string($path) || $path === '')
		return '';

	$path = rawurldecode($path);

	if(!preg_match('#/admin/?(.*)$#i', $path, $m))
		return '';

	return RouteNormalizeAdminPath($m[1]);
}

/**
 * Map admin/prefs.common.php → prefs/common (and users.php → users).
 *
 * @return array<string, string> path key => script filename
 */
function RouteBuildAdminConventionMap()
{
	$map = array();
	$adminDir = RouteRootDir() . 'admin/';

	foreach(glob($adminDir . '*.php') as $file)
	{
		$base = basename($file);
		if($base === 'app.php')
			continue;

		if(preg_match('/^([^.]+)\.([^.]+)\.php$/', $base, $m))
			$map[strtolower($m[1] . '/' . $m[2])] = $base;
		else
			$map[strtolower(pathinfo($base, PATHINFO_FILENAME))] = $base;
	}

	return $map;
}

/**
 * @return array<string, string>
 */
function RouteGetAdminConventionMap()
{
	static $map = null;

	if($map !== null)
		return $map;

	$cacheFile = RouteRootDir() . 'temp/route-admin-map.cache.php';
	if(file_exists($cacheFile) && is_readable($cacheFile))
	{
		$cached = include $cacheFile;
		if(is_array($cached))
		{
			foreach(array_keys($cached) as $routeKey)
			{
				if($routeKey !== strtolower($routeKey))
				{
					$cached = null;
					@unlink($cacheFile);
					break;
				}
			}
			if(is_array($cached))
			{
				$map = $cached;
				return $map;
			}
		}
	}

	$map = RouteBuildAdminConventionMap();

	if(is_writable(RouteRootDir() . 'temp/'))
		file_put_contents($cacheFile, '<?php return ' . var_export($map, true) . ';');

	return $map;
}

/**
 * Invalidate admin route map cache (after adding admin scripts).
 */
function RouteInvalidateAdminMapCache()
{
	$cacheFile = RouteRootDir() . 'temp/route-admin-map.cache.php';
	if(file_exists($cacheFile))
		@unlink($cacheFile);
}

/**
 * Match admin pretty path to legacy script + request parameters.
 *
 * @param string $adminPath
 * @return array{script: string, params: array<string, string>}|null
 */
function RouteMatchAdminPath($adminPath)
{
	$adminPath = RouteNormalizeAdminPath($adminPath);
	$segments = ($adminPath === '') ? array() : explode('/', $adminPath);

	if(empty($segments))
		return array('script' => 'welcome.php', 'params' => array());

	if(strcasecmp($segments[0], 'plugin') === 0)
	{
		$params = array();
		if(isset($segments[1]) && $segments[1] !== '')
			$params['plugin'] = RouteResolvePluginInternalName($segments[1]);
		if(isset($segments[2]) && $segments[2] !== '')
			$params['do'] = RouteNormalizePathSegment($segments[2]);
		if(count($segments) > 3)
		{
			$rest = array_slice($segments, 3);
			foreach($rest as $i => $part)
				$rest[$i] = RouteNormalizePathSegment($part);

			if(isset($rest[0]) && preg_match('/^\d+$/', $rest[0]))
			{
				$params['id'] = $rest[0];
				if(count($rest) > 1)
					$params['action'] = implode('/', array_slice($rest, 1));
			}
			else
				$params['action'] = implode('/', $rest);
		}

		return array('script' => 'plugin.page.php', 'params' => $params);
	}

	$map = RouteGetAdminConventionMap();
	$count = count($segments);

	for($len = $count; $len >= 1; $len--)
	{
		$key = implode('/', array_slice($segments, 0, $len));
		if(!isset($map[$key]))
			continue;

		$params = array();
		$rest = array_slice($segments, $len);
		if(!empty($rest))
			$params['action'] = RouteNormalizePathSegment($rest[0]);

		return array('script' => $map[$key], 'params' => $params);
	}

	return null;
}

/**
 * Merge route params into $_GET / $_REQUEST (legacy compatibility).
 *
 * @param array<string, string> $params
 */
function RouteApplyRequestParams(array $params)
{
	foreach($params as $key => $value)
	{
		if($key === 'action')
			$value = RouteRestoreLegacyAction($value);
		else if($key === 'do')
			$value = RouteRestoreLegacyDo($value);
		$_GET[$key] = $value;
		$_REQUEST[$key] = $value;
	}
}

/**
 * Resolve admin pretty URL to legacy script path (must be required in global scope).
 *
 * @return string Absolute path to admin/*.php
 */
function RouteResolveAdminScript()
{
	$adminPath = RouteParseAdminPathFromRequest();
	$match = RouteMatchAdminPath($adminPath);

	if($match === null)
	{
		header('HTTP/1.0 404 Not Found');
		echo 'Admin route not found.';
		exit();
	}

	RouteApplyRequestParams($match['params']);

	$rootDir = RouteRootDir();
	$script = $rootDir . 'admin/' . $match['script'];
	if(!is_file($script))
	{
		header('HTTP/1.0 404 Not Found');
		echo 'Admin script not found.';
		exit();
	}

	$adminDir = $rootDir . 'admin';
	if(is_dir($adminDir))
		@chdir($adminDir);

	return $script;
}

/**
 * @deprecated Use RouteResolveAdminScript() + require in global scope (see route-bootstrap.inc.php).
 */
function RouteDispatchAdmin()
{
	require RouteResolveAdminScript();
	exit();
}

/**
 * Convert admin script name to pretty path prefix (without leading admin/).
 *
 * @param string $script e.g. prefs.common.php
 * @return string e.g. prefs/common
 */
function RouteAdminScriptToPathKey($script)
{
	$script = basename($script);
	if(!preg_match('/\.php$/i', $script))
		$script .= '.php';

	if(preg_match('/^([^.]+)\.([^.]+)\.php$/', $script, $m))
		return $m[1] . '/' . $m[2];

	return pathinfo($script, PATHINFO_FILENAME);
}

/**
 * Default action for an admin script (prefs.common.php → common, plugins.php → plugins).
 *
 * @param string $script e.g. prefs.common.php
 * @return string Lowercase action name
 */
function RouteAdminDefaultAction($script)
{
	$script = basename($script);
	if(!preg_match('/\.php$/i', $script))
		$script .= '.php';

	// admins.php: legacy default view is "account", not the script name "admins"
	if(strcasecmp($script, 'admins.php') === 0)
		return 'account';

	if(preg_match('/^([^.]+)\.([^.]+)\.php$/i', $script, $m))
		return strtolower($m[2]);

	return strtolower(pathinfo($script, PATHINFO_FILENAME));
}

/**
 * Build admin URL (relative to admin/ base; use with &lt;base href="…admin/"&gt; when routing is on).
 *
 * @param string $script   e.g. prefs.common.php or prefs.common
 * @param array<string, mixed> $params
 * @param bool $trailingAmp Append & or ? for templates that append sessionUrlSuffix
 * @return string
 */
function AdminUrl($script, array $params = array(), $trailingAmp = true)
{
	if(substr($script, -4) !== '.php')
	{
		if(strpos($script, '.') !== false)
			$script .= '.php';
		else
			$script .= '.php';
	}

	if(!AdminRoutingActive())
	{
		$url = $script;
		if(!empty($params))
		{
			$url .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
			if($trailingAmp)
				$url .= '&';
		}
		else if($trailingAmp)
			$url .= '?';

		return $url;
	}

	if(basename($script) === 'plugin.page.php' && isset($params['plugin']) && (string)$params['plugin'] !== '')
	{
		$pathKey = 'plugin/' . rawurlencode(RoutePluginAdminSlug((string)$params['plugin']));
		unset($params['plugin']);
		if(isset($params['do']) && (string)$params['do'] !== '')
		{
			$pathKey .= '/' . rawurlencode(strtolower((string)$params['do']));
			unset($params['do']);
		}
		if(isset($params['id']) && preg_match('/^\d+$/', (string)$params['id']))
		{
			$pathKey .= '/' . rawurlencode((string)$params['id']);
			unset($params['id']);
		}
	}
	else
	{
		$pathKey = RouteAdminScriptToPathKey($script);
	}

	$action = null;
	if(isset($params['action']) && (string)$params['action'] !== '')
	{
		$actionParam = strtolower((string)$params['action']);
		if($actionParam !== RouteAdminDefaultAction($script))
			$action = rawurlencode($actionParam);
		unset($params['action']);
	}

	$url = $pathKey;
	if($action !== null)
		$url .= '/' . $action;

	if(!empty($params))
		$url .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

	if($trailingAmp)
	{
		if(strpos($url, '?') !== false)
			$url .= '&';
		else
			$url .= '?';
	}

	return AdminPublicUrl($url);
}

/**
 * Session-aware admin URL.
 *
 * @param string $script
 * @param array<string, mixed> $params
 * @param bool $trailingAmp
 * @return string
 */
function AdminSessionUrl($script, array $params = array(), $trailingAmp = true)
{
	return SessionUrl(AdminUrl($script, $params, $trailingAmp));
}

/**
 * Whether the current admin request needs FQDN asset URLs (pretty URL / front controller).
 *
 * @return bool
 */
function RouteAdminNeedsAbsoluteUrls()
{
	if(defined('BM_ROUTE_FRONT') && BM_ROUTE_FRONT)
		return true;

	$script = isset($_SERVER['SCRIPT_NAME']) ? (string)$_SERVER['SCRIPT_NAME'] : '';
	if($script !== '' && substr($script, -strlen('/admin/app.php')) === '/admin/app.php')
		return true;

	// If an admin front controller exists and we are currently in /admin/,
	// force admin URL conversion even on legacy script entry points
	// (e.g. /admin/admins.php) so navigation consistently emits pretty URLs.
	$uriPath = isset($_SERVER['REQUEST_URI']) ? parse_url((string)$_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
	if(is_string($uriPath) && $uriPath !== '')
	{
		$prefix = RouteAdminUrlPrefix();
		if($prefix !== '' && strpos($uriPath, $prefix) === 0)
			$uriPath = substr($uriPath, strlen($prefix));
		$uriPath = '/' . ltrim($uriPath, '/');

		if(strpos($uriPath, '/admin/') === 0 && is_file(RouteRootDir() . 'admin/app.php'))
			return true;
	}

	return false;
}

/**
 * FQDN install root URL (trailing slash), honouring HTTPS.
 *
 * @return string
 */
function AdminFqdnSelfUrl()
{
	return RouteInstallRootUrl();
}

/**
 * FQDN admin base URL (trailing slash).
 *
 * @return string
 */
function AdminFqdnBaseUrl()
{
	return AdminFqdnSelfUrl() . 'admin/';
}

/**
 * Assign FQDN admin asset URLs (tpldir, API base, clientlang, …).
 *
 * @param Template $tpl
 */
function AssignTemplateAdminRouteVars($tpl)
{
	$routing = AdminRoutingActive();
	$fqdn = AdminUseFqdnUrls();

	$tpl->assign('urlRoutingEnabled', $routing);
	$tpl->assign('adminAbsoluteUrls', $fqdn);

	if(!$fqdn)
	{
		$tpl->assign('adminBaseHref', '');
		$tpl->assign('adminApiBase', '');
		return;
	}

	$adminBase = AdminFqdnBaseUrl();
	$selfUrl = AdminFqdnSelfUrl();

	$tpl->assign('adminBaseHref', $adminBase);
	$tpl->assign('tpldir', $adminBase . 'templates/');
	$tpl->assign('adminApiBase', $adminBase);
	$tpl->assign('adminClientLangUrl', SessionUrl($selfUrl . 'clientlang.php'));
	$tpl->assign('adminManifestUrl', SessionUrl($adminBase . 'manifest.php'));
	$tpl->assign('adminPushSyncUrl', SessionUrl($adminBase . 'push-api.php?action=sync'));
}

/**
 * Smarty: {adminurl script="prefs.common.php" params="action=session"}
 *
 * @param array $params
 * @return string
 */
function TemplateAdminUrl($params)
{
	$script = isset($params['script']) ? $params['script'] : '';
	$queryParams = array();

	if(isset($params['params']) && trim($params['params']) != '')
		parse_str($params['params'], $queryParams);

	$trailingAmp = !isset($params['trailingAmp']) || $params['trailingAmp'] !== 'false';

	return AdminSessionUrl($script, $queryParams, $trailingAmp);
}
