<?php
/*
 * b1gMail koobi 7 auth plugin
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
 * Koobi 7 auth plugin
 *
 */
class Koobi7AuthPlugin extends BMPlugin
{
	var $_uidFormat = 'K7:%d';

	/**
	 * constructor
	 *
	 * @return Koobi7AuthPlugin
	 */
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'Koobi7 Authentication Plugin';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'ps@b1g.de';
		$this->version				= '1.2';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'Koobi7-Auth';
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
		$db->Query('CREATE TABLE {pre}koobi7_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL)');

		// insert initial row
		list($domain) = explode(':', $bm_prefs['domains']);
		$db->Query('REPLACE INTO {pre}koobi7_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain) VALUES'
					. '(?,?,?,?,?,?,?)',
			0,
			'localhost',
			'koobi7-user',
			'password',
			'koobi7',
			'koobi7_',
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
		$db->Query('DROP TABLE {pre}koobi7_plugin_prefs');

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
		$res = $db->Query('SELECT * FROM {pre}koobi7_plugin_prefs LIMIT 1');
		$koobi7_prefs = $res->FetchArray();
		$res->Free();

		// enabled?
		if($koobi7_prefs['enableAuth'] != 1)
			return(false);

		// our domain?
		if(strtolower($userDomain) != strtolower($koobi7_prefs['userDomain']))
			return(false);

		// connect to koobi7 DB
		$mysql = @mysqli_connect($koobi7_prefs['mysqlHost'], $koobi7_prefs['mysqlUser'], $koobi7_prefs['mysqlPass'], true);
		if($mysql)
		{
			if(mysqli_select_db($koobi7_prefs['mysqlDB'], $mysql))
			{
				$koobi7DB = new DB($mysql);

				// search user
				$res = $koobi7DB->Query('SELECT `Id`,`Kennwort`,`Email`,`Vorname`,`Nachname`,`Strasse_Nr`,`Postleitzahl`,`Ort`,`Telefon`,`Telefax`,`Land` FROM ' . $koobi7_prefs['mysqlPrefix'] . 'benutzer WHERE `Benutzername`=? AND `Aktiv`=1',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();

				// check password
				if(md5($passwordMD5) === $row['Kennwort'])
				{
					$uid = sprintf($this->_uidFormat, $row['Id']);
					$myUserName = sprintf('%s@%s', $userName, $userDomain);

					// split street/no
					$strasseNrParts = explode(' ', $row['Strasse_Nr']);
					if(count($strasseNrParts) > 1)
					{
						$no = array_pop($strasseNrParts);
						$street = implode(' ', $strasseNrParts);
					}
					else
					{
						$street = $row['Strasse_Nr'];
						$no = '';
					}

					// find country
					$countries = CountryList();
					$country = $bm_prefs['std_land'];
					foreach($countries as $countryID=>$countryName)
						if($countryName == $row['Land'])
						{
							$country = $countryID;
							break;
						}

					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for Koobi7 user <%s> (%d)',
							$userName,
							$row['Id']),
							PRIO_PLUGIN,
							__FILE__,
							__LINE__);
						$bmUID = BMUser::CreateAccount($myUserName,
							$row['Vorname'],
							$row['Nachname'],
							$street,
							$no,
							$row['Postleitzahl'],
							$row['Ort'],
							$country,
							$row['Telefon'],
							$row['Telefax'],
							$row['Email'],
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
							'altmail'		=> $row['Email'],
							'vorname'		=> $row['Vorname'],
							'nachname'		=> $row['Nachname'],
							'strasse'		=> $street,
							'hnr'			=> $no,
							'plz'			=> $row['Postleitzahl'],
							'ort'			=> $row['Ort'],
							'land'			=> $country,
							'tel'			=> $row['Telefon'],
							'fax'			=> $row['Telefax']
						)
					);
					return($result);
				}
				else
					return(false);
			}
			else
				PutLog('Failed to select koobi7 db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);

			unset($koobi7DB);
			mysqli_close($mysql);
		}
		else
			PutLog('MySQL connection to koobi7 db failed',
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
			$db->Query('UPDATE {pre}koobi7_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']));
		}

		// get config
		$res = $db->Query('SELECT * FROM {pre}koobi7_plugin_prefs LIMIT 1');
		$koobi7_prefs = $res->FetchArray();
		$res->Free();

		// assign
		$tpl->assign('domains', explode(':', $bm_prefs['domains']));
		$tpl->assign('koobi7_prefs', $koobi7_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('koobi7auth.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('Koobi7AuthPlugin');
?>