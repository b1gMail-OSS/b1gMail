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
AdminRequirePrivilege('prefs.countries');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'countries';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['countries'],
		'relIcon'	=> 'country32.png',
		'link'		=> 'prefs.countries.php?',
		'active'	=> $_REQUEST['action'] == 'countries'
	)
);

/**
 * fields
 */
if($_REQUEST['action'] == 'countries')
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
			$db->Query('INSERT INTO {pre}staaten(land) VALUES(?)',
				HTMLFormat($_REQUEST['land']));
			$cacheManager->Delete('countryList');
			$cacheManager->Delete('countryListWithDetails');
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}staaten WHERE id=?',
				(int)$_REQUEST['delete']);
			$cacheManager->Delete('countryList');
			$cacheManager->Delete('countryListWithDetails');
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get country IDs
			$countryIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 8) == 'country_')
					$countryIDs[] = (int)substr($key, 8);

			if(count($countryIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}staaten WHERE id IN(' . implode(',', $countryIDs) . ')');
				}

				$cacheManager->Delete('countryList');
				$cacheManager->Delete('countryListWithDetails');
			}
		}

		// fetch
		$countries = array();
		$res = $db->Query('SELECT id,land,is_eu,vat FROM {pre}staaten ORDER BY id ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$countries[$row['id']] = array(
				'id'		=> $row['id'],
				'land'		=> $row['land'],
				'is_eu'		=> $row['is_eu'] == 'yes',
				'vat'		=> $row['vat'] ? sprintf('%.02f', $row['vat']) : '',
				'plzDB'		=> file_exists(B1GMAIL_REL . 'plz/' . $row['id'] . '.plz')
			);
		$res->Free();

		// assign
		$tpl->assign('countries', $countries);
		$tpl->assign('page', 'prefs.countries.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}staaten SET land=?,is_eu=?,vat=? WHERE id=?',
				HTMLFormat($_REQUEST['land']),
				isset($_REQUEST['is_eu']) ? 'yes' : 'no',
				min(100, max(0, (double)str_replace(',', '.', $_REQUEST['vat']))),
				(int)$_REQUEST['id']);
			$cacheManager->Delete('countryList');
			$cacheManager->Delete('countryListWithDetails');
			header('Location: prefs.countries.php?sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,land,is_eu,vat FROM {pre}staaten WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$country = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('country', $country);
		$tpl->assign('page', 'prefs.countries.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['countries']);
$tpl->display('page.tpl');
