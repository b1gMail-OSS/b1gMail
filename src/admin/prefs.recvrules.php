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
AdminRequirePrivilege('prefs.recvrules');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'recvrules';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['recvrules'],
		'relIcon'	=> 'rule32.png',
		'link'		=> 'prefs.recvrules.php?',
		'active'	=> $_REQUEST['action'] == 'recvrules'
	)/*,
	1 => array(
		'title'		=> $lang_admin['autodetection'],
		'link'		=> 'prefs.recvrules.php?action=autodetection&',
		'active'	=> $_REQUEST['action'] == 'autodetection'
	)*/
);

/**
 * receive rules
 */
if($_REQUEST['action'] == 'recvrules')
{
	if(!isset($_REQUEST['do']))
		$_REQUEST['do'] = 'list';

	//
	// list
	//
	if($_REQUEST['do'] == 'list')
	{
		// add?
		if(isset($_REQUEST['add']))
		{
			$db->Query('INSERT INTO {pre}recvrules(field,expression,action,value,type) VALUES(?,?,?,?,?)',
				$_REQUEST['field'],
				$_REQUEST['expression'],
				(int)$_REQUEST['ruleAction'],
				(int)$_REQUEST['value'],
				(int)$_REQUEST['type']);
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}recvrules WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// import
		if(isset($_REQUEST['import']))
		{
			if(isset($_FILES['rulefile'])
				&& $_FILES['rulefile']['error'] == 0
				&& $_FILES['rulefile']['size'] > 5)
			{
				// request temp file
				$tempFileID = RequestTempFile(0);
				$tempFileName = TempFileName($tempFileID);

				// move uploaded file to temp file
				if(move_uploaded_file($_FILES['rulefile']['tmp_name'], $tempFileName))
				{
					// read file
					$fp = fopen($tempFileName, 'rb');
					$importData = fread($fp, filesize($tempFileName));
					fclose($fp);

					// try to unserialize
					$importArray = @unserialize($importData);

					// check format
					if(is_array($importArray)
						&& isset($importArray['type'])
						&& $importArray['type'] = 'b1gMailRuleFile'
						&& count($importArray['data']) > 0)
					{
						// import
						foreach($importArray['data'] as $rule)
						{
							$db->Query('INSERT INTO {pre}recvrules(field,expression,action,value,type) VALUES(?,?,?,?,?)',
								$rule['field'],
								$rule['expression'],
								$rule['action'],
								$rule['value'],
								$rule['type']);
						}
					}
				}

				// release temp file
				ReleaseTempFile(0, $tempFileID);
			}
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get rule IDs
			$ruleIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 5) == 'rule_')
					$ruleIDs[] = (int)substr($key, 5);

			if(count($ruleIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}recvrules WHERE id IN(' . implode(',', $ruleIDs) . ')');
				}
				else if($_REQUEST['massAction'] == 'export')
				{
					// get rows
					$exportArray = array();
					$res = $db->Query('SELECT field,expression,action,value,type FROM {pre}recvrules WHERE id IN(' . implode(',', $ruleIDs) . ') ORDER BY id ASC');
					while($row = $res->FetchArray(MYSQLI_ASSOC))
						$exportArray[] = $row;
					$res->Free();

					// export as file
					$exportData = serialize(array('type' => 'b1gMailRuleFile', 'data' => $exportArray));
					header('Pragma: public');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=rules.bmrecvrules');
					header('Content-Length: ' . strlen($exportData));
					echo($exportData);
					exit();
				}
			}
		}

		// retrieve rules
		$rules = array();
		$res = $db->Query('SELECT id,field,expression,action,value,type FROM {pre}recvrules ORDER BY type,action,id ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$rules[$row['id']] = array(
				'id'			=> $row['id'],
				'field'			=> $row['field'],
				'expression'	=> $row['expression'],
				'action'		=> $ruleActionTable[$row['action']],
				'value'			=> $row['value'],
				'type'			=> $ruleTypeTable[$row['type']]
			);
		}
		$res->Free();

		// assign
		$tpl->assign('ruleActionTable', $ruleActionTable);
		$tpl->assign('ruleTypeTable', $ruleTypeTable);
		$tpl->assign('rules', $rules);
		$tpl->assign('page', 'prefs.recvrules.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			$db->Query('UPDATE {pre}recvrules SET field=?, expression=?, action=?, value=?, type=? WHERE id=?',
				$_REQUEST['field'],
				$_REQUEST['expression'],
				(int)$_REQUEST['ruleAction'],
				(int)$_REQUEST['value'],
				(int)$_REQUEST['type'],
				(int)$_REQUEST['id']);
			header('Location: prefs.recvrules.php?sid=' . session_id());
			exit();
		}

		// get rule data
		$res = $db->Query('SELECT id,field,expression,action,value,type FROM {pre}recvrules WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$rule = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// assign
		$tpl->assign('rule', $rule);
		$tpl->assign('ruleActionTable', $ruleActionTable);
		$tpl->assign('ruleTypeTable', $ruleTypeTable);
		$tpl->assign('page', 'prefs.recvrules.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['recvrules']);
$tpl->display('page.tpl');
?>