<?php
/*
 * b1gMail session helpers and lifecycle (Phase 0+)
 */

/**
 * Ensure session-related preference columns exist.
 */
function EnsureSessionPrefColumns()
{
	global $db;

	$columns = array(
		'session_lifetime'       => 'int(11) NOT NULL DEFAULT 480',
		'session_idle_timeout'   => 'int(11) NOT NULL DEFAULT 30',
		'session_warn_before'    => 'int(11) NOT NULL DEFAULT 2',
		'session_cookie_mode'    => "enum('yes','no') NOT NULL DEFAULT 'yes'",
		'session_url_compat'     => "enum('yes','no') NOT NULL DEFAULT 'no'",
		'admin_whitelist_ips'    => 'text NOT NULL',
	);

	foreach($columns as $column => $definition)
	{
		$res = $db->Query('SHOW COLUMNS FROM {pre}prefs LIKE ?', $column);
		$exists = $res->RowCount() > 0;
		$res->Free();

		if(!$exists)
			$db->Query('ALTER TABLE {pre}prefs ADD COLUMN `' . $column . '` ' . $definition);
	}
}

/**
 * Apply defaults for session prefs loaded from DB.
 */
function SessionApplyPrefDefaults()
{
	global $bm_prefs;

	if(!isset($bm_prefs['session_lifetime']) || (int)$bm_prefs['session_lifetime'] < 0)
		$bm_prefs['session_lifetime'] = 480;
	else
		$bm_prefs['session_lifetime'] = (int)$bm_prefs['session_lifetime'];

	if(!isset($bm_prefs['session_idle_timeout']) || (int)$bm_prefs['session_idle_timeout'] < 0)
		$bm_prefs['session_idle_timeout'] = 30;
	else
		$bm_prefs['session_idle_timeout'] = (int)$bm_prefs['session_idle_timeout'];

	if(!isset($bm_prefs['session_warn_before']) || (int)$bm_prefs['session_warn_before'] < 0)
		$bm_prefs['session_warn_before'] = 2;
	else
		$bm_prefs['session_warn_before'] = (int)$bm_prefs['session_warn_before'];

	if(!isset($bm_prefs['session_cookie_mode']) || ($bm_prefs['session_cookie_mode'] != 'yes' && $bm_prefs['session_cookie_mode'] != 'no'))
		$bm_prefs['session_cookie_mode'] = 'yes';

	if(!isset($bm_prefs['session_url_compat']) || ($bm_prefs['session_url_compat'] != 'yes' && $bm_prefs['session_url_compat'] != 'no'))
		$bm_prefs['session_url_compat'] = 'no';
}

/**
 * Remove sid query parameter from a URL.
 *
 * @param string $url
 * @return string
 */
function SessionStripSidFromUrl($url)
{
	if(!is_string($url) || $url === '')
		return $url;

	$url = preg_replace('/([?&])sid=[^&]*(?=&|$)/', '$1', $url);
	$url = preg_replace('/\?&/', '?', $url);

	return rtrim($url, '?&');
}

/**
 * Whether sid may appear in generated URLs (legacy dual-mode only).
 *
 * @return bool
 */
function SessionUrlSidEnabled()
{
	return SessionUrlCompatEnabled() && !SessionCookieModeEnabled();
}

/**
 * Normalize session pref values before save.
 *
 * @param int $lifetime
 * @param int $idle
 * @param int $warn
 */
function SessionNormalizePrefValues(&$lifetime, &$idle, &$warn)
{
	$lifetime = max(0, (int)$lifetime);
	$idle = max(0, (int)$idle);
	$warn = max(0, (int)$warn);

	if($idle > 0 && $lifetime > 0 && $idle > $lifetime)
		$idle = $lifetime;

	if($warn > 0 && $idle > 0 && $warn >= $idle)
		$warn = max(1, $idle - 1);
}

function SessionCookieModeEnabled()
{
	global $bm_prefs;

	return isset($bm_prefs['session_cookie_mode'])
		&& $bm_prefs['session_cookie_mode'] == 'yes';
}

/**
 * @return bool
 */
function SessionRequestIsHttps()
{
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		return(true);

	if(isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
		return(true);

	if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
		&& strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
		return(true);

	return(false);
}

/**
 * Client IP for session binding and login notifications (proxy-aware).
 *
 * @return string
 */
function SessionClientIp()
{
	if(!empty($_SERVER['HTTP_CF_CONNECTING_IP']))
	{
		$cfIp = trim((string)$_SERVER['HTTP_CF_CONNECTING_IP']);
		if(filter_var($cfIp, FILTER_VALIDATE_IP))
			return $cfIp;
	}

	$remote = isset($_SERVER['REMOTE_ADDR']) ? trim((string)$_SERVER['REMOTE_ADDR']) : '';
	if($remote === '' || !filter_var($remote, FILTER_VALIDATE_IP))
		return $remote;

	$viaProxy = !filter_var($remote, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	if($viaProxy && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		foreach(explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']) as $part)
		{
			$part = trim($part);
			if($part !== '' && filter_var($part, FILTER_VALIDATE_IP))
				return $part;
		}
	}

	return $remote;
}

function BMSecureSetCookie($name, $value, $expire = 0)
{
	$secure = SessionRequestIsHttps();

	if(PHP_VERSION_ID >= 70300)
	{
		setcookie($name, $value, array(
			'expires'  => $expire,
			'path'     => '/',
			'secure'   => $secure,
			'httponly' => true,
			'samesite' => 'Lax',
		));
	}
	else
	{
		setcookie($name, $value, $expire, '/; samesite=Lax', '', $secure, true);
	}

	$_COOKIE[$name] = $value;
}

/**
 * Parse comma-separated IP list (empty parts ignored).
 *
 * @param string $value
 * @return array
 */
function ParseCommaSeparatedIpList($value)
{
	$entries = array();

	foreach(explode(',', (string)$value) as $part)
	{
		$part = trim($part);
		if($part !== '')
			$entries[] = $part;
	}

	return $entries;
}

/**
 * @return array
 */
function AdminIpWhitelistEntries()
{
	global $bm_prefs;

	if(!isset($bm_prefs['admin_whitelist_ips']) || trim($bm_prefs['admin_whitelist_ips']) === '')
		return array();

	$raw = trim((string)$bm_prefs['admin_whitelist_ips']);
	$entries = json_decode($raw, true);
	if(!is_array($entries) && $raw !== '')
		$entries = @unserialize($raw, array('allowed_classes' => false));

	if(!is_array($entries))
		return array();

	return array_values(array_filter(array_map('trim', $entries), function($ip)
	{
		return $ip !== '' && filter_var($ip, FILTER_VALIDATE_IP);
	}));
}

/**
 * Block admin access when IP is not on the whitelist.
 */
function AdminIpWhitelistEnforce()
{
	global $lang_admin;

	if(!ADMIN_MODE)
		return;

	$whitelist = AdminIpWhitelistEntries();
	if(count($whitelist) === 0)
		return;

	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	if($ip !== '' && in_array($ip, $whitelist, true))
		return;

	$title = (isset($lang_admin['admin_ip_denied_title']) && $lang_admin['admin_ip_denied_title'] !== '')
		? $lang_admin['admin_ip_denied_title']
		: 'Access denied';
	$text = isset($lang_admin['admin_ip_denied'])
		? $lang_admin['admin_ip_denied']
		: 'Admin access from this IP address is not allowed.';

	BmErrorPageHtmlResponse(array(
		'title'    => $title,
		'text'     => $text,
		'httpCode' => 403,
	));
}

function SessionUrlCompatEnabled()
{
	global $bm_prefs;

	return isset($bm_prefs['session_url_compat']) && $bm_prefs['session_url_compat'] == 'yes';
}

/**
 * Configure PHP session runtime (call after ReadConfig).
 */
function ConfigureSessionRuntime()
{
	if(SessionCookieModeEnabled())
	{
		@ini_set('session.use_cookies', '1');
		@ini_set('session.use_only_cookies', '1');
		SetSessionCookieParams();
	}
}

/**
 * Set secure defaults for the PHP session cookie.
 */
function SetSessionCookieParams()
{
	$secure = SessionRequestIsHttps();

	if(PHP_VERSION_ID >= 70300)
	{
		session_set_cookie_params(array(
			'lifetime' => 0,
			'path'     => '/',
			'secure'   => $secure,
			'httponly' => true,
			'samesite' => 'Lax',
		));
	}
	else
	{
		session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
	}
}

/**
 * Build URL without leaking session id (cookie transport).
 *
 * @param string $url
 * @return string
 */
function SessionUrl($url)
{
	if(!is_string($url) || $url === '')
		return($url);

	if(defined('ADMIN_MODE') && ADMIN_MODE && function_exists('AdminConvertLegacyUrl'))
	{
		$converted = AdminConvertLegacyUrl($url);
		if($converted !== null)
			$url = $converted;
	}
	else if((!defined('ADMIN_MODE') || !ADMIN_MODE) && function_exists('PublicConvertLegacyUrl'))
	{
		$converted = PublicConvertLegacyUrl($url);
		if($converted !== null)
			$url = $converted;
	}

	if(strpos($url, '?') !== false && substr($url, -1) !== '?' && substr($url, -1) !== '&')
		$url = rtrim($url, '?&');

	$url = SessionStripSidFromUrl($url);

	if(!SessionUrlSidEnabled())
		return($url);

	@session_start();
	$sid = session_id();
	if($sid == '')
		return($url);

	return($url . (strpos($url, '?') !== false ? '&' : '?') . 'sid=' . rawurlencode($sid));
}

/**
 * Redirect without session id in URL (cookie transport).
 *
 * @param string $url
 */
function SessionRedirect($url)
{
	header('Location: ' . SessionUrl($url));
	exit();
}

/**
 * Check whether a user or admin session is active (no redirect).
 *
 * @param string $context 'user' or 'admin'
 * @return bool
 */
function SessionIsLoggedIn($context = 'user')
{
	$privileges = ($context === 'admin') ? PRIVILEGES_ADMIN : PRIVILEGES_USER;

	return RequestPrivileges($privileges, true);
}

/**
 * Detect active session for login pages (no redirect).
 *
 * @param string $context 'user' or 'admin'
 * @return bool
 */
function SessionAssignLoginPageActive($context = 'user')
{
	global $tpl, $userRow, $adminRow;

	SessionEnsureActiveWithCookie();

	if(isset($tpl) && is_object($tpl))
		AssignTemplateSessionUrlVars($tpl);

	$homeUrl = ($context === 'admin') ? 'welcome.php' : 'start.php';
	$logoutUrl = ($context === 'admin') ? 'index.php?action=logout' : 'start.php?action=logout';

	if(!SessionIsLoggedIn($context))
	{
		$tpl->assign('sessionActive', false);
		return false;
	}

	$tpl->assign('sessionActive', true);
	$tpl->assign('sessionActiveUrl', SessionUrl($homeUrl));
	$tpl->assign('sessionActiveLogoutUrl', SessionUrl($logoutUrl));

	if($context === 'admin')
		$tpl->assign('sessionActiveUser', isset($adminRow['username']) ? $adminRow['username'] : '');
	else
		$tpl->assign('sessionActiveUser', isset($userRow['email']) ? $userRow['email'] : '');

	return true;
}

/**
 * Send JSON for NLI login AJAX and end the request.
 *
 * @param array $payload
 */
function IndexLoginJsonResponse(array $payload)
{
	while(ob_get_level() > 0)
		ob_end_clean();

	if(!headers_sent())
	{
		header('Content-Type: application/json; charset=utf-8');
		if(!empty($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_HOST']))
		{
			$originParts = parse_url((string)$_SERVER['HTTP_ORIGIN']);
			if(!empty($originParts['scheme']) && !empty($originParts['host'])
				&& in_array(strtolower($originParts['scheme']), array('http', 'https'), true))
			{
				$originHost = $originParts['host']
					.(isset($originParts['port']) ? ':'.$originParts['port'] : '');
				if(strcasecmp($originHost, (string)$_SERVER['HTTP_HOST']) === 0)
				{
					header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
					header('Vary: Origin');
				}
			}
		}
	}

	echo json_encode($payload);
	exit();
}

/**
 * Keep plaintext password in the NLI session for SMS/email signup validation.
 */
function SessionPendingLoginRemember($email, $password)
{
	$email = (string)$email;
	$password = (string)$password;
	if($email === '' || $password === '')
		return;

	$_SESSION['bm_pendingLoginEmail'] = $email;
	$_SESSION['bm_pendingLoginPassword'] = $password;
	$_SESSION['bm_pendingLoginAt'] = time();
}

/**
 * @param string $email
 * @return string
 */
function SessionPendingLoginPassword($email)
{
	if(empty($_SESSION['bm_pendingLoginPassword'])
		|| empty($_SESSION['bm_pendingLoginEmail'])
		|| empty($_SESSION['bm_pendingLoginAt']))
		return '';

	if((int)$_SESSION['bm_pendingLoginAt'] + 900 < time())
	{
		SessionPendingLoginClear();
		return '';
	}

	if(!hash_equals((string)$_SESSION['bm_pendingLoginEmail'], (string)$email))
		return '';

	return (string)$_SESSION['bm_pendingLoginPassword'];
}

function SessionPendingLoginClear()
{
	unset(
		$_SESSION['bm_pendingLoginEmail'],
		$_SESSION['bm_pendingLoginPassword'],
		$_SESSION['bm_pendingLoginAt']
	);
}

/**
 * Redirect to LI home when already logged in (login form resubmit).
 */
function SessionRedirectUserHomeIfLoggedIn()
{
	if(!SessionIsLoggedIn('user'))
		return false;

	$url = 'start.php';
	if(isset($_REQUEST['target']) && $_REQUEST['target'] == 'inbox')
		$url = 'email.php?folder=0';
	else if(isset($_REQUEST['target']) && $_REQUEST['target'] == 'webdisk')
		$url = 'webdisk.php';
	else if(isset($_REQUEST['target']) && $_REQUEST['target'] == 'membership')
		$url = 'prefs.php?action=membership';

	if(isset($_REQUEST['ajax']))
		IndexLoginJsonResponse(array('action' => 'redirect', 'url' => SessionUrl($url)));

	SessionRedirect($url);
}

function SessionIsApiRequest()
{
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
		return(true);

	if(isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		return(true);

	if(isset($_REQUEST['ajax']) && ($_REQUEST['ajax'] === 'true' || $_REQUEST['ajax'] === '1' || $_REQUEST['ajax'] === true))
		return(true);

	return(false);
}

function SessionCurrentAction()
{
	return isset($_REQUEST['action']) ? (string)$_REQUEST['action'] : '';
}

function SessionAllowsLockedAccess()
{
	static $allowed = array(
		'sessionStatus',
		'sessionUnlock',
		'sessionKeepAlive',
		'sessionLock',
		'sessionLockNow',
		'logout',
	);

	$action = SessionCurrentAction();
	if(function_exists('RouteRestoreLegacyAction'))
		$action = RouteRestoreLegacyAction($action);

	return in_array($action, $allowed, true);
}

/**
 * @return bool
 */
function SessionIsLogoutRequest()
{
	if(!isset($_REQUEST['action']))
		return false;

	$action = (string)$_REQUEST['action'];
	if(function_exists('RouteRestoreLegacyAction'))
		$action = RouteRestoreLegacyAction($action);

	return $action === 'logout';
}

/**
 * Redirect target after user logout (ACP pref or NLI home).
 *
 * @return string
 */
function SessionLogoutRedirectUrl()
{
	global $bm_prefs;

	$url = isset($bm_prefs['logouturl']) ? trim((string)$bm_prefs['logouturl']) : '';
	if($url !== '')
		return $url;

	if(function_exists('PublicNavUrl'))
		return PublicNavUrl('index.php');

	return rtrim($bm_prefs['selfurl'], '/') . '/';
}

/**
 * Log out current webmail user and redirect to logout URL / NLI home.
 * Works even when the session is expired or no longer passes RequestPrivileges.
 */
function SessionHandleUserLogout()
{
	SessionStart();

	if(isset($_SESSION['bm_userID']) && (int)$_SESSION['bm_userID'] > 0)
	{
		$user = _new('BMUser', array((int)$_SESSION['bm_userID']));
		$user->Logout();
	}
	else if(empty($_SESSION['bm_adminLoggedIn']))
	{
		unset($_SESSION['bm_userLoggedIn'], $_SESSION['bm_userID']);
		@session_destroy();
	}

	SessionRedirect(SessionLogoutRedirectUrl());
}

/**
 * @param bool $admin
 * @return array
 */
function SessionLifecycleKeys($admin = false)
{
	if($admin)
	{
		return array(
			'login'    => 'bm_adminLoginTime',
			'activity' => 'bm_adminLastActivity',
			'locked'   => 'bm_adminUiLocked',
		);
	}

	return array(
		'login'    => 'bm_loginTime',
		'activity' => 'bm_lastActivity',
		'locked'   => 'bm_uiLocked',
	);
}

/**
 * Initialize lifecycle timestamps after successful login.
 *
 * @param bool $admin
 */
function SessionInitLoginTimestamps($admin = false)
{
	$keys = SessionLifecycleKeys($admin);
	$now = time();

	$_SESSION[$keys['login']] = $now;
	$_SESSION[$keys['activity']] = $now;
	$_SESSION[$keys['locked']] = false;
}

/**
 * Regenerate session id after login.
 */
function SessionRegenerateOnLogin()
{
	if(session_status() !== PHP_SESSION_ACTIVE)
		@session_start();

	@session_regenerate_id(true);
	CsrfRegenerateToken();
	SessionRebindAdminCookieLock();
}

/**
 * After session_regenerate_id(), admin cookie lock names change — re-issue cookie.
 */
function SessionRebindAdminCookieLock()
{
	if(empty($_SESSION['bm_adminLoggedIn']) || empty($_SESSION['adminsessionSecret']))
		return;

	BMSecureSetCookie(
		'bm_admin_sessionSecret_'.substr(session_id(), 0, 16),
		$_SESSION['adminsessionSecret'],
		0
	);
}

/**
 * Start PHP session with a cookie so CSRF tokens survive GET → POST (login forms).
 * Used when global session_cookie_mode is off (legacy URL sid transport).
 */
function SessionEnsureActiveWithCookie()
{
	if(session_status() === PHP_SESSION_ACTIVE)
		return;

	@ini_set('session.use_cookies', '1');
	@ini_set('session.use_trans_sid', '0');
	if(SessionCookieModeEnabled())
		@ini_set('session.use_only_cookies', '1');
	SetSessionCookieParams();
	session_start();
}

/**
 * Resume the PHP session for authenticated requests (user, admin, API).
 *
 * Legacy mode stores the session id in a cookie at login (SessionEnsureActiveWithCookie)
 * while init.inc.php keeps session.use_cookies=0; a bare session_start() would ignore
 * that cookie and start an empty session (ACP appears logged out).
 */
function SessionStart()
{
	if(session_status() === PHP_SESSION_ACTIVE)
		return;

	if(SessionCookieModeEnabled())
		@session_start();
	else
		SessionEnsureActiveWithCookie();
}

/**
 * Ensure a CSRF token exists in the current session.
 *
 * @return string
 */
function CsrfTokenEnsure()
{
	if(session_status() !== PHP_SESSION_ACTIVE)
		SessionStart();

	CsrfRehydrateFromCookie();

	if(empty($_SESSION['bm_csrfToken']))
		$_SESSION['bm_csrfToken'] = bin2hex(random_bytes(32));

	if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST')
		CsrfSyncCookieSet($_SESSION['bm_csrfToken']);

	return $_SESSION['bm_csrfToken'];
}

/**
 * CSRF sync cookie (HttpOnly). JS uses the hidden field / bmCsrfToken, not document.cookie.
 *
 * @param string $token
 */
function CsrfSyncCookieSet($token)
{
	BMSecureSetCookie('bm_csrf', $token, 0);
}

/**
 * Remove the CSRF sync cookie.
 */
function CsrfSyncCookieClear()
{
	BMSecureSetCookie('bm_csrf', '', time() - 3600);
	unset($_COOKIE['bm_csrf']);
}

/**
 * @return string
 */
function CsrfTokenGet()
{
	return CsrfTokenEnsure();
}

/**
 * Rotate CSRF token (after login / privilege change).
 */
function CsrfRegenerateToken()
{
	$_SESSION['bm_csrfToken'] = bin2hex(random_bytes(32));
	CsrfSyncCookieSet($_SESSION['bm_csrfToken']);
}

/**
 * Re-bind CSRF session token from the double-submit cookie (new PHP session on POST).
 */
function CsrfRehydrateFromCookie()
{
	if(session_status() !== PHP_SESSION_ACTIVE || !empty($_SESSION['bm_csrfToken']))
		return;

	if(empty($_COOKIE['bm_csrf']))
		return;

	$cookie = (string)$_COOKIE['bm_csrf'];
	if(!preg_match('/^[a-f0-9]{64}$/i', $cookie))
		return;

	$_SESSION['bm_csrfToken'] = $cookie;
}

/**
 * @return string
 */
function CsrfRequestToken()
{
	if(isset($_POST['csrf_token']))
		return (string)$_POST['csrf_token'];
	if(isset($_SERVER['HTTP_X_CSRF_TOKEN']))
		return (string)$_SERVER['HTTP_X_CSRF_TOKEN'];
	return '';
}

/**
 * Scripts/actions that do not require CSRF validation.
 *
 * @return bool
 */
function CsrfIsExemptRequest()
{
	if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST')
		return true;

	$script = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : '';

	if(in_array($script, array('cron.php', 'clientlang.php'), true))
		return true;

	return false;
}

/**
 * @return bool
 */
function CsrfValidateRequest()
{
	if(CsrfIsExemptRequest())
		return true;

	$token = CsrfRequestToken();
	if($token === '')
		return false;

	if(session_status() === PHP_SESSION_ACTIVE
		&& !empty($_SESSION['bm_csrfToken'])
		&& hash_equals((string)$_SESSION['bm_csrfToken'], $token))
		return true;

	// Pre-auth login: session cookie may be missing; token was mirrored in bm_csrf on page load.
	if(isset($_COOKIE['bm_csrf']) && hash_equals((string)$_COOKIE['bm_csrf'], $token))
		return true;

	return false;
}

/**
 * Localized CSRF error strings (title, body, reload button).
 *
 * @param bool|null $admin
 * @return array{title:string,text:string,reload:string,error:string}
 */
function CsrfErrorLang($admin = null)
{
	global $lang_user, $lang_admin;

	if($admin === null)
		$admin = (defined('ADMIN_MODE') && ADMIN_MODE);

	$lang = ($admin && is_array($lang_admin)) ? $lang_admin : $lang_user;

	$title = (is_array($lang) && !empty($lang['csrf_error_title']))
		? $lang['csrf_error_title']
		: 'Security check failed';
	$text = (is_array($lang) && !empty($lang['csrf_error_text']))
		? $lang['csrf_error_text']
		: ((is_array($lang) && !empty($lang['csrf_error']))
			? $lang['csrf_error']
			: 'Please reload the page and try again.');
	$reload = (is_array($lang) && !empty($lang['csrf_error_reload']))
		? $lang['csrf_error_reload']
		: 'Reload page';

	return array(
		'title'  => $title,
		'text'   => $text,
		'reload' => $reload,
		'error'  => $text,
	);
}

/**
 * Standalone error page (CSRF, IP whitelist, etc.).
 *
 * @param string $title
 * @param string $text
 * @param int    $httpCode
 * @param string $action reload|none
 * @param string $actionLabel Button label when action is reload
 */
function BmStandaloneErrorHtmlResponse($title, $text, $httpCode = 403, $action = 'reload', $actionLabel = '')
{
	$opts = array(
		'title'    => $title,
		'text'     => $text,
		'httpCode' => $httpCode,
	);

	if($action === 'reload')
	{
		$opts['actions'] = array(array(
			'label'   => ($actionLabel !== '') ? $actionLabel : 'Reload page',
			'onclick' => 'location.reload()',
			'primary' => true,
		));
	}

	BmErrorPageHtmlResponse($opts);
}

/**
 * Standalone HTML page for invalid CSRF on full-page POST requests.
 *
 * @param bool|null $admin
 */
function CsrfErrorHtmlResponse($admin = null)
{
	if($admin === null && (!defined('ADMIN_MODE') || !ADMIN_MODE)
		&& isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST'
		&& isset($_REQUEST['do']) && $_REQUEST['do'] === 'login'
		&& function_exists('PublicNavUrl'))
	{
		$url = PublicNavUrl('index.php?action=login');
		$url .= (strpos($url, '?') !== false ? '&' : '?') . 'csrf_error=1';
		SessionRedirect($url);
	}

	$l = CsrfErrorLang($admin);
	BmStandaloneErrorHtmlResponse($l['title'], $l['text'], 403, 'reload', $l['reload']);
}

/**
 * Reject invalid CSRF tokens on state-changing requests.
 */
function CsrfEnforceOnPost()
{
	if(CsrfValidateRequest())
		return;

	$lang = CsrfErrorLang();

	if(SessionIsApiRequest())
	{
		SessionJsonResponse(array(
			'ok'        => false,
			'csrfError' => true,
			'title'     => $lang['title'],
			'text'      => $lang['text'],
			'reload'    => $lang['reload'],
			'error'     => $lang['error'],
		), 403);
	}

	CsrfErrorHtmlResponse();
}

/**
 * Assign CSRF template variables.
 *
 * @param Template $tpl
 */
function AssignTemplateCsrfVars($tpl)
{
	if(!isset($tpl) || !is_object($tpl))
		return;

	$token = CsrfTokenGet();
	$tpl->assign('csrfToken', $token);
	$tpl->assign('csrfField', '<input type="hidden" name="csrf_token" value="'
		. htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '" />');
}

/**
 * @param bool $admin
 * @return int
 */
function SessionLifetimeSeconds($admin = false)
{
	global $bm_prefs;

	return max(0, (int)$bm_prefs['session_lifetime']) * TIME_ONE_MINUTE;
}

/**
 * @param bool $admin
 * @return int
 */
function SessionIdleTimeoutSeconds($admin = false)
{
	global $bm_prefs;

	return max(0, (int)$bm_prefs['session_idle_timeout']) * TIME_ONE_MINUTE;
}

/**
 * @param bool $admin
 * @return int
 */
function SessionWarnBeforeSeconds($admin = false)
{
	global $bm_prefs;

	return max(0, (int)$bm_prefs['session_warn_before']) * TIME_ONE_MINUTE;
}

/**
 * @param string $data
 * @param string $key
 * @return int|null
 */
function SessionParseSerializedInt($data, $key)
{
	if(preg_match('/'.preg_quote($key, '/').'\|i:(-?\d+);/', $data, $m))
		return((int)$m[1]);

	return(null);
}

/**
 * @param string $data
 * @param string $key
 * @return bool|null
 */
function SessionParseSerializedBool($data, $key)
{
	if(preg_match('/'.preg_quote($key, '/').'\|b:(0|1);/', $data, $m))
		return($m[1] === '1');

	return(null);
}

/**
 * Logged-in session file for target (lifetime not exceeded).
 *
 * @param string $sessionData
 * @param int    $targetId
 * @param bool   $admin
 * @return bool
 */
function SessionFileHasPushAuth($sessionData, $targetId, $admin = false)
{
	global $bm_prefs;

	ReadConfig();

	$targetId = (int)$targetId;
	if($targetId <= 0 || $sessionData === '')
		return(false);

	if($admin)
	{
		if(strpos($sessionData, 'bm_adminLoggedIn|b:1') === false)
			return(false);

		$id = SessionParseSerializedInt($sessionData, 'bm_adminID');
		if($id !== $targetId)
			return(false);
	}
	else
	{
		if(strpos($sessionData, 'bm_userLoggedIn|b:1') === false)
			return(false);

		$id = SessionParseSerializedInt($sessionData, 'bm_userID');
		if($id !== $targetId)
			return(false);
	}

	$keys = SessionLifecycleKeys($admin);
	$loginTime = SessionParseSerializedInt($sessionData, $keys['login']);
	$lifetime = SessionLifetimeSeconds($admin);
	$now = time();

	if($lifetime > 0 && $loginTime !== null && ($now - $loginTime) >= $lifetime)
		return(false);

	return(true);
}

/**
 * UI locked or idle timeout exceeded (requires SessionFileHasPushAuth).
 *
 * @param string $sessionData
 * @param bool   $admin
 * @return bool
 */
function SessionFileIsPushLocked($sessionData, $admin = false)
{
	$keys = SessionLifecycleKeys($admin);
	$activity = SessionParseSerializedInt($sessionData, $keys['activity']);
	$locked = SessionParseSerializedBool($sessionData, $keys['locked']);
	$idle = SessionIdleTimeoutSeconds($admin);
	$now = time();

	if($locked === true)
		return(true);

	if($idle > 0 && $activity !== null && ($now - $activity) >= $idle)
		return(true);

	return(false);
}

/**
 * Whether serialized session data represents an active login suitable for push delivery.
 *
 * @param string $sessionData
 * @param int    $targetId
 * @param bool   $admin
 * @return bool
 */
function SessionFileAllowsPush($sessionData, $targetId, $admin = false)
{
	return(SessionFileHasPushAuth($sessionData, $targetId, $admin)
		&& !SessionFileIsPushLocked($sessionData, $admin));
}

/**
 * Push session state for target: none, locked (generic push), active (full details).
 *
 * @param int  $targetId
 * @param bool $admin
 * @return string none|locked|active
 */
function SessionTargetGetPushSessionState($targetId, $admin = false)
{
	$targetId = (int)$targetId;
	if($targetId <= 0)
		return('none');

	$sessionPath = SessionStoragePath();
	if(!is_dir($sessionPath) || !is_readable($sessionPath))
		return('none');

	$hasLocked = false;
	$files = @scandir($sessionPath);
	if(!is_array($files))
		return('none');

	foreach($files as $file)
	{
		if($file === '.' || $file === '..')
			continue;

		$fullPath = $sessionPath.$file;
		if(!is_file($fullPath) || !is_readable($fullPath))
			continue;

		$data = @file_get_contents($fullPath);
		if($data === false || !SessionFileHasPushAuth($data, $targetId, $admin))
			continue;

		if(!SessionFileIsPushLocked($data, $admin))
			return('active');

		$hasLocked = true;
	}

	return($hasLocked ? 'locked' : 'none');
}

/**
 * @param bool $admin
 * @return string
 */
function SessionStoragePath()
{
	return(B1GMAIL_DIR.'temp/session/');
}

/**
 * @param int  $targetId
 * @param bool $admin
 * @return string newest active session id or empty
 */
function SessionFindActivePushSessionId($targetId, $admin = false)
{
	$targetId = (int)$targetId;
	if($targetId <= 0)
		return('');

	$sessionPath = SessionStoragePath();
	if(!is_dir($sessionPath) || !is_readable($sessionPath))
		return('');

	$bestSid = '';
	$bestMtime = 0;
	$files = @scandir($sessionPath);
	if(!is_array($files))
		return('');

	foreach($files as $file)
	{
		if($file === '.' || $file === '..')
			continue;

		$fullPath = $sessionPath.$file;
		if(!is_file($fullPath) || !is_readable($fullPath))
			continue;

		$data = @file_get_contents($fullPath);
		if($data === false || !SessionFileAllowsPush($data, $targetId, $admin))
			continue;

		$mtime = @filemtime($fullPath);
		if($mtime === false)
			$mtime = 0;

		if($mtime >= $bestMtime)
		{
			$bestMtime = $mtime;
			$bestSid = (strpos($file, 'sess_') === 0) ? substr($file, 5) : $file;
		}
	}

	return($bestSid);
}

/**
 * @param int $userId
 * @return bool
 */
function SessionUserHasActivePushSession($userId)
{
	return(SessionFindActivePushSessionId((int)$userId, false) !== '');
}

/**
 * @param int $adminId
 * @return bool
 */
function SessionAdminHasActivePushSession($adminId)
{
	return(SessionFindActivePushSessionId((int)$adminId, true) !== '');
}

/**
 * @param int $userId
 * @return string none|locked|active
 */
function SessionUserGetPushSessionState($userId)
{
	return(SessionTargetGetPushSessionState((int)$userId, false));
}

/**
 * @param int $adminId
 * @return string none|locked|active
 */
function SessionAdminGetPushSessionState($adminId)
{
	return(SessionTargetGetPushSessionState((int)$adminId, true));
}

/**
 * Ensure lifecycle keys exist for legacy sessions.
 *
 * @param bool $admin
 */
function SessionEnsureLifecycleKeys($admin = false)
{
	$keys = SessionLifecycleKeys($admin);
	$now = time();

	if(!isset($_SESSION[$keys['login']]))
		$_SESSION[$keys['login']] = isset($_SESSION['bm_loginTime']) && !$admin
			? (int)$_SESSION['bm_loginTime']
			: $now;

	if(!isset($_SESSION[$keys['activity']]))
		$_SESSION[$keys['activity']] = (int)$_SESSION[$keys['login']];

	if(!isset($_SESSION[$keys['locked']]))
		$_SESSION[$keys['locked']] = false;
}

/**
 * Check/update lifecycle state.
 *
 * @param bool $admin
 * @return string ok|expired|locked
 */
function SessionCheckAndUpdateLifecycle($admin = false)
{
	global $bm_prefs;

	SessionEnsureLifecycleKeys($admin);

	$keys = SessionLifecycleKeys($admin);
	$now = time();
	$lifetime = SessionLifetimeSeconds($admin);
	$idle = SessionIdleTimeoutSeconds($admin);

	if($lifetime > 0 && ($now - (int)$_SESSION[$keys['login']]) >= $lifetime)
		return('expired');

	if($idle > 0 && !$_SESSION[$keys['locked']]
		&& ($now - (int)$_SESSION[$keys['activity']]) >= $idle)
		$_SESSION[$keys['locked']] = true;

	if(!$_SESSION[$keys['locked']] && !SessionAllowsLockedAccess())
		$_SESSION[$keys['activity']] = $now;

	if($_SESSION[$keys['locked']])
		return('locked');

	return('ok');
}

/**
 * @param bool $admin
 * @return array
 */
function SessionGetStatusArray($admin = false)
{
	global $bm_prefs;

	SessionEnsureLifecycleKeys($admin);

	$keys = SessionLifecycleKeys($admin);
	$now = time();
	$lifetime = SessionLifetimeSeconds($admin);
	$idle = SessionIdleTimeoutSeconds($admin);
	$warnBefore = SessionWarnBeforeSeconds($admin);
	$locked = !empty($_SESSION[$keys['locked']]);

	$expiresIn = ($lifetime > 0)
		? max(0, $lifetime - ($now - (int)$_SESSION[$keys['login']]))
		: 0;

	$idleIn = ($idle > 0 && !$locked)
		? max(0, $idle - ($now - (int)$_SESSION[$keys['activity']]))
		: 0;

	$warn = false;
	if(!$locked && $warnBefore > 0 && $idle > 0 && $idleIn > 0 && $idleIn <= $warnBefore)
		$warn = true;

	return array(
		'ok'         => true,
		'locked'     => $locked,
		'expiresIn'  => $expiresIn,
		'idleIn'     => $idleIn,
		'warn'       => $warn,
		'lifetime'   => (int)$bm_prefs['session_lifetime'],
		'idle'       => (int)$bm_prefs['session_idle_timeout'],
		'warnBefore' => (int)$bm_prefs['session_warn_before'],
		'csrfToken'  => CsrfTokenGet(),
	);
}

function SessionLoginRedirectUrl($admin = false)
{
	global $bm_prefs;

	if($admin)
		return SessionUrl('index.php?expired=1');

	return($bm_prefs['selfurl'] . 'index.php?action=login&expired=1');
}

function SessionLockRedirectUrl($admin = false)
{
	if($admin)
		return SessionUrl('welcome.php?action=sessionLock');

	return SessionUrl('start.php?action=sessionLock');
}

/**
 * Display name for admin session-lock UI (firstname + lastname, else username).
 *
 * @param array<string, mixed> $adminRow
 * @return string
 */
function SessionAdminLockDisplayName(array $adminRow)
{
	$name = trim((isset($adminRow['firstname']) ? (string)$adminRow['firstname'] : '')
		. ' ' . (isset($adminRow['lastname']) ? (string)$adminRow['lastname'] : ''));

	if($name !== '')
		return $name;

	return isset($adminRow['username']) ? (string)$adminRow['username'] : '';
}

/**
 * Smarty vars for admin session-lock page / redirect after failed unlock.
 */
function SessionAssignAdminLockTplVars()
{
	global $tpl, $adminRow;

	$tpl->assign('sessionLockMode', 'admin');
	$tpl->assign('sessionLockName', SessionAdminLockDisplayName($adminRow));
	$tpl->assign('sessionLockUsername', isset($adminRow['username']) ? (string)$adminRow['username'] : '');
	$tpl->assign('sessionLockEmail', isset($adminRow['email']) ? (string)$adminRow['email'] : '');
}

/**
 * Destroy auth state after hard session expiry.
 *
 * @param bool $admin
 */
function SessionDestroyAuthState($admin = false)
{
	if($admin)
	{
		unset(
			$_SESSION['bm_adminLoggedIn'],
			$_SESSION['bm_adminID'],
			$_SESSION['bm_adminAuth'],
			$_SESSION['adminsessionSecret'],
			$_SESSION['bm_adminLoginTime'],
			$_SESSION['bm_adminLastActivity'],
			$_SESSION['bm_adminUiLocked']
		);
	}
	else if(isset($_SESSION['bm_userID']))
	{
		$user = _new('BMUser', array((int)$_SESSION['bm_userID']));
		$user->Logout();
	}
}

function SessionJsonResponse($data, $httpCode = 200)
{
	if(!headers_sent())
	{
		header('Content-Type: application/json; charset=utf-8');
		http_response_code($httpCode);
	}

	echo json_encode($data);
	exit();
}

/**
 * @param bool $admin
 */
function SessionExpiredResponse($admin = false)
{
	SessionDestroyAuthState($admin);

	$redirect = SessionLoginRedirectUrl($admin);

	if(SessionIsApiRequest())
		SessionJsonResponse(array(
			'ok'             => false,
			'sessionExpired' => true,
			'redirect'       => $redirect,
		), 401);

	header('Location: ' . SessionUrl($redirect));
	exit();
}

/**
 * @param bool $admin
 */
function SessionLockedResponse($admin = false)
{
	$redirect = SessionLockRedirectUrl($admin);

	if(SessionIsApiRequest())
		SessionJsonResponse(array(
			'ok'       => false,
			'locked'   => true,
			'redirect' => $redirect,
		), 401);

	header('Location: ' . $redirect);
	exit();
}

/**
 * Process lifecycle after successful privilege check.
 *
 * @param int $privileges
 * @param bool $allowLocked
 */
function SessionProcessLifecycleAfterAuth($privileges, $allowLocked = false)
{
	$admin = (($privileges & PRIVILEGES_ADMIN) != 0);
	$user = (($privileges & PRIVILEGES_USER) != 0);

	if($admin && ADMIN_MODE)
	{
		$state = SessionCheckAndUpdateLifecycle(true);
		if($state === 'expired')
			SessionExpiredResponse(true);
		if($state === 'locked' && !$allowLocked && !SessionAllowsLockedAccess())
			SessionLockedResponse(true);
	}
	else if($user && !$admin)
	{
		$state = SessionCheckAndUpdateLifecycle(false);
		if($state === 'expired' && !SessionIsLogoutRequest())
			SessionExpiredResponse(false);
		if($state === 'locked' && !$allowLocked && !SessionAllowsLockedAccess())
			SessionLockedResponse(false);
	}

	CsrfEnforceOnPost();
}

/**
 * @param bool $admin
 * @return bool
 */
function SessionUnlock($passwordPlain, $admin = false)
{
	global $db;

	$passwordPlain = trim((string)$passwordPlain);
	if($passwordPlain === '')
		return(false);

	$keys = SessionLifecycleKeys($admin);

	if($admin)
	{
		if(!isset($_SESSION['bm_adminID']))
			return(false);

		$res = $db->Query('SELECT password FROM {pre}admins WHERE adminid=?', (int)$_SESSION['bm_adminID']);
		if($res->RowCount() != 1)
			return(false);
		list($hash) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if(!password_verify($passwordPlain, $hash))
			return(false);
	}
	else
	{
		if(!isset($_SESSION['bm_userID']))
			return(false);

		if(!BMUser::AuthenticatePasswordForSession((int)$_SESSION['bm_userID'], $passwordPlain))
			return(false);
	}

	$_SESSION[$keys['locked']] = false;
	$_SESSION[$keys['activity']] = time();

	return(true);
}

/**
 * @param bool $admin
 */
function SessionKeepAlive($admin = false)
{
	global $db, $userRow;

	$keys = SessionLifecycleKeys($admin);
	$_SESSION[$keys['activity']] = time();
	$_SESSION[$keys['locked']] = false;

	if(!$admin && !empty($userRow['id']))
	{
		$now = time();
		$lastBump = isset($_SESSION['bm_lastNotifyDb']) ? (int)$_SESSION['bm_lastNotifyDb'] : 0;
		if($now - $lastBump >= 60)
		{
			$db->Query('UPDATE {pre}users SET last_notify=? WHERE id=?', $now, (int)$userRow['id']);
			$_SESSION['bm_lastNotifyDb'] = $now;
		}
	}
}

/**
 * Manually lock the UI while keeping the session active.
 *
 * @param bool $admin
 */
function SessionLockUi($admin = false)
{
	$keys = SessionLifecycleKeys($admin);
	SessionEnsureLifecycleKeys($admin);
	$_SESSION[$keys['locked']] = true;
}

/**
 * Handle user session API actions.
 *
 * @param string $action
 */
function SessionHandleUserApi($action)
{
	global $tpl, $thisUser, $userRow, $lang_user;

	switch($action)
	{
	case 'sessionStatus':
		SessionJsonResponse(SessionGetStatusArray(false));
		break;

	case 'sessionKeepAlive':
		SessionKeepAlive(false);
		SessionJsonResponse(SessionGetStatusArray(false));
		break;

	case 'sessionLockNow':
		SessionLockUi(false);
		SessionJsonResponse(array('ok' => true) + SessionGetStatusArray(false));
		break;

	case 'sessionUnlock':
		$passwordPlain = isset($_POST['password']) ? $_POST['password'] : (isset($_REQUEST['password']) ? $_REQUEST['password'] : '');
		if(SessionUnlock($passwordPlain, false))
		{
			if(SessionIsApiRequest())
				SessionJsonResponse(array('ok' => true) + SessionGetStatusArray(false));

			header('Location: ' . SessionUrl('start.php'));
			exit();
		}

		if(SessionIsApiRequest())
		{
			SessionJsonResponse(array(
				'ok'    => false,
				'error' => isset($lang_user['session_unlock_error']) ? $lang_user['session_unlock_error'] : 'Bad password',
			), 403);
		}

		$tpl->assign('pageTitle', $lang_user['session_locked']);
		$tpl->assign('sessionLockMode', 'user');
		$tpl->assign('sessionLockEmail', isset($userRow['email']) ? $userRow['email'] : '');
		$tpl->assign('sessionLockName', trim((isset($userRow['vorname']) ? $userRow['vorname'] : '') . ' ' . (isset($userRow['nachname']) ? $userRow['nachname'] : '')));
		$tpl->assign('sessionUnlockError', isset($lang_user['session_unlock_error']) ? $lang_user['session_unlock_error'] : 'Bad password');
		$tpl->display('li/session-lock-page.tpl');
		break;

	case 'sessionLock':
		$tpl->assign('pageTitle', $lang_user['session_locked']);
		$tpl->assign('sessionLockMode', 'user');
		$tpl->assign('sessionLockEmail', isset($userRow['email']) ? $userRow['email'] : '');
		$tpl->assign('sessionLockName', trim((isset($userRow['vorname']) ? $userRow['vorname'] : '') . ' ' . (isset($userRow['nachname']) ? $userRow['nachname'] : '')));
		$tpl->assign('sessionUnlockError', '');
		$tpl->display('li/session-lock-page.tpl');
		break;
	}
}

/**
 * Handle admin session API actions.
 *
 * @param string $action
 */
function SessionHandleAdminApi($action)
{
	global $tpl, $adminRow, $lang_admin;

	switch($action)
	{
	case 'sessionStatus':
		SessionJsonResponse(SessionGetStatusArray(true));
		break;

	case 'sessionKeepAlive':
		SessionKeepAlive(true);
		SessionJsonResponse(SessionGetStatusArray(true));
		break;

	case 'sessionLockNow':
		SessionLockUi(true);
		SessionJsonResponse(array('ok' => true) + SessionGetStatusArray(true));
		break;

	case 'sessionUnlock':
		$passwordPlain = isset($_POST['password']) ? $_POST['password'] : (isset($_REQUEST['password']) ? $_REQUEST['password'] : '');
		if(SessionUnlock($passwordPlain, true))
		{
			if(SessionIsApiRequest())
				SessionJsonResponse(array('ok' => true) + SessionGetStatusArray(true));

			header('Location: ' . SessionUrl('welcome.php'));
			exit();
		}

		if(SessionIsApiRequest())
		{
			SessionJsonResponse(array(
				'ok'    => false,
				'error' => isset($lang_admin['session_unlock_error']) ? $lang_admin['session_unlock_error'] : 'Bad password',
			), 403);
		}

		$tpl->assign('pageTitle', $lang_admin['session_locked']);
		SessionAssignAdminLockTplVars();
		$tpl->assign('sessionUnlockError', isset($lang_admin['session_unlock_error']) ? $lang_admin['session_unlock_error'] : 'Bad password');
		$tpl->display('session-lock-page.tpl');
		break;

	case 'sessionLock':
		$tpl->assign('pageTitle', $lang_admin['session_locked']);
		SessionAssignAdminLockTplVars();
		$tpl->assign('sessionUnlockError', '');
		$tpl->display('session-lock-page.tpl');
		break;
	}
}

/**
 * Assign session id and URL suffix vars for Smarty templates.
 *
 * @param Template $tpl
 */
function AssignTemplateSessionUrlVars($tpl)
{
	global $bm_prefs;

	$sid = session_id();
	$tpl->assign('sid', $sid);
	$tpl->assign('sessionLifetime', (int)$bm_prefs['session_lifetime']);
	$tpl->assign('sessionIdleTimeout', (int)$bm_prefs['session_idle_timeout']);
	$tpl->assign('sessionWarnBefore', (int)$bm_prefs['session_warn_before']);
	$tpl->assign('sessionUrlCompat', SessionUrlCompatEnabled());
	$tpl->assign('sessionCookieMode', SessionCookieModeEnabled());

	if(SessionUrlSidEnabled() && $sid != '')
	{
		$encoded = rawurlencode($sid);
		$tpl->assign('sessionUrlSuffix', '&sid=' . $encoded);
		$tpl->assign('sessionUrlSuffixHtml', '&amp;sid=' . $encoded);
		$tpl->assign('sessionUrlPrefix', '?sid=' . $encoded);
		$tpl->assign('sessionUrlPrefixHtml', '?sid=' . $encoded);
	}
	else
	{
		$tpl->assign('sessionUrlSuffix', '');
		$tpl->assign('sessionUrlSuffixHtml', '');
		$tpl->assign('sessionUrlPrefix', '');
		$tpl->assign('sessionUrlPrefixHtml', '');
	}

	AssignTemplateCsrfVars($tpl);

	if(defined('ADMIN_MODE') && ADMIN_MODE)
		AssignTemplateAdminRouteVars($tpl);
	else if(function_exists('AssignTemplatePublicRouteVars'))
		AssignTemplatePublicRouteVars($tpl);

	if(!defined('ADMIN_MODE') || !ADMIN_MODE)
	{
		if(function_exists('AssignTemplatePublicNavUrls'))
			AssignTemplatePublicNavUrls($tpl);
	}
}

/**
 * Smarty helper: {csrffield}
 *
 * @param array $params
 * @return string
 */
function TemplateCsrfField($params = array())
{
	$name = isset($params['name']) ? $params['name'] : 'csrf_token';

	return '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8')
		. '" value="' . htmlspecialchars(CsrfTokenGet(), ENT_QUOTES, 'UTF-8') . '" />';
}

/**
 * Smarty helper: {sessionurl file="start.php" params="action=start"}
 *
 * @param array $params
 * @return string
 */
function TemplateSessionUrl($params)
{
	$url = isset($params['file']) ? $params['file'] : '';
	if(isset($params['params']) && trim($params['params']) != '')
		$url .= (strpos($url, '?') !== false ? '&' : '?') . $params['params'];

	return SessionUrl($url);
}

/**
 * Smarty note: {sessionurl params='...'} only works with static query strings (single quotes).
 * Dynamic values use double quotes: params="action={$item}" — or append {$sessionUrlSuffix} / {$sessionUrlSuffixHtml}.
 */
