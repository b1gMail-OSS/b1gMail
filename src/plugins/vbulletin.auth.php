<?php
/*
 * VBulletin auth plugin
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
 * VBulletin auth plugin
 *
 */
class VBulletinAuthPlugin extends BMPlugin
{
	var $_uidFormat = 'vB:%d';

	/**
	 * constructor
	 *
	 * @return VBulletinAuthPlugin
	 */
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'VBulletin Authentication PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.8';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'VBulletin-Auth';
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
		$db->Query('CREATE TABLE {pre}vb_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL)');

		// insert initial row
		list($domain) = explode(':', $bm_prefs['domains']);
		$db->Query('REPLACE INTO {pre}vb_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'vbulletin-user',
			'password',
			'vbulletin',
			'',
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
		$db->Query('DROP TABLE {pre}vb_plugin_prefs');

		return(true);
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
		global $db, $bm_prefs;

		// get config
		$res = $db->Query('SELECT * FROM {pre}vb_plugin_prefs LIMIT 1');
		$vb_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($vb_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($vb_prefs['userDomain']))
			return(false);

		// connect to vBulletin DB
		$mysql = @mysqli_connect($vb_prefs['mysqlHost'], $vb_prefs['mysqlUser'], $vb_prefs['mysqlPass'], true);
		if($mysql)
		{
			if(mysqli_select_db($vb_prefs['mysqlDB'], $mysql))
			{
				$vbDB = new DB($mysql);

				// search user
				$res = $vbDB->Query('SELECT userid,salt,password,email FROM ' . $vb_prefs['mysqlPrefix'] . 'user WHERE username=?',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				// check password
				if($row['password'] === md5($passwordMD5 . $row['salt']))
				{
					$uid = 'vBulletin:' . $row['userid'];
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for vBulletin user <%s> (%d)',
							$userName,
							$row['userid']),
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

					// return
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
				PutLog('Failed to select vBulletin db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($vbDB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to vBulletin db failed',
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

		if(strpos($userRow['uid'], 'vBulletin:') === false || $userRow['vorname'] != '' || $userRow['nachname'] != '')
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
			$db->Query('UPDATE {pre}vb_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']));
		}

		// get config
		$res = $db->Query('SELECT * FROM {pre}vb_plugin_prefs LIMIT 1');
		$vb_prefs = $res->FetchArray();
		$res->Free();

		// assign
		$tpl->assign('domains', explode(':', $bm_prefs['domains']));
		$tpl->assign('vb_prefs', $vb_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('vbauth.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('VBulletinAuthPlugin');
?>