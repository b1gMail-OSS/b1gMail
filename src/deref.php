<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al, 2022-2025 b1gMail.eu
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

if(!defined('B1GMAIL_INIT'))
	require './serverlib/init.inc.php';

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	$_REQUEST['action'] ?? ''));

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
$targetURL = DerefExtractTargetUrl();
if($targetURL !== '')
{
	$targetURL = str_replace('%23', '#', DerefCleanTargetUrl($targetURL));
	DerefAssignTplVars($targetURL);
	$tpl->assign('pref_exturl_warning', $bm_prefs['exturl_warning']);
	ModuleFunction('OnDerefPage', array($targetURL));
	$tpl->display('nli/deref.tpl');
}
