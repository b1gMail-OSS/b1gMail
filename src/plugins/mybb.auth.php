<?php
/*
 * MyBB auth plugin
 * (c) 2022 b1gMail.eu
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
 * MyBB auth plugin
 */
class MyBBAuthPlugin extends BMPlugin
{
	/** @var string Pretty-URL: /admin/plugin/mybbauthplugin/ */
	public $admin_route_slug = 'mybbauthplugin';

	var $_uidFormat = 'MyBB:%d';

	public function __construct()
	{
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'MyBB Authentication PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.2';
		$this->designedfor			= '7.5.0';

		$this->admin_pages			= true;
		$this->admin_page_title		= 'MyBB-Auth';
		$this->admin_page_icon		= 'mybb32.png';
	}

	public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if($lang == 'deutsch')
			$lang_admin['mybb_auth_saved'] = 'Einstellungen wurden gespeichert.';
		else
			$lang_admin['mybb_auth_saved'] = 'Settings have been saved.';
	}

	public function Install()
	{
		global $db, $bm_prefs;

		$DatabaseStructure = [
            'bm60_mybb_plugin_prefs' => [
                'fields' => [
                    ['enableAuth', 'tinyint(4)', 'NO'],
                    ['mysqlHost', 'varchar(128)', 'NO'],
                    ['mysqlUser', 'varchar(128)', 'NO'],
					['mysqlPass', 'varchar(128)', 'NO'],
					['mysqlDB', 'varchar(128)', 'NO'],
					['mysqlPrefix', 'varchar(128)', 'NO'],
                    ['userDomain', 'varchar(128)', 'NO']
                ],
                'indexes' => [],
            ],
        ];
        SyncDBStruct($DatabaseStructure);

		list($domain) = GetDomainList();
		$db->Query('REPLACE INTO {pre}mybb_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'MyBB-user',
			'password',
			'MyBB',
			'mybb_',
			$domain);

		return(true);
	}

	public function Uninstall()
	{
		global $db;

		$db->Query('DROP TABLE {pre}mybb_plugin_prefs');

		return(true);
	}

	/**
	 * @return string
	 */
	protected function _mybbTabLink()
	{
		return 'plugin.page.php?plugin=' . rawurlencode($this->internal_name) . '&';
	}

	/**
	 * @param array<string, mixed> $query
	 * @param bool                 $trailingAmp
	 * @return string
	 */
	protected function _mybbAdminUrl(array $query = array(), $trailingAmp = true)
	{
		$params = array_merge(array('plugin' => $this->internal_name), $query);

		if(function_exists('AdminSessionUrl'))
			return AdminSessionUrl('plugin.page.php', $params, $trailingAmp);

		$url = $this->_adminLink();
		unset($params['plugin']);
		foreach($params as $key => $val)
		{
			if((string)$val === '')
				continue;
			$url .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$val);
		}
		if($trailingAmp)
			$url .= (strpos($url, '?') !== false ? '&' : '?');

		return $url;
	}

	/**
	 * @return array{type: string, text: string}|null
	 */
	protected function _mybbTakeFlashMsg()
	{
		if(!isset($_SESSION['mybbauth_admin_msg']))
			return null;

		$msg = $_SESSION['mybbauth_admin_msg'];
		unset($_SESSION['mybbauth_admin_msg']);

		if(!is_array($msg) || !isset($msg['type'], $msg['text']))
			return null;

		return array(
			'type' => (string)$msg['type'],
			'text' => (string)$msg['text'],
		);
	}

	/**
	 * Legacy GET save → Redirect (CSRF-safe POST forms preferred).
	 */
	protected function _mybbRedirectLegacyGet()
	{
		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET')
			return;

		if(isset($_REQUEST['do']) && (string)$_REQUEST['do'] === 'save')
		{
			SessionRedirect($this->_mybbAdminUrl(array(), false));
			exit();
		}
	}

	/**
	 * Save prefs (POST + CSRF via {csrffield} in template).
	 */
	protected function _mybbHandlePost()
	{
		global $db, $lang_admin;

		if(!isset($_POST['save']))
			return;

		$db->Query('UPDATE {pre}mybb_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
			isset($_POST['enableAuth']) ? 1 : 0,
			trim((string)($_POST['mysqlHost'] ?? '')),
			trim((string)($_POST['mysqlUser'] ?? '')),
			(string)($_POST['mysqlPass'] ?? ''),
			trim((string)($_POST['mysqlDB'] ?? '')),
			trim((string)($_POST['mysqlPrefix'] ?? '')),
			trim((string)($_POST['userDomain'] ?? '')));

		$_SESSION['mybbauth_admin_msg'] = array(
			'type' => 'success',
			'text' => $lang_admin['mybb_auth_saved'],
		);

		SessionRedirect($this->_mybbAdminUrl(array(), false));
		exit();
	}

	public function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain = '')
	{
		global $db, $bm_prefs;

		$res = $db->Query('SELECT * FROM {pre}mybb_plugin_prefs LIMIT 1');
		$mybb_prefs = $res->FetchArray();
		$res->Free();

		if($mybb_prefs['enableAuth'] != 1)
			return(false);

		if(strtolower($userDomain) != strtolower($mybb_prefs['userDomain']))
			return(false);

		$mysql = @mysqli_connect($mybb_prefs['mysqlHost'], $mybb_prefs['mysqlUser'], $mybb_prefs['mysqlPass'], $mybb_prefs['mysqlDB']);

		if($mysql)
		{
			if(mysqli_select_db($mysql, $mybb_prefs['mysqlDB']))
			{
				$MyBBDB = new DB($mysql);

				$res = $MyBBDB->Query('SELECT uid,salt,password,email FROM ' . $mybb_prefs['mysqlPrefix'] . 'users WHERE username=?',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				if($row['password'] === md5(md5($row['salt']).$passwordMD5))
				{
					$uid = 'MyBB:' . $row['uid'];
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for MyBB user <%s> (%d)',
							$userName,
							$row['uid']),
							PRIO_PLUGIN,
							__FILE__,
							__LINE__);
						$bmUID = BMUser::CreateAccount($myUserName,
							'',
							'',
							'',
							'',
							'',
							'',
							$bm_prefs['std_land'],
							'',
							'',
							$row['email'],
							'',
							$passwordMD5,
							array(),
							true,
							$uid);
					}

					$result = array(
						'uid'		=> $uid,
						'profile'	=> array(
							'altmail'	=> $row['email']
						)
					);
					return($result);
				}
				else
					return(false);
			}
			else
				PutLog('Failed to select MyBB db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($MyBBDB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to MyBB db failed',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__);

		return(false);
	}

	public function FileHandler($file, $action)
	{
		global $userRow;

		if(!isset($userRow) || !is_array($userRow))
			return(false);

		if(strpos($userRow['uid'], 'MyBB:') === false || $userRow['vorname'] != '' || $userRow['nachname'] != '')
			return(false);

		$file = strtolower($file);
		$action = strtolower($action);

		if($file != 'index.php' && ($file != 'prefs.php' || $action != 'contact')
								&& ($file != 'start.php' || $action != 'logout'))
		{
			header('Location: prefs.php?action=contact');
			exit();
		}
	}

	public function AdminHandler()
	{
		global $tpl, $lang_admin;

		$this->_mybbRedirectLegacyGet();

		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
			$this->_mybbHandlePost();

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'link'		=> $this->_mybbTabLink(),
				'active'	=> true,
				'icon'		=> '../plugins/templates/images/mybb32.png',
			),
		);

		$tpl->assign('tabs', $tabs);
		$tpl->assign('mybbPlugin', $this->internal_name);
		$tpl->assign('pageURL', $this->_mybbAdminUrl(array(), true));
		$this->_prefsPage();
	}

	private function _prefsPage()
	{
		global $tpl, $db;

		$res = $db->Query('SELECT * FROM {pre}mybb_plugin_prefs LIMIT 1');
		$mybb_prefs = $res->FetchArray();
		$res->Free();

		$tpl->assign('domains', GetDomainList());
		$tpl->assign('mybb_prefs', $mybb_prefs);
		$tpl->assign('mybbMsg', $this->_mybbTakeFlashMsg());
		$tpl->assign('page', $this->_templatePath('mybbauth.plugin.prefs.tpl'));
	}
}

$plugins->registerPlugin('MyBBAuthPlugin');
