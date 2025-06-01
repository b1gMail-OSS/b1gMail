<?php

/*
 * Logout Hinweis
 * Copyright (c) 2025 b1gMail Project
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
 */
class LogoutHinweis extends BMPlugin
{
	function __construct()
	{
		// plugin info
		$this->type = BMPLUGIN_DEFAULT;
		$this->name = 'Logout Hinweis';
		$this->author = 'b1gMail Project, M.Cholys';
		$this->version = '1.2.0';
		$this->designedfor = '7.4.1';
		$this->update_url = 'https://service.b1gmail.org/plugin_updates/';
	}

	// Installation
	function Install()
	{
		global $db;

		$db->Query('ALTER TABLE {pre}users ADD loggedout tinyint(4) NOT NULL DEFAULT 1;');
		PutLog('Zeile <loggedout> in Tabelle bm60_users erstellt !',
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		$db->Query('ALTER TABLE {pre}users ADD loggedout2 tinyint(4) NOT NULL DEFAULT 1;');
		PutLog('Zeile <loggedout2> in Tabelle bm60_users erstellt !',
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		return (true);
	}

	// Deinstallation
	function Uninstall()
	{
		global $db;

		$db->Query('ALTER TABLE {pre}users DROP loggedout;');
		PutLog('Zeile <loggedout> in Tabelle bm60_users geloescht !',
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		$db->Query('ALTER TABLE {pre}users DROP loggedout2;');
		PutLog('Zeile <loggedout2> in Tabelle bm60_users geloescht !',
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		// Delete old column
		$res = $db->Query("SHOW COLUMNS FROM {pre}users LIKE 'hinweistext'");
		if($res->RowCount() > 0) {
			$db->Query('ALTER TABLE {pre}users DROP hinweistext;');
			PutLog('Zeile <hinweistext> in Tabelle bm60_users geloescht !',PRIO_PLUGIN,
			__FILE__,
			__LINE__);
		}

			
		return (true);
	}

	/*
	 * Sprachvariablen
	 */
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		if (strpos($lang, 'deutsch') !== false) {
			$lang_custom['loginhinweis_sub'] = 'Logout Hinweis - Text';
			$lang_custom['loginhinweis_text'] = 'Hallo,' . "\n\n" . 'Bei Ihrem letzten Besuch haben Sie vergessen, Ihr Postfach per <b>Abmelden</b> zu schließen.'. "\n\n" .'
					Zur Ihrer eigenen Sicherheit sollten Sie jedoch immer daran denken, Ihr Postfach per <b>Abmelden</b> zu verlassen, damit kein Zugriff auf Ihre persönlichen Daten möglich ist.';
			$lang_user['logouthinweis_title'] = 'Logout Hinweis';
			$lang_user['go_to_mailbox'] = 'Weiter zur Mailbox';
		} else {
			$lang_custom['loginhinweis_sub'] = 'Logout Notice - Text';
			$lang_custom['loginhinweis_text'] = 'Hello,' . "\n\n" . 'During your last visit, you forgot to close your mailbox by click Log out. '. "\n\n" .'
					For your own security, however, you should always remember to log out of your mailbox so that your personal data cannot be accessed.';
			$lang_user['logouthinweis_title'] = 'Logout Notice';
			$lang_user['go_to_mailbox'] = 'Go to Mailbox';
		}
	}

	// Login
	public function OnLogin($userID, $interface = 'web')
	{
		global $db;
		if (INTERFACE_MODE != true) {
			$db->Query('UPDATE {pre}users SET loggedout2=loggedout WHERE id=?', $userID);
			$db->Query('UPDATE {pre}users SET loggedout=0 WHERE id=?', $userID);
		}
	}

    public function FileHandler($file, $action)
    {
        $this->_FileHandlerall($file, $action);
    }

	public function FileHandlerMobile($file, $action)
    {
        $this->_FileHandlerall($file, $action);
    }

	private function _FileHandlerall($file, $action) {
		global $db, $tpl, $lang_user, $lang_custom, $thisUser;
		if (!RequestPrivileges(PRIVILEGES_USER, true)) {
            return;
        }

        if (INTERFACE_MODE != true) {
			$sql = $db->Query('SELECT loggedout2 FROM {pre}users WHERE id=?', $thisUser->_id);
			$row = $sql->FetchArray();
			$loggedout3 = $row['loggedout2'];
			$sql->Free();

			if ($loggedout3 == 0) {
				$sql = $db->Query('UPDATE {pre}users SET loggedout2=1 WHERE id=?', $thisUser->_id);
				$tpl->assign('title', $lang_user['logouthinweis_title']);
				$tpl->assign('msg', nl2br($lang_custom['loginhinweis_text']));
				if ($_SERVER['PHP_SELF'] == '/m/index.php') {  // Wenn mobiles Login
					$tpl->assign('backLink', 'email.php?sid=' . session_id());
				} else {
					$tpl->assign('backLink', 'start.php?sid=' . session_id());
				}
				$tpl->assign('page', $this->_templatePath('logouthinweis.plugin.tpl'));
				$tpl->display('nli/index.tpl');
				exit();
			}
		}
	}

	// Logout
	public function OnLogout($userID)
	{
		global $db;

		// Aenderung fuer logout
		$db->Query("UPDATE {pre}users SET loggedout='1' WHERE id=?", $userID);
	}
}

$plugins->registerPlugin('LogoutHinweis');
