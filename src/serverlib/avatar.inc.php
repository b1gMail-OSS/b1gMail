<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

define('AVATAR_MAX_UPLOAD_BYTES', 2097152);
define('AVATAR_MAX_DIMENSION', 4096);

/**
 * @return int[]
 */
function AvatarSizes()
{
	return array(32, 128);
}

/**
 * @return string[]
 */
function AvatarSources()
{
	return array('initials', 'upload', 'libravatar', 'gravatar', 'libravatar_gravatar_initials');
}

/**
 * @param mixed $source
 * @return string
 */
function AvatarNormalizeSource($source)
{
	if(!is_string($source) || !in_array($source, AvatarSources(), true))
		return 'initials';

	return $source;
}

/**
 * @param string $accountType
 * @return bool
 */
function AvatarValidAccountType($accountType)
{
	return $accountType === 'user' || $accountType === 'admin';
}

/**
 * Base directory for profile avatars (under configured data folder).
 *
 * @return string Trailing slash
 */
function AvatarDataBaseDir()
{
	if(defined('B1GMAIL_DATA_DIR'))
		$base = B1GMAIL_DATA_DIR;
	else
	{
		global $bm_prefs;
		$base = (isset($bm_prefs['datafolder']) && is_string($bm_prefs['datafolder']) && $bm_prefs['datafolder'] !== '')
			? $bm_prefs['datafolder']
			: (B1GMAIL_DIR . 'data/');
	}

	return rtrim(str_replace('\\', '/', $base), '/') . '/avatars/';
}

/**
 * Legacy storage path (temp); used only for one-time migration.
 *
 * @return string Trailing slash
 */
function AvatarLegacyTempBaseDir()
{
	return B1GMAIL_DIR . 'temp/avatars/';
}

/**
 * Move avatars from temp/avatars to the data folder for one account.
 *
 * @param string $accountType
 * @param int    $accountID
 */
function AvatarMigrateAccountFromTemp($accountType, $accountID)
{
	if(!AvatarValidAccountType($accountType))
		return;

	$accountID = max(0, (int)$accountID);
	if($accountID < 1)
		return;

	$legacyDir = AvatarLegacyTempBaseDir() . $accountType . '/' . $accountID . '/';
	if(!is_dir($legacyDir))
		return;

	$targetDir = AvatarDataBaseDir() . $accountType . '/' . $accountID . '/';
	if(is_file($targetDir . '128.jpg'))
		return;

	if(!is_dir(AvatarDataBaseDir()) && !@mkdir(AvatarDataBaseDir(), 0755, true) && !is_dir(AvatarDataBaseDir()))
		return;

	$parent = dirname(rtrim($targetDir, '/'));
	if(!is_dir($parent) && !@mkdir($parent, 0755, true) && !is_dir($parent))
		return;

	if(is_dir($targetDir))
	{
		foreach(array('32.jpg', '128.jpg', 'meta.json') as $file)
		{
			$from = $legacyDir . $file;
			$to = $targetDir . $file;
			if(is_file($from) && !is_file($to))
				@rename($from, $to);
		}
		@rmdir($legacyDir);
		return;
	}

	@rename($legacyDir, rtrim($targetDir, '/'));
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @return string
 */
function AvatarStorageDir($accountType, $accountID)
{
	AvatarMigrateAccountFromTemp($accountType, $accountID);

	return AvatarDataBaseDir() . $accountType . '/' . max(0, (int)$accountID) . '/';
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @return string
 */
function AvatarMetaPath($accountType, $accountID)
{
	return AvatarStorageDir($accountType, $accountID) . 'meta.json';
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @param int    $size
 * @return string
 */
function AvatarImagePath($accountType, $accountID, $size)
{
	return AvatarStorageDir($accountType, $accountID) . (int)$size . '.jpg';
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @return bool
 */
function AvatarHasCustomImage($accountType, $accountID)
{
	return is_file(AvatarImagePath($accountType, $accountID, 128))
		&& is_readable(AvatarImagePath($accountType, $accountID, 128));
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @return string
 */
function AvatarGetStoredSource($accountType, $accountID)
{
	$path = AvatarMetaPath($accountType, $accountID);
	if(!is_file($path) || !is_readable($path))
		return 'initials';

	$raw = @file_get_contents($path);
	if($raw === false || trim($raw) === '')
		return 'initials';

	$data = @json_decode($raw, true);
	if(!is_array($data) || empty($data['source']))
		return 'initials';

	return AvatarNormalizeSource($data['source']);
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @param string $source
 */
function AvatarSetStoredSource($accountType, $accountID, $source)
{
	$dir = AvatarStorageDir($accountType, $accountID);
	if(!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir))
		return;

	$source = AvatarNormalizeSource($source);
	@file_put_contents(AvatarMetaPath($accountType, $accountID),
		json_encode(array('source' => $source), JSON_UNESCAPED_UNICODE));
}

/**
 * @param string $email
 * @return string
 */
function AvatarGetEmailHash($email)
{
	return md5(strtolower(trim($email)));
}

/**
 * Login/local part for user avatar initials when first and last name are empty.
 *
 * @param string $email
 * @return string
 */
function AvatarUsernameFallbackForUser($email)
{
	$email = function_exists('DecodeEMail') ? DecodeEMail(trim($email)) : trim($email);
	if($email === '')
		return '';

	$at = strpos($email, '@');

	return $at !== false ? substr($email, 0, $at) : $email;
}

/**
 * @param string $vorname
 * @param string $nachname
 * @param string $usernameFallback Used only when both names are empty (admin username or e-mail local part)
 * @return string
 */
function AvatarGetInitials($vorname, $nachname, $usernameFallback = '')
{
	$initials = '';

	if(trim($vorname) != '')
		$initials .= mb_strtoupper(mb_substr($vorname, 0, 1));
	if(trim($nachname) != '')
		$initials .= mb_strtoupper(mb_substr($nachname, 0, 1));
	if($initials === '' && trim($usernameFallback) != '')
		$initials = mb_strtoupper(mb_substr(trim($usernameFallback), 0, 1));

	return $initials;
}

/**
 * @param string $email
 * @param int $size
 * @param string $default
 * @return string
 */
function AvatarGetGravatarUrl($email, $size, $default = '404')
{
	return 'https://www.gravatar.com/avatar/' . AvatarGetEmailHash($email)
		. '?s=' . max(1, (int)$size)
		. '&d=' . rawurlencode($default);
}

/**
 * @param string $email
 * @param int $size
 * @param string $default
 * @return string
 */
function AvatarGetLibravatarUrl($email, $size, $default = '404')
{
	return 'https://seccdn.libravatar.org/avatar/' . AvatarGetEmailHash($email)
		. '?s=' . max(1, (int)$size)
		. '&d=' . rawurlencode($default);
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @param int    $size
 * @return string
 */
function AvatarGetImageUrl($accountType, $accountID, $size)
{
	global $bm_prefs;

	$size = in_array((int)$size, AvatarSizes(), true) ? (int)$size : 32;
	$params = array('size' => $size);

	if(AvatarHasCustomImage($accountType, $accountID))
	{
		$mtime = @filemtime(AvatarImagePath($accountType, $accountID, $size));
		if($mtime > 0)
			$params['v'] = $mtime;
	}

	if($accountType === 'admin')
	{
		$base = function_exists('AdminFqdnBaseUrl')
			? AdminFqdnBaseUrl()
			: (rtrim($bm_prefs['selfurl'], '/') . '/admin/');

		return SessionUrl($base . 'avatar.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
	}

	return SessionUrl(rtrim($bm_prefs['selfurl'], '/') . '/avatar.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
}

/**
 * @param string $source
 * @param string $accountType
 * @param int    $accountID
 * @return string
 */
function AvatarResolveDisplayMode($source, $accountType, $accountID)
{
	$source = AvatarNormalizeSource($source);

	if(AvatarHasCustomImage($accountType, $accountID) && $source === 'upload')
		return 'upload';

	if($source === 'upload')
		return 'initials';

	return $source;
}

/**
 * @param BMUser $thisUser
 * @return string
 */
function AvatarGetUserSource($thisUser)
{
	return AvatarNormalizeSource($thisUser->GetPref('avatar_source'));
}

/**
 * @param int $adminID
 * @return string
 */
function AvatarGetAdminSource($adminID)
{
	$adminID = (int)$adminID;

	if(AvatarHasCustomImage('admin', $adminID))
	{
		if(AvatarGetStoredSource('admin', $adminID) !== 'upload')
			AvatarSetStoredSource('admin', $adminID, 'upload');

		return 'upload';
	}

	return AvatarGetStoredSource('admin', $adminID);
}

/**
 * @param string $accountType
 * @param int    $accountID
 */
function AvatarDeleteCustom($accountType, $accountID)
{
	if(!AvatarValidAccountType($accountType))
		return;

	$dir = AvatarStorageDir($accountType, $accountID);
	foreach(AvatarSizes() as $size)
	{
		$file = AvatarImagePath($accountType, $accountID, $size);
		if(is_file($file))
			@unlink($file);
	}
	if(is_file(AvatarMetaPath($accountType, $accountID)))
		@unlink(AvatarMetaPath($accountType, $accountID));
	if(is_dir($dir))
		@rmdir($dir);
}

/**
 * @param int $code PHP upload error code
 * @return string
 */
function AvatarMapUploadError($code)
{
	switch((int)$code)
	{
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		return 'avatar_upload_too_large';
	case UPLOAD_ERR_NO_FILE:
		return 'avatar_upload_no_file';
	case UPLOAD_ERR_PARTIAL:
		return 'avatar_upload_partial';
	default:
		return 'avatar_upload_failed';
	}
}

/**
 * Ensure base storage exists.
 */
function AvatarEnsureStorageBase()
{
	$base = AvatarDataBaseDir();
	if(!is_dir($base))
		@mkdir($base, 0755, true);
}

/**
 * @param string $tmpPath
 * @return resource|false
 */
function AvatarLoadImageResource($tmpPath)
{
	if(!function_exists('imagecreatetruecolor'))
		return false;

	$info = @getimagesize($tmpPath);
	if(!is_array($info) || empty($info[2]))
		return false;

	if($info[0] > AVATAR_MAX_DIMENSION || $info[1] > AVATAR_MAX_DIMENSION)
		return false;

	$allowed = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
	if(defined('IMAGETYPE_WEBP'))
		$allowed[] = IMAGETYPE_WEBP;
	if(!in_array($info[2], $allowed, true))
		return false;

	$data = @file_get_contents($tmpPath);
	if($data === false || $data === '')
		return false;

	$im = @imagecreatefromstring($data);
	if($im === false)
		return false;

	if(!imageistruecolor($im))
	{
		$converted = @imagepalettetotruecolor($im);
		if($converted)
			$im = $converted;
	}

	return $im;
}

/**
 * @param resource $im
 * @param int      $size
 * @return resource
 */
function AvatarResizeSquare($im, $size)
{
	$width = imagesx($im);
	$height = imagesy($im);
	$min = min($width, $height);
	$srcX = (int)max(0, ($width - $min) / 2);
	$srcY = (int)max(0, ($height - $min) / 2);
	$dst = imagecreatetruecolor($size, $size);
	$white = imagecolorallocate($dst, 255, 255, 255);
	imagefilledrectangle($dst, 0, 0, $size, $size, $white);
	imagecopyresampled($dst, $im, 0, 0, $srcX, $srcY, $size, $size, $min, $min);

	return $dst;
}

/**
 * @param string      $accountType
 * @param int         $accountID
 * @param array|null  $file $_FILES entry
 * @return string empty on success, else error key
 */
function AvatarHandleUpload($accountType, $accountID, $file)
{
	if(!AvatarValidAccountType($accountType) || $accountID < 1)
		return 'avatar_upload_failed';

	if(!function_exists('imagecreatetruecolor'))
		return 'avatar_upload_no_gd';

	if(!is_array($file))
		return 'avatar_upload_no_file';

	$uploadError = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
	if($uploadError !== UPLOAD_ERR_OK)
		return AvatarMapUploadError($uploadError);

	if(($file['size'] ?? 0) > AVATAR_MAX_UPLOAD_BYTES)
		return 'avatar_upload_too_large';

	$tmp = $file['tmp_name'] ?? '';
	if($tmp === '' || !is_uploaded_file($tmp) || !is_readable($tmp))
		return 'avatar_upload_failed';

	AvatarEnsureStorageBase();

	if(AvatarLoadImageResource($tmp) === false)
		return 'avatar_upload_invalid_type';

	$dir = AvatarStorageDir($accountType, $accountID);
	if(!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir))
		return 'avatar_upload_not_writable';

	foreach(AvatarSizes() as $size)
	{
		$im = AvatarLoadImageResource($tmp);
		if($im === false)
			return 'avatar_upload_failed';

		$scaled = AvatarResizeSquare($im, $size);
		$target = AvatarImagePath($accountType, $accountID, $size);
		if(!@imagejpeg($scaled, $target, 88))
			return 'avatar_upload_not_writable';
	}

	AvatarSetStoredSource($accountType, $accountID, 'upload');

	return '';
}

/**
 * @param string $accountType
 * @param int    $accountID
 * @param int    $size
 */
function AvatarOutputImage($accountType, $accountID, $size)
{
	$size = in_array((int)$size, AvatarSizes(), true) ? (int)$size : 32;
	$path = AvatarImagePath($accountType, $accountID, $size);

	if(!AvatarHasCustomImage($accountType, $accountID))
	{
		header('HTTP/1.1 404 Not Found');
		exit();
	}

	if(!is_readable($path) && $size !== 128)
		$path = AvatarImagePath($accountType, $accountID, 128);

	if(!is_readable($path))
	{
		header('HTTP/1.1 404 Not Found');
		exit();
	}

	header('Content-Type: image/jpeg');
	header('Cache-Control: private, max-age=3600');
	header('X-Content-Type-Options: nosniff');
	if(!headers_sent())
		header('Content-Length: ' . filesize($path));
	readfile($path);
	exit();
}

/**
 * @param Template $tpl
 * @param string   $email
 * @param string   $vorname
 * @param string   $nachname
 * @param string   $avatarSource
 * @param string   $accountType
 * @param int      $accountID
 */
function AvatarAssignTemplateVars($tpl, $email, $vorname, $nachname, $avatarSource, $accountType = 'user', $accountID = 0, $usernameFallback = '')
{
	$avatarSource = AvatarNormalizeSource($avatarSource);
	$initials = AvatarGetInitials($vorname, $nachname, $usernameFallback);
	$displayMode = AvatarResolveDisplayMode($avatarSource, $accountType, $accountID);

	$tpl->assign('_userAvatarMode', $displayMode);
	$tpl->assign('_userInitials', $initials);
	$tpl->assign('_userAvatarHasCustom', AvatarHasCustomImage($accountType, $accountID));

	foreach(array(32 => 'Sm', 128 => 'Xl') as $size => $suffix)
	{
		$tpl->assign('_userAvatarGravatarUrl' . $suffix, AvatarGetGravatarUrl($email, $size));
		$tpl->assign('_userAvatarLibravatarUrl' . $suffix, AvatarGetLibravatarUrl($email, $size));
		if($accountID > 0)
			$tpl->assign('_userAvatarUploadUrl' . $suffix, AvatarGetImageUrl($accountType, $accountID, $size));
	}
}

/**
 * @param Template $tpl
 * @param array    $adminRow
 */
function AvatarAssignAdminTemplateVars($tpl, $adminRow)
{
	$adminID = (int)$adminRow['adminid'];
	$source = AvatarGetAdminSource($adminID);
	$email = isset($adminRow['email']) ? $adminRow['email'] : '';
	$displayMode = AvatarResolveDisplayMode($source, 'admin', $adminID);

	AvatarAssignTemplateVars($tpl,
		$email,
		$adminRow['firstname'] ?? '',
		$adminRow['lastname'] ?? '',
		$source,
		'admin',
		$adminID,
		isset($adminRow['username']) ? (string)$adminRow['username'] : '');

	$tpl->assign('_adminAvatarMode', $displayMode);
	$tpl->assign('_adminAvatarSource', $source);
	$tpl->assign('_adminAvatarHasCustom', AvatarHasCustomImage('admin', $adminID));
}
