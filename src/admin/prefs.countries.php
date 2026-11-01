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
 * Parse a VAT rate from form input. Accepts 8.1 and 8,1.
 *
 * @param mixed $vat
 * @return float
 */
function ParseCountryVat($vat)
{
	$vat = trim(str_replace(' ', '', (string)$vat));
	if(strpos($vat, ',') !== false && strpos($vat, '.') !== false)
		$vat = str_replace('.', '', $vat);
	$vat = str_replace(',', '.', $vat);
	return min(100, max(0, (float)$vat));
}

/**
 * VAT rate for display. One decimal separator, trailing zeros removed.
 *
 * @param mixed $vat
 * @param bool $emptyIfZero
 * @param string $decimal
 * @return string
 */
function FormatCountryVat($vat, $emptyIfZero = true, $decimal = ',')
{
	$value = ParseCountryVat($vat);
	if($emptyIfZero && $value <= 0)
		return('');
	$formatted = number_format($value, 2, '.', '');
	$formatted = rtrim(rtrim($formatted, '0'), '.');
	if($formatted === '')
		$formatted = '0';
	if($decimal !== '.')
		$formatted = str_replace('.', $decimal, $formatted);
	return($formatted);
}

/**
 * VAT rate for SQL. Always a dot, independent of the process locale.
 *
 * @param float $vat
 * @return string
 */
function CountryVatSql($vat)
{
	return number_format((float)$vat, 2, '.', '');
}

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
				'vat'		=> FormatCountryVat($row['vat']),
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
			$vatInput = isset($_POST['vat']) ? (string)$_POST['vat'] : '0';
			$db->Query('UPDATE {pre}staaten SET land=?,is_eu=?,vat=? WHERE id=?',
				HTMLFormat($_REQUEST['land']),
				isset($_REQUEST['is_eu']) ? 'yes' : 'no',
				CountryVatSql(ParseCountryVat($vatInput)),
				(int)$_REQUEST['id']);
			$cacheManager->Delete('countryList');
			$cacheManager->Delete('countryListWithDetails');
			SessionRedirect('prefs.countries.php');
		}

		// fetch
		$res = $db->Query('SELECT id,land,is_eu,vat FROM {pre}staaten WHERE id=?',
			(int)$_REQUEST['id']);
		assert('$res->RowCount() != 0');
		$country = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();
		$country['vat'] = FormatCountryVat($country['vat'], false);

		// assign
		$tpl->assign('country', $country);
		$tpl->assign('page', 'prefs.countries.edit.tpl');
	}
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['prefs'] . ' &raquo; ' . $lang_admin['countries']);
$tpl->display('page.tpl');
