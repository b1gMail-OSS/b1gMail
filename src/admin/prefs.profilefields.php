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
AdminRequirePrivilege('prefs.profilefields');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'fields';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['profilefields'],
		'relIcon'	=> 'field32.png',
		'link'		=> 'prefs.profilefields.php?',
		'active'	=> $_REQUEST['action'] == 'fields'
	)
);

/**
 * fields
 */
if($_REQUEST['action'] == 'fields')
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
			$db->Query('INSERT INTO {pre}profilfelder(feld,typ,pflicht,rule,extra,show_signup,show_li) VALUES(?,?,?,?,?,?,?)',
				$_REQUEST['feld'],
				(int)$_REQUEST['typ'],
				isset($_REQUEST['pflicht']) ? 'yes' : 'no',
				$_REQUEST['rule'],
				$_REQUEST['extra'],
				isset($_REQUEST['show_signup']) ? 'yes' : 'no',
				isset($_REQUEST['show_li']) ? 'yes' : 'no');
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}profilfelder WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get field IDs
			$fieldIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 6) == 'field_')
					$fieldIDs[] = (int)substr($key, 6);

			if(count($fieldIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}profilfelder WHERE id IN(' . implode(',', $fieldIDs) . ')');
				}
			}
		}

		// fetch
		$fields = array();
		$res = $db->Query('SELECT id,feld,typ,pflicht,rule,extra FROM {pre}profilfelder ORDER BY id ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$fields[$row['id']] = array(
				'id'		=> $row['id'],
				'feld'		=> $row['feld'],
				'typ'		=> $fieldTypeTable[$row['typ']],
				'pflicht'	=> $row['pflicht'] == 'yes',
				'rule'		=> $row['rule'],
				'extra'		=> $row['extra']
			);
		$res->Free();

		// assign
		$tpl->assign('fields', $fields);
		$tpl->assign('fieldTypeTable', $fieldTypeTable);
		$tpl->assign('page', 'prefs.profilefields.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}profilfelder SET feld=?,typ=?,pflicht=?,rule=?,extra=?,show_signup=?,show_li=? WHERE id=?',
				$_REQUEST['feld'],
				(int)$_REQUEST['typ'],
				isset($_REQUEST['pflicht']) ? 'yes' : 'no',
				$_REQUEST['rule'],
				$_REQUEST['extra'],
				isset($_REQUEST['show_signup']) ? 'yes' : 'no',
				isset($_REQUEST['show_li']) ? 'yes' : 'no',
				(int)$_REQUEST['id']);
			header('Location: prefs.profilefields.php?sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,feld,typ,pflicht,rule,extra,show_signup,show_li FROM {pre}profilfelder WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$field = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('field', $field);
		$tpl->assign('fieldTypeTable', $fieldTypeTable);
		$tpl->assign('page', 'prefs.profilefields.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['profilefields']);
$tpl->display('page.tpl');
?>