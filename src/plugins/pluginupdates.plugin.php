<?php
/*
 * b1gMail Plugin Update Notifier Plugin
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
 * Plugin update notifier plugin.
 */
class PluginUpdatesPlugin extends BMPlugin
{
    public function __construct()
    {
        // plugin info
        $this->type = BMPLUGIN_DEFAULT;
        $this->name = 'Plugin Updates';
        $this->author = 'b1gMail Project';
        $this->mail = 'info@b1gmail.org';
        $this->version = '1.2';
        $this->update_url = 'https://service.b1gmail.org/plugin_updates/';
        $this->website = 'https://www.b1gmail.org/';
    }

    public function Install()
    {
        global $db;

        // db struct
        $databaseStructure =                      // checksum: 3fcac3f06a07e9ebdda20613c81ebec2
              'YToxOntzOjIyOiJibTYwX21vZF9wbHVnaW51cGRhdGVzIjthOjI6e3M6NjoiZmllbGRzIjthOjM'
            .'6e2k6MDthOjY6e2k6MDtzOjc6Im1vZE5hbWUiO2k6MTtzOjExOiJ2YXJjaGFyKDMyKSI7aToyO3'
            .'M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O3M6MDoiIjtpOjU7czowOiIiO31pOjE7YTo2OntpO'
            .'jA7czoxMzoibGF0ZXN0VmVyc2lvbiI7aToxO3M6MTA6InZhcmNoYXIoOCkiO2k6MjtzOjI6Ik5P'
            .'IjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjk6Imxhc3RDaGV'
            .'jayI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6Ik1VTCI7aTo0O3M6MT'
            .'oiMCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToyOntzOjc6IlBSSU1BUlkiO2E6MTp7a'
            .'TowO3M6NzoibW9kTmFtZSI7fXM6OToibGFzdENoZWNrIjthOjE6e2k6MDtzOjk6Imxhc3RDaGVj'
            .'ayI7fX19fQ==';
        $databaseStructure = unserialize(base64_decode($databaseStructure));

        // sync struct
        SyncDBStruct($databaseStructure);

        // log
        PutLog(sprintf('%s v%s installed',
            $this->name,
            $this->version),
            PRIO_PLUGIN,
            __FILE__,
            __LINE__);

        return true;
    }

    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if ($lang == 'deutsch') {
            $lang_admin['pluginupd_notification'] = 'F&uuml;r die folgenden Plugins stehen Updates zur Verf&uuml;gung:';
        } else {
            $lang_admin['pluginupd_notification'] = 'Updates for the following plugins are available:';
        }
    }

    public function OnCron()
    {
        global $plugins, $db;

        $table = [];
        $res = $db->Query('SELECT `modName`,`lastCheck`,`latestVersion` FROM {pre}mod_pluginupdates');
        while ($row = $res->FetchArray()) {
            $table[$row['modName']] = $row;
        }
        $res->Free();

        $startTime = time();

        foreach ($plugins->_plugins as $className => $pluginInfo) {
            if (!isset($table[$className])
                || $table[$className]['lastCheck'] + 3 * TIME_ONE_HOUR <= time()) {
                $latestVersion = '';
                $resultCode = $plugins->callFunction('CheckForUpdates', $className, false, [&$latestVersion]);

                $db->Query('REPLACE INTO {pre}mod_pluginupdates(`modName`,`lastCheck`,`latestVersion`) VALUES(?,?,?)',
                    $className,
                    time(),
                    $latestVersion);
            }

            // allow max. 2 seconds for update checking
            if (time() > $startTime + 2) {
                break;
            }
        }
    }

    public function getNotices()
    {
        global $db, $plugins, $lang_admin;

        $result = [];

        $outdatedPlugins = [];
        $res = $db->Query('SELECT `modName`,`latestVersion` FROM {pre}mod_pluginupdates WHERE `latestVersion`!=? ORDER BY `modName` ASC',
            '');
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            if (isset($plugins->_plugins[$row['modName']])) {
                $pluginVersion = $plugins->getParam('version', $row['modName']);

                if ($plugins->callFunction('IsVersionNewer', $row['modName'], false, [$row['latestVersion'], $pluginVersion])) {
                    $outdatedPlugins[] = [
                        'name' => $plugins->getParam('name', $row['modName']),
                        'website' => $plugins->getParam('website', $row['modName']),
                        'installed' => $plugins->getParam('version', $row['modName']),
                        'available' => $row['latestVersion'],
                    ];
                }
            }
        }
        $res->Free();

        if (count($outdatedPlugins) > 0) {
            $text = $lang_admin['pluginupd_notification'].' ';
            foreach ($outdatedPlugins as $plugin) {
                $text .= sprintf('<b><a href="%s" target="_blank">%s</a></b> (%s), ',
                    $plugin['website'],
                    HTMLFormat($plugin['name']),
                    HTMLFormat($plugin['available']));
            }
            $text = substr($text, 0, -2);

            $result[] = [
                'type' => 'info',
                'text' => $text,
                'link' => 'plugins.php?action=updates&',
            ];
        }

        return $result;
    }
}

/*
 * register plugin
 */
$plugins->registerPlugin('PluginUpdatesPlugin');
