<?php
/*
 * b1gMail wbb3 auth plugin
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
 * WBB3 auth plugin
 *
 */
class WBB3AuthPlugin extends BMPlugin
{
	var $_uidFormat = 'WBB3:%d';

	/**
	 * constructor
	 *
	 * @return WBB3AuthPlugin
	 */
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'WBB3 Authentication Plugin';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.7';
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';
		$this->website				= 'https://www.b1gmail.org/';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'WBB3-Auth';
		$this->admin_page_icon		= 'wbb32.png';
	}

 	/**
 	 * get list of domains
 	 *
 	 * @return array
 	 */
	function _getDomains()
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
	function Install()
	{
		global $db, $bm_prefs;

		// create prefs table
		$db->Query('CREATE TABLE {pre}wbb3_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL, userGroups varchar(255) NOT NULL DEFAULT \'\')');

		// insert initial row
		list($domain) = $this->_getDomains();
		$db->Query('REPLACE INTO {pre}wbb3_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'wbb3-user',
			'password',
			'wcf',
			'wcf1_',
			$domain);

		return(true);
	}

	/**
	 * uninstallation routine
	 *
	 * @return bool
	 */
	function Uninstall()
	{
		global $db;

		// drop prefs table
		$db->Query('DROP TABLE {pre}wbb3_plugin_prefs');

		return(true);
	}

	/**
	 * normalize username to valid local part for email address
	 *
	 * @param string $userName
	 * @return string
	 */
	function _normalizeUsername($userName)
	{
		if(function_exists('CharsetDecode'))
			$userName = CharsetDecode($userName, 'utf-8', 'ISO-8859-15');
		$userName = str_replace(array(chr(0xFC), chr(0xDC)), 'ue', $userName);
		$userName = str_replace(array(chr(0xF6), chr(0xD6)), 'oe', $userName);
		$userName = str_replace(array(chr(0xE4), chr(0xC4)), 'ae', $userName);
		$userName = str_replace(chr(0xDF), 'ss', $userName);
		$userName = str_replace(' ', '.', $userName);
		$userName = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', $userName);
		$userName = preg_replace('/[\_]+/', '_', $userName);
		$userName = preg_replace('/[\.]+/', '.', $userName);
		return($userName);
	}

	/**
	 * authentication handler
	 *
	 * @param string $userName
	 * @param string $userDomain
	 * @param string $passwordMD5
	 * @return array
	 */
	function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain = '')
	{
		global $db, $bm_prefs, $currentCharset;

		// get config
		$res = $db->Query('SELECT * FROM {pre}wbb3_plugin_prefs LIMIT 1');
		$wbb3_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($wbb3_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($wbb3_prefs['userDomain']))
			return(false);

		// connect to wbb3 DB
		$mysql = @mysqli_connect($wbb3_prefs['mysqlHost'], $wbb3_prefs['mysqlUser'], $wbb3_prefs['mysqlPass'], true);
		if($mysql)
		{
			if(mysqli_select_db($wbb3_prefs['mysqlDB'], $mysql))
			{
				$wbb3DB = new DB($mysql);

				// charset
				if(strtolower($currentCharset) == 'utf-8' || strtolower($currentCharset) == 'utf8')
					$wbb3DB->Query('SET NAMES utf8');
				else
					$wbb3DB->Query('SET NAMES latin1');

				// search user
				$res = $wbb3DB->Query('SELECT `userID`,`password`,`salt`,`email` FROM ' . $wbb3_prefs['mysqlPrefix'] . 'user WHERE LOWER(`username`)=LOWER(?) AND `activationCode`=0 AND `banned`=0 AND `reactivationCode`=0',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				// check group
				if(trim($wbb3_prefs['userGroups']) != '')
				{
					$allowedGroups = explode(',', $wbb3_prefs['userGroups']);

					$res = $wbb3DB->Query('SELECT COUNT(*) FROM ' . $wbb3_prefs['mysqlPrefix'] . 'user_to_groups WHERE `userID`=? AND `groupID` IN ?',
						$row['userID'], $allowedGroups);
					list($rowCount) = $res->FetchArray(MYSQLI_NUM);
					$res->Free();

					if($rowCount < 1)
					{
						PutLog(sprintf('Rejected login attempt of WBB3 user <%s> (%d) (not in an allowed group)',
								$userName,
								$row['userID']),
							PRIO_PLUGIN,
							__FILE__,
							__LINE__);

						return(false);
					}
				}

				// check password
				if($row['password'] === sha1($row['salt'] . sha1($row['salt'] . sha1($passwordPlain))))
				{
					$uid = sprintf($this->_uidFormat, $row['userID']);
					$myUserName = sprintf('%s@%s', $this->_normalizeUsername($userName), $userDomain);

					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user <%s> for wbb3 user <%s> (%d)',
							$myUserName,
							$userName,
							$row['userID']),
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
					else
					{
						$res = $db->Query('SELECT `uid` FROM {pre}users WHERE `email`=?',
							$myUserName);
						$row = $res->FetchArray(MYSQLI_ASSOC);
						$res->Free();

						if($row['uid'] != $uid)
						{
							PutLog(sprintf('Username conflict in wbb3 auth plugin: UID of user <%s> is not <%s>',
								$myUserName,
								$uid),
								PRIO_WARNING,
								__FILE__,
								__LINE__);
							return(false);
						}
					}

					// return
					$result = array(
						'uid'		=> $uid,
						'profile'	=> array(
							'altmail'		=> $row['email']
						)
					);
					return($result);
				}
				else
					return(false);
			}
			else
				PutLog('Failed to select wbb3 db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($wbb3DB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to wbb3 db failed',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__);

		return(false);
	}

	/**
	 * user page handler
	 *
	 */
	function FileHandler($file, $action)
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
	function AdminHandler()
	{
		global $tpl, $plugins, $lang_admin;

		if(!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'prefs';

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'icon'		=> '../plugins/templates/images/wbb32.png',
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
	function _prefsPage()
	{
		global $tpl, $db, $bm_prefs;

		// save?
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			$db->Query('UPDATE {pre}wbb3_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?,userGroups=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']),
				isset($_REQUEST['groups']) && is_array($_REQUEST['groups']) && !isset($_REQUEST['allGroups']) ? implode(',', $_REQUEST['groups']) : '');
		}

		// get config
		$res = $db->Query('SELECT * FROM {pre}wbb3_plugin_prefs LIMIT 1');
		$wbb3_prefs = $res->FetchArray();
		$res->Free();

		// connect to wbb3 DB
		$groups = array();
		$mysql = @mysqli_connect($wbb3_prefs['mysqlHost'], $wbb3_prefs['mysqlUser'], $wbb3_prefs['mysqlPass'], true);
		if($mysql)
		{
			if(mysqli_select_db($wbb3_prefs['mysqlDB'], $mysql))
			{
				$wbb3DB = new DB($mysql);
				$activatedGroups = explode(',', $wbb3_prefs['userGroups']);

				$res = $wbb3DB->Query('SELECT `groupID`,`groupName` FROM ' . $wbb3_prefs['mysqlPrefix'] . 'group WHERE `groupType`>=3 ORDER BY `groupName` ASC');
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					$row['active'] = in_array($row['groupID'], $activatedGroups);
					$groups[$row['groupID']] = $row;
				}
				$res->Free();
			}
		}

		// assign
		$tpl->assign('groups', $groups);
		$tpl->assign('domains', $this->_getDomains());
		$tpl->assign('wbb3_prefs', $wbb3_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('wbb3auth.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('WBB3AuthPlugin');
