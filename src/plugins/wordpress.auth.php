<?php
/*
 * WordPress auth plugin
 * (c) 2022-2026 b1gMail.eu
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
 * Verifies WordPress password hashes without loading WordPress.
 */
class WordPressPasswordVerifier
{
	public static function Verify($password, $hash)
	{
		if (!is_string($password) || !is_string($hash)
			|| $hash === '' || strlen($password) > 4096)
		{
			return false;
		}

		// WordPress 6.8+: bcrypt hash with HMAC-SHA-384 preprocessing.
		if (strpos($hash, '$wp$') === 0)
		{
			$preparedPassword = base64_encode(
				hash_hmac('sha384', $password, 'wp-sha384', true)
			);

			return password_verify($preparedPassword, substr($hash, 3));
		}

		// Portable PHPass hashes used by older WordPress versions.
		if (strpos($hash, '$P$') === 0 || strpos($hash, '$H$') === 0)
			return self::VerifyPortablePhpass($password, $hash);

		// Older bcrypt hashes.
		if (strpos($hash, '$2y$') === 0
			|| strpos($hash, '$2a$') === 0
			|| strpos($hash, '$2b$') === 0)
		{
			return password_verify($password, $hash);
		}

		// Compatibility with very old WordPress MD5 hashes.
		if (preg_match('/\A[a-f0-9]{32}\z/i', $hash))
			return hash_equals(strtolower($hash), md5($password));

		return false;
	}

	private static function VerifyPortablePhpass($password, $storedHash)
	{
		$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		if (strlen($storedHash) !== 34)
			return false;

		$countLog2 = strpos($itoa64, $storedHash[3]);
		if ($countLog2 === false || $countLog2 < 7 || $countLog2 > 30)
			return false;

		$salt = substr($storedHash, 4, 8);
		if (strlen($salt) !== 8)
			return false;

		$count = 1 << $countLog2;
		$hash = md5($salt . $password, true);

		for ($i = 0; $i < $count; $i++)
			$hash = md5($hash . $password, true);

		$encoded = '';
		$i = 0;

		do
		{
			$value = ord($hash[$i++]);
			$encoded .= $itoa64[$value & 0x3f];

			if ($i < 16)
				$value |= ord($hash[$i]) << 8;
			$encoded .= $itoa64[($value >> 6) & 0x3f];

			if ($i++ >= 16)
				break;

			if ($i < 16)
				$value |= ord($hash[$i]) << 16;
			$encoded .= $itoa64[($value >> 12) & 0x3f];

			if ($i++ >= 16)
				break;

			$encoded .= $itoa64[($value >> 18) & 0x3f];
		}
		while ($i < 16);

		$computedHash = substr($storedHash, 0, 12) . $encoded;

		return hash_equals($storedHash, $computedHash);
	}
}

/**
 * WordPress authentication plugin.
 */
class WordPressAuthPlugin extends BMPlugin
{
	var $_uidFormat = 'WP:%d';

	/**
	 * Constructor.
	 *
	 * @return WordPressAuthPlugin
	 */
	public function __construct()
	{
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'WordPress Authentication PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.0';
		$this->designedfor			= '7.4.2';

		// Admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'WordPress-Auth';
	}

	/**
	 * Installation routine.
	 *
	 * @return bool
	 */
	public function Install()
	{
		global $db;
		$DatabaseStructure = [
			'{pre}wordpress_plugin_prefs' => [
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

		// Insert initial row.
		list($domain) = GetDomainList();
		$db->Query(
			'REPLACE INTO {pre}wordpress_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
			. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'WordPress-user',
			'password',
			'WordPress',
			'wp_',
			$domain
		);

		return true;
	}

	/**
	 * Uninstallation routine.
	 *
	 * @return bool
	 */
	public function Uninstall()
	{
		global $db;

		// Drop the retained preferences table.
		$db->Query('DROP TABLE IF EXISTS {pre}wordpress_plugin_prefs');

		return true;
	}

	/**
	 * Authentication handler.
	 *
	 * @param string $userName
	 * @param string $userDomain
	 * @param string $passwordMD5
	 * @param string $passwordPlain
	 * @return array|bool
	 */
	public function OnAuthenticate($userName, $userDomain, $passwordMD5, $passwordPlain = '')
	{
		global $db, $bm_prefs;

		// WordPress password hashes require the original password.
		if (!is_string($passwordPlain) || $passwordPlain === '')
			return false;

		// Get configuration.
		$res = $db->Query('SELECT * FROM {pre}wordpress_plugin_prefs LIMIT 1');
		$wordpress_prefs = $res->FetchArray();
		$res->Free();

		if (!is_array($wordpress_prefs)
			|| (int)$wordpress_prefs['enableAuth'] !== 1)
		{
			return false;
		}

		// Only authenticate for the configured domain.
		if (strtolower($userDomain) !== strtolower($wordpress_prefs['userDomain']))
			return false;

		/*
		 * The table prefix is part of the SQL identifier, so it cannot be
		 * passed as a query parameter. Restrict it to safe identifier chars.
		 */
		$prefix = $wordpress_prefs['mysqlPrefix'];
		if (!is_string($prefix) || !preg_match('/\A[A-Za-z0-9_]*\z/', $prefix))
		{
			PutLog(
				'Invalid WordPress table prefix',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__
			);
			return false;
		}

		$mysql = @mysqli_connect(
			$wordpress_prefs['mysqlHost'],
			$wordpress_prefs['mysqlUser'],
			$wordpress_prefs['mysqlPass'],
			$wordpress_prefs['mysqlDB']
		);

		if (!$mysql)
		{
			PutLog(
				'MySQL connection to WordPress db failed',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__
			);
			return false;
		}

		$row = false;
		$authenticated = false;

		try
		{
			$WordPressDB = new DB($mysql);

			// WordPress users are stored in <prefix>users.
			$res = $WordPressDB->Query(
				'SELECT ID,user_pass,user_email FROM ' . $prefix . 'users WHERE user_login=?',
				$userName
			);

			if ($res->RowCount() > 0)
			{
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$authenticated = is_array($row)
					&& WordPressPasswordVerifier::Verify(
						$passwordPlain,
						$row['user_pass']
					);
			}

			$res->Free();
		}
		catch (Throwable $e)
		{
			PutLog(
				'WordPress authentication query failed',
				PRIO_PLUGIN,
				__FILE__,
				__LINE__
			);
		}
		finally
		{
			mysqli_close($mysql);
		}

		if (!$authenticated)
			return false;

		$uid = 'WP:' . $row['ID'];
		$myUserName = sprintf('%s@%s', $userName, $userDomain);

		// Create the b1gMail account on first login.
		if (BMUser::GetID($myUserName) == 0)
		{
			PutLog(
				sprintf(
					'Creating b1gMail user for WordPress user <%s> (%d)',
					$userName,
					$row['ID']
				),
				PRIO_PLUGIN,
				__FILE__,
				__LINE__
			);

			BMUser::CreateAccount(
				$myUserName,
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
				$uid
			);
		}

		return array(
			'uid' => $uid,
			'profile' => array(
				'altmail' => $row['user_email']
			)
		);
	}

	/**
	 * User page handler.
	 */
	public function FileHandler($file, $action)
	{
		global $userRow;

		if (!isset($userRow) || !is_array($userRow))
			return false;

		if (strpos($userRow['uid'], 'WP:') !== 0
			|| $userRow['vorname'] != ''
			|| $userRow['nachname'] != '')
		{
			return false;
		}

		$file = strtolower($file);
		$action = strtolower($action);

		if ($file != 'index.php'
			&& ($file != 'prefs.php' || $action != 'contact')
			&& ($file != 'start.php' || $action != 'logout'))
		{
			header('Location: prefs.php?action=contact&sid=' . session_id());
			exit();
		}
	}

	/**
	 * Admin handler.
	 */
	public function AdminHandler()
	{
		global $tpl, $plugins, $lang_admin;

		if (!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'prefs';

		$tabs = array(
			0 => array(
				'title' => $lang_admin['prefs'],
				'link' => $this->_adminLink() . '&',
				'active' => $_REQUEST['action'] == 'prefs'
			)
		);

		$tpl->assign('tabs', $tabs);

		if ($_REQUEST['action'] == 'prefs')
			$this->_prefsPage();
	}

	/**
	 * Admin preferences page.
	 */
	private function _prefsPage()
	{
		global $tpl, $db;

		// Save settings.
		if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			$db->Query(
				'UPDATE {pre}wordpress_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain'])
			);
		}

		// Get configuration.
		$res = $db->Query('SELECT * FROM {pre}wordpress_plugin_prefs LIMIT 1');
		$wordpress_prefs = $res->FetchArray();
		$res->Free();

		$tpl->assign('domains', GetDomainList());
		$tpl->assign('wordpress_prefs', $wordpress_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('wordpressauth.plugin.prefs.tpl'));
	}
}

/**
 * Register plugin.
 */
$plugins->registerPlugin('WordPressAuthPlugin');
?>