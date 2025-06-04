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
 *
 */
class MyBBAuthPlugin extends BMPlugin
{
	var $_uidFormat = 'MyBB:%d';

	/**
	 * constructor
	 *
	 * @return MyBBAuthPlugin
	 */
	public function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'MyBB Authentication PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.1';
		$this->designedfor			= '7.4.2';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'MyBB-Auth';
		$this->admin_page_icon		= "mybb32.png";
	}

	/**
	 * installation routine
	 *
	 * @return bool
	 */
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

		// insert initial row
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

	/**
	 * uninstallation routine
	 *
	 * @return bool
	 */
	public function Uninstall()
	{
		global $db;

		// drop prefs table
		$db->Query('DROP TABLE {pre}mybb_plugin_prefs');

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
	public function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain = '')
	{
		global $db, $bm_prefs;

		// get config
		$res = $db->Query('SELECT * FROM {pre}mybb_plugin_prefs LIMIT 1');
		$mybb_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($mybb_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($mybb_prefs['userDomain']))
			return(false);

		// connect to MyBB DB
		$mysql = @mysqli_connect($mybb_prefs['mysqlHost'], $mybb_prefs['mysqlUser'], $mybb_prefs['mysqlPass'], $mybb_prefs['mysqlDB']);
		
		if($mysql)
		{
			if(mysqli_select_db($mysql, $mybb_prefs['mysqlDB']))
			{
				$MyBBDB = new DB($mysql);

				// search user
				$res = $MyBBDB->Query('SELECT uid,salt,password,email FROM ' . $mybb_prefs['mysqlPrefix'] . 'users WHERE username=?',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();
				
				// check password
				if($row['password'] === md5(md5($row['salt']).$passwordMD5))
				{
					$uid = 'MyBB:' . $row['uid'];
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					// create user in b1gMail?
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

	/**
	 * user page handler
	 *
	 */
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

		if(!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'prefs';

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['action'] == 'prefs',
				'icon' => '../plugins/templates/images/mybb32.png',
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

		// save?
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			$db->Query('UPDATE {pre}mybb_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']));
		}

		// get config
		$res = $db->Query('SELECT * FROM {pre}mybb_plugin_prefs LIMIT 1');
		$mybb_prefs = $res->FetchArray();
		$res->Free();

		// assign
		$tpl->assign('domains', GetDomainList());
		$tpl->assign('mybb_prefs', $mybb_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('mybbauth.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('MyBBAuthPlugin');
?>