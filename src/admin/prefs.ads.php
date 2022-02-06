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
AdminRequirePrivilege('prefs.ads');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'banners';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['banners'],
		'relIcon'	=> 'ad32.png',
		'link'		=> 'prefs.ads.php?',
		'active'	=> $_REQUEST['action'] == 'banners'
	)
);

/**
 * banners
 */
if($_REQUEST['action'] == 'banners')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// add
		if(isset($_REQUEST['add']))
		{
			$db->Query('INSERT INTO {pre}ads(code,weight,paused,category,`comments`) VALUES(?,?,?,?,?)',
				$_REQUEST['code'],
				max(1, min($_REQUEST['weight'], 100)),
				isset($_REQUEST['paused']) ? 'yes' : 'no',
				$_REQUEST['category'],
				$_REQUEST['comments']);
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}ads WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// reset stats
		if(isset($_REQUEST['reset']))
		{
			$db->Query('UPDATE {pre}ads SET `views`=0 WHERE id=?',
				(int)$_REQUEST['reset']);
		}

		// activate
		if(isset($_REQUEST['activate']))
		{
			$db->Query('UPDATE {pre}ads SET paused=? WHERE id=?',
				'no',
				(int)$_REQUEST['activate']);
		}

		// deactivate
		if(isset($_REQUEST['deactivate']))
		{
			$db->Query('UPDATE {pre}ads SET paused=? WHERE id=?',
				'yes',
				(int)$_REQUEST['deactivate']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get ad IDs
			$adIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 3) == 'ad_')
					$adIDs[] = (int)substr($key, 3);

			if(count($adIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}ads WHERE id IN(' . implode(',', $adIDs) . ')');
				}
				else if($_REQUEST['massAction'] == 'pause')
				{
					$db->Query('UPDATE {pre}ads SET paused=? WHERE id IN(' . implode(',', $adIDs) . ')',
						'yes');
				}
				else if($_REQUEST['massAction'] == 'continue')
				{
					$db->Query('UPDATE {pre}ads SET paused=? WHERE id IN(' . implode(',', $adIDs) . ')',
						'no');
				}
			}
		}

		// fetch
		$ads = array();
		$res = $db->Query('SELECT id,code,views,paused,weight,category FROM {pre}ads ORDER BY paused DESC, views DESC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['paused'] = $row['paused'] == 'yes';
			$ads[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('ads', $ads);
		$tpl->assign('page', 'prefs.ads.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}ads SET code=?, weight=?, paused=?, category=?, `comments`=? WHERE id=?',
				$_REQUEST['code'],
				max(1, min($_REQUEST['weight'], 100)),
				isset($_REQUEST['paused']) ? 'yes' : 'no',
				$_REQUEST['category'],
				$_REQUEST['comments'],
				(int)$_REQUEST['id']);
			header('Location: prefs.ads.php?sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,code,paused,weight,category,comments FROM {pre}ads WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$ad = $res->FetchArray(MYSQLI_ASSOC);
		$ad['paused'] = $ad['paused'] == 'yes';
		$res->Free();

		// assign
		$tpl->assign('ad', $ad);
		$tpl->assign('page', 'prefs.ads.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['ads']);
$tpl->display('page.tpl');
?>