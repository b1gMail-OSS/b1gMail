<?php
/*
 * b1gMail Openfire Integration plugin
 * (c) Home of the Sebijk.com
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
class modopenfire extends BMPlugin
{
	/** @var string Pretty-URL: /admin/plugin/modopenfire/ */
	public $admin_route_slug = 'modopenfire';

	public function __construct()
	{
		$this->name				= 'Jabber Openfire-Integration';
		$this->version			= '1.2.0';
		$this->type				= BMPLUGIN_DEFAULT;
		$this->designedfor		= '7.5.0';

		$this->author			= 'Home of the Sebijk.com';
		$this->web				= 'http://www.sebijk.com';
		$this->mail				= 'sebijk@web.de';

		$this->admin_pages		= true;
		$this->admin_page_title	= 'Openfire-Integration';
		$this->admin_page_icon	= 'openfire_icon.png';
	}

	public function Install()
	{
		global $db;

		$DatabaseStructure = array(
			'{pre}mod_openfire' => array(
				'fields' => array(
					array('enableAuth', 'tinyint(1)', 'NO'),
					array('secretkey', 'varchar(255)', 'NO'),
					array('domain', 'varchar(255)', 'NO'),
					array('port', 'int(10)', 'NO'),
					array('https', 'tinyint(1)', 'NO'),
				),
				'indexes' => array(),
			),
		);
		SyncDBStruct($DatabaseStructure);

		$db->Query('REPLACE INTO {pre}mod_openfire (enableAuth, secretkey, domain, port, https) VALUES (?,?,?,?,?);',
			(int)0,
			'YourSecretKey',
			'localhost',
			(int)9091,
			(int)1);

		PutLog('Plugin "'.$this->name.' - '.$this->version.'" wurde erfolgreich installiert.', PRIO_PLUGIN, __FILE__, __LINE__);

		return true;
	}

	public function Uninstall()
	{
		global $db;

		$db->Query('DROP TABLE {pre}mod_openfire;');

		PutLog('Plugin "'.$this->name.' - '.$this->version.'" wurde erfolgreich deinstalliert.', PRIO_PLUGIN, __FILE__, __LINE__);

		return true;
	}

	/**
	 * Tab link for page.tpl ({sessionurl file=$tab.link}).
	 *
	 * @return string
	 */
	protected function _ofTabLink()
	{
		return 'plugin.page.php?plugin=' . rawurlencode($this->internal_name) . '&';
	}

	/**
	 * Pretty admin URL for this plugin.
	 *
	 * @param array<string, mixed> $query
	 * @param bool                 $trailingAmp
	 * @return string
	 */
	protected function _ofAdminUrl(array $query = array(), $trailingAmp = true)
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
	protected function _ofTakeFlashMsg()
	{
		if(!isset($_SESSION['openfire_admin_msg']))
			return null;

		$msg = $_SESSION['openfire_admin_msg'];
		unset($_SESSION['openfire_admin_msg']);

		if(!is_array($msg) || !isset($msg['type'], $msg['text']))
			return null;

		return array(
			'type' => (string)$msg['type'],
			'text' => (string)$msg['text'],
		);
	}

	/**
	 * Save prefs (POST + CSRF via {csrffield} in template).
	 */
	protected function _ofHandlePost()
	{
		global $db, $lang_admin;

		if(!isset($_REQUEST['save']))
			return;

		$db->Query('UPDATE {pre}mod_openfire SET domain=?,secretkey=?,enableAuth=?,port=?,https=?',
			trim((string)($_POST['openfire_domain'] ?? '')),
			trim((string)($_POST['openfire_userservice_secretkey'] ?? '')),
			isset($_POST['openfire_enableAuth']) ? 1 : 0,
			max(1, (int)($_POST['openfire_port'] ?? 9091)),
			isset($_POST['openfire_https']) ? 1 : 0);

		$_SESSION['openfire_admin_msg'] = array(
			'type' => 'success',
			'text' => $lang_admin['openfire_updated_data'],
		);

		SessionRedirect($this->_ofAdminUrl(array(), false));
		exit();
	}

	public function AdminHandler()
	{
		global $db, $tpl, $lang_admin;

		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
			$this->_ofHandlePost();

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['prefs'],
				'link'		=> $this->_ofTabLink(),
				'active'	=> true,
				'icon'		=> '../plugins/templates/images/openfire_logo.png',
			),
		);

		$res = $db->Query('SELECT enableAuth, secretkey, domain, port, https FROM {pre}mod_openfire');
		$openfire_prefs = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$tpl->assign('tabs', $tabs);
		$tpl->assign('ofPlugin', $this->internal_name);
		$tpl->assign('pageURL', $this->_ofAdminUrl(array(), true));
		$tpl->assign('openfire_prefs', $openfire_prefs);
		$tpl->assign('ofMsg', $this->_ofTakeFlashMsg());
		$tpl->assign('page', $this->_templatePath('openfire.plugin.prefs.tpl'));
	}

	public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if($lang == 'deutsch')
		{
			$lang_admin['openfire_domain']		= 'Openfire-Domain';
			$lang_admin['openfire_port']		= 'Openfire Adminport';
			$lang_admin['openfire_https']		= 'HTTPS für Adminbereich nutzen';
			$lang_admin['openfire_secretkey']	= 'Secret Key vom User Service Plugin';
			$lang_admin['openfire_updated_data']	= 'Die Daten wurden erfolgreich aktualisiert!';
		}
		else
		{
			$lang_admin['openfire_domain']		= 'Openfire Domain';
			$lang_admin['openfire_port']		= 'Openfire Adminport';
			$lang_admin['openfire_https']		= 'Use HTTPS for Admin?';
			$lang_admin['openfire_secretkey']	= 'Secret Key of User Service Plugin';
			$lang_admin['openfire_updated_data']	= 'Data successfully updated!';
		}
	}

	public function OnSignup($userid, $usermail)
	{
		global $suEMailLocal, $suPass1, $suEMail, $suFirstname, $suSurname;

		if($this->_enableAuth())
		{
			$benutzername = trim($suEMailLocal);
			$jabber_kennwort = trim($suPass1);
			$voller_name = trim($suFirstname).' '.trim($suSurname);

			$url = $this->_getUrl().'&type=add&username='.$this->_toRawUrl($benutzername).'&password='.$this->_toRawUrl($jabber_kennwort).'&name='.$this->_toRawUrl($voller_name).'&email='.$this->_toRawUrl($suEMail);
			$this->_sendhttp($url);
		}
	}

	public function OnDeleteUser($id)
	{
		global $db;

		if($this->_enableAuth())
		{
			$res = $db->Query('SELECT email FROM {pre}users WHERE id=?', $id);
			$jabber = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			$benutzername = explode('@', $jabber['email']);

			$url = $this->_getUrl().'&type=delete&username='.$this->_toRawUrl($benutzername[0]);
			$this->_sendhttp($url);
		}
	}

	public function OnUserPasswordChange($userID, $oldPasswordMD5, $newPasswordMD5, $newPasswordPlain)
	{
		global $userRow;

		if($this->_enableAuth())
		{
			$voller_name = trim($userRow['vorname']).' '.trim($userRow['nachname']);
			$benutzername = explode('@', $userRow['email']);

			$url = $this->_getUrl().'&type=update&username='.$this->_toRawUrl($benutzername[0]).'&password='.$this->_toRawUrl($newPasswordPlain).'&name='.$this->_toRawUrl($voller_name).'&email='.$this->_toRawUrl($userRow['email']);
			$this->_sendhttp($url);
		}
	}

	protected function _getUrl()
	{
		global $db;

		$res = $db->Query('SELECT https, domain, port, secretkey FROM {pre}mod_openfire');
		$jabber_row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$http_modus = ($jabber_row['https'] == 1) ? 'https' : 'http';

		return $http_modus.'://'.$jabber_row['domain'].':'.$jabber_row['port'].'/plugins/userService/userservice?secret='.$jabber_row['secretkey'];
	}

	protected function _enableAuth()
	{
		global $db;

		$res = $db->Query('SELECT enableAuth FROM {pre}mod_openfire');
		$jabber_row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return ($jabber_row['enableAuth'] ?? 0) == 1;
	}

	protected function _sendhttp($url)
	{
		if(!class_exists('BMHTTP'))
			include B1GMAIL_DIR.'serverlib/http.class.php';

		$http = _new('BMHTTP', array($url));
		$http->DownloadToString();
	}

	protected function _toRawUrl($text)
	{
		return rawurlencode($text);
	}
}

$plugins->registerPlugin('modopenfire');
