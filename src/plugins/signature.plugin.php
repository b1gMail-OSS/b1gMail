<?php
/*
 * b1gMail signature plugin
 * (c) 2021 Patrick Schlangen et al
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

/**
 * signature plugin
 */
class SignaturePlugin extends BMPlugin
{
    public function __construct()
    {
        $this->type = BMPLUGIN_DEFAULT;
        $this->name = 'Signature plugin';
        $this->author = 'b1gMail Project';
        $this->version = '1.5';
        $this->designedfor = '7.5.0';
        $this->update_url = '';
        $this->website = 'https://www.b1gmail.org/';

        $this->admin_pages = true;
        $this->admin_page_title = 'Signaturen';
        $this->admin_page_icon = 'modsig_sig32.png';
        $this->admin_route_slug = 'signature';
    }

    public function Install()
    {
        $databaseStructure =
              'YToxOntzOjE5OiJibTYwX21vZF9zaWduYXR1cmVzIjthOjI6e3M6NjoiZmllbGRzIjthOjc6e2k'
            . '6MDthOjY6e2k6MDtzOjExOiJzaWduYXR1cmVpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6Mj'
            . 'oiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO'
            . '2E6Njp7aTowO3M6NDoidGV4dCI7aToxO3M6NDoidGV4dCI7aToyO3M6MjoiTk8iO2k6MztzOjA6'
            . 'IiI7aTo0O3M6MDoiIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czo2OiJncm91cHMiO2k6MTt'
            . 'zOjExOiJ2YXJjaGFyKDMyKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiKiI7aT'
            . 'o1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6NDoiaHRtbCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO'
            . '2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDthOjY6'
            . 'e2k6MDtzOjc6ImNvdW50ZXIiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czo'
            . 'wOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NTthOjY6e2k6MDtzOjY6IndlaWdodCI7aT'
            . 'oxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IjEwM'
            . 'CI7aTo1O3M6MDoiIjt9aTo2O2E6Njp7aTowO3M6NjoicGF1c2VkIjtpOjE7czoxMDoidGlueWlu'
            . 'dCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM'
            . '6NzoiaW5kZXhlcyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MTE6InNpZ25hdHVyZW'
            . 'lkIjt9fX19';
        $databaseStructure = unserialize(base64_decode($databaseStructure));

        SyncDBStruct($databaseStructure);

        PutLog(sprintf('%s v%s installed',
                $this->name,
                $this->version),
            PRIO_PLUGIN,
            __FILE__,
            __LINE__);

        return true;
    }

    public function Uninstall()
    {
        return true;
    }

    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang == 'deutsch') {
            $lang_admin['modsig_signatures'] = 'Signaturen';
            $lang_admin['modsig_signature'] = 'Signatur';
            $lang_admin['modsig_add'] = 'Signatur hinzuf&uuml;gen';
            $lang_admin['modsig_used'] = 'Verwendet';
            $lang_admin['modsig_html'] = 'HTML-Code';
        } else {
            $lang_admin['modsig_signatures'] = 'Signatures';
            $lang_admin['modsig_signature'] = 'Signature';
            $lang_admin['modsig_add'] = 'Add signature';
            $lang_admin['modsig_used'] = 'Used';
            $lang_admin['modsig_html'] = 'HTML code';
        }
    }

    public function OnSendMail(&$text, $html)
    {
        global $db, $userRow;

        if (!isset($userRow) || !is_array($userRow)) {
            return;
        }

        $res = $db->Query('SELECT `signatureid`,`text` FROM {pre}mod_signatures '
                . 'WHERE `paused`=0 AND `html`=? AND (`groups`=? OR (`groups`=? OR `groups` LIKE ? OR `groups` LIKE ? OR `groups` LIKE ?)) ORDER BY (counter/weight) ASC LIMIT 1',
                $html ? 1 : 0,
                '*',
                $userRow['gruppe'],
                $userRow['gruppe'] . ',%',
                '%,' . $userRow['gruppe'] . ',%',
                '%,' . $userRow['gruppe']);
        if ($res->RowCount() != 1) {
            return;
        }
        list($signatureID, $signatureText) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        if ($html) {
            $text .= '<br />' . $signatureText;
        } else {
            $text .= "\n" . $signatureText;
        }

        $db->Query('UPDATE {pre}mod_signatures SET `counter`=`counter`+1 WHERE `signatureid`=?',
            $signatureID);
    }

    /**
     * Pretty admin URL for this plugin (ACP routing).
     *
     * @param array<string, mixed> $params
     * @param bool                 $trailingAmp
     * @return string
     */
    protected function _sigAdminUrl(array $params = array(), $trailingAmp = true)
    {
        $params = array_merge(array('plugin' => $this->internal_name), $params);

        if (function_exists('AdminSessionUrl')) {
            return AdminSessionUrl('plugin.page.php', $params, $trailingAmp);
        }

        $url = $this->_adminLink();
        unset($params['plugin']);
        foreach ($params as $key => $val) {
            if ((string) $val === '') {
                continue;
            }
            $url .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $val);
        }
        if ($trailingAmp) {
            $url .= (strpos($url, '?') !== false ? '&' : '?');
        }

        return $url;
    }

    public function AdminHandler()
    {
        global $tpl, $lang_admin;

        $do = $_REQUEST['do'] ?? $_REQUEST['action'] ?? 'list';
        if ($do === '' || $do === 'signatures') {
            $do = 'list';
        }

        $tabs = array(
            0 => array(
                'title' => $lang_admin['modsig_signatures'],
                'link' => $this->_adminLink() . '&do=list&',
                'icon' => '../plugins/templates/images/modsig_sig32.png',
                'active' => $do === 'list' || $do === 'edit',
            ),
        );

        $tpl->assign('groups', BMGroup::GetSimpleGroupList());
        $tpl->assign('tabs', $tabs);
        $tpl->assign('pageURL', $this->_sigAdminUrl(array('do' => 'list')));
        $tpl->assign('sigPlugin', $this->internal_name);
        $tpl->assign('modsigHtmlMode', false);
        $tpl->assign('modsigTextValue', '');
        $tpl->assign('modsigTextFieldTpl', $this->_templatePath('modsig.textfield.tpl'));

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->_sigHandlePost($do);
        } else {
            $this->_sigHandleLegacyGet($do);
        }

        if ($do === 'edit' && isset($_REQUEST['id'])) {
            $this->_sigEditPage();
        } else {
            $this->_sigListPage();
        }
    }

    /**
     * State-changing POST actions (CSRF-protected).
     *
     * @param string $do
     */
    protected function _sigHandlePost($do)
    {
        global $db;

        if ($do === 'edit' && isset($_REQUEST['save'], $_REQUEST['id'])) {
            $db->Query('UPDATE {pre}mod_signatures SET `text`=?,`html`=?,`weight`=?,`groups`=?,`paused`=? WHERE `signatureid`=?',
                $_REQUEST['text'],
                isset($_REQUEST['html']) ? 1 : 0,
                $_REQUEST['weight'],
                is_array($_REQUEST['groups']) ? implode(',', $_REQUEST['groups']) : '*',
                isset($_REQUEST['paused']) ? 1 : 0,
                (int) $_REQUEST['id']);
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }

        if ($do !== 'list') {
            return;
        }

        $op = isset($_REQUEST['op']) ? (string) $_REQUEST['op'] : '';

        if ($op === 'activate' || isset($_REQUEST['activate'])) {
            $id = (int) ($op === 'activate' ? ($_REQUEST['id'] ?? 0) : $_REQUEST['activate']);
            if ($id > 0) {
                $db->Query('UPDATE {pre}mod_signatures SET `paused`=0 WHERE `signatureid`=?', $id);
            }
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }

        if ($op === 'deactivate' || isset($_REQUEST['deactivate'])) {
            $id = (int) ($op === 'deactivate' ? ($_REQUEST['id'] ?? 0) : $_REQUEST['deactivate']);
            if ($id > 0) {
                $db->Query('UPDATE {pre}mod_signatures SET `paused`=1 WHERE `signatureid`=?', $id);
            }
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }

        if ($op === 'delete' || isset($_REQUEST['delete'])) {
            $id = (int) ($op === 'delete' ? ($_REQUEST['id'] ?? 0) : $_REQUEST['delete']);
            if ($id > 0) {
                $db->Query('DELETE FROM {pre}mod_signatures WHERE `signatureid`=?', $id);
            }
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }

        if ($op === 'add' || isset($_REQUEST['add'])) {
            $db->Query('INSERT INTO {pre}mod_signatures(`text`,`html`,`weight`,`groups`,`paused`) VALUES(?,?,?,?,?)',
                $_REQUEST['text'],
                isset($_REQUEST['html']) ? 1 : 0,
                $_REQUEST['weight'],
                is_array($_REQUEST['groups']) ? implode(',', $_REQUEST['groups']) : '*',
                isset($_REQUEST['paused']) ? 1 : 0);
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }

        if (($op === 'massaction' || (isset($_REQUEST['do']) && $_REQUEST['do'] === 'massAction'))
            && isset($_REQUEST['sigs']) && is_array($_REQUEST['sigs'])) {
            $sigs = array_map('intval', $_REQUEST['sigs']);
            $massAction = $_REQUEST['massAction'] ?? '';

            if ($massAction === 'pause') {
                $db->Query('UPDATE {pre}mod_signatures SET `paused`=1 WHERE `signatureid` IN ?', $sigs);
            } elseif ($massAction === 'continue') {
                $db->Query('UPDATE {pre}mod_signatures SET `paused`=0 WHERE `signatureid` IN ?', $sigs);
            } elseif ($massAction === 'delete') {
                $db->Query('DELETE FROM {pre}mod_signatures WHERE `signatureid` IN ?', $sigs);
            }

            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }
    }

    /**
     * Legacy GET mutations (bookmark/old links); redirect after apply.
     *
     * @param string $do
     */
    protected function _sigHandleLegacyGet($do)
    {
        global $db;

        if ($do !== 'list') {
            return;
        }

        $redirect = false;

        if (isset($_REQUEST['activate'])) {
            $db->Query('UPDATE {pre}mod_signatures SET `paused`=0 WHERE `signatureid`=?',
                (int) $_REQUEST['activate']);
            $redirect = true;
        }
        if (isset($_REQUEST['deactivate'])) {
            $db->Query('UPDATE {pre}mod_signatures SET `paused`=1 WHERE `signatureid`=?',
                (int) $_REQUEST['deactivate']);
            $redirect = true;
        }
        if (isset($_REQUEST['delete'])) {
            $db->Query('DELETE FROM {pre}mod_signatures WHERE `signatureid`=?',
                (int) $_REQUEST['delete']);
            $redirect = true;
        }

        if ($redirect) {
            SessionRedirect($this->_sigAdminUrl(array('do' => 'list'), false));
            exit();
        }
    }

    protected function _sigListPage()
    {
        global $tpl, $db;

        $signatures = array();
        $res = $db->Query('SELECT * FROM {pre}mod_signatures ORDER BY `html` ASC, `text` ASC');
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $row['displayText'] = $row['html'] == 1
                ? $row['text']
                : '<span style="font-family:courier;size:11px;">' . nl2br(HTMLFormat($row['text'])) . '</span>';
            $signatures[$row['signatureid']] = $row;
        }
        $res->Free();

        $tpl->assign('signatures', $signatures);
        $tpl->assign('page', $this->_templatePath('modsig.overview.tpl'));
    }

    protected function _sigEditPage()
    {
        global $tpl, $db;

        $res = $db->Query('SELECT * FROM {pre}mod_signatures WHERE `signatureid`=?',
            (int) $_REQUEST['id']);
        if ($res->RowCount() != 1) {
            exit();
        }
        $signature = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();

        $sigGroups = explode(',', $signature['groups']);
        $groupList = BMGroup::GetSimpleGroupList();
        foreach ($groupList as $key => $val) {
            $groupList[$key]['checked'] = in_array($key, $sigGroups);
        }

        $tpl->assign('groups', $groupList);
        $tpl->assign('sig', $signature);
        $tpl->assign('modsigHtmlMode', (int) $signature['html'] === 1);
        $tpl->assign('page', $this->_templatePath('modsig.edit.tpl'));
    }
}

$plugins->registerPlugin('SignaturePlugin');
