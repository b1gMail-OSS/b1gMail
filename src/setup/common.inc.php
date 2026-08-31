<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */

// error reporting
error_reporting(E_ERROR | E_WARNING | E_PARSE);

SetupStartSession();

// example data
include('./data/example.data.php');

define('SETUP_PHP_MIN', '8.1.0');
define('SETUP_DEFAULT_PREFIX', 'bm60_');
define('SETUP_DEFAULT_TEMPLATE', 'tabler');

$setupCardBodyOpen = false;

// files and folders that should have write permissions
$writeableFiles = array(
	'serverlib/config.inc.php',
	'serverlib/version.inc.php',
	'admin/templates/cache/',
	'logs/',
	'temp/',
	'temp/session/',
	'temp/cache/',
	'templates/tabler/cache/',
	'data/',
	'setup/'
);

// constants
define('VERSION_IS_OLDER',		-1);
define('VERSION_IS_EQUAL',		0);
define('VERSION_IS_NEWER',		1);

/**
 * Encode a (possible non-ASCII) domain to IDN form.
 *
 * @param string $domain
 * @return string
 */
function EncodeDomain($domain)
{
	$domain = (string)$domain;
	if($domain === '' || !function_exists('idn_to_ascii'))
		return $domain;

	if(defined('INTL_IDNA_VARIANT_UTS46'))
	{
		$encoded = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
		if($encoded !== false)
			return $encoded;
	}

	$encoded = @idn_to_ascii($domain);
	return $encoded !== false ? $encoded : $domain;
}

/**
 * @param string $str
 * @return string
 */
function SetupH($str)
{
	$str = html_entity_decode((string)$str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * @return void
 */
function SetupOpenCardBody()
{
	global $setupCardBodyOpen;

	if(empty($setupCardBodyOpen))
	{
		echo '<div class="card-body">';
		$setupCardBodyOpen = true;
	}
}

/**
 * @return void
 */
function SetupCloseCardBody()
{
	global $setupCardBodyOpen;

	if(!empty($setupCardBodyOpen))
	{
		echo '</div>';
		$setupCardBodyOpen = false;
	}
}

/**
 * Remove leftover file-cache objects from a previous installation.
 *
 * @return void
 */
function SetupClearFileCache()
{
	$dirs = array(
		__DIR__.'/../temp/cache',
		__DIR__.'/../temp',
	);
	foreach($dirs as $dir)
	{
		if(!is_dir($dir))
			continue;
		foreach(glob($dir.'/*.cache') ?: array() as $file)
		{
			if(is_file($file))
				@unlink($file);
		}
	}
}

/**
 * @return void
 */
function SetupStartSession()
{
	if(session_status() === PHP_SESSION_ACTIVE)
		return;

	$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
	session_name('b1gmail_setup');
	session_set_cookie_params(array(
		'lifetime' => 0,
		'path'     => '/',
		'secure'   => $https,
		'httponly' => true,
		'samesite' => 'Lax',
	));
	session_start();
	if(empty($_SESSION['setup_started']))
	{
		session_regenerate_id(true);
		$_SESSION['setup_started'] = 1;
	}
}

/**
 * @return array
 */
function SetupSecretKeys()
{
	return array('mysql_pass', 'pop3_pass', 'smtp_pass', 'adminpw', 'adminpw2');
}

function SetupCapturePost()
{
	if(($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST')
		return;

	if(!isset($_SESSION['setup_data']) || !is_array($_SESSION['setup_data']))
		$_SESSION['setup_data'] = array();

	$skip = array_flip(array_merge(array('step', 'setup_csrf'), SetupSecretKeys()));
	foreach($_POST as $key => $val)
	{
		if(isset($skip[$key]))
			continue;
		if(is_string($val))
			$_SESSION['setup_data'][$key] = $val;
	}

	if(isset($_POST['receive_method']))
		$_SESSION['setup_data']['pop3_tls'] = isset($_POST['pop3_tls']) ? '1' : '';
	if(isset($_POST['send_method']))
		$_SESSION['setup_data']['smtp_auth'] = isset($_POST['smtp_auth']) ? 'yes' : 'no';
}

/**
 * Carry secrets in hidden fields instead of the session.
 *
 * @return void
 */
function SetupEmitHiddenSecrets()
{
	foreach(SetupSecretKeys() as $key)
	{
		if(!isset($_POST[$key]) || !is_string($_POST[$key]) || $_POST[$key] === '')
			continue;
		echo '<input type="hidden" name="'.SetupH($key).'" value="'.SetupH($_POST[$key]).'" />';
	}
}

/**
 * @param string $key
 * @param string $default
 * @return string
 */
function SetupInput($key, $default = '')
{
	if(isset($_POST[$key]) && is_string($_POST[$key]))
		return $_POST[$key];
	if(in_array($key, SetupSecretKeys(), true))
		return $default;
	if(isset($_SESSION['setup_data'][$key]) && is_string($_SESSION['setup_data'][$key]))
		return $_SESSION['setup_data'][$key];
	return $default;
}

/**
 * @return string
 */
function SetupCsrfToken()
{
	if(empty($_SESSION['setup_csrf']) || !is_string($_SESSION['setup_csrf']))
		$_SESSION['setup_csrf'] = bin2hex(random_bytes(16));
	return $_SESSION['setup_csrf'];
}

/**
 * @return bool
 */
function SetupCsrfOk($requirePost = false)
{
	$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
	if($requirePost && $method !== 'POST')
		return false;
	if($method !== 'POST')
		return true;
	$token = $_POST['setup_csrf'] ?? '';
	return is_string($token) && hash_equals(SetupCsrfToken(), $token);
}

/**
 * @return string
 */
function SetupDetectSelfUrl()
{
	$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
	$scheme = $https ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	if(!preg_match('/^[a-zA-Z0-9.\-:[\]]+$/', (string)$host))
		$host = 'localhost';
	$uri = $_SERVER['REQUEST_URI'] ?? '/setup/';
	$base = preg_replace('#/setup(?:/[^?]*)?(?:\?.*)?$#', '/', $uri);
	if(!is_string($base) || $base === '')
		$base = '/';
	return $scheme.'://'.$host.$base;
}

/**
 * @param string $host
 * @return bool
 */
function SetupHostAllowed($host)
{
	$host = trim((string)$host);
	if($host === '' || strlen($host) > 253)
		return false;
	if(preg_match('/[\s\/\\\\@?#]/', $host))
		return false;

	$check = $host;
	if(preg_match('/^\[(.+)\]$/', $host, $m))
		$check = $m[1];
	elseif(substr_count($host, ':') === 1 && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
	{
		$parts = explode(':', $host, 2);
		$check = $parts[0];
	}

	$ip = filter_var($check, FILTER_VALIDATE_IP);
	if($ip !== false)
		return strpos($ip, '169.254.') !== 0 && $ip !== '0.0.0.0' && $ip !== '::';

	if($check !== 'localhost'
		&& !preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/', $check))
		return false;

	$resolved = @gethostbyname($check);
	if(is_string($resolved) && $resolved !== $check && strpos($resolved, '169.254.') === 0)
		return false;

	return true;
}

/**
 * @param string $name
 * @return string
 */
function SetupMysqlIdent($name)
{
	return str_replace('`', '', (string)$name);
}

/**
 * @return bool
 */
function SetupConfigLooksInstalled()
{
	$path = dirname(__DIR__).'/serverlib/config.inc.php';
	if(!is_readable($path))
		return false;
	$src = (string)@file_get_contents($path);
	return $src !== ''
		&& preg_match('/\$mysql\s*=\s*array\s*\(/', $src)
		&& preg_match('/[\'"]db[\'"]\s*=>/', $src);
}

/**
 * @param string $prefix
 * @return string
 */
function SetupNormalizePrefix($prefix)
{
	$prefix = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$prefix);
	if($prefix === '' || !preg_match('/^[A-Za-z]/', $prefix))
		return SETUP_DEFAULT_PREFIX;
	if(substr($prefix, -1) !== '_')
		$prefix .= '_';
	return substr($prefix, 0, 16);
}

/**
 * @param string $user
 * @return string
 */
function SetupNormalizeAdminUser($user)
{
	$user = preg_replace('/[^a-zA-Z0-9._-]/', '', (string)$user);
	if(strlen($user) < 2 || strlen($user) > 32)
		return 'admin';
	return $user;
}

/**
 * @param array $databaseStructure
 * @param string $prefix
 * @return array
 */
function SetupRewriteStructurePrefix($databaseStructure, $prefix)
{
	if($prefix === SETUP_DEFAULT_PREFIX)
		return $databaseStructure;

	$out = array();
	foreach($databaseStructure as $tableName => $info)
	{
		$newName = (strpos($tableName, SETUP_DEFAULT_PREFIX) === 0)
			? $prefix.substr($tableName, strlen(SETUP_DEFAULT_PREFIX))
			: $tableName;
		$out[$newName] = $info;
	}
	return $out;
}

/**
 * @param string $plain
 * @param string $context
 * @return string
 */
function SetupHashPassword($plain, $context = 'admin')
{
	$hashFile = dirname(__DIR__).'/serverlib/passwordhash.inc.php';
	if(is_readable($hashFile))
	{
		require_once $hashFile;
		if(!isset($GLOBALS['bm_prefs']) || !is_array($GLOBALS['bm_prefs']))
			$GLOBALS['bm_prefs'] = array();
		PasswordHashApplyPrefDefaults();
		return PasswordHashCreate($plain, $context);
	}
	return password_hash((string)$plain, PASSWORD_BCRYPT, array('cost' => 12));
}

/**
 * @return string
 */
function SetupCreateSignKey()
{
	return str_pad(bin2hex(random_bytes(16)), 32, '0', STR_PAD_LEFT);
}

/**
 * @param string $file
 * @return bool
 */
function SetupWriteLock($file = 'lock')
{
	$path = __DIR__.'/'.$file;
	if(!is_writable(__DIR__))
		return false;
	return file_put_contents($path, '1') !== false;
}

/**
 * @return array
 */
function SetupRequiredExtensions()
{
	return array(
		'mysqli'   => function_exists('mysqli_connect'),
		'mbstring' => function_exists('mb_convert_encoding') || function_exists('iconv'),
		'json'     => function_exists('json_decode'),
		'openssl'  => extension_loaded('openssl'),
		'session'  => function_exists('session_start'),
	);
}

/**
 * @param bool $ok
 * @return string
 */
function SetupStatusIcon($ok)
{
	if($ok)
		return '<span class="setup-status setup-status-ok" aria-label="ok"><i class="ti ti-check"></i></span>';
	return '<span class="setup-status setup-status-err" aria-label="error"><i class="ti ti-x"></i></span>';
}

/**
 * @param string $result
 * @return string
 */
function SetupResultIcon($result)
{
	if($result === 'ok')
		return SetupStatusIcon(true);
	if($result === 'warning')
		return '<span class="setup-status setup-status-warn" aria-label="warning"><i class="ti ti-alert-triangle"></i></span>';
	return SetupStatusIcon(false);
}

/**
 * @param string $tone danger|warning|success|info
 * @return string
 */
function SetupAlertIconSvg($tone)
{
	if($tone === 'danger')
		return '<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path>';
	if($tone === 'warning')
		return '<path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path>';
	if($tone === 'success')
		return '<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M9 12l2 2l4 -4"></path>';
	return '<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path>';
}

/**
 * @param string $tone
 * @param string $description HTML allowed
 * @param string $heading
 * @param array  $options id, class, dismissible
 * @return string
 */
function SetupAlert($tone, $description, $heading = '', $options = array())
{
	global $lang_setup;

	$allowed = array('danger', 'warning', 'success', 'info');
	if(!in_array($tone, $allowed, true))
		$tone = 'info';

	if($heading === '')
	{
		if($tone === 'danger')
			$heading = $lang_setup['error'] ?? '';
		elseif($tone === 'success')
			$heading = $lang_setup['success'] ?? '';
		elseif($tone === 'warning' || $tone === 'info')
			$heading = $lang_setup['notice'] ?? '';
	}

	$dismissible = !isset($options['dismissible']) || $options['dismissible'];
	$class = 'alert alert-'.SetupH($tone);
	if($dismissible)
		$class .= ' alert-dismissible';
	if(!empty($options['class']))
		$class .= ' '.$options['class'];

	$html = '<div class="'.$class.'" role="alert"';
	if(!empty($options['id']))
		$html .= ' id="'.SetupH($options['id']).'"';
	$html .= '>';
	$html .= '<div class="alert-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2" aria-hidden="true">';
	$html .= SetupAlertIconSvg($tone);
	$html .= '</svg></div>';
	$html .= '<div>';
	if($heading !== '')
		$html .= '<h4 class="alert-heading">'.$heading.'</h4>';
	$html .= '<div class="alert-description">'.$description.'</div>';
	$html .= '</div>';
	if($dismissible)
		$html .= '<a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>';
	$html .= '</div>';
	return $html;
}

/**
 * @param array $rows
 * @return void
 */
function SetupRenderCheckTable($rows)
{
	global $lang_setup;

	SetupCloseCardBody();
	echo '<div class="table-responsive"><table class="table table-vcenter card-table setup-check-table">';
	echo '<thead><tr><th></th><th>'.SetupH($lang_setup['required']).'</th><th>'.SetupH($lang_setup['available']).'</th><th class="w-1"></th></tr></thead><tbody>';
	foreach($rows as $row)
	{
		echo '<tr>';
		echo '<th>'.SetupH($row['label']).'</th>';
		echo '<td>'.SetupH($row['required']).'</td>';
		echo '<td>'.SetupH($row['available']).'</td>';
		echo '<td>'.SetupStatusIcon(!empty($row['ok'])).'</td>';
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	SetupOpenCardBody();
}

/**
 * @param array $files
 * @return array
 */
function SetupCollectWritableRows($files)
{
	global $lang_setup;

	$rows = array();
	$chmodCommands = array();
	$allOk = true;
	foreach($files as $file)
	{
		$ok = is_writable('../'.$file);
		if(!$ok)
		{
			$allOk = false;
			$mode = is_dir('../'.$file) ? '0755' : '0644';
			if(!isset($chmodCommands[$mode]))
				$chmodCommands[$mode] = array();
			$chmodCommands[$mode][] = $file;
		}
		$rows[] = array(
			'label'     => $file,
			'required'  => $lang_setup['writeable'],
			'available' => $ok ? $lang_setup['writeable'] : $lang_setup['notwriteable'],
			'ok'        => $ok,
		);
	}
	return array($rows, $chmodCommands, $allOk);
}

/**
 * @param array $chmodCommands
 * @return string
 */
function SetupRenderChmod($chmodCommands)
{
	global $lang_setup;

	if($chmodCommands === array())
		return '';

	$cmd = '';
	foreach($chmodCommands as $mode => $files)
	{
		$cmd .= 'chmod '.$mode;
		foreach($files as $file)
			$cmd .= " \\\n\t".$file;
		$cmd .= "\n";
	}

	return '<details class="setup-chmod"><summary>'.$lang_setup['showchmod'].'</summary>'
		.'<textarea readonly="readonly" class="form-control font-monospace setup-log" rows="5">'.SetupH(trim($cmd)).'</textarea></details>';
}

/**
 * @param string $inputId
 * @param string $name
 * @param string $value
 * @param bool $required
 * @return string
 */
function SetupPasswordField($inputId, $name, $value = '', $required = false)
{
	return '<div class="input-group input-group-flat">'
		.'<input type="password" class="form-control" id="'.SetupH($inputId).'" name="'.SetupH($name).'" value="'.SetupH($value).'"'
		.($required ? ' required="required"' : '').' autocomplete="new-password" />'
		.'<span class="input-group-text">'
		.'<a href="#" class="link-secondary" data-setup-toggle-password="'.SetupH($inputId).'" aria-label="toggle">'
		.'<i class="ti ti-eye"></i></a></span></div>';
}

/**
 * @param bool $update
 * @param bool|string $convert
 * @return array
 */
function SetupWizardSteps($update, $convert)
{
	global $lang_setup;

	if($update)
	{
		return array(
			STEP_WELCOME      => array('title' => $lang_setup['welcome'], 'icon' => 'home'),
			STEP_SYSTEMCHECK  => array('title' => $lang_setup['syscheck'], 'icon' => 'list-check'),
			STEP_UPDATE       => array('title' => $lang_setup['update'], 'icon' => 'refresh'),
		);
	}
	if($convert)
	{
		$convertLabel = isset($lang_setup['converting']) ? $lang_setup['converting'] : 'Convert';
		return array(
			STEP_WELCOME      => array('title' => $lang_setup['welcome'], 'icon' => 'home'),
			STEP_SYSTEMCHECK  => array('title' => $lang_setup['syscheck'], 'icon' => 'list-check'),
			STEP_CONVERT      => array('title' => $convertLabel, 'icon' => 'transform'),
		);
	}
	return array(
		STEP_SELECT_LANGUAGE => array('title' => $lang_setup['selectlanguage'], 'icon' => 'language'),
		STEP_WELCOME         => array('title' => $lang_setup['welcome'], 'icon' => 'home'),
		STEP_SYSTEMCHECK     => array('title' => $lang_setup['syscheck'], 'icon' => 'list-check'),
		STEP_MYSQL           => array('title' => $lang_setup['db'], 'icon' => 'database'),
		STEP_CHECK_MYSQL     => array('title' => $lang_setup['emailcfg'], 'icon' => 'mail'),
		STEP_CHECK_EMAIL     => array('title' => $lang_setup['misc'], 'icon' => 'key'),
		STEP_INSTALL         => array('title' => $lang_setup['installing'], 'icon' => 'rocket'),
	);
}

/**
 * @param bool $update
 * @param bool|string $convert
 * @return string
 */
function SetupRenderStepper($update, $convert)
{
	global $step;

	$steps = SetupWizardSteps($update, $convert);
	$keys = array_keys($steps);
	$currentIdx = array_search($step, $keys, true);
	if($currentIdx === false)
		$currentIdx = 0;

	$html = '<ul class="nav nav-pills card-header-pills">';
	foreach($keys as $idx => $stepId)
	{
		$meta = $steps[$stepId];
		$icon = '<span class="nav-link-icon d-none d-sm-inline-block"><i class="ti ti-'.SetupH($meta['icon']).'"></i></span>';
		$label = '<span class="nav-link-title">'.SetupH($meta['title']).'</span>';
		$html .= '<li class="nav-item">';
		if($idx < $currentIdx)
		{
			$html .= '<button type="submit" class="nav-link" name="step" value="'.(int)$stepId.'">'.$icon.$label.'</button>';
		}
		elseif($idx === $currentIdx)
		{
			$html .= '<span class="nav-link active" aria-current="page">'.$icon.$label.'</span>';
		}
		else
		{
			$html .= '<span class="nav-link disabled" tabindex="-1" aria-disabled="true">'.$icon.$label.'</span>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}

/**
 * @param string $file
 * @return void
 */
function SetupAbortIfLocked($file = 'lock', $update = false, $convert = false)
{
	global $lang_setup;

	if(!file_exists(__DIR__.'/'.$file))
		return;

	if(SetupIsAjaxStep())
	{
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'ERROR:Locked';
		exit;
	}

	pageHeader($update, $convert);
	echo '<h1>'.SetupH($lang_setup['error']).'</h1>';
	echo SetupAlert('danger', $lang_setup['lock_detected'] ?? 'Lockfile detected. Please remove the lock file if you want to rerun.');
	pageFooter();
	exit;
}

/**
 * @return bool
 */
function SetupIsAjaxStep()
{
	return isset($_REQUEST['step']) && (int)$_REQUEST['step'] === 4
		&& isset($_REQUEST['do']) && (string)$_REQUEST['do'] !== '';
}

/**
 * Block a second install when config.inc.php already describes a database.
 *
 * @return void
 */
function SetupAbortIfAlreadyInstalled()
{
	global $lang_setup;

	if(!SetupConfigLooksInstalled())
		return;

	pageHeader();
	echo '<h1>'.SetupH($lang_setup['error']).'</h1>';
	echo SetupAlert('danger', $lang_setup['already_installed'] ?? 'An existing installation was found. Remove setup/lock only if you really want to reinstall.');
	pageFooter();
	exit;
}

/**
 * escape string for use in sql query
 *
 * @param string $str
 * @return string
 */
function SQLEscape($str, $handle)
{
	return(mysqli_real_escape_string($handle, $str));
}

/**
 * compare versions
 *
 * @param string $ver1
 * @param string $ver2
 * @return int
 */
function CompareVersions($ver1, $ver2)
{
	$version1Parts = explode('.', $ver1);
	$version2Parts = explode('.', $ver2);

	$count = max(count($version1Parts), count($version2Parts));

	if(count($version1Parts) < $count)
		$version1Parts = array_pad($version1Parts, $count, 0);
	if(count($version2Parts) < $count)
		$version2Parts = array_pad($version2Parts, $count, 0);

	for($i=0; $i<$count; $i++)
	{
		if($version1Parts[$i] == $version2Parts[$i])
			continue;
		else if($version1Parts[$i] > $version2Parts[$i])
			return(VERSION_IS_NEWER);
		else if($version1Parts[$i] < $version2Parts[$i])
			return(VERSION_IS_OLDER);
	}

	return(VERSION_IS_EQUAL);
}

/**
 * setup page header
 *
 */
function pageHeader($update = false, $convert = false)
{
	global $lang_setup, $lang, $step;

	if($convert === 'mb4' || $convert === 'utf8mb4')
	{
		$mode = 'convertmb4';
		$action = 'utf8mb4convert.php';
		$heading = 'UTF8MB4';
	}
	elseif($convert)
	{
		$mode = 'convert';
		$action = 'utf8convert.php';
		$heading = $lang_setup['utf8convert'];
	}
	elseif($update)
	{
		$mode = 'update';
		$action = 'update.php';
		$heading = $lang_setup['update'];
	}
	else
	{
		$mode = 'setup';
		$action = 'index.php';
		$heading = $lang_setup['setup'];
	}

	header('Content-Type: text/html; charset=UTF-8');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('X-Robots-Tag: noindex, nofollow');
	header('X-Frame-Options: DENY');
	header('Referrer-Policy: no-referrer');
	$langAttr = ($lang === 'deutsch') ? 'de' : 'en';
	?>
<!doctype html>
<html lang="<?php echo SetupH($langAttr); ?>">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<title>b1gMail - <?php echo SetupH($heading); ?></title>
	<link rel="stylesheet" href="../admin/templates/css/tabler.min.css" />
	<link rel="stylesheet" href="../admin/templates/css/tabler-icons.min.css" />
	<link rel="stylesheet" href="../admin/templates/css/inter.css" />
	<link rel="stylesheet" href="res/setup.css" />
	<link rel="icon" type="image/png" href="../admin/templates/images/favicon-256x256.png" />
</head>
<body id="setupBody">
<div class="page">
	<div class="container setup-wrap py-4">
		<div class="text-center mb-4">
			<img class="setup-logo" src="../admin/templates/images/logo_text.png" alt="b1gMail" />
		</div>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo SetupH($heading); ?></h3>
			</div>
			<form action="<?php echo SetupH($action); ?>" method="post" autocomplete="off">
				<input type="hidden" name="setup_csrf" value="<?php echo SetupH(SetupCsrfToken()); ?>" />
				<?php SetupEmitHiddenSecrets(); ?>
				<?php if($step != STEP_SELECT_LANGUAGE) { ?>
				<input type="hidden" name="lng" value="<?php echo SetupH($lang); ?>" />
				<?php } ?>
				<div class="card-header bg-muted-lt">
					<?php echo SetupRenderStepper($update, $convert); ?>
				</div>
	<?php
	SetupOpenCardBody();
}

/**
 * setup page footer
 *
 */
function pageFooter($update = false, $convert = false)
{
	global $lang_setup, $nextStep, $backStep, $nextDisabled;

	SetupCloseCardBody();
	if(isset($nextStep) || isset($backStep))
	{
		echo '<div class="card-footer">';
		echo '<div class="d-flex align-items-center justify-content-between gap-2">';
		if(isset($backStep))
			echo '<button type="submit" class="btn btn-outline-secondary" name="step" value="'.(int)$backStep.'">'.$lang_setup['back'].'</button>';
		else
			echo '<span></span>';
		if(isset($nextStep) && $nextStep != -1)
		{
			$disabled = !empty($nextDisabled) ? ' disabled="disabled"' : '';
			echo '<button type="submit" class="btn btn-primary ms-auto" id="next_button" name="step" value="'.(int)$nextStep.'"'.$disabled.'>'.$lang_setup['next'].' &raquo;</button>';
		}
		echo '</div>';
		echo '</div>';
	}
	?>
			</form>
		</div>
	</div>
</div>
<script src="../admin/templates/js/tabler.min.js"></script>
<script src="res/setup.js"></script>
</body>
</html>
	<?php
}

/**
 * read setup language file
 *
 */
function ReadLanguage()
{
	global $lang, $lang_setup, $step, $exampleData;

	SetupCapturePost();

	// language?
	if(!isset($_GET['lng']) && !isset($_POST['lng']))
	{
		// try auto detection
		$acceptLanguages = explode(';', str_replace(array(' ', ','), ';', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
		$lang = 'english';
		foreach($acceptLanguages as $acceptLanguage)
			if($acceptLanguage == 'de')
			{
				$lang = 'deutsch';
				break;
			}
			else if($acceptLanguage == 'en')
			{
				$lang = 'english';
				break;
			}
	}
	else
		$lang = isset($_GET['lng']) ? $_GET['lng'] : $_POST['lng'];

	// load language
	$lang_setup = array();
	$lang = preg_replace('/[^a-z]/', '', $lang);
	$langFile = './' . $lang . '.lang.php';
	if(!file_exists($langFile))
	{
		$step = STEP_SELECT_LANGUAGE;
		$lang = 'deutsch';
		$langFile = './' . $lang . '.lang.php';
	}
	include($langFile);
}

/**
 * check mysql login
 *
 * @param string $host
 * @param string $user
 * @param string $pass
 * @param string $db
 * @return bool
 */
function CheckMySQLLogin($host, $user, $pass, $db)
{
	$result = false;

	if(!SetupHostAllowed($host))
		return false;

	$connection = @mysqli_connect($host, $user, $pass);
	if($connection)
	{
		if(@mysqli_select_db($connection, $db))
			$result = $connection;
	}

	return($result);
}

/**
 * encode a single (possibly international) email address to IDN form
 *
 * @param string $email Email
 * @return string
 */
function EncodeSingleEMail($email)
{
	if(strpos($email, '@') !== false)
	{
		list($localPart, $domainPart) = explode('@', $email);
		$email = $localPart . '@' . EncodeDomain($domainPart);
	}
	return $email;
}

/**
 * check pop3 login
 *
 * @param string $host
 * @param string $user
 * @param string $pass
 * @return bool
 */
function CheckPOP3Login($host, $user, $pass, $port = 110, $tls = false)
{
	$result = false;
	$port = (int)$port;
	if($port <= 0)
		$port = $tls ? 995 : 110;

	if(!SetupHostAllowed($host))
		return false;

	$target = ($tls ? 'ssl://' : '').$host;
	$sock = @fsockopen($target, $port, $errno, $errstr, 10);
	if($sock)
	{
		if(($response = @fgets($sock, 255))
			&& substr($response, 0, 3) == '+OK')
		{
			@fwrite($sock, 'USER ' . EncodeSingleEMail($user) . "\r\n");

			if(($response = @fgets($sock, 255))
				&& substr($response, 0, 3) == '+OK')
			{
				@fwrite($sock, 'PASS ' . $pass . "\r\n");

				if(($response = @fgets($sock, 255))
					&& substr($response, 0, 3) == '+OK')
				{
					$result = true;
				}
			}
		}

		@fwrite($sock, 'QUIT' . "\r\n");
		@fclose($sock);
	}

	return($result);
}

/**
 * password generator
 *
 * @return string
 */
function GeneratePW($length = 16)
{
	$chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	$max = strlen($chars) - 1;
	$result = '';
	for($i = 0; $i < $length; $i++)
		$result .= $chars[random_int(0, $max)];
	return $result;
}


/**
 * synchronize DB structure against an DB structure array
 *
 * @param resource $connection
 * @param array $databaseStructure (New/correct) DB structure
 * @param bool $return Return queries?
 * @param bool $return Return queries?
 * @return array
 */
function SyncDBStruct($connection, $databaseStructure, $return = true, $utf8Mode = false)
{
	// queries to execute
	$syncQueries = array();

	// get tables
	$myTables = array();
	$res = mysqli_query($connection, 'SHOW TABLES');
	while($row = mysqli_fetch_array($res, MYSQLI_NUM))
		$myTables[] = $row[0];
	mysqli_free_result($res);

	// compare tables
	foreach($databaseStructure as $tableName=>$tableInfo)
	{
		$tableFields = $tableInfo['fields'];
		$tableIndexes = $tableInfo['indexes'];

		//
		// table exists => compare fields and indexes
		//
		if(in_array($tableName, $myTables))
		{
			// get my fields
			$myFields = array();
			$res = mysqli_query($connection, 'SHOW FIELDS FROM ' . $tableName);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
			{
				if($row['Null'] == '') $row['Null'] = 'NO';
				$myFields[$row['Field']] = array($row['Field'], stripslashes($row['Type']), $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
			}
			mysqli_free_result($res);

			// get my indexes
			$myIndexes = array();
			$res = mysqli_query($connection, 'SHOW INDEX FROM ' . $tableName);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC))
				if(isset($myIndexes[$row['Key_name']]))
					$myIndexes[$row['Key_name']][] = $row['Column_name'];
				else
					$myIndexes[$row['Key_name']] = array($row['Column_name']);
			mysqli_free_result($res);

			// compare fields
			foreach($tableFields as $field)
			{
				$op = false;

				if(!isset($myFields[$field[0]]))
				{
					$op = 'ADD';
				}
				else
				{
					$myField = $myFields[$field[0]];
					if($myField[1] != $field[1]
						|| $myField[2] != $field[2]
						|| ($myField[4] != $field[4] && !(($myField[4]==0 && $field[4]=='') || ($myField[4]=='' && $field[4]==0)))
						|| $myField[5] != $field[5])
					{
						$op = 'MODIFY';
					}
				}

				if($op !== false)
				{
					$syncQueries[] = sprintf('ALTER TABLE %s %s `%s` %s%s%s%s%s',
						$tableName,
						$op,
						$field[0],
						$field[1],
						$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
										? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
										: '') : '',
						$field[2] == 'NO' ? ' NOT NULL' : '',
						$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
							? (is_numeric($field[4])
									? ' DEFAULT ' . $field[4]
									: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
							: ''),
						$field[5] != '' ? ' ' . $field[5] : '');
				}
			}

			// compare indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				// keys
				if($indexName != 'PRIMARY')
				{
					$op = false;

					if(!isset($myIndexes[$indexName]))
					{
						$op = true;
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						$op = true;
						$syncQueries[] = sprintf('ALTER TABLE %s DROP KEY `%s`',
							$tableName,
							$indexName);
					}

					if($op)
					{
						$syncQueries[] = sprintf('ALTER TABLE %s ADD KEY `%s`(%s)',
							$tableName,
							$indexName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}

				// primary keys
				else
				{
					if(!isset($myIndexes[$indexName]))
					{
						// add
						$syncQueries[] = sprintf('ALTER TABLE %s ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
					else if($myIndexes[$indexName] != $indexFields)
					{
						// drop, add
						$syncQueries[] = sprintf('ALTER TABLE %s DROP PRIMARY KEY, ADD PRIMARY KEY(%s)',
							$tableName,
							'`' . implode('`,`', $indexFields) . '`');
					}
				}
			}
		}

		//
		// table does not exist => create
		//
		else
		{
			$stmt = sprintf('CREATE TABLE %s(' . "\n",
				$tableName);

			// fields
			foreach($tableFields as $field)
			{
				$stmt .= sprintf(' `%s` %s%s%s%s%s,' . "\n",
					$field[0],
					$field[1],
					$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
									? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
									: '') : '',
					$field[2] == 'NO' ? ' NOT NULL' : '',
					$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
						? (is_numeric($field[4])
								? ' DEFAULT ' . $field[4]
								: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
						: ''),
					$field[5] != '' ? ' ' . $field[5] : '');
			}

			// indexes
			foreach($tableIndexes as $indexName=>$indexFields)
			{
				if($indexName == 'PRIMARY')
					$stmt .= sprintf(' PRIMARY KEY (%s),' . "\n",
						'`' . implode('`,`', $indexFields) . '`');
				else
					$stmt .= sprintf(' KEY `%s`(%s),' . "\n",
						$indexName,
						'`' . implode('`,`', $indexFields) . '`');
			}

			$stmt = substr($stmt, 0, -2) . "\n" . ')';

			$syncQueries[] = $stmt;
		}
	}

	// return
	if($return)
		return($syncQueries);

	// execute queries
	$result = array();
	foreach($syncQueries as $query)
		if(mysqli_query($connection, $query))
			$result[$query] = true;
		else
			$result[$query] = false;

	// return
	return($result);
}

/**
 * create datbase structure
 *
 * @param resource $connection
 * @param array $databaseStructure
 * @param bool $utf8Mode
 * @return array
 */
function CreateDatabaseStructure($connection, $databaseStructure, $utf8Mode = false, $dbName = '')
{
	// queries to execute
	$syncQueries = array();

	if($utf8Mode && $dbName != '')
		$syncQueries[] = 'ALTER DATABASE `'  . SetupMysqlIdent($dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';

	// create tables
	foreach($databaseStructure as $tableName=>$tableInfo)
	{
		$tableFields = $tableInfo['fields'];
		$tableIndexes = $tableInfo['indexes'];

		$stmt = sprintf('CREATE TABLE %s(' . "\n",
			$tableName);

		// fields
		foreach($tableFields as $field)
		{
			$stmt .= sprintf(' `%s` %s%s%s%s%s,' . "\n",
				$field[0],
				$field[1],
				$utf8Mode ? (strpos($field[1], 'char') !== false || strpos($field[2], 'text') !== false
							? ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
							: '') : '',
				$field[2] == 'NO' ? ' NOT NULL' : '',
				$field[4] == 'NULL' ? ' DEFAULT NULL' : ($field[4] != ''
					? (is_numeric($field[4])
							? ' DEFAULT ' . $field[4]
							: ' DEFAULT \'' . SQLEscape($field[4], $connection) . '\'')
					: ''),
				$field[5] != '' ? ' ' . $field[5] : '');
		}

		// indexes
		foreach($tableIndexes as $indexName=>$indexFields)
		{
			if($indexName == 'PRIMARY')
				$stmt .= sprintf(' PRIMARY KEY (%s),' . "\n",
					'`' . implode('`,`', $indexFields) . '`');
			else
				$stmt .= sprintf(' KEY `%s`(%s),' . "\n",
					$indexName,
					'`' . implode('`,`', $indexFields) . '`');
		}

		$stmt = substr($stmt, 0, -2) . "\n" . ')';

		$syncQueries[] = $stmt;
	}

	// execute queries
	$result = array();
	foreach($syncQueries as $query)
		if(mysqli_query($connection, $query))
			$result[$query] = true;
		else
			$result[$query] = mysqli_error($connection);

	// return
	return($result);
}

/**
 * convert string encoding
 *
 * @param string $str String
 * @param string $from In encoding
 * @param string $to Out encoding
 * @return string
 */
function ConvertEncoding($str, $from, $to)
{
	if(function_exists('mb_convert_encoding'))
		return(mb_convert_encoding($str, $to, $from));
	else if(function_exists('iconv'))
		return(iconv($from, $to, $str));
	else if(function_exists('utf8_encode') && strtolower($to) == 'utf-8' && strpos(strtolower($from), 'iso-8859-1') !== false)
		return(utf8_encode($str));
	return($str);
}

/**
 * get language file info
 *
 */
function GetLanguageInfo($fileName)
{
	$result = array();
	$fp = @fopen($fileName, 'r');
	if(is_resource($fp))
	{
		while($line = fgets($fp))
		{
			if(substr($line, 0, strlen('// b1gMailLang::')) == '// b1gMailLang::')
			{
				list(, $langTitle,
						$langAuthor,
						$langAuthorMail,
						$langAuthorWeb,
						$langCharset,
						$langLocale) = explode('::', trim($line));
				$result['ctime'] = filectime($fileName);
				$result['title'] = $langTitle;
				$result['author'] = $langAuthor;
				$result['authorMail'] = $langAuthorMail;
				$result['authorWeb'] = $langAuthorWeb;
				$result['charset'] = $langCharset;
				$result['locale'] = $langLocale;
				$result['writeable'] = is_writeable($fileName);
				$result['langDefLine'] = trim($line);
				break;
			}
		}

		fclose($fp);
	}
	return($result);
}

/**
 * Ensure config.inc.php defines B1GMAIL_SIGNKEY (legacy installs / incomplete migrations).
 *
 * @param string $configPath
 * @return bool True when define is present afterwards
 */
function ConfigEnsureSignKeyInFile($configPath)
{
	if(!is_readable($configPath))
		return false;

	$contents = file_get_contents($configPath);
	if($contents === false)
		return false;

	if(preg_match('/define\s*\(\s*[\'"]B1GMAIL_SIGNKEY[\'"]\s*,/i', $contents))
		return true;

	if(!is_writable($configPath))
		return false;

	$signKey = SetupCreateSignKey();
	$line = "define('B1GMAIL_SIGNKEY', '".$signKey."');";

	if(strpos($contents, '?>') !== false)
		$contents = str_replace("\n?>", "\n".$line."\n?>", $contents);
	else
		$contents = rtrim($contents)."\n".$line."\n";

	return file_put_contents($configPath, $contents) !== false;
}

// target version
$target_version = '7.5.0-RC2';