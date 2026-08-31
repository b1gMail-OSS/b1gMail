<?php
/*
 * b1gMail PLZ editor plugin
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
 * PLZ editor plugin.
 */
class PLZEditorPlugin extends BMPlugin
{
	/** @var string Pretty-URL: /admin/plugin/plzeditorplugin/ */
	public $admin_route_slug = 'plzeditorplugin';

	public function __construct()
	{
		$this->type				= BMPLUGIN_DEFAULT;
		$this->name				= 'PLZ-Editor';
		$this->author			= 'b1gMail Project';
		$this->mail				= 'info@b1gmail.org';
		$this->version			= '1.6.0';
		$this->designedfor		= '7.5.0';
		$this->update_url		= '';
		$this->website			= 'https://www.b1gmail.org/';

		$this->admin_pages		= true;
		$this->admin_page_title	= 'PLZ-Editor';
	}

	public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if($lang == 'deutsch')
		{
			$lang_admin['plzeditor_title']			= 'PLZ-Editor';
			$lang_admin['plzeditor_test']			= 'PLZ testen';
			$lang_admin['plzeditor_add']			= 'PLZ hinzuf&uuml;gen';
			$lang_admin['plzeditor_zip']			= 'PLZ';
			$lang_admin['plzeditor_city']			= 'Ort';
			$lang_admin['plzeditor_test_success']	= 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde in der PLZ-Datenbank von %s gefunden.';
			$lang_admin['plzeditor_test_error']		= 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde <b>nicht</b> in der PLZ-Datenbank von %s gefunden.';
			$lang_admin['plzeditor_add_success']	= 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde in die PLZ-Datenbank von %s eingef&uuml;gt.';
			$lang_admin['plzeditor_add_error']		= 'Das PLZ/Ort-Paar konnte nicht hinzugef&uuml;gt werden. Bitte stellen Sie sicher, dass die Datei <code>%s</code> Schreibrechte hat (CHMOD 777).';
			$lang_admin['plzeditor_invalid']		= 'Bitte PLZ, Ort und Land vollst&auml;ndig ausw&auml;hlen.';
		}
		else
		{
			$lang_admin['plzeditor_title']			= 'ZIP editor';
			$lang_admin['plzeditor_test']			= 'Test ZIP code';
			$lang_admin['plzeditor_add']			= 'Add ZIP code';
			$lang_admin['plzeditor_zip']			= 'ZIP';
			$lang_admin['plzeditor_city']			= 'City';
			$lang_admin['plzeditor_test_success']	= 'The ZIP/city pair &quot;%s %s&quot; exists in the ZIP database of %s.';
			$lang_admin['plzeditor_test_error']		= 'The ZIP/city pair &quot;%s %s&quot; <b>does not</b> exist in the ZIP database of %s.';
			$lang_admin['plzeditor_add_success']	= 'The ZIP/city pair &quot;%s %s&quot; has been added to the ZIP database of %s.';
			$lang_admin['plzeditor_add_error']		= 'The ZIP/city pair could not be added. Please ensure that the file <code>%s</code> has write permissions (CHMOD 777).';
			$lang_admin['plzeditor_invalid']		= 'Please enter ZIP, city and select a country.';
		}
	}

	/**
	 * @return list<string>
	 */
	protected function _plzMainActions()
	{
		return array('editor');
	}

	/**
	 * Pretty-URL path segment (do) → internal main tab (action).
	 */
	protected function _plzNormalizeRequest()
	{
		$mainActions = $this->_plzMainActions();

		if(!isset($_REQUEST['action']) || $_REQUEST['action'] === '')
			$_REQUEST['action'] = 'editor';

		$pathDo = isset($_REQUEST['do']) ? strtolower((string)$_REQUEST['do']) : '';
		$pathAction = isset($_REQUEST['action']) ? strtolower((string)$_REQUEST['action']) : '';

		if($pathDo !== '' && in_array($pathDo, $mainActions, true))
		{
			if($pathAction !== '' && !in_array($pathAction, $mainActions, true))
			{
				$sub = $_REQUEST['action'];
				$_REQUEST['action'] = $_REQUEST['do'];
				$_REQUEST['do'] = $sub;
			}
			else
			{
				$_REQUEST['action'] = $_REQUEST['do'];
				unset($_REQUEST['do']);
			}
		}

		if(isset($_REQUEST['action']) && $_REQUEST['action'] !== '')
			$_REQUEST['action'] = strtolower((string)$_REQUEST['action']);

		if(isset($_REQUEST['do']) && $_REQUEST['do'] !== '')
			$_REQUEST['do'] = strtolower((string)$_REQUEST['do']);
	}

	/**
	 * Tab link for page.tpl ({sessionurl file=$tab.link}).
	 *
	 * @param string $action
	 * @return string
	 */
	protected function _plzTabLink($action = 'editor')
	{
		$url = 'plugin.page.php?plugin=' . rawurlencode($this->internal_name);
		if($action !== '' && $action !== 'editor')
			$url .= '&do=' . rawurlencode($action);
		$url .= '&';

		return $url;
	}

	/**
	 * Pretty admin URL for this plugin.
	 *
	 * @param string|null          $subDo   test, add, …
	 * @param array<string, mixed> $query
	 * @param bool                 $trailingAmp
	 * @return string
	 */
	protected function _plzAdminUrl($subDo = null, array $query = array(), $trailingAmp = true)
	{
		$params = array_merge(array('plugin' => $this->internal_name, 'do' => 'editor'), $query);

		if($subDo !== null && $subDo !== '')
			$params['action'] = $subDo;

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
	protected function _plzTakeFlashMsg()
	{
		if(!isset($_SESSION['plzeditor_admin_msg']))
			return null;

		$msg = $_SESSION['plzeditor_admin_msg'];
		unset($_SESSION['plzeditor_admin_msg']);

		if(!is_array($msg) || !isset($msg['type'], $msg['text']))
			return null;

		return array(
			'type' => (string)$msg['type'],
			'text' => (string)$msg['text'],
		);
	}

	/**
	 * POST: test / add ZIP (CSRF via {csrffield} in template).
	 */
	protected function _plzHandlePost()
	{
		global $lang_admin;

		if(($_REQUEST['action'] ?? '') !== 'editor')
			return;

		$sub = $_REQUEST['do'] ?? '';
		if(!in_array($sub, array('test', 'add'), true))
			return;

		$zip = trim((string)($_REQUEST['zip'] ?? ''));
		$city = trim((string)($_REQUEST['city'] ?? ''));
		$country = (int)($_REQUEST['country'] ?? 0);
		$countryList = CountryList();

		if($zip === '' || $city === '' || !isset($countryList[$country]))
		{
			$_SESSION['plzeditor_admin_msg'] = array(
				'type' => 'danger',
				'text' => $lang_admin['plzeditor_invalid'],
			);
			SessionRedirect($this->_plzAdminUrl(null, array(), false));
			exit();
		}

		$countryName = $countryList[$country];
		$zipHtml = htmlspecialchars($zip, ENT_QUOTES, 'UTF-8');
		$cityHtml = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');

		if($sub === 'test')
		{
			$result = ZIPCheck($zip, $city, $country);
			$_SESSION['plzeditor_admin_msg'] = array(
				'type' => $result ? 'success' : 'danger',
				'text' => sprintf(
					$result ? $lang_admin['plzeditor_test_success'] : $lang_admin['plzeditor_test_error'],
					$zipHtml,
					$cityHtml,
					$countryName),
			);
		}
		else if($sub === 'add')
		{
			$result = $this->_ZIPAdd($zip, $city, $country);
			$_SESSION['plzeditor_admin_msg'] = array(
				'type' => $result ? 'success' : 'danger',
				'text' => $result
					? sprintf($lang_admin['plzeditor_add_success'], $zipHtml, $cityHtml, $countryName)
					: sprintf($lang_admin['plzeditor_add_error'], 'plz/' . $country . '.plz'),
			);
		}

		SessionRedirect($this->_plzAdminUrl(null, array(), false));
		exit();
	}

	public function AdminHandler()
	{
		global $tpl, $bm_prefs, $lang_admin;

		$this->_plzNormalizeRequest();

		if(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST')
			$this->_plzHandlePost();

		$action = $_REQUEST['action'] ?? 'editor';

		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['plzeditor_title'],
				'relIcon'	=> 'country32.png',
				'link'		=> $this->_plzTabLink('editor'),
				'active'	=> $action === 'editor',
			),
		);

		$tpl->assign('tabs', $tabs);
		$tpl->assign('plzPlugin', $this->internal_name);
		$tpl->assign('pageURL', $this->_plzAdminUrl(null, array(), true));

		if($action === 'editor')
			$this->_plzEditorPage($bm_prefs);
	}

	protected function _plzEditorPage($bm_prefs)
	{
		global $tpl;

		$tpl->assign('plzFiles', $this->_getPLZFiles());
		$tpl->assign('defaultCountryID', $bm_prefs['std_land']);
		$tpl->assign('plzMsg', $this->_plzTakeFlashMsg());
		$tpl->assign('page', $this->_templatePath('plzeditor.editor.tpl'));
	}

	protected function _getPLZFiles()
	{
		$result = array();
		$countries = CountryList();
		$plzDir = B1GMAIL_DIR . 'plz/';

		if(!is_dir($plzDir))
			return $result;

		$d = dir($plzDir);
		while(($filename = $d->read()) !== false)
		{
			if(substr($filename, -4) != '.plz')
				continue;

			$countryID = substr($filename, 0, -4);
			if(isset($countries[$countryID]))
				$result[$countryID] = $countries[$countryID];
		}
		$d->close();

		return $result;
	}

	protected function _ZIPAdd($plz, $ort, $staat)
	{
		if(ZIPCheck($plz, $ort, $staat))
			return true;

		$filePath = B1GMAIL_DIR . 'plz/' . (int)$staat . '.plz';

		if(!file_exists($filePath) || !is_writeable($filePath))
			return false;

		$strip_chars = array(',', ';', '-', '?', ':', '?', '1', ' ', 'ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ae', 'oe', 'ue', 'AE', 'OE', 'UE', 'Ae', 'Oe', 'Ue');

		$plz = preg_replace('/^([0]*)/', '', $plz);
		$ort = strtolower($ort);
		$ort = str_replace($strip_chars, '', $ort);
		$hash = $plz . soundex($ort);
		$hash = crc32($hash);
		$hash = pack('i', $hash);

		$fp = fopen($filePath, 'ab');
		fwrite($fp, $hash, 4);
		fclose($fp);

		return true;
	}
}

$plugins->registerPlugin('PLZEditorPlugin');
