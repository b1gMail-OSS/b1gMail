<?php
/*
 * Plugin addalias
 * (c) 2025 b1gMail.eu
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
class addalias extends BMPlugin 
{
    /*
    * Eigenschaften des Plugins
    */
    public function __construct()
	{
		$this->name					= "Add Alias";
		$this->version				= '1.2.0';
        $this->designedfor         	= '7.4.2';
		$this->type					= BMPLUGIN_DEFAULT;
		
		$this->author				= 'b1gMail.eu Project, dotaachen';

		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';
		$this->website				= 'https://www.b1gmail.eu';

		$this->admin_pages			= true;
		$this->admin_page_title		= "Add Alias";
		$this->admin_page_icon		= "addalias_icon.png";
	}

    /*
    * Link und Tabs im Adminbereich 
    */
    public function AdminHandler()
    {
        global $tpl, $lang_admin;

        // Standardaktion setzen
        $action = $_REQUEST['action'] ?? 'page1';

        // Tabs im Adminbereich
        $tabs = [
			[
				'title'		=> $lang_admin['create'],
				'link'		=> $this->_adminLink() . '&action=page1&',
				'active'	=> $action == 'page1',
				'icon'		=> '../plugins/templates/images/addalias_logo.png'
			],
			[
				'title'		=> $lang_admin['faq'],
				'link'		=> $this->_adminLink() . '&action=page2&',
				'active'	=> $action == 'page2',
				'icon'		=> './templates/images/faq32.png'
			],
		];
        $tpl->assign('tabs', $tabs);

        // Plugin aufruf mit Action 
        if ($action === 'page1') {
            $tpl->assign('page', $this->_templatePath('addalias1.pref.tpl'));
            $this->_Page1();
        } elseif ($action === 'page2') {
            $tpl->assign('page', $this->_templatePath('addalias2.pref.tpl'));
        }
    }

    /*
    * Sprachvariablen
    */
    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        global $lang_user;

        $lang_admin['addalias_name'] = 'Add Alias';
        if ($lang === 'deutsch') {
            $lang_admin['addalias_text'] = "Mit diesem Plugin können Sie einzelnen Benutzern einen Alias erstellen.";
            $lang_admin['faq_addalias_question'] = 'Wie erstelle ich einen Alias für einen Benutzer?';
            $lang_admin['faq_addalias_answer'] = 'Wählen Sie die Gruppe aus, in der Benutzer ist, dem Sie den Alias erstellen wollen.<br />
            Nach dem Klicken auf Weiter, werden Ihnen die Benutzer aus der Gruppe angezeigt. Wählen Sie dort den Benutzer aus.<br />
            Danach können Sie entweder die externe Adresse (zum senden) oder die interne Adresse (zum empfangen und senden) eintragen.<br />
            Nach dem Klicken auf "Ausführen" wird der Alias erstellt.';
        } else {
            $lang_admin['addalias_text'] = 'With this plugin you can create Aliases for users via Administration Panel';
            $lang_admin['faq_addalias_question'] = 'How create an alias for an user?';
            $lang_admin['faq_addalias_answer'] = 'Select the group containing the user for whom you want to create the alias.
            After clicking Next, the users from the group will be displayed. Select the user there. <br />
            You can then enter either the external address (for sending) or the internal address (for receiving and sending). <br />
            After clicking on "Execute", the alias is created.';
        }
        $lang_admin['addresstaken'] = $lang_user['addresstaken'];
        $lang_admin['alias'] = $lang_user['alias'];
        $lang_admin['aliastype_1'] = $lang_user['aliastype_1'];
        $lang_admin['aliastype_2'] = $lang_user['aliastype_2'];
    }

    /*
    * Install
    */
    public function Install()
    {
        PutLog('Plugin "' . $this->name . ' - ' . $this->version . '" installed successfully', PRIO_PLUGIN, __FILE__, __LINE__);
        return true;
    }

    /*
    * Uninstall
    */
    public function Uninstall()
    {
        PutLog('Plugin "' . $this->name . ' - ' . $this->version . '" removed.', PRIO_PLUGIN, __FILE__, __LINE__);
        return true;
    }
    /*
    * Gruppen, Benutzer auswählen und Alias erstellen
    */
    private function _Page1()
    {
        global $tpl, $db;

        // Define vars
        $tpl_use = 0;
        $gruppen = [];
        $users = [];

        // Select all groups
        $res = $db->Query('SELECT id, titel FROM {pre}gruppen ORDER BY titel ASC');
        while ($row = $res->FetchArray()) {
            $gruppen[$row['id']] = [
                'id' => $row['id'],
                'titel' => $row['titel'],
            ];
        }
        $res->Free();
		unset($res);

        // Wenn gruppe_hidden benutzt wird, Gruppe füllen
        if (!empty($_REQUEST['gruppe_hidden'])) {
            $_REQUEST['gruppe'] = $_REQUEST['gruppe_hidden'];
        }

        // Wenn Gruppe alle, dann alle Benutzer abfragen
        if (isset($_REQUEST['gruppe']) && $_REQUEST['gruppe'] == -1) {
            $res = $db->Query('SELECT id, email FROM {pre}users ORDER BY email ASC');
        } elseif (isset($_REQUEST['gruppe'])) {
            $res = $db->Query('SELECT id, email FROM {pre}users WHERE gruppe=? ORDER BY email ASC', (int)$_REQUEST['gruppe']);
        }

        // fill array
        if (isset($res)) {
            while ($row = $res->FetchArray()) {
                $users[$row['id']] = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                ];
            }
            $res->Free();
        }

        // Template Variable je nach Fortschritt ändern
        if (isset($_REQUEST['gruppe'])) {
            $tpl_use = 1;
            $_REQUEST['gruppe_hidden'] = $_REQUEST['gruppe'];
        }
        if (isset($_REQUEST['user'])) {
            $tpl_use = 2;
            $domainList = array_merge(GetDomainList(), BMGroup::GetGroupDomains());

            // Added by Sebijk - Aliasdomains der Nutzer abfragen
            $res = $db->Query('SELECT id, saliase FROM {pre}users WHERE id = ?', (int)$_REQUEST['user']);
            $row = $res->FetchArray();
            if (!empty($row['saliase'])) {
                $domainList3 = explode(':', $row['saliase']);
                $domainList = array_merge($domainList, $domainList3);
            }
            $res->Free();

            $tpl->assign('domainList', $domainList);
            $_REQUEST['user_hidden'] = (int)$_REQUEST['user'];
        }

        // Wenn email_domain gefüllt, dann DB speichern
        $tpl_email_locked = false;
        if (isset($_REQUEST['email_domain'])) {
            $tpl_use = 3;
            $emailAddress = !empty($_REQUEST['typ_1_email']) ? $_REQUEST['typ_1_email'] : $_REQUEST['email_local'] . '@' . $_REQUEST['email_domain'];
            $emailAddress = ExtractMailAddresses($emailAddress);

            // Emailadresse besetzt durch Benutzer
            $res = $db->Query('SELECT id FROM {pre}users WHERE email=?', $emailAddress);
            if ($res->RowCount() >= 1) {
                $tpl_email_locked = true;
            }
            $res->Free();

            // Emailadresse besetzt durch Aliase
            $res = $db->Query('SELECT id FROM {pre}aliase WHERE email=?', $emailAddress);
            if ($res->RowCount() >= 1) {
                $tpl_email_locked = true;
            }
            $res->Free();

            if (!$tpl_email_locked) {
                $type = !empty($_REQUEST['typ_1_email']) ? 1 : 3;
                $db->Query('INSERT INTO {pre}aliase(email, user, type, date) VALUES(?, ?, ?, ?)', $emailAddress, (int)$_REQUEST['user_hidden'], $type, (int)time());
            }
            $_REQUEST['gruppe_hidden'] = "";
        }

        // Template Variablen übergeben
        $tpl->assign('gruppen', $gruppen);
        $tpl->assign('users', $users);    
        $tpl->assign('selected_gruppe', ($_REQUEST['gruppe_hidden'] ?? ''));
        $tpl->assign('selected_user', ($_REQUEST['user_hidden'] ?? ''));
        $tpl->assign('tpl_use', $tpl_use);
        $tpl->assign('tpl_email_locked', $tpl_email_locked);
    }
}

/*
 * register plugin
 */
$plugins->registerPlugin('addalias');