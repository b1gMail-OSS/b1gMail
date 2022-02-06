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

include('../serverlib/admin.inc.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('pluginsadmin');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'plugins';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['plugins'],
		'relIcon'	=> 'plugin32.png',
		'link'		=> 'plugins.php?',
		'active'	=> $_REQUEST['action'] == 'plugins'
	),
	1 => array(
		'title'		=> $lang_admin['widgets'],
		'relIcon'	=> 'wlayout_add32.png',
		'link'		=> 'plugins.php?action=widgets&',
		'active'	=> $_REQUEST['action'] == 'widgets'
	),
	2 => array(
		'title'		=> $lang_admin['updates'],
		'relIcon'	=> 'updates.png',
		'link'		=> 'plugins.php?action=updates&',
		'active'	=> $_REQUEST['action'] == 'updates'
	),
	3 => array(
		'title'		=> $lang_admin['install'],
		'relIcon'	=> 'plugin_add.png',
		'link'		=> 'plugins.php?action=install&',
		'active'	=> $_REQUEST['action'] == 'install'
	)
);

/**
 * plugins/widgets
 */
if($_REQUEST['action'] == 'plugins'
	|| $_REQUEST['action'] == 'widgets')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'activatePlugin'
		&& isset($_REQUEST['plugin']) && isset($plugins->_inactivePlugins[$_REQUEST['plugin']]))
	{
		$plugins->activatePlugin($_REQUEST['plugin']);
		$tpl->assign('reloadMenu', true);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deactivatePlugin'
		&& isset($_REQUEST['plugin']) && isset($plugins->_plugins[$_REQUEST['plugin']]))
	{
		$plugins->deactivatePlugin($_REQUEST['plugin']);
		$tpl->assign('reloadMenu', true);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'pausePlugin'
		&& isset($_REQUEST['plugin']) && isset($plugins->_plugins[$_REQUEST['plugin']]))
	{
		$plugins->pausePlugin($_REQUEST['plugin']);
		$tpl->assign('reloadMenu', true);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'unpausePlugin'
		&& isset($_REQUEST['plugin']) && isset($plugins->_inactivePlugins[$_REQUEST['plugin']]))
	{
		$plugins->unpausePlugin($_REQUEST['plugin']);
		$tpl->assign('reloadMenu', true);
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deletePackage'
		&& isset($_REQUEST['package']) && strlen($_REQUEST['package']) == 32)
	{
		BMPluginPackage::Uninstall($_REQUEST['package']);
		$tpl->assign('reloadMenu', true);
	}

	$pluginList = array();

	// build plugin list
	foreach($plugins->_plugins as $className=>$pluginInfo)
	{
		if(($_REQUEST['action'] == 'plugins' && ($plugins->getParam('type', $className) == BMPLUGIN_DEFAULT
													|| $plugins->getParam('type', $className) == BMPLUGIN_FILTER))
			|| ($_REQUEST['action'] == 'widgets' && $plugins->getParam('type', $className) == BMPLUGIN_WIDGET))
		{
			if(!isset($pluginList[$pluginInfo['signature']]))
				$pluginList[$pluginInfo['signature']] = array(
					'name'		=> $pluginInfo['packageName'],
					'plugins'	=> array()
				);
			$pluginList[$pluginInfo['signature']]['plugins'][] = array(
				'name'		=> $className,
				'title'		=> $plugins->getParam('name', $className),
				'version'	=> $plugins->getParam('version', $className),
				'author'	=> $plugins->getParam('author', $className),
				'type'		=> $pluginTypeTable[$plugins->getParam('type', $className)],
				'installed'	=> $plugins->getParam('installed', $className),
				'paused'	=> $plugins->getParam('paused', $className)
			);
		}
	}
	foreach($plugins->_inactivePlugins as $className=>$pluginInfo)
	{
		if(($_REQUEST['action'] == 'plugins' && ($pluginInfo['type'] == BMPLUGIN_DEFAULT
													|| $pluginInfo['type'] == BMPLUGIN_FILTER))
			|| ($_REQUEST['action'] == 'widgets' && $pluginInfo['type'] == BMPLUGIN_WIDGET))
		{
			if(!isset($pluginList[$pluginInfo['signature']]))
				$pluginList[$pluginInfo['signature']] = array(
					'name'		=> $pluginInfo['packageName'],
					'plugins'	=> array()
				);
			$pluginList[$pluginInfo['signature']]['plugins'][] = array(
				'name'		=> $className,
				'title'		=> $pluginInfo['name'],
				'version'	=> $pluginInfo['version'],
				'author'	=> $pluginInfo['author'],
				'type'		=> $pluginTypeTable[$pluginInfo['type']],
				'installed'	=> $pluginInfo['installed'],
				'paused'	=> $pluginInfo['paused']
			);
		}
	}

	function __PluginSort($a, $b)
	{
		return(strcasecmp(($a['installed'] ? '0' : '1') . $a['title'], ($b['installed'] ? '0' : '1') . $b['title']));
	}

	function __PluginListSort($a, $b)
	{
		return(strcasecmp($a['name'], $b['name']));
	}

	foreach($pluginList as $key=>$val)
		uasort($pluginList[$key]['plugins'], '__PluginSort');

	uasort($pluginList, '__PluginListSort');


	$tpl->assign('action', $_REQUEST['action']);
	$tpl->assign('plugins', $pluginList);
	$tpl->assign('page', 'plugins.list.tpl');
}

/**
 * update check page
 */
else if($_REQUEST['action'] == 'updates')
{
	$pluginList = array();

	// build plugin list
	foreach($plugins->_plugins as $className=>$pluginInfo)
	{
		if(!isset($pluginList[$pluginInfo['signature']]))
			$pluginList[$pluginInfo['signature']] = array(
				'name'		=> $pluginInfo['packageName'],
				'plugins'	=> array()
			);
		$pluginList[$pluginInfo['signature']]['plugins'][] = array(
			'name'		=> $className,
			'title'		=> $plugins->getParam('name', $className),
			'version'	=> $plugins->getParam('version', $className),
			'author'	=> $plugins->getParam('author', $className),
			'website'	=> $plugins->getParam('website', $className),
			'type'		=> $pluginTypeTable[$plugins->getParam('type', $className)],
			'installed'	=> true
		);
	}

	$tpl->assign('plugins', $pluginList);
	$tpl->assign('page', 'plugins.updates.tpl');
}

/**
 * perform update check
 */
else if($_REQUEST['action'] == 'updateCheck'
	&& isset($_REQUEST['plugin']))
{
	$latestVersion = '';
	$resultCode = $plugins->callFunction('CheckForUpdates', $_REQUEST['plugin'], false, array(&$latestVersion));

	printf('%s;%d;%s;%s',
		$_REQUEST['plugin'],
		$resultCode,
		$latestVersion,
		$plugins->getParam('website', $_REQUEST['plugin']));
	exit();
}

/**
 * install
 */
else if($_REQUEST['action'] == 'install')
{
	//
	// form
	//
	if(!isset($_REQUEST['do']))
	{
		$tpl->assign('page', 'plugins.install.tpl');
	}

	//
	// upload
	//
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'uploadPlugin')
	{
		$formatError = true;

		if(isset($_FILES['package']) && $_FILES['package']['error'] == 0
			&& $_FILES['package']['size'] > 0)
		{
			// request temp file
			$tempFileID = RequestTempFile(0);
			$tempFileName = TempFileName($tempFileID);

			// move file
			$fileName = $_FILES['package']['name'];
			move_uploaded_file($_FILES['package']['tmp_name'], $tempFileName);

			// open file
			$package = _new('BMPluginPackage', array($fp = fopen($tempFileName, 'rb')));
			if($package->ParseFile())
			{
				$meta = $package->metaInfo;

				foreach($meta as $key=>$val)
					if(is_string($val))
						$meta[$key] = CharsetDecode($val, FALLBACK_CHARSET);

				$formatError = false;
				$tpl->assign('id', 				$tempFileID);
				$tpl->assign('meta', 			$meta);
				$tpl->assign('signature', 		$package->signature);
				$tpl->assign('versionsMatch', 	$package->metaInfo['for_b1gmail'] == B1GMAIL_VERSION);
				$tpl->assign('b1gmailVersion',	B1GMAIL_VERSION);
				$tpl->assign('page', 			'plugin.install.info.tpl');
			}

			// close file
			fclose($fp);
		}

		// invalid file => message
		if($formatError)
		{
			$tpl->assign('msgTitle', $lang_admin['install']);
			$tpl->assign('msgText', $lang_admin['plugin_formaterr']);
			$tpl->assign('msgIcon', 'error32');
			$tpl->assign('backLink', 'plugins.php?action=install&');
			$tpl->assign('page', 'msg.tpl');
		}
	}

	//
	// check signature
	//
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'checkSignature'
		&& isset($_REQUEST['signature']) && strlen($_REQUEST['signature']) == 32)
	{
		$result = BMPluginPackage::VerifySignature($_REQUEST['signature']);

		if($result === false)
		{
			$tpl->assign('icon', 	'error32');
			$tpl->assign('title', 	$lang_admin['sigfailed']);
			$tpl->assign('text', 	$lang_admin['sigfailed_desc']);
		}
		else if($result == SIGNATURE_OFFICIAL)
		{
			$tpl->assign('icon', 	'sig_ok');
			$tpl->assign('title', 	$lang_admin['sigofficial']);
			$tpl->assign('text', 	$lang_admin['sigofficial_desc']);
		}
		else if($result == SIGNATURE_VERIFIED)
		{
			$tpl->assign('icon', 	'sig');
			$tpl->assign('title', 	$lang_admin['sigver']);
			$tpl->assign('text', 	$lang_admin['sigver_desc']);
		}
		else if($result == SIGNATURE_UNKNOWN)
		{
			$tpl->assign('icon', 	'sig_unknown');
			$tpl->assign('title', 	$lang_admin['sigunknown']);
			$tpl->assign('text', 	$lang_admin['sigunknown_desc']);
		}
		else if($result == SIGNATURE_MALICIOUS)
		{
			$tpl->assign('icon', 	'sig_mal');
			$tpl->assign('title', 	$lang_admin['sigmal']);
			$tpl->assign('text', 	$lang_admin['sigmal_desc']);
		}

		$tpl->display('plugin.install.signature.tpl');
		exit();
	}

	//
	// install
	//
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'installPlugin'
		&& isset($_REQUEST['id']) && ValidTempFile(0, (int)$_REQUEST['id']))
	{
		if(isset($_REQUEST['step']))
			$step = max(1, min(2, (int)$_REQUEST['step']));
		else
			$step = 1;

		$id = (int)$_REQUEST['id'];
		$tempFileName = TempFileName($id);

		// open file
		$package = _new('BMPluginPackage', array($fp = fopen($tempFileName, 'rb')));
		if($package->ParseFile())
		{
			if($step == 1)
			{
				if($package->InstallStep1())
				{
					$url = sprintf('plugins.php?action=install&do=installPlugin&id=%d&step=2&sid=%s',
						$id,
						session_id());
					header('Location: ' . $url);
					fclose($fp);
					exit();
				}
				else
				{
					$tpl->assign('msgTitle', $lang_admin['install']);
					$tpl->assign('msgText', $lang_admin['plugin_insterr']);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('backLink', 'plugins.php?action=install&');
					$tpl->assign('page', 'msg.tpl');
					fclose($fp);
					ReleaseTempFile(0, $id);
				}
			}

			else if($step == 2)
			{
				if($package->InstallStep2())
				{
					$tpl->assign('reloadMenu', true);
					$tpl->assign('msgTitle', $lang_admin['install']);
					$tpl->assign('msgText', $lang_admin['plugin_installed']);
					$tpl->assign('msgIcon', 'info32');
					$tpl->assign('backLink', 'plugins.php?');
					$tpl->assign('page', 'msg.tpl');
				}
				else
				{
					$tpl->assign('msgTitle', $lang_admin['install']);
					$tpl->assign('msgText', $lang_admin['plugin_insterr']);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('backLink', 'plugins.php?action=install&');
					$tpl->assign('page', 'msg.tpl');
				}

				// close and release file
				fclose($fp);
				ReleaseTempFile(0, $id);
			}
		}
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['plugins'] . ' &raquo; ' . $lang_admin['plugins']);
$tpl->display('page.tpl');
?>