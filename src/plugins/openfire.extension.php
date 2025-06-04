<?php
/*
 * b1gMail Openfire Integration plugin
 * (c) Home of the Sebijk.com
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
class modopenfire extends BMPlugin
{
    /*
    * Eigenschaften des Plugins
    */
    public function __construct()
    {
        $this->name					= 'Jabber Openfire-Integration';
		$this->version				= '1.1.4';
		$this->type					= BMPLUGIN_DEFAULT;
        $this->designedfor			= '7.4.2';

		$this->author				= 'Home of the Sebijk.com';
		$this->web					= 'http://www.sebijk.com';
		$this->mail					= 'sebijk@web.de';

		$this->admin_pages			=  true;
		$this->admin_page_title		= 'Openfire-Integration';
		$this->admin_page_icon		= "openfire_icon.png";
    }

    /*
     * installation routine
     */
    public function Install()
    {
        global $db;

        $DatabaseStructure = [
            '{pre}mod_openfire' => [
                'fields' => [
                    ['enableAuth', 'tinyint(1)', 'NO'],
                    ['secretkey', 'varchar(255)', 'NO'],
                    ['domain', 'varchar(255)', 'NO'],
                    ['port', 'int(10)', 'NO'],
                    ['https', 'tinyint(1)', 'NO'],
                ],
                'indexes' => [],
            ],
        ];
        SyncDBStruct($DatabaseStructure);

        $db->Query('REPLACE INTO {pre}mod_openfire (enableAuth, secretkey, domain, port, https) VALUES (?,?,?,?,?);',
            (int) 0,
            'YourSecretKey',
            'localhost',
            (int) 9091,
            (int) 1);

        PutLog('Plugin "'.$this->name.' - '.$this->version.'" wurde erfolgreich installiert.', PRIO_PLUGIN, __FILE__, __LINE__);

        return true;
    }

    /*
     * uninstallation routine
     */
    public function Uninstall()
    {
        global $db;

        // drop von mod_openfire
        $db->Query('DROP TABLE {pre}mod_openfire;');

        PutLog('Plugin "'.$this->name.' - '.$this->version.'" wurde erfolgreich deinstalliert.', PRIO_PLUGIN, __FILE__, __LINE__);

        return true;
    }

    /*
    *  Link  und Tabs im Adminbereich
    */
    public function AdminHandler()
    {
        global $db, $tpl, $lang_admin;

        $tabs = [
            0 => [
                'title' => $lang_admin['prefs'],
                'link' => $this->_adminLink().'&',
                'active' => $_REQUEST['plugin'] == 'modopenfire',
                'icon' => '../plugins/templates/images/openfire_logo.png',
            ],
        ];
        $tpl->assign('tabs', $tabs);

        if (isset($_POST['save'])) {
            $db->Query('UPDATE {pre}mod_openfire SET domain=?,secretkey=?,enableAuth=?,port=?,https=?',
                $_POST['openfire_domain'],
                $_POST['openfire_userservice_secretkey'],
                (int) isset($_POST['openfire_enableAuth']) ? 1 : 0,
                (int) $_POST['openfire_port'],
                (int) isset($_POST['openfire_https']) ? 1 : 0);

            $tpl->assign('erfolg', '<b>'.$lang_admin['openfire_updated_data'].'</b><br />');
        }

        $res = $db->Query('SELECT enableAuth, secretkey, domain, port, https FROM {pre}mod_openfire');
        $openfire_prefs = $res->FetchArray();
        $res->Free();

        $tpl->assign('openfire_prefs', $openfire_prefs);
        $tpl->assign('pageURL', $this->_adminLink());
        $tpl->assign('page', $this->_templatePath('openfire.plugin.prefs.tpl'));
    }

    /*
    *  Sprach variablen
    */
    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang == 'deutsch') {
            $lang_admin['openfire_domain'] = 'Openfire-Domain';
            $lang_admin['openfire_port'] = 'Openfire Adminport';
            $lang_admin['openfire_https'] = 'HTTPS für Adminbereich nutzen';
            $lang_admin['openfire_secretkey'] = 'Secret Key vom User Service Plugin';
            $lang_admin['openfire_updated_data'] = 'Die Daten wurden erfolgreich aktualisiert!';
        }
        else {
            $lang_admin['openfire_domain'] = 'Openfire Domain';
            $lang_admin['openfire_port'] = 'Openfire Adminport';
            $lang_admin['openfire_https'] = 'Use HTTPS for Admin?';
            $lang_admin['openfire_secretkey'] = 'Secret Key of User Service Plugin';
            $lang_admin['openfire_updated_data'] = 'Data successfully updated!';
        }
    }

    /*
     * OnSignup
     */
    public function OnSignup($userid, $usermail)
    {
        global $suEMailLocal, $suPass1, $suEMail, $suFirstname, $suSurname;

        if ($this->_enableAuth()) {
            $benutzername = trim($suEMailLocal);
            $jabber_kennwort = trim($suPass1);
            $voller_name = trim($suFirstname).' '.trim($suSurname);

            $url = $this->_getUrl().'&type=add&username='.$this->_toRawUrl($benutzername).'&password='.$this->_toRawUrl($jabber_kennwort).'&name='.$this->_toRawUrl($voller_name).'&email='.$this->_toRawUrl($suEMail);
            $this->_sendhttp($url);
        }
    }

    /*
     * OnDeleteUser
     */
    public function OnDeleteUser($id)
    {
        global $db;

        if ($this->_enableAuth()) {
            $res = $db->Query('SELECT email FROM {pre}users WHERE id=?',
                $id);
            $jabber = $res->FetchArray();
            $res->Free();

            $benutzername = explode('@', $jabber['email']);

            $url = $this->_getUrl().'&type=delete&username='.$this->_toRawUrl($benutzername[0]);
            $this->_sendhttp($url);
        }
    }

    public function OnUserPasswordChange($userID, $oldPasswordMD5, $newPasswordMD5, $newPasswordPlain)
    {
        global $userRow;

        if ($this->_enableAuth()) {
            $voller_name = trim($userRow['vorname']).' '.trim($userRow['nachname']);
            $benutzername = explode('@', $userRow['email']);

            $url = $this->_getUrl().'&type=update&username='.$this->_toRawUrl($benutzername[0]).'&password='.$this->_toRawUrl($newPasswordPlain).'&name='.$this->_toRawUrl($voller_name).'&email='.$this->_toRawUrl($userRow['email']);
            $this->_sendhttp($url);
        }
    }

    private function _getUrl()
    {
        global $db;

        $res = $db->Query('SELECT https, domain, port, secretkey FROM {pre}mod_openfire');
        $jabber_row = $res->FetchArray();
        $res->Free();

        if ($jabber_row['https'] == 1) {
            $http_modus = 'https';
        } else {
            $http_modus = 'http';
        }

        return $http_modus.'://'.$jabber_row['domain'].':'.$jabber_row['port'].'/plugins/userService/userservice?secret='.$jabber_row['secretkey'];
    }

    private function _enableAuth()
    {
        global $db;

        $res = $db->Query('SELECT enableAuth FROM {pre}mod_openfire');
        $jabber_row = $res->FetchArray();
        $res->Free();

        if ($jabber_row['enableAuth'] == 1) {
            return true;
        }

        return false;
    }

    private function _sendhttp($url)
    {
        if (!class_exists('BMHTTP')) {
            include B1GMAIL_DIR.'serverlib/http.class.php';
        }

        $http = _new('BMHTTP', [$url]);
        $result = $http->DownloadToString();
    }

    private function _toRawUrl($text)
    {
        return rawurlencode($text);
    }
}
/*
 * register plugin
 */
$plugins->registerPlugin('modopenfire');
