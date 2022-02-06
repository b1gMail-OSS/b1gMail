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
AdminRequirePrivilege('prefs.coupons');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'coupons';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['coupons'],
		'relIcon'	=> 'coupon32.png',
		'link'		=> 'prefs.coupons.php?',
		'active'	=> $_REQUEST['action'] == 'coupons'
	)
);

/**
 * coupons
 */
if($_REQUEST['action'] == 'coupons')
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
			// params
			$count = (int)$_REQUEST['anzahl'];
			$from = isset($_REQUEST['von_unlim']) ? -1 : SmartyDateTime('von');
			$to = isset($_REQUEST['bis_unlim']) ? -1 : SmartyDateTime('bis');

			// benefits
			$benefits = array();
			$benefits['sms'] = isset($_REQUEST['ver_credits']) ? (int)$_REQUEST['ver_credits_count'] : 0;
			$benefits['gruppe'] = isset($_REQUEST['ver_gruppe']) ? (int)$_REQUEST['ver_gruppe_id'] : 0;

			// add coupon codes
			$codes = explode("\n", trim($_REQUEST['code']));
			foreach($codes as $code)
				if(($code = trim($code)) != '')
					$db->Query('INSERT INTO {pre}codes(code,von,bis,anzahl,ver,valid_signup,valid_loggedin) VALUES(?,?,?,?,?,?,?)',
						$code,
						$from,
						$to,
						$count,
						serialize($benefits),
						isset($_REQUEST['valid_signup']) ? 'yes' : 'no',
						isset($_REQUEST['valid_loggedin']) ? 'yes' : 'no');
		}

		// delete
		if(isset($_REQUEST['delete']))
		{
			$db->Query('DELETE FROM {pre}codes WHERE id=?',
				(int)$_REQUEST['delete']);
		}

		// mass action
		if(isset($_REQUEST['executeMassAction']))
		{
			// get coupon IDs
			$couponIDs = array();
			foreach($_POST as $key=>$val)
				if(substr($key, 0, 7) == 'coupon_')
					$couponIDs[] = (int)substr($key, 7);

			if(count($couponIDs) > 0)
			{
				if($_REQUEST['massAction'] == 'delete')
				{
					// delete row
					$db->Query('DELETE FROM {pre}codes WHERE id IN(' . implode(',', $couponIDs) . ')');
				}
			}
		}

		// fetch
		$coupons = array();
		$res = $db->Query('SELECT id,code,von,bis,anzahl,ver,used,valid_loggedin,valid_signup FROM {pre}codes ORDER BY id ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['ver'] = unserialize($row['ver']);
			$row['valid_loggedin'] = $row['valid_loggedin'] == 'yes';
			$row['valid_signup'] = $row['valid_signup'] == 'yes';
			$coupons[$row['id']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('coupons', $coupons);
		$tpl->assign('groups', BMGroup::GetSimpleGroupList());
		$tpl->assign('page', 'prefs.coupons.tpl');
	}

	//
	// edit
	//
	else if($_REQUEST['do'] == 'edit')
	{
		// save?
		if(isset($_REQUEST['save']))
		{
			// params
			$count = (int)$_REQUEST['anzahl'];
			$from = isset($_REQUEST['von_unlim']) ? -1 : SmartyDateTime('von');
			$to = isset($_REQUEST['bis_unlim']) ? -1 : SmartyDateTime('bis');

			// benefits
			$benefits = array();
			$benefits['sms'] = isset($_REQUEST['ver_credits']) ? (int)$_REQUEST['ver_credits_count'] : 0;
			$benefits['gruppe'] = isset($_REQUEST['ver_gruppe']) ? (int)$_REQUEST['ver_gruppe_id'] : 0;

			// save
			$db->Query('UPDATE {pre}codes SET code=?,von=?,bis=?,anzahl=?,ver=?,valid_loggedin=?,valid_signup=? WHERE id=?',
				$_REQUEST['code'],
				$from,
				$to,
				$count,
				serialize($benefits),
				isset($_REQUEST['valid_loggedin']) ? 'yes' : 'no',
				isset($_REQUEST['valid_signup']) ? 'yes' : 'no',
				$_REQUEST['id']);
			header('Location: prefs.coupons.php?sid=' . session_id());
			exit();
		}

		// fetch
		$res = $db->Query('SELECT id,code,von,bis,anzahl,ver,usedby,valid_signup,valid_loggedin FROM {pre}codes WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$coupon = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		// used by...
		$usedBy = array();
		$usedByIDs = @unserialize($coupon['usedby']);
		if(!is_array($usedByIDs))
			$usedByIDs = array();
		if(count($usedByIDs) > 0)
		{
			$res = $db->Query('SELECT id,email,vorname,nachname,strasse,hnr,plz,ort FROM {pre}users WHERE id IN(' . implode(',', $usedByIDs) . ') ORDER BY id ASC');
			while($row = $res->FetchArray())
			{
				$aliases = array();
				$aliasRes = $db->Query('SELECT email FROM {pre}aliase WHERE type=? AND user=? ORDER BY email ASC',
					ALIAS_RECIPIENT|ALIAS_SENDER,
					$row['id']);
				while($aliasRow = $aliasRes->FetchArray())
					$aliases[] = $aliasRow['email'];
				$aliasRes->Free();

				$row['aliases'] = count($aliases) > 0
									? implode(', ', $aliases)
									: '';
				$usedBy[] = $row;
			}
			$res->Free();
		}

		// assign
		$coupon['ver'] = unserialize($coupon['ver']);
		$tpl->assign('coupon', $coupon);
		$tpl->assign('usedBy', $usedBy);
		$tpl->assign('groups', BMGroup::GetSimpleGroupList());
		$tpl->assign('page', 'prefs.coupons.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['coupons']);
$tpl->display('page.tpl');
?>