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

if(!isset($_REQUEST['plugin']) || !isset($plugins->_plugins[$_REQUEST['plugin']])
	|| !$plugins->getParam('admin_pages', $_REQUEST['plugin']))
{
	DisplayError(0x14, 'Invalid plugin page call',
		'The requested plugin cannot be found or does not support plugin pages.',
		isset($_REQUEST['plugin']) ? sprintf("Plugin:\n%s", $_REQUEST['plugin']) : '',
		__FILE__,
		__LINE__);
	die();
}

if(!($adminRow['type']==0 || (isset($adminRow['privileges']['plugins']) && isset($adminRow['privileges']['plugins'][$_REQUEST['plugin']]))))
{
	DisplayError(0x02, 'Unauthorized', 'You are not authrized to view or change this dataset or page. Possible reasons are too few permissions or an expired session.',
			sprintf("Requested privileges:\n%s",
			$priv),
		__FILE__,
		__LINE__);
	exit();
}

$plugins->callFunction('AdminHandler', $_REQUEST['plugin']);

$tpl->assign('title', $lang_admin['plugins']
						. ' &raquo; '
						. $plugins->getParam('admin_page_title', $_REQUEST['plugin']));
$tpl->display('page.tpl');
?>