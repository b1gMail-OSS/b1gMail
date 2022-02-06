<?php
/*
 * Copyright (c) 2007 - 2008, Home of the Sebijk.com
 * http://www.sebijk.com
 */
class modopenfire extends BMPlugin
{	
	/*
	* Eigenschaften des Plugins
	*/
	function modopenfire()
	{
		$this->name					= 'Jabber Openfire-Integration';
		$this->version				= '1.1.0';
		$this->designedfor			= '7.3.0';
		$this->type					= BMPLUGIN_DEFAULT;

		$this->author				= 'Home of the Sebijk.com';
		$this->web					= 'http://www.sebijk.com';
		$this->mail					= 'sebijk@web.de';

		$this->update_url			= 'http://my.b1gmail.com/update_service/';
		$this->website				= 'http://my.b1gmail.com/details/69/';

		$this->admin_pages			=  true;
		$this->admin_page_title		= 'Openfire-Integration';
		$this->admin_page_icon		= "openfire_icon.png";
	}

	/*
	 * installation routine
	 */	
	function Install()
	{
		global $db;

		// create mod_openfire table utf8
		$db->Query("CREATE TABLE `{pre}mod_openfire` (
			`enableAuth` tinyint(1) NOT NULL,
			`secretkey` varchar(255) NOT NULL,
			`domain` varchar(255) NOT NULL,
			`port` int(10) NOT NULL,
			`https` tinyint(1) NOT NULL
			) ENGINE=MyISAM;");

		$db->Query("REPLACE INTO {pre}mod_openfire (enableAuth, secretkey, domain, port, https) VALUES (?,?,?,?,?);",
			(int) 0,
			'YourSecretKey',
			'localhost',
			(int) 9091,
			(int) 1);

		PutLog('Plugin "'. $this->name .' - '. $this->version .'" wurde erfolgreich installiert.', PRIO_PLUGIN, __FILE__, __LINE__);
		return(true);
	}

	/*
	 * uninstallation routine
	 */
	function Uninstall()
	{
		global $db;

		// drop von mod_openfire
		$db->Query("DROP TABLE {pre}mod_openfire;");

		PutLog('Plugin "'. $this->name .' - '. $this->version .'" wurde erfolgreich deinstalliert.', PRIO_PLUGIN, __FILE__, __LINE__);
		return(true);
	}

	/*
	*  Link  und Tabs im Adminbereich 
	*/
	function AdminHandler()
	{
		global $db, $tpl, $lang_admin;

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['plugin'] == 'modopenfire',
				'icon'		=> '../plugins/templates/images/openfire_logo.png'
			)
		);
		$tpl->assign('tabs', $tabs);

		if(isset($_POST['save']))
		{
			$db->Query("UPDATE {pre}mod_openfire SET domain=?,secretkey=?,enableAuth=?,port=?,https=?",
				$_POST['openfire_domain'],
				$_POST['openfire_userservice_secretkey'],
				(int) isset($_POST['openfire_enableAuth']) ? 1 : 0,
				(int) $_POST['openfire_port'],
				(int) isset($_POST['openfire_https']) ? 1 : 0);

			$tpl->assign('erfolg', "<b>Die Daten wurden erfolgreich aktualisiert!</b><br />");
		}

		$res = $db->Query("SELECT enableAuth, secretkey, domain, port, https FROM {pre}mod_openfire");
		$openfire_prefs = $res->FetchArray();
		$res->Free();

		$tpl->assign('openfire_prefs', $openfire_prefs);
		$tpl->assign('pageURL', $this->_adminLink());
		$tpl->assign('page', $this->_templatePath('openfire.plugin.prefs.tpl'));
	}

	/*
	*  Sprach variablen
	*/
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		$lang_admin['openfire_domain']					= 'Openfire-Domain';
		$lang_admin['openfire_port']					= 'Openfire Adminport';
		$lang_admin['openfire_https']					= 'HTTPS f&uuml;r Adminbereich nutzen';
		$lang_admin['openfire_secretkey']				= 'Secret Key vom User Service Plugin';
	}

	/*
	 * OnSignup
	 */
	function OnSignup($userid, $usermail)
	{
		global $suEMailLocal, $suPass1, $suEMail, $suFirstname, $suSurname;

		if($this->_enableAuth()) 
		{
			$benutzername = trim($suEMailLocal);
			$jabber_kennwort = trim($suPass1);
			$voller_name = trim($suFirstname)." ".trim($suSurname);

			$url = $this->_getUrl()."&type=add&username=".$this->_toRawUrl($benutzername)."&password=".$this->_toRawUrl($jabber_kennwort)."&name=".$this->_toRawUrl($voller_name)."&email=".$this->_toRawUrl($suEMail);
			$this->_sendhttp($url);
		}
	}

	/*
	 * OnDeleteUser
	 */
	function OnDeleteUser($id)
	{
		global $db;

		if($this->_enableAuth()) 
		{
			$res = $db->Query("SELECT email FROM {pre}users WHERE id=?",
				$id);
			$jabber = $res->FetchArray();
			$res->Free();
	
			$benutzername = explode("@", $jabber['email']);

			$url = $this->_getUrl()."&type=delete&username=".$this->_toRawUrl($benutzername[0]);
			$this->_sendhttp($url);
		}
	}

	function OnUserPasswordChange($userID, $oldPasswordMD5, $newPasswordMD5, $newPasswordPlain)
	{
		global $userRow;

		if($this->_enableAuth())
		{
			$voller_name = trim($userRow['vorname'])." ".trim($userRow['nachname']);
			$benutzername = explode("@", $userRow['email']);

			$url = $this->_getUrl()."&type=update&username=".$this->_toRawUrl($benutzername[0])."&password=".$this->_toRawUrl($newPasswordPlain)."&name=".$this->_toRawUrl($voller_name)."&email=".$this->_toRawUrl($userRow['email']);
			$this->_sendhttp($url);
		}
	}

	function _getUrl()
	{
		global $db;
 
		$res = $db->Query("SELECT https, domain, port, secretkey FROM {pre}mod_openfire");
		$jabber_row = $res->FetchArray();
		$res->Free();

		if($jabber_row['https'] == 1)
		{
			$http_modus = "https";
		} else {
			$http_modus = "http";
		}
		return $http_modus."://".$jabber_row['domain'].":".$jabber_row['port']."/plugins/userService/userservice?secret=".$jabber_row['secretkey'];
	}

	function _enableAuth()
	{
		global $db;
 
		$res = $db->Query("SELECT enableAuth FROM {pre}mod_openfire");
		$jabber_row = $res->FetchArray();
		$res->Free();

		if($jabber_row['enableAuth'] == 1)
			return true;

		return false;
	}

	function _sendhttp($url)
	{
		if(!class_exists('BMHTTP'))
			include(B1GMAIL_DIR . 'serverlib/http.class.php');

		$http = _new('BMHTTP', array($url));
		$result = $http->DownloadToString();
	}

	function _toRawUrl($text)
	{
		global $bm_prefs;

		if(!$bm_prefs['db_is_utf8'])
			$text = utf8_encode($text);

		return rawurlencode($text);
	}
}
/**
 * register plugin
 */
$plugins->registerPlugin('modopenfire');
?>