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

// Support malformed legacy URLs like /admin/plugin/addalias&action=page2
if (isset($_REQUEST['plugin']) && is_string($_REQUEST['plugin']) && strpos($_REQUEST['plugin'], '&') !== false) {
    $pluginParts = explode('&', $_REQUEST['plugin'], 2);
    $_REQUEST['plugin'] = $pluginParts[0];
    if (isset($pluginParts[1]) && $pluginParts[1] !== '') {
        parse_str($pluginParts[1], $pluginParams);
        if (is_array($pluginParams)) {
            foreach ($pluginParams as $k => $v) {
                if (!isset($_REQUEST[$k])) {
                    $_REQUEST[$k] = $v;
                }
            }
        }
    }
}

$pluginSlug = isset($_REQUEST['plugin']) ? trim((string) $_REQUEST['plugin']) : '';
$pluginModule = $pluginSlug !== '' ? $plugins->resolveActivePluginModule($pluginSlug) : '';
if ($pluginModule !== '') {
    $_REQUEST['plugin'] = $pluginModule;
}

if ($pluginModule === '' || !$plugins->getParam('admin_pages', $pluginModule)) {
    DisplayError(0x14, 'Invalid plugin page call',
        'The requested plugin cannot be found, is not active, or does not support plugin pages.',
        $pluginSlug !== '' ? sprintf("Plugin:\n%s", $pluginSlug) : '',
        __FILE__,
        __LINE__);
    die();
}

if (!($adminRow['type'] == 0 || (isset($adminRow['privileges']['plugins']) && isset($adminRow['privileges']['plugins'][$pluginModule])))) {
    DisplayError(0x02, 'Unauthorized', 'You are not authrized to view or change this dataset or page. Possible reasons are too few permissions or an expired session.',
        sprintf("Requested privileges:\n%s",
            $priv),
        __FILE__,
        __LINE__);
    exit();
}

$plugins->callFunction('AdminHandler', $pluginModule);

$page = $tpl->getTemplateVars('page');
if ($page === false || $page === null || (is_string($page) && trim($page) === '')) {
    DisplayError(0x14, 'Invalid plugin page call',
        'The plugin did not provide a template for this page.',
        sprintf("Plugin:\n%s", $pluginModule),
        __FILE__,
        __LINE__);
    die();
}

$tpl->assign('title', $lang_admin['plugins']
    . ' &raquo; '
    . $plugins->getParam('admin_page_title', $pluginModule));
$tpl->display('page.tpl');
