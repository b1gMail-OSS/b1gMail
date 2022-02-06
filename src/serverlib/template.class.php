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

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

/**
 * smarty
 */
include(B1GMAIL_DIR . 'serverlib/3rdparty/smarty/Smarty.class.php');

/**
 * template class (extends smarty)
 */
class Template extends Smarty
{
	var $_cssFiles, $_jsFiles;
	var $tplDir;
	var $reassignFolderList = false;
	var $hookTable = array();

	/**
	 * constructor
	 *
	 * @return Template
	 */
	function __construct()
	{
		global $bm_prefs, $lang_user, $lang_info;

		$this->_cssFiles = array('nli' => array(), 'li' => array(), 'admin' => array());
		$this->_jsFiles = array('nli' => array(), 'li' => array(), 'admin' => array());

		// template & cache directories
		if(ADMIN_MODE)
		{
			$this->template_dir	= B1GMAIL_DIR . 'admin/templates/';
			$this->compile_dir	= B1GMAIL_DIR . 'admin/templates/cache/';
			$this->assign('tpldir', $this->tplDir = './templates/');
		}
		else
		{
			$this->template_dir = B1GMAIL_DIR . 'templates/' . $bm_prefs['template'] . '/';
			$this->compile_dir 	= B1GMAIL_DIR . 'templates/' . $bm_prefs['template'] . '/cache/';
			$this->assign('tpldir', $this->tplDir = B1GMAIL_REL . 'templates/' . $bm_prefs['template'] . '/');
		}

		// variables
		$this->assign('service_title', 			HTMLFormat($bm_prefs['titel']));
		$this->assign('charset', 				$lang_info['charset']);
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$this->assign('selfurl', 			str_replace('http://', 'https://', $bm_prefs['selfurl']));
		else
			$this->assign('selfurl', 			$bm_prefs['selfurl']);
		$this->assign('_tpldir', 				'templates/' . $bm_prefs['template'] . '/');
		$this->assign('_tplname', 				$bm_prefs['template']);
		$this->assign('_regEnabled', 			$bm_prefs['regenabled'] == 'yes');
		$this->assign('serverTZ',				date('Z'));

		// post vars?
		if(isset($_POST['transPostVars']))
		{
			$_safePost = array();
			foreach($_POST as $key=>$val)
				$_safePost[$key] = HTMLFormat($val);
			$this->assign('_safePost', $_safePost);
		}

		// functions
		$this->register_function('banner', 			'TemplateBanner');
		$this->register_function('lng', 			'TemplateLang');
		$this->register_function('comment', 		'TemplateComment');
		$this->register_function('date', 			'TemplateDate');
		$this->register_function('size', 			'TemplateSize');
		$this->register_function('text', 			'TemplateText');
		$this->register_function('domain', 			'TemplateDomain');
		$this->register_function('email', 			'TemplateEMail');
		$this->register_function('progressBar',		'TemplateProgressBar');
		$this->register_function('miniCalendar',	'TemplateMiniCalendar');
		$this->register_function('fileSelector',	'TemplateFileSelector');
		$this->register_function('pageNav',			'TemplatePageNav');
		$this->register_function('addressList',		'TemplateAddressList');
		$this->register_function('storeTime',		'TemplateStoreTime');
		$this->register_function('halfHourToTime',	'TemplateHalfHourToTime');
		$this->register_function('implode',			'TemplateImplode');
		$this->register_function('mobileNr',		'TemplateMobileNr');
		$this->register_function('hook',			'TemplateHook');
		$this->register_function('fileDateSig',		'TemplateFileDateSig');
		$this->register_function('number',			'TemplateNumber');
		$this->register_function('fieldDate',		'TemplateFieldDate');

		// module handler
		ModuleFunction('OnCreateTemplate', array(&$this));
	}

	/**
	 * register with a template hook
	 *
	 * @param string $id Hook ID
	 * @param string $tpl File name of template to be included
	 */
	function registerHook($id, $tpl)
	{
		if(!isset($this->hookTable[$id]))
			$this->hookTable[$id] = array($tpl);
		else
			$this->hookTable[$id][] = $tpl;
	}

	/**
	 * adds a JS file to be included in the page
	 *
	 * @param string $area Area (nli/li/admin)
	 * @param string $file Filename
	 */
	function addJSFile($area, $file)
	{
		if(isset($this->_jsFiles[$area]))
			if(!in_array($file, $this->_jsFiles[$area]))
			{
				if(file_Exists($file))
					$file .= '?' . substr(md5(filemtime($file)), 0, 6);
				$this->_jsFiles[$area][] = $file;
				return(true);
			}

		return(false);
	}

	/**
	 * adds a CSS file to be included in the page
	 *
	 * @param string $area Area (nli/li/admin)
	 * @param string $file Filename
	 */
	function addCSSFile($area, $file)
	{
		if(isset($this->_cssFiles[$area]))
			if(!in_array($file, $this->_cssFiles[$area]))
			{
				$this->_cssFiles[$area][] = $file;
				return(true);
			}

		return(false);
	}

	function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false)
	{
		global $thisUser, $userRow, $groupRow, $lang_user, $plugins, $bm_prefs, $adminRow, $currentLanguage;

		$this->assign('templatePrefs', GetTemplatePrefs($bm_prefs['template']));

		// admin mode?
		if(ADMIN_MODE && isset($adminRow))
		{
			$this->assign('adminRow', $adminRow);

			$bmVer = B1GMAIL_VERSION;

			$this->assign('bmver', $bmVer);

			$pluginMenuItems = array();
			foreach($plugins->_plugins as $className=>$pluginInfo)
				if($plugins->getParam('admin_pages', $className))
					$pluginMenuItems[$className] = array('title' => $plugins->getParam('admin_page_title', $className),
															'icon' => $plugins->getParam('admin_page_icon', $className),);

			asort($pluginMenuItems);
			$this->assign('pluginMenuItems', $pluginMenuItems);

			$this->assign('isGerman', strpos(strtolower($currentLanguage), 'deutsch') !== false);
		}

		// tabs
		if(isset($userRow) && isset($groupRow))
		{
			$newMenu = array(
				array(
					'icon'		=> 'send_mail',
					'faIcon'	=> 'fa-envelope-o',
					'link'		=> 'email.compose.php?sid=',
					'text'		=> $lang_user['email'],
					'order'		=> 100
				),
				array(
					'sep'		=> true,
					'order'		=> 200
				),
				array(
					'icon'		=> 'ico_calendar',
					'faIcon'	=> 'fa-calendar',
					'link'		=> 'organizer.calendar.php?action=addDate&sid=',
					'text'		=> $lang_user['date2'],
					'order'		=> 300
				),
				array(
					'icon'		=> 'ico_todo',
					'faIcon'	=> 'fa-tasks',
					'link'		=> 'organizer.todo.php?action=addTask&sid=',
					'text'		=> $lang_user['task'],
					'order'		=> 400
				),
				array(
					'icon'		=> 'ico_addressbook',
					'faIcon'	=> 'fa-address-book-o',
					'link'		=> 'organizer.addressbook.php?action=addContact&sid=',
					'text'		=> $lang_user['contact'],
					'order'		=> 500
				),
				array(
					'icon'		=> 'ico_notes',
					'faIcon'	=> 'fa-sticky-note-o',
					'link'		=> 'organizer.notes.php?action=addNote&sid=',
					'text'		=> $lang_user['note'],
					'order'		=> 600
				)
			);

			$pageTabs = array(
				'start' => array(
					'icon'		=> 'start',
					'faIcon'	=> 'fa-home',
					'link'		=> 'start.php?sid=',
					'text'		=> $lang_user['start'],
					'order'		=> 100
				),
				'email' => array(
					'icon'		=> 'email',
					'faIcon'	=> 'fa-envelope-o',
					'link'		=> 'email.php?sid=',
					'text'		=> $lang_user['email'],
					'order'		=> 200
				)
			);

			if($thisUser->SMSEnabled())
			{
				$pageTabs['sms'] = array(
					'icon'		=> 'sms',
					'faIcon'	=> 'fa-comments',
					'link'		=> 'sms.php?sid=',
					'text'		=> $lang_user['sms'],
					'order'		=> 300
				);

				$newMenu[] = array(
					'sep'		=> true,
					'order'		=> 800
				);
				$newMenu[] = array(
					'icon'		=> 'ico_composesms',
					'faIcon'	=> 'fa-comments',
					'link'		=> 'sms.php?sid=',
					'text'		=> $lang_user['sms'],
					'order'		=> 801
				);
			}

			$pageTabs = array_merge($pageTabs, array(
				'organizer' => array(
					'icon'		=> 'organizer',
					'faIcon'	=> 'fa-calendar',
					'link'		=> 'organizer.php?sid=',
					'text'		=> $lang_user['organizer'],
					'order'		=> 400
				)));

			if($groupRow['webdisk'] + $userRow['diskspace_add'] > 0)
			{
				$pageTabs = array_merge($pageTabs, array(
					'webdisk' => array(
						'icon'		=> 'webdisk',
						'faIcon'	=> 'fa-cloud',
						'link'		=> 'webdisk.php?sid=',
						'text'		=> $lang_user['webdisk'],
						'order'		=> 500
					)));

				$newMenu[] = array(
					'sep'		=> true,
					'order'		=> 700
				);
				$newMenu[] = array(
					'icon'		=> 'webdisk_file',
					'faIcon'	=> 'fa-file-o',
					'link'		=> 'webdisk.php?do=uploadFilesForm&sid=',
					'text'		=> $lang_user['file'],
					'order'		=> 701
				);
			}

			$moduleResult = $plugins->callFunction('getUserPages', false, true, array(true));
			foreach($moduleResult as $userPages)
				$pageTabs = array_merge($pageTabs, $userPages);

			$moduleResult = $plugins->callFunction('getNewMenu', false, true, array(true));
			foreach($moduleResult as $newEntries)
				$newMenu = array_merge($newMenu, $newEntries);

			$pageTabs = array_merge($pageTabs, array(
				'prefs' => array(
					'icon'		=> 'prefs',
					'faIcon'	=> 'fa-cog',
					'link'		=> 'prefs.php?sid=',
					'text'		=> $lang_user['prefs'],
					'order'		=> 600
				)));

			// sort by order
			if(is_array($tabOrder = @unserialize($bm_prefs['taborder'])))
				foreach($tabOrder as $orderKey=>$orderVal)
					if(isset($pageTabs[$orderKey]))
						if($orderVal == -1)
							unset($pageTabs[$orderKey]);
						else
							$pageTabs[$orderKey]['order'] = $orderVal;

			ModuleFunction('BeforePageTabsAssign', array(&$pageTabs));
			uasort($pageTabs, 'TemplateTabSort');
			uasort($newMenu, 'TemplateTabSort');

			$this->assign('pageTabs', $pageTabs);
			$this->assign('pageTabsCount', count($pageTabs));
			$this->assign('newMenu', $newMenu);
			$this->assign('_userEmail', $userRow['email']);
			$this->assign('searchDetailsDefault', $userRow['search_details_default']=='yes');
			$this->assign('ftsBGIndexing', $bm_prefs['fts_bg_indexing']=='yes' && $groupRow['ftsearch']=='yes' && FTS_SUPPORT);

			if($groupRow['notifications'] == 'yes')
			{
				$this->assign('bmUnreadNotifications', $thisUser->GetUnreadNotifications());
				$this->assign('bmNotifyInterval', $bm_prefs['notify_interval']);
				$this->assign('bmNotifySound', $userRow['notify_sound'] == 'yes');
			}
		}

		// pugin pages (not logged in)
		else
		{
			$menu = array();
			$moduleResult = $plugins->callFunction('getUserPages', false, true, array(false));
			foreach($moduleResult as $userPages)
				$menu = array_merge($menu, $userPages);
			$this->assign('pluginUserPages', $menu);
		}

		// folder list
		if($this->reassignFolderList)
		{
			global $mailbox;

			if(isset($mailbox) && is_object($mailbox))
			{
				list(, $pageMenu) = $mailbox->GetPageFolderList();
				$this->assign('folderList', $pageMenu);
			}
		}

		ModuleFunction('BeforeDisplayTemplate', array($resource_name, &$this));

		$this->assign('_cssFiles', $this->_cssFiles);
		$this->assign('_jsFiles', $this->_jsFiles);

		StartPageOutput();
		return Smarty::fetch($resource_name, $cache_id, $compile_id, $display);
	}
}

/**
 * helper functions
 */
function TemplateTabSort($a, $b)
{
	$aOrder = isset($a['order']) ? $a['order'] : 599;
	$bOrder = isset($b['order']) ? $b['order'] : 599;

	if($aOrder == $bOrder)
		return(0);
	else if($aOrder < $bOrder)
		return(-1);
	else
		return(1);
}

/**
 * functions registered with smarty
 */
function TemplateFileDateSig($params, &$smarty)
{
	$fileName = $smarty->template_dir . $params['file'];
	if(!file_exists($fileName))
		return('');
	$time = filemtime($fileName);
	return(substr(md5($time), 0, 6));
}
function TemplateBanner($params, &$smarty)
{
	global $db, $groupRow;

	if(isset($groupRow) && is_array($groupRow) && $groupRow['ads'] == 'no')
		return('');

	if(isset($params['category']) && (($category = trim($params['category'])) != ''))
		$res = $db->Query('SELECT id,code FROM {pre}ads WHERE paused=? AND category=? ORDER BY (views/weight) ASC LIMIT 1',
			'no',
			$category);
	else
		$res = $db->Query('SELECT id,code FROM {pre}ads WHERE paused=? ORDER BY (views/weight) ASC LIMIT 1',
			'no');
	if($res->RowCount() == 1)
	{
		list($bannerID, $bannerCode) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$db->Query('UPDATE {pre}ads SET views=views+1 WHERE id=?',
			$bannerID);

		return($bannerCode);
	}

	return('');
}
function TemplateImplode($params, &$smarty)
{
	return(implode($params['glue'], $params['pieces']));
}
function TemplateLang($params, &$smarty)
{
	global $lang_user, $lang_client, $lang_admin;

	$phrase = $params['p'];

	if(ADMIN_MODE && isset($lang_admin[$phrase]))
		return($lang_admin[$phrase]);

	if(!ADMIN_MODE && isset($lang_user[$phrase]))
		return($lang_user[$phrase]);

	return('#UNKNOWN_PHRASE(' . $phrase . ')#');
}
function TemplateHalfHourToTime($params, &$smarty)
{
	$value = $params['value'];

	if(isset($params['dateStart']))
	{
		return(mktime(
			$value%2==0 ? $value/2 : ($value-1)/2,
			$value%2==0 ? 0 : 30,
			0,
			date('m', $params['dateStart']),
			date('d', $parmas['dateStart']),
			date('Y', $params['dateStart'])
		));
	}

	if($value%2==0)
		return(sprintf('%d:%02d', $value/2, 0));
	else
		return(sprintf('%d:%02d', ($value-1)/2, 30));
}
function TemplateComment($params, &$smarty)
{
	if(!DEBUG)
		return('');
	return('<!-- ' . $params['text'] . ' -->');
}
function TemplateDate($params, &$smarty)
{
	global $userRow, $bm_prefs, $lang_user;

	if(isset($params['nozero']) && $params['timestamp']==0)
		return('-');

	if(isset($userRow))
		$format = $userRow['datumsformat'];
	else
		$format = $bm_prefs['datumsformat'];
	$ts = $params['timestamp'];

	if($ts == -1)
		return($lang_user['unknown']);

	$diff = time() - $ts;
	if(isset($params['elapsed']))
	{
		if($diff >= 0 && $diff < TIME_ONE_MINUTE)
			$elapsed = sprintf($diff == 1 ? $lang_user['elapsed_second'] : $lang_user['elapsed_seconds'], $diff);
		else if($diff >= TIME_ONE_MINUTE && $diff < TIME_ONE_HOUR)
			$elapsed = sprintf(round($diff/TIME_ONE_MINUTE, 0) == 1 ? $lang_user['elapsed_minute'] : $lang_user['elapsed_minutes'], round($diff/TIME_ONE_MINUTE, 0));
		else if($diff >= TIME_ONE_HOUR && $diff < TIME_ONE_DAY)
			$elapsed = sprintf(round($diff/TIME_ONE_HOUR, 0) == 1 ? $lang_user['elapsed_hour'] : $lang_user['elapsed_hours'], round($diff/TIME_ONE_HOUR, 0));
		else if($diff >= TIME_ONE_DAY)
			$elapsed = sprintf(round($diff/TIME_ONE_DAY, 0) == 1 ? $lang_user['elapsed_day'] : $lang_user['elapsed_days'], round($diff/TIME_ONE_DAY, 0));
		else
			$elapsed = '';
	}
	else
		$elapsed = '';

	if(isset($params['dayonly']))
	{
		return(date('d.m.Y', $ts) . $elapsed);
	}
	else if(isset($params['format']))
	{
		return(_strftime($params['format'], $ts));
	}
	else if(isset($params['short']))
	{
		if(date('d.m.Y', $ts) == date('d.m.Y'))
			return(date('H:i', $ts));
		else if($ts > time()-6*TIME_ONE_DAY)
			return(_strftime('%A', $ts));
		else
			return(date('d.m.y', $ts));
	}
	else if(!isset($params['nice']))
	{
		return(date($format, $ts) . $elapsed);
	}
	else
	{
		$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

		if($ts >= $today && $ts <= $today+TIME_ONE_DAY)
			return(sprintf('%s, %s', $lang_user['today'], date('H:i:s', $ts)) . $elapsed);
		else if($ts >= $today-86400 && $ts < $today)
			return(sprintf('%s, %s', $lang_user['yesterday'], date('H:i:s', $ts)) . $elapsed);
		else
			return(date($format, $ts) . $elapsed);
	}
}
function TemplateSize($params, &$smarty)
{
	global $lang_user;

	$size = $params['bytes'];

	if($size == -1)
		return('<i>' . $lang_user['unlimited'] . '</i>');

	if($size < 1024)
		return((int)$size . ' B');
	else if($size < 1024*1024)
		return(sprintf('%.2f KB', round($size/1024, 2)));
	else if($size < 1024*1024*1024)
		return(sprintf('%.2f MB', round($size/1024/1024, 2)));
	else
		return(sprintf('%.2f GB', round($size/1024/1024/1024, 2)));
}
function cutHTML($str, $length, $add = '')
{
	// no &#;-entities -> use substr
	if(!preg_match('/\&#([x0-9]*);/', $str)
		&& !preg_match('/\&([a-zA-Z]*);/', $str))
		return(_strlen($str) > $length ? _substr($str, 0, $length-_strlen($add)) . $add : $str);

	// otherwise use the complicated way
	$tooLong = false;
	$result = array();
	for($i=0; $i<strlen($str); $i++)
	{
		$match = false;
		if(strlen($str)-$i > 3
			&& (preg_match('/^\&#([x0-9]*);/', substr($str, $i), $match)
				|| preg_match('/^\&([a-zA-Z]*);/', substr($str, $i), $match)))
		{
			$result[] = $match[0];
			$i += strlen($match[0])-1;
		}
		else
			$result[] = $str[$i];

		if(count($result) >= $length)
		{
			$tooLong = true;
			break;
		}
	}

	if($tooLong)
		return(implode('', array_slice($result, 0, $length-strlen($add))) . $add);
	else
		return(implode('', $result));
}
function TemplateFieldDate($params, &$smarty)
{
	global $bm_prefs;

	$val = $params['value'];
	if(empty($val))
		return '-';

	$parts = explode('-', $val);
	if(count($parts) != 3)
		return '-';

	list($y, $m, $d) = $parts;
	if($y == 0 || $m == 0 || $d == 0)
		return '-';

	return sprintf('%02d.%02d.%04d', $d, $m, $y);
}
function TemplateNumber($params, &$smarty)
{
	$no = (int)$params['value'];
	if(isset($params['min']))
		$no = max($params['min'], $no);
	if(isset($params['max']))
		$no = min($params['max'], $no);
	return($no);
}
function TemplateDomain($params, &$smarty)
{
	$domain = $params['value'];
	return(HTMLFormat(DecodeDomain($domain)));
}
function TemplateEMail($params, &$smarty)
{
	$email = DecodeEMail($params['value']);
	if(isset($params['cut']))
		$email = cutHTML($email, $params['cut'], '...');
	return(HTMLFormat($email));
}
function TemplateText($params, &$smarty)
{
	$text = $params['value'];

	if(isset($params['ucFirst']))
		$text = ucfirst($text);

	if(isset($params['escape']))
	{
		$text = addslashes($text);
		if(isset($params['noentities']))
			$text = str_replace('/', '\/', $text);
	}

	if(isset($params['cut']))
		$text = cutHTML($text, $params['cut'], '...');

	if($text == '' && !isset($params['allowEmpty']))
		return(' - ');

	if(isset($params['stripTags']))
		$text = strip_tags($text);

	if(isset($params['noentities']))
	{
		return($text);
	}
	else
	{
		$text = HTMLFormat($text, isset($params['allowDoubleEnc']) && $params['allowDoubleEnc']);
		return($text);
	}
}
function TemplateAddressList($params, &$smarty)
{
	$list = '';
	$short = isset($params['short']);

	foreach($params['list'] as $addressItem)
	{
		if($short)
		{
			if(isset($params['simple']))
				$list .= '; ' . trim(HTMLFormat($addressItem['name']) != '' ? HTMLFormat($addressItem['name']) : HTMLFormat(DecodeEMail($addressItem['mail'])));
			else
				$list .= sprintf(' <a class="mailAddressLink" href="javascript:void(0);" onclick="currentEMail=\'%s\';showAddressMenu(event);">%s</a>',
					addslashes(DecodeEMail($addressItem['mail'])),
					trim(HTMLFormat($addressItem['name']) != '' ? HTMLFormat($addressItem['name']) : HTMLFormat(DecodeEMail($addressItem['mail']))));
		}
		else
		{
			if(isset($params['simple']))
				$list .= '; ' . trim(HTMLFormat($addressItem['name']) . ' ' . (trim($addressItem['name']) != '' ? '&lt;' . HTMLFormat(DecodeEMail($addressItem['mail'])) . '&gt;'
							: HTMLFormat(DecodeEMail($addressItem['mail']))));
			else
				$list .= sprintf(' <a class="mailAddressLink" href="javascript:void(0);" onclick="currentEMail=\'%s\';showAddressMenu(event);">%s</a>',
					DecodeEMail(addslashes($addressItem['mail'])),
					trim(HTMLFormat($addressItem['name']) . ' ' . (trim($addressItem['name']) != '' ? '&lt;' . HTMLFormat(DecodeEMail($addressItem['mail'])) . '&gt;'
					: HTMLFormat(DecodeEMail($addressItem['mail'])))));
		}
	}

	if(isset($params['simple']))
		$list = substr($list, 2);

	return(trim($list));
}
function TemplateProgressBar($params, &$smarty)
{
	$value = $params['value'];
	$max = $params['max'];
	$width = $params['width'];
	$name = isset($params['name']) ? $params['name'] : mt_rand(0, 1000);

	if($max == 0)
		$valueWidth = 0;
	else
		$valueWidth = $width/$max * $value;

	return(sprintf('<div class="progressBar" id="pb_%s" style="width:%dpx;"><div class="progressBarValue" id="pb_%s_value" style="width:%dpx;"></div></div>',
		$name,
		$width,
		$name,
		min($width-2, $valueWidth)));
}
function TemplateMiniCalendar($params, &$smarty)
{
	global $userRow;
	if(!isset($userRow))
		return('Not logged in');
	if(!class_exists('BMCalendar'))
		include(B1GMAIL_DIR . 'serverlib/calendar.class.php');
	$calendar = _new('BMCalendar', array($userRow['id']));
	return($calendar->GenerateMiniCalendar(-1, -1));
}
function TemplateFileSelector($params, &$smarty)
{
	global $lang_user, $groupRow;

	$name = $params['name'];
	$size = isset($params['size']) ? (int)$params['size'] : 30;

	return(sprintf('<table width="100%%" cellspacing="1" cellpadding="0">'
				.		'<tr>'
				.			'<td width="10"><select onchange="changeFileSelectorSource(this, \'%s\')">'
				.								'<option value="local">%s</option>'
				. ((isset($groupRow) && is_array($groupRow) && $groupRow['webdisk'] > 0) ?
												'<option value="webdisk">%s</option>' : '<!-- %s -->')
				.							'</select></td>'
				.			'<td width="5">&nbsp;</td>'
				.			'<td><div id="fileSelector_local_%s" style="display:;"><input type="file" id="localFile_%s" name="localFile_%s%s" size="%d" style="width: 100%%;"%s /></div>'
				.				'<div id="fileSelector_webdisk_%s" style="display:none;"><input type="hidden" name="webdiskFile_%s_id" id="webdiskFile_%s_id" value="" /><input type="text" id="webdiskFile_%s" name="webdiskFile_%s" size="%d" readonly="readonly" /> <input onclick="webdiskDialog(\'%s\', \'open\', \'webdiskFile_%s\')" type="button" value="..." /></div></td>'
				.		'</tr>'
				.	'</table>',
				$name,
				$lang_user['localfile'],
				$lang_user['webdiskfile'],
				$name,
				$name,
				$name,
				isset($params['multiple']) ? '[]' : '',
				$size,
				isset($params['multiple']) ? ' multiple="multiple"' : '',
				$name,
				$name,
				$name,
				$name,
				$name,
				$size,
				session_id(),
				$name));
}
function TemplatePageNav($params, &$smarty)
{
	$tpl_on = $params['on'];
	$tpl_off = $params['off'];
	$aktuelle_seite = $params['page'];
	$anzahl_seiten = $params['pages'];
	$ret = '';

	$seiten = array($aktuelle_seite-3,
		$aktuelle_seite-2,
		$aktuelle_seite-1,
		$aktuelle_seite,
		$aktuelle_seite+1,
		$aktuelle_seite+2,
		$aktuelle_seite+3);

	if($aktuelle_seite > 1)
	{
		$ret .= str_replace('.t', '&lt;&lt;', str_replace('.s', ($aktuelle_seite-1), $tpl_off));
	}

	foreach($seiten as $key=>$val)
	{
		if($val >= 1 && $val <= $anzahl_seiten)
		{
			if($aktuelle_seite == $val)
			{
				$ret .= str_replace(array('.s', '.t'), $val, $tpl_on);
			}
			else
			{
				$ret .= str_replace(array('.s', '.t'), $val, $tpl_off);
			}
		}
	}

	if($aktuelle_seite < $anzahl_seiten)
	{
		$ret .= str_replace('.t', '&gt;&gt;', str_replace('.s', ($aktuelle_seite+1), $tpl_off));
	}

	return($ret);
}
function TemplateStoreTime($params, &$smarty)
{
	global $lang_user;

	$time = $params['value'];

	if($time == 86400)
		return('1 ' . $lang_user['days']);
	else if($time == 172800)
		return('2 ' . $lang_user['days']);
	else if($time == 432000)
		return('5 ' . $lang_user['days']);
	else if($time == 604800)
		return('7 ' . $lang_user['days']);
	else if($time == 1209600)
		return('2 ' . $lang_user['weeks']);
	else if($time == 2419200)
		return('4 ' . $lang_user['weeks']);
	else if($time == 4828400)
		return('2 ' . $lang_user['months']);
	else
		return('-');
}
function TemplateHook($params, &$smarty)
{
	$result = '';

	if(DEBUG && isset($_REQUEST['_showHooks']))
		$result .= '<div>#' . $params['id'] . '</div>';

	if(DEBUG)
		$result .= '<!-- hook(' . $params['id'] . ') -->';

	if(isset($smarty->hookTable) && is_array($smarty->hookTable)
	   && isset($smarty->hookTable[$params['id']]))
	{
		foreach($smarty->hookTable[$params['id']] as $file)
			$result .= $smarty->fetch($file);
	}

	if(DEBUG)
		$result .= '<!-- /hook(' . $params['id'] . ') -->';

	return($result);
}
function TemplateMobileNr($params, &$smarty)
{
	global $groupRow;

	$value = isset($params['value']) ? $params['value'] : '';
	$name = $params['name'];
	$size = isset($params['size']) ? $params['size'] : '100%';

	if(trim($groupRow['sms_pre']) != '')
	{
		$preOptions = '';
		$haveValue = false;
		$entries = explode(':', $groupRow['sms_pre']);
		foreach($entries as $entry)
		{
			if(trim($entry) != '')
			{
				if(substr($value, 0, strlen($entry)) == $entry && !$haveValue)
				{
					$preOptions .= sprintf('<option value="%s" selected="selected">%s</option>',
						$entry,
						$entry);
					$value = substr($value, strlen($entry));
					$haveValue = true;
				}
				else
				{
					$preOptions .= sprintf('<option value="%s">%s</option>',
						$entry,
						$entry);
				}
			}
		}

		return(sprintf('<table width="%s" cellspacing="0" cellpadding="0">'
					.		'<tr>'
					.			'<td width="1" nowrap="nowrap"><nobr>(<select name="%s_pre" id="%s_pre">%s</select>)&nbsp;</nobr></td>'
					.			'<td><input type="text" name="%s_no" id="%s_no" style="width:100%%;" value="%s" /></td>'
					.		'</tr>'
					.	'</table>',
			$size,
			$name, $name,
			$preOptions,
			$name, $name,
			$value));
	}
	else
	{
		return(sprintf('<input type="text" name="%s" id="%s" style="width:%s;" value="%s" />',
			$name,
			$name,
			$size,
			HTMLFormat($value)));
	}
}
