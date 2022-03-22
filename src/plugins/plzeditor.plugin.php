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
    public function __construct()
    {
        // plugin info
        $this->type = BMPLUGIN_DEFAULT;
        $this->name = 'PLZ-Editor';
        $this->author = 'b1gMail Project';
        $this->mail = 'info@b1gmail.org';
        $this->version = '1.5';
        $this->update_url = 'https://service.b1gmail.org/plugin_updates/';
        $this->website = 'https://www.b1gmail.org/';

        $this->admin_pages = true;
        $this->admin_page_title = 'PLZ-Editor';
    }

    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang == 'deutsch') {
            $lang_admin['plzeditor_title'] = 'PLZ-Editor';
            $lang_admin['plzeditor_test'] = 'PLZ testen';
            $lang_admin['plzeditor_add'] = 'PLZ hinzuf&uuml;gen';
            $lang_admin['plzeditor_zip'] = 'PLZ';
            $lang_admin['plzeditor_city'] = 'Ort';
            $lang_admin['plzeditor_test_success'] = 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde in der PLZ-Datenbank von %s gefunden.';
            $lang_admin['plzeditor_test_error'] = 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde <b>nicht</b> in der PLZ-Datenbank von %s gefunden.';
            $lang_admin['plzeditor_add_success'] = 'Das PLZ/Ort-Paar &quot;%s %s&quot; wurde in die PLZ-Datenbank von %s eingef&uuml;gt.';
            $lang_admin['plzeditor_add_error'] = 'Das PLZ/Ort-Paar konnte nicht hinzugef&uuml;gt werden. Bitte stellen Sie sicher, dass die Datei <code>%s</code> Schreibrechte hat (CHMOD 777).';
        } else {
            $lang_admin['plzeditor_title'] = 'ZIP editor';
            $lang_admin['plzeditor_test'] = 'Test ZIP code';
            $lang_admin['plzeditor_add'] = 'Add ZIP code';
            $lang_admin['plzeditor_zip'] = 'ZIP';
            $lang_admin['plzeditor_city'] = 'City';
            $lang_admin['plzeditor_test_success'] = 'The ZIP/city pair &quot;%s %s&quot; exists in the ZIP database of %s.';
            $lang_admin['plzeditor_test_error'] = 'The ZIP/city pair &quot;%s %s&quot; <b>does not</b> exist in the ZIP database of %s.';
            $lang_admin['plzeditor_add_success'] = 'The ZIP/city pair &quot;%s %s&quot; has been added to the ZIP database of %s.';
            $lang_admin['plzeditor_add_error'] = 'The ZIP/city pair could not be added. Please ensure that the file <code>%s</code> has write permissions (CHMOD 777).';
        }
    }

    public function AdminHandler()
    {
        global $tpl, $bm_prefs, $lang_admin;

        if (!isset($_REQUEST['action'])) {
            $_REQUEST['action'] = 'editor';
        }

        $tabs = [
            0 => [
                'title' => $lang_admin['plzeditor_title'],
                'link' => $this->_adminLink().'&',
                'active' => $_REQUEST['action'] == 'editor',
            ],
        ];

        $countryList = CountryList();

        $tpl->assign('tabs', $tabs);

        if ($_REQUEST['action'] == 'editor') {
            if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'test') {
                $result = ZIPCheck(trim($_REQUEST['zip']),
                    trim($_REQUEST['city']),
                    (int) $_REQUEST['country']);

                if ($result) {
                    $tpl->assign('success', sprintf($lang_admin['plzeditor_test_success'],
                        htmlentities(trim($_REQUEST['zip'])),
                        htmlentities(trim($_REQUEST['city'])),
                        $countryList[$_REQUEST['country']]));
                } else {
                    $tpl->assign('error', sprintf($lang_admin['plzeditor_test_error'],
                        htmlentities(trim($_REQUEST['zip'])),
                        htmlentities(trim($_REQUEST['city'])),
                        $countryList[$_REQUEST['country']]));
                }
            } elseif (isset($_REQUEST['do']) && $_REQUEST['do'] == 'add') {
                $result = $this->_ZIPAdd(trim($_REQUEST['zip']),
                    trim($_REQUEST['city']),
                    (int) $_REQUEST['country']);

                if ($result) {
                    $tpl->assign('success', sprintf($lang_admin['plzeditor_add_success'],
                        htmlentities(trim($_REQUEST['zip'])),
                        htmlentities(trim($_REQUEST['city'])),
                        $countryList[$_REQUEST['country']]));
                } else {
                    $tpl->assign('error', sprintf($lang_admin['plzeditor_add_error'],
                        'plz/'.(int) $_REQUEST['country'].'.plz'));
                }
            }

            $plzFiles = $this->_getPLZFiles();

            $tpl->assign('pageURL', $this->_adminLink());
            $tpl->assign('plzFiles', $plzFiles);
            $tpl->assign('defaultCountryID', $bm_prefs['std_land']);
            $tpl->assign('page', $this->_templatePath('plzeditor.editor.tpl'));
        }
    }

    private function _getPLZFiles()
    {
        $result = [];
        $countries = CountryList();
        $plzDir = B1GMAIL_DIR.'plz/';

        $d = dir($plzDir);
        while ($filename = $d->read()) {
            if (substr($filename, -4) != '.plz') {
                continue;
            }

            $countryID = substr($filename, 0, -4);
            if (isset($countries[$countryID])) {
                $result[$countryID] = $countries[$countryID];
            }
        }
        $d->close();

        return $result;
    }

    private function _ZIPAdd($plz, $ort, $staat)
    {
        if (ZIPCheck($plz, $ort, $staat)) {
            return true;
        }

        $filePath = B1GMAIL_DIR.'plz/'.(int) $staat.'.plz';

        if (!file_exists($filePath) || !is_writeable($filePath)) {
            return false;
        }

        $strip_chars = [',', ';', '-', '?', ':', '?', '1', ' ', 'ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ae', 'oe', 'ue', 'AE', 'OE', 'UE', 'Ae', 'Oe', 'Ue'];

        $plz = preg_replace('/^([0]*)/', '', $plz);
        $ort = strtolower($ort);
        $ort = str_replace($strip_chars, '', $ort);
        $hash = $plz.soundex($ort);
        $hash = crc32($hash);
        $hash = pack('i', $hash);

        $fp = fopen($filePath, 'ab');
        fwrite($fp, $hash, 4);
        fclose($fp);

        return true;
    }
}

/*
 * register plugin
 */
$plugins->registerPlugin('PLZEditorPlugin');
