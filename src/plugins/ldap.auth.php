<?php
/*
 * LDAP auth plugin for b1gMail
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
 * LDAP auth plugin.
 */
class LDAPAuthPlugin extends BMPlugin
{
    public $_uidFormat = 'LDAP:%d';

    /**
     * constructor.
     *
     * @return LDAPAuthPlugin
     */
    public function __construct()
    {
        // plugin info
        $this->type = BMPLUGIN_DEFAULT;
        $this->name = 'LDAP Authentication PlugIn';
        $this->author = 'Home of the Sebijk.com';
        $this->version = '1.0.0 Beta 1';

        // admin pages
        $this->admin_pages = true;
        $this->admin_page_title = 'LDAP-Auth';
    }

    /**
 	 * get list of domains
 	 *
 	 * @return array
 	 */
    private function _getDomains()
	{
		  global $bm_prefs;

		  if(function_exists('GetDomainList'))
			  return GetDomainList();
		  else
			  return explode(':', $bm_prefs['domains']);
	  }

    /**
     * installation routine.
     *
     * @return bool
     */
    public function Install()
    {
        global $db, $bm_prefs;

        $DatabaseStructure = [
            '{pre}ldap_plugin_prefs' => [
                'fields' => [
                    ['enableAuth', 'tinyint(1)', 'NO'],
                    ['ldapprotocol', 'tinyint(4)', 'NO'],
                    ['ldapserver', 'varchar(300)', 'NO'],
                    ['ldapport', 'int(10)', 'NO'],
                    ['confidentiality', 'varchar(10)', 'NO'],
                    ['proxy_usr', 'varchar(300)', 'NO'],
                    ['proxy_pwd', 'varchar(300)', 'NO'],
                    ['ou', 'varchar(500)', 'NO'],
                    ['basedn', 'varchar(500)', 'NO'],
                    ['ldapmail', 'varchar(500)', 'NO'],
                    ['ldapnaming', 'varchar(500)', 'NO'],
                    ['inclusionattribute', 'varchar(500)', 'NO'],
                    ['inclusionvalue', 'varchar(500)', 'NO'],
                    ['userDomain', 'varchar(128)', 'NO'],
                ],
                'indexes' => [],
            ],
        ];
        //$db->Query('DROP TABLE IF EXISTS {pre}ldap_plugin_prefs');
        SyncDBStruct($DatabaseStructure);

        // insert initial row
        [$domain] = $this->_getDomains();
        $db->Query('REPLACE INTO {pre}ldap_plugin_prefs(enableAuth, ldapprotocol, ldapserver, ldapport, confidentiality, proxy_usr, 
        proxy_pwd, ou, basedn, ldapmail, ldapnaming, inclusionattribute, inclusionvalue, userDomain) VALUES'
                    .'(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            0,
            3,
            '',
            389,
            'tls',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $domain);

        return true;
    }

    /**
     * uninstallation routine.
     *
     * @return bool
     */
    public function Uninstall()
    {
        global $db;

        // drop prefs table
        //$db->Query('DROP TABLE IF EXISTS {pre}ldap_plugin_prefs');

        return true;
    }

    /**
	 * get notices for ACP start page
	 *
	 * @return array
	 */
	public function getNotices()
	{
		global $lang_admin;

		$result = array();
		if(!extension_loaded('ldap'))
		{
			$result[] = array(
				'type'	=> 'error',
				'text'	=> $lang_admin['ldapauth_exterror']
			);
		}

		return($result);
	}

    function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
        if($lang == 'deutsch') {
            $lang_admin['ou'] = 'Organisationseinheit(OU) der Benutzer';
            $lang_admin['protocol'] = 'Protokoll';
            $lang_admin['encryption'] = 'Verschlüsselung';
            $lang_admin['ldapauth_exterror'] = 'Die php-ldap Erweiterung fehlt. LDAP-Authentifizierung ist daher nicht möglich';
        }
        else {
            $lang_admin['ou'] = 'Organizational Unit (OU) of users';
            $lang_admin['protocol'] = 'Protocol';
            $lang_admin['encryption'] = 'Encryption';
            $lang_admin['ldapauth_exterror'] = 'php-ldap is missing. LDAP Authenification is not possible';
        }
    }

    /**
     * authentication handler.
     *
     * @param string $userName
     * @param string $userDomain
     * @param string $passwordMD5
     *
     * @return array
     */
    public function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain='')
    {
        global $db, $bm_prefs;

        // get config
        $res = $db->Query('SELECT * FROM {pre}ldap_plugin_prefs LIMIT 1');
        $ldap_prefs = $res->FetchArray();
        $res->Free();

        // enabled?
        if ($ldap_prefs['enableAuth'] != 1) {
            return false;
        }
        if(!extension_loaded('ldap')) {
            PutLog('PHP LDAP extension is not installed. LDAP Auth disabled',
							PRIO_NOTE,
							__FILE__,
							__LINE__);
            return false;
        }

        // our domain?
        if (strtolower($userDomain) != strtolower($ldap_prefs['userDomain'])) {
            return false;
        }
        // connect to LDAP Server
        if ($ldap_prefs['confidentiality'] == 'ssl') {
            $ldap_conn = ldap_connect('ldaps://'.$ldap_prefs['ldapserver'].':'.$ldap_prefs['ldapport']);
        } else {
            $ldap_conn = ldap_connect($ldap_prefs['ldapserver'], $ldap_prefs['ldapport']);
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_prefs['ldapprotocol']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

        $ldConnection = false;
        if ($ldap_prefs['confidentiality'] == 'tls') {
            ldap_start_tls($ldap_conn);
        }
        $ldConnection = true;
        // TODO retrieve LDAP extended error message on bind fail
        // Try AD login with UPN if OU field is empty
        if (empty($ldap_prefs['ou'])&& $ldap_conn) {
            $ldap_login = ldap_bind($ldap_conn, $userName.'@'.$userDomain, $passwordPlain);
        }
        // Try with proxy user
        elseif ($ldap_prefs['proxy_usr']) {
            // Bind with proxy
            $proxy = ldap_bind($ldap_conn, $ldap_prefs['proxy_usr'], $ldap_prefs['proxy_pwd']);
            if (!$proxy) {
                return $proxy;
            }
            // Resolve DN with search
            if (empty($ldap_prefs['ou'])) {
                $filter = 'userPrincipalName='.$userName.'@'.$userDomain;
                $dn = $ldap_prefs['basedn'];
            } else {
                $filter = $ldap_prefs['ldapnaming'].'='.$userName;
                $dn = $ldap_prefs['ou'].','.$ldap_prefs['basedn'];
            }
            $sr = ldap_search($ldap_conn, $dn, $filter);
            $ldap_user = ldap_first_entry($ldap_conn, $sr);
            // Get Attributes
            $ldap_user_attr = ldap_get_attributes($ldap_conn, $ldap_user);

            $dn = ldap_get_dn($ldap_conn, $ldap_user);
            // Bind with DN
            $ldap_login = ldap_bind($ldap_conn, $dn, $passwordPlain);
        }
        // Try bind with fixed DN
        elseif ($ldConnection) {
            $params = $ldap_prefs['ldapnaming'].'='.$userName.','.$ldap_prefs['ou'].','.$ldap_prefs['basedn'];
            $ldap_login = ldap_bind($ldap_conn, $params, $passwordPlain);
        } else {
            $ldap_login = false;
        }
        if ($ldap_login==true) {
            $uid = 'LDAP:'.$row['userid'];
            $myUserName = sprintf('%s@%s', $userName, $userDomain);

            // create user in b1gMail?
            if (BMUser::GetID($myUserName) == 0) {
                PutLog(sprintf('Creating b1gMail user for LDAP user <%s> (%d)',
                            $userName,
                            $row['userid']),
                            PRIO_PLUGIN,
                            __FILE__,
                            __LINE__);
                $bmUID = BMUser::CreateAccount($myUserName,
                        !empty($ldap_user_attr['givenName']) ? $ldap_user_attr['givenName'][0] : '',
                        !empty($ldap_user_attr['sn']) ? $ldap_user_attr['sn'][0] : '',
                        '',
                        '',
                        '',
                        '',
                        $bm_prefs['std_land'],
                        '',
                        '',
                        '',
                        '',
                        $passwordMD5,
                        [],
                        true,
                        $uid);
            }

            // return
            $result = [
                        'uid' => $uid,
                    ];

            return $result;
        } else {
            PutLog('Failed LDAP login',
                    PRIO_PLUGIN,
                    __FILE__,
                    __LINE__);

            return false;
        }

        return false;
    }

    /**
     * user page handler.
     */
    public function FileHandler($file, $action)
    {
        global $userRow;

        if (!isset($userRow) || !is_array($userRow)) {
            return false;
        }

        if (strpos($userRow['uid'], 'LDAP:') === false || $userRow['vorname'] != '' || $userRow['nachname'] != '') {
            return false;
        }

        $file = strtolower($file);
        $action = strtolower($action);

        if ($file != 'index.php' && ($file != 'prefs.php' || $action != 'contact')
                                && ($file != 'start.php' || $action != 'logout')) {
            header('Location: prefs.php?action=contact&sid='.session_id());
            exit();
        }
    }

    /**
     * admin handler.
     */
    public function AdminHandler()
    {
        global $tpl, $plugins, $lang_admin;

        if (!isset($_REQUEST['action'])) {
            $_REQUEST['action'] = 'prefs';
        }

        $tabs = [
            0 => [
                'title' => $lang_admin['prefs'],
                'link' => $this->_adminLink().'&',
                'active' => $_REQUEST['action'] == 'prefs',
            ],
        ];

        $tpl->assign('tabs', $tabs);

        if ($_REQUEST['action'] == 'prefs') {
            $this->_prefsPage();
        }
    }

    /**
     * admin prefs page.
     */
    public function _prefsPage()
    {
        global $tpl, $db, $bm_prefs;

        // save?
        if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'save') {
            $db->Query('UPDATE {pre}ldap_plugin_prefs SET enableAuth=?,
            ldapprotocol=?,ldapserver=?, ldapport=?, confidentiality=?, proxy_usr=?, 
        proxy_pwd=?, ou=?, basedn=?, ldapmail=?, ldapnaming=?, inclusionattribute=?, inclusionvalue=?,userDomain=?',
                isset($_REQUEST['enableAuth']) ? 1 : 0,
                intval($_REQUEST['ldapprotocol']),
                $_REQUEST['ldapserver'],
                $_REQUEST['ldapport'],
                $_REQUEST['confidentiality'],
                $_REQUEST['proxy_usr'],
                $_REQUEST['proxy_pwd'],
                $_REQUEST['ou'],
                $_REQUEST['basedn'],
                $_REQUEST['ldapmail'],
                $_REQUEST['ldapnaming'],
                $_REQUEST['inclusionattribute'],
                $_REQUEST['inclusionvalue'],
                trim($_REQUEST['userDomain']));
        }

        // get config
        $res = $db->Query('SELECT * FROM {pre}ldap_plugin_prefs LIMIT 1');
        $ldap_prefs = $res->FetchArray();
        $res->Free();

        // assign
        $tpl->assign('domains', $this->_getDomains());
        $tpl->assign('ldap_prefs', $ldap_prefs);
        $tpl->assign('pageURL', $this->_adminLink());
        $tpl->assign('page', $this->_templatePath('ldapauth.plugin.prefs.tpl'));
    }
}

/*
 * register plugin
 */
$plugins->registerPlugin('LDAPAuthPlugin');
