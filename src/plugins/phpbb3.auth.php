<?php
/*
 * b1gMail phpBB3 auth plugin
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
 * phpBB3 auth plugin
 *
 */
class phpBB3AuthPlugin extends BMPlugin
{
	var $_uidFormat = 'phpBB3:%d';

	/**
	 * constructor
	 *
	 * @return phpBB3AuthPlugin
	 */
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'phpBB3 Authentication Plugin';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.2';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'phpBB3-Auth';
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
		$db->Query('CREATE TABLE {pre}phpbb3_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL)');

		// insert initial row
		list($domain) = explode(':', $bm_prefs['domains']);
		$db->Query('REPLACE INTO {pre}phpbb3_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'phpbb3-user',
			'password',
			'phpbb3',
			'phpbb_',
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
		$db->Query('DROP TABLE {pre}phpbb3_plugin_prefs');

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
		$res = $db->Query('SELECT * FROM {pre}phpbb3_plugin_prefs LIMIT 1');
		$phpbb3_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($phpbb3_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($phpbb3_prefs['userDomain']))
			return(false);

		// connect to phpbb3 DB
		$mysql = @mysqli_connect($phpbb3_prefs['mysqlHost'], $phpbb3_prefs['mysqlUser'], $phpbb3_prefs['mysqlPass'], true);
		if($mysql)
		{
			if(mysqli_select_db($phpbb3_prefs['mysqlDB'], $mysql))
			{
				$phpbb3DB = new DB($mysql);

				// search user
				$res = $phpbb3DB->Query('SELECT `user_id`,`user_password`,`user_email` FROM ' . $phpbb3_prefs['mysqlPrefix'] . 'users WHERE `username`=? AND (`user_type`=0 OR `user_type`=3)',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				// check password
				if($this->phpbb_check_hash($passwordPlain, $row['user_password']))
				{
					$uid = sprintf($this->_uidFormat, $row['user_id']);
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for phpBB3 user <%s> (%d)',
							$userName,
							$row['user_id']),
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
							$row['user_email'],
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
							'altmail'		=> $row['user_email']
						)
					);
					return($result);
				}
				else
					return(false);
			}
			else
				PutLog('Failed to select phpBB3 db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($phpbb3DB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to phpBB3 db failed',
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
			$db->Query('UPDATE {pre}phpbb3_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']));
		}

		// get config
		$res = $db->Query('SELECT * FROM {pre}phpbb3_plugin_prefs LIMIT 1');
		$phpbb3_prefs = $res->FetchArray();
		$res->Free();

		// assign
		$tpl->assign('domains', explode(':', $bm_prefs['domains']));
		$tpl->assign('phpbb3_prefs', $phpbb3_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('phpbb3auth.plugin.prefs.tpl'));
	}


	/**
	 * the following functions were taken from phpBB / the portable PHP hashing framework
	 * and are placed in public domain
	 *
	 */

	function phpbb_check_hash($password, $hash)
	{
		$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		if (strlen($hash) == 34)
		{
			return ($this->_hash_crypt_private($password, $hash, $itoa64) === $hash) ? true : false;
		}

		return (md5($password) === $hash) ? true : false;
	}

	/**
	* Encode hash
	*/
	function _hash_encode64($input, $count, &$itoa64)
	{
		$output = '';
		$i = 0;

		do
		{
			$value = ord($input[$i++]);
			$output .= $itoa64[$value & 0x3f];

			if ($i < $count)
			{
				$value |= ord($input[$i]) << 8;
			}

			$output .= $itoa64[($value >> 6) & 0x3f];

			if ($i++ >= $count)
			{
				break;
			}

			if ($i < $count)
			{
				$value |= ord($input[$i]) << 16;
			}

			$output .= $itoa64[($value >> 12) & 0x3f];

			if ($i++ >= $count)
			{
				break;
			}

			$output .= $itoa64[($value >> 18) & 0x3f];
		}
		while ($i < $count);

		return $output;
	}

	/**
	* The crypt function/replacement
	*/
	function _hash_crypt_private($password, $setting, &$itoa64)
	{
		$output = '*';

		// Check for correct hash
		if (substr($setting, 0, 3) != '$H$')
		{
			return $output;
		}

		$count_log2 = strpos($itoa64, $setting[3]);

		if ($count_log2 < 7 || $count_log2 > 30)
		{
			return $output;
		}

		$count = 1 << $count_log2;
		$salt = substr($setting, 4, 8);

		if (strlen($salt) != 8)
		{
			return $output;
		}

		/**
		* We're kind of forced to use MD5 here since it's the only
		* cryptographic primitive available in all versions of PHP
		* currently in use.  To implement our own low-level crypto
		* in PHP would result in much worse performance and
		* consequently in lower iteration counts and hashes that are
		* quicker to crack (by non-PHP code).
		*/
		if (PHP_VERSION >= 5)
		{
			$hash = md5($salt . $password, true);
			do
			{
				$hash = md5($hash . $password, true);
			}
			while (--$count);
		}
		else
		{
			$hash = pack('H*', md5($salt . $password));
			do
			{
				$hash = pack('H*', md5($hash . $password));
			}
			while (--$count);
		}

		$output = substr($setting, 0, 12);
		$output .= $this->_hash_encode64($hash, 16, $itoa64);

		return $output;
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('phpBB3AuthPlugin');
