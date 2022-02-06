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

// if the language cookie is set and the client indicated that is has cached the file before,
// check if the cached version is up to date *before* including init.inc.php to decrease
// clientlang.php latency
if(isset($_COOKIE['bm_language'])
	&& (isset($_SERVER['HTTP_IF_NONE_MATCH']) || isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])))
{
	$sanitizedLang = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $_COOKIE['bm_language']);
	$langFn = 'languages/' . $sanitizedLang . '.lang.php';
	if(file_exists($langFn))
	{
		$lastModified = filemtime($langFn);
		$eTag = md5_file($langFn);

		if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified
			|| (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $eTag))
		{
			header('HTTP/1.1 304 Not Modified');
			exit();
		}
	}
}

include('./serverlib/init.inc.php');
if(isset($_REQUEST['sid']) && trim($_REQUEST['sid']) != '')
	RequestPrivileges(PRIVILEGES_USER, true) || RequestPrivileges(PRIVILEGES_ADMIN, true);

$lastModified = filemtime(B1GMAIL_DIR . 'languages/' . $currentLanguage . '.lang.php');
$eTag = md5_file(B1GMAIL_DIR . 'languages/' . $currentLanguage . '.lang.php');

header('Content-Type: text/javascript; charset=' . $currentCharset);
header('Cache-Control: private');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
header('ETag: ' . $eTag);

if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified
	|| (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $eTag))
{
	header('HTTP/1.1 304 Not Modified');
	exit();
}

echo '<!--' . "\n";
echo 'var lang = new Array();' . "\n";

foreach($lang_client as $key=>$value)
{
	printf('lang[\'%s\'] = "%s";' . "\n",
		$key,
		str_replace("\n", '\n', str_replace("\r", '', addslashes($value))));
}

echo '//-->';
