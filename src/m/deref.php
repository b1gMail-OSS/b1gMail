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

include('../serverlib/init.inc.php');

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * check referer
 */
if(!isset($_SERVER['HTTP_REFERER'])
	|| strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower($_SERVER['HTTP_HOST'])) === false)
{
	if($bm_prefs['cookie_lock'] == 'yes')
	{
		$ok = false;
		foreach($_COOKIE as $key=>$val)
			if(substr($key, 0, strlen('sessionSecret_')) == 'sessionSecret_')
				$ok = true;
		if(!$ok)
			die('Access denied');
	}
}

/**
 * deref code
 */
$url = $_SERVER['REQUEST_URI'];
$sepPos = strpos($url, '?');
if($sepPos !== false)
{
	$targetURL = substr($url, $sepPos+1);
	$tpl->assign('url', HTMLFormat($targetURL));
	$tpl->display('nli/deref.tpl');
}
?>