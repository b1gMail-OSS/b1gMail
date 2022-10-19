<?php
/*
 * b1gMail wbb2 auth plugin
 * (c) 2021 Patrick Schlangen et al, (c) 2009 IND-InterNetDienst Schlei
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
 * wbb2 auth plugin
 *
 */
class wbb2AuthPlugin extends BMPlugin
{
	var $_uidFormat = 'wbb2:%d';
	
	/**
	 * constructor
	 *
	 * @return wbb2AuthPlugin
	 */
	public function __construct()
	{
		
		// plugin info
		$this->name					= 'wbb2 Authentication Plugin';
		$this->author			    = 'b1gMail Project, IND-InterNetDienst Schlei';
		$this->web					= 'http://www.ind.de/';
		$this->mail					= 'b1gmail.com@ind.de';
		$this->version				= '1.0.3';
		$this->type             	= BMPLUGIN_DEFAULT;
		$this->update_url       	= 'http://my.b1gmail.com/update_service/';
		
		// admin pages
		$this->admin_pages				= true;
		$this->admin_page_title			= 'wbb2-Auth';
		$this->admin_page_icon		= "wbb2.png";
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
		$db->Query('CREATE TABLE IF NOT EXISTS {pre}wbb2_plugin_prefs(enableAuth tinyint(4) NOT NULL DEFAULT 0, mysqlHost varchar(128) NOT NULL, mysqlUser varchar(128) NOT NULL, mysqlPass varchar(128) NOT NULL, mysqlDB varchar(128) NOT NULL, mysqlPrefix varchar(128) NOT NULL, userDomain varchar(128) NOT NULL, enableReg tinyint(4) NOT NULL DEFAULT 0)');
		
		// insert initial row
		list($domain) = explode(':', $bm_prefs['domains']);
		$db->Query('REPLACE INTO {pre}wbb2_plugin_prefs(enableAuth, mysqlHost, mysqlUser, mysqlPass, mysqlDB, mysqlPrefix, userDomain, enableReg) VALUES'
					. '(?,?,?,?,?,?,?,?)',
			0,
			'localhost',
			'wbb2-user',
			'password',
			'wcf',
			'bb1_',
			$domain,
			1);
		
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
		$db->Query('DROP TABLE {pre}wbb2_plugin_prefs');
		
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
		$res = $db->Query('SELECT * FROM {pre}wbb2_plugin_prefs LIMIT 1');
		$wbb2_prefs = $res->FetchArray();
		$res->Free();
		
		// enabled?
		if($wbb2_prefs['enableAuth'] != 1)
			return(false);
		
		// our domain?
		if(strtolower($userDomain) != strtolower($wbb2_prefs['userDomain']))
			return(false);
		
		// connect to wbb2 DB
		$mysql = @mysqli_connect($wbb2_prefs['mysqlHost'], $wbb2_prefs['mysqlUser'], $wbb2_prefs['mysqlPass'], $wbb2_prefs['mysqlDB']);
		if($mysql)
		{
			if(mysqli_select_db($mysql, $wbb2_prefs['mysqlDB']))
			{
				$wbb2DB = new DB($mysql);
				
				// search user
				$res = $wbb2DB->Query('SELECT userid,password,email FROM ' . $wbb2_prefs['mysqlPrefix'] . 'users WHERE username=? AND activation=1 AND blocked=0',
					$userName);
				if($res->RowCount() == 0)
					return(false);
				$row = $res->FetchArray(MYSQLI_ASSOC);
				$res->Free();
				// check password

				if($row['password'] == $passwordMD5)
				{
					$uid = sprintf($this->_uidFormat, $row['userID']);

					// Wenn Benutzername mit Leerzeichen durch . ersetzen
					$userName = str_replace(" ", ".", $userName);

					$myUserName = sprintf('%s@%s', $userName, $userDomain);
				
					// create user in b1gMail?
					if(BMUser::GetID($myUserName) == 0)
					{
						PutLog(sprintf('Creating b1gMail user for wbb2 user <%s> (%d)',
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
				PutLog('Failed to select wbb2 db',
					PRIO_PLUGIN,
					__FILE__,
					__LINE__);
			
			unset($wbb2DB);
			mysqli_close($mysql);
		}
		else 
			PutLog('MySQL connection to wbb2 db failed',
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
	public function _prefsPage()
	{
		global $tpl, $db, $bm_prefs;
		
		// save?
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'save')
		{
			$db->Query('UPDATE {pre}wbb2_plugin_prefs SET enableAuth=?,mysqlHost=?,mysqlUser=?,mysqlPass=?,mysqlDB=?,mysqlPrefix=?,userDomain=?,enableReg=?',
				isset($_REQUEST['enableAuth']) ? 1 : 0,
				$_REQUEST['mysqlHost'],
				$_REQUEST['mysqlUser'],
				$_REQUEST['mysqlPass'],
				$_REQUEST['mysqlDB'],
				$_REQUEST['mysqlPrefix'],
				trim($_REQUEST['userDomain']),
				isset($_REQUEST['enableReg']) ? 1 : 0);
		}
		
		// get config
		$res = $db->Query('SELECT * FROM {pre}wbb2_plugin_prefs LIMIT 1');
		$wbb2_prefs = $res->FetchArray();
		$res->Free();
			
		// assign
		$tpl->assign('domains', $this->_getDomains());
		$tpl->assign('wbb2_prefs', $wbb2_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('wbb2auth.plugin.prefs.tpl'));
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('wbb2AuthPlugin');