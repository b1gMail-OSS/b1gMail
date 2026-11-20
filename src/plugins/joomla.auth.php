<?php
/*
 * b1gMail joomla! auth plugin
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
 * Joomla auth plugin
 *
 */
class JoomlaAuthPlugin extends BMPlugin
{
	var $_uidFormat = 'J!:%d';

	/**
	 * constructor
	 *
	 * @return JoomlaAuthPlugin
	 */
	public function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'Joomla! Authentication Plugin';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.6';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'Joomla!-Auth';
		$this->admin_page_icon		= 'joomla32.png';
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
	 * installation routine
	 *
	 * @return bool
	 */
	public function Install()
	{
		global $db, $bm_prefs;

		// create prefs table
		$db->Query('CREATE TABLE {pre}joomla_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL)');

		// insert initial row
		list($domain) = $this->_getDomains();
		$db->Query('REPLACE INTO {pre}joomla_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'joomla-user',
			'password',
			'joomla',
			'jos_',
			$domain);

		return(true);
	}

	/**
	 * uninstallation routine
	 *
	 * @return bool
	 */
	public function Uninstall()
	{
		global $db;

		// drop prefs table
		$db->Query('DROP TABLE {pre}joomla_plugin_prefs');

		return(true);
	}

	public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if(strpos($lang, 'deutsch') === 0)
			$lang_admin['joomla_auth_saved'] = 'Einstellungen wurden gespeichert.';
		else
			$lang_admin['joomla_auth_saved'] = 'Settings have been saved.';
	}

	/**
	 * authentication handler
	 *
	 * @param string $userName
	 * @param string $userDomain
	 * @param string $passwordMD5
	 * @return array
	 */
	public function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain = '')
	{
		global $db, $bm_prefs;

		// get config
		$res = $db->Query('SELECT * FROM {pre}joomla_plugin_prefs LIMIT 1');
		$joomla_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($joomla_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($joomla_prefs['userDomain']))
			return(false);

		// connect to joomla! DB
		$mysql = @mysqli_connect($joomla_prefs['mysqlHost'], $joomla_prefs['mysqlUser'], $joomla_prefs['mysqlPass'], $joomla_prefs['mysqlDB']);
		if($mysql)
		{
			if(mysqli_select_db($mysql, $joomla_prefs['mysqlDB']))
			{
				$joomlaDB = new DB($mysql);

				// search user
				$res = $joomlaDB->Query('SELECT `id`,`password`,`email`,`name` FROM ' . $joomla_prefs['mysqlPrefix'] . 'users WHERE `username`=? AND `block`=0',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				// check password
				if(password_verify($passwordPlain, $row['password']))
				{
					$uid = sprintf($this->_uidFormat, $row['id']);
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					// split name
					$userNameParts = explode(' ', $row['name']);
					if(count($userNameParts) > 1)
					{
						$userFirstName = $userNameParts[0];
						$userLastName = implode(' ', array_slice($userNameParts, 1));
					}
					else
					{
						$userFirstName = $row['name'];
						$userLastName = '';
					}

					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for Joomla! user <%s> (%d)',
							$userName,
							$row['id']),
							PRIO_PLUGIN,
							__FILE__,
							__LINE__);
						$bmUID = BMUser::CreateAccount($myUserName,
							$userFirstName,
							$userLastName,
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

					// return
					$result = array(
						'uid'		=> $uid,
						'profile'	=> array(
							'altmail'		=> $row['email'],
							'vorname'		=> $userFirstName,
							'nachname'		=> $userLastName
						)
					);
					return($result);
				}
				else
					return(false);
			}
			else
				PutLog('Failed to select Joomla! db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($joomlaDB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to Joomla! db failed',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__);

		return(false);
	}

	/**
	 * user page handler
	 *
	 */
	public function FileHandler($file, $action)
	{
		global $userRow;

		if(!isset($userRow) || !is_array($userRow))
			return(false);

		if(strpos($userRow['uid'], substr($this->_uidFormat, 0, strpos($this->_uidFormat, ':')+1)) === false || $userRow['vorname'] != '' || $userRow['nachname'] != '')
			return(false);

		$file = strtolower($file);
		$action = strtolower($action);

		if($file != 'index.php' && ($file != 'prefs.php' || $action != 'contact')
								&& ($file != 'start.php' || $action != 'logout'))
		{
			header('Location: prefs.php?action=contact&sid=' . session_id());
			exit();
		}
	}

	/**
	 * admin handler
	 *
	 */
	public function AdminHandler()
	{
		global $tpl, $plugins, $lang_admin;
		$this->_JoomlaRedirectLegacyGet();

		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
			$this->_JoomlaHandlePost();

        if (!isset($_REQUEST['action'])) {
            $_REQUEST['action'] = 'prefs';
        }

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'icon'		=> '../plugins/templates/images/joomla32.png',
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['action'] == 'prefs'
			)
		);

		$tpl->assign('tabs', $tabs);

		if($_REQUEST['action'] == 'prefs')
			$this->_prefsPage();
	}

	/**
	 * admin prefs page
	 *
	 */
	private function _prefsPage()
	{
		global $tpl, $db, $bm_prefs;

		// get config
		$res = $db->Query('SELECT * FROM {pre}joomla_plugin_prefs LIMIT 1');
		$joomla_prefs = $res->FetchArray();
		$res->Free();

		// assign
		$tpl->assign('domains', $this->_getDomains());
		$tpl->assign('joomla_prefs', $joomla_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('joomlaauth.plugin.prefs.tpl'));
	}

	/**
	 * @return string
	 */
	protected function _JoomlaTabLink()
	{
		return 'plugin.page.php?plugin=' . rawurlencode($this->internal_name) . '&';
	}

	/**
	 * @param array<string, mixed> $query
	 * @param bool                 $trailingAmp
	 * @return string
	 */
	protected function _JoomlaAdminUrl(array $query = array(), $trailingAmp = true)
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
	protected function _JoomlaTakeFlashMsg()
	{
		if(!isset($_SESSION['joomlaauth_admin_msg']))
			return null;

		$msg = $_SESSION['joomlaauth_admin_msg'];
		unset($_SESSION['joomlaauth_admin_msg']);

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
	protected function _JoomlaRedirectLegacyGet()
	{
		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET')
			return;

		if(isset($_REQUEST['do']) && (string)$_REQUEST['do'] === 'save')
		{
			SessionRedirect($this->_JoomlaAdminUrl(array(), false));
			exit();
		}
	}

	/**
	 * Save prefs (POST + CSRF via {csrffield} in template).
	 */
	protected function _JoomlaHandlePost()
	{
		global $db, $lang_admin;

		if(!isset($_POST['save']))
			return;

        $db->Query('UPDATE {pre}joomla_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']));

		$_SESSION['joomlaauth_admin_msg'] = array(
			'type' => 'success',
			'text' => $lang_admin['joomla_auth_saved'],
		);

		SessionRedirect($this->_JoomlaAdminUrl(array(), false));
		exit();
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('JoomlaAuthPlugin');
?>
