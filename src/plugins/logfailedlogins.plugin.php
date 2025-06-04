<?php
/*
 * Log Failed logins plugin
 * (c) 2025 b1gMail.eu Project
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
class BMPlugin_logFailedLogins extends BMPlugin
{
    public function __construct()
    {

        $this->name = 'Log Failed Logins';
        $this->author = 'b1gMail.eu Project';
        $this->web = 'http://www.b1gmail.eu';

        $this->version = '1.0.0';
        $this->designedfor = '7.4.1';

        $this->type = BMPLUGIN_DEFAULT;

    }

    // Installation
    public function Install()
    {
        PutLog('Plugin "'.$this->name.' - '.$this->version.'" installed successfully.', PRIO_PLUGIN, __FILE__, __LINE__);

        return true;
    }

    // Uninstallation
    public function Uninstall()
    {
        PutLog('Plugin "'.$this->name.' - '.$this->version.'" removed.', PRIO_PLUGIN, __FILE__, __LINE__);

        return true;
    }

    public function OnLoginFailed($userMail, $password, $reason)
    {
        global $db;

        $res = $db->Query('(SELECT `id` FROM bm60_users WHERE email=?) UNION (SELECT `user` FROM bm60_aliase WHERE email=? AND login=\'yes\')', $userMail, $userMail);
        $row = $res->FetchArray();
        $userId = $row['id'];
        $res->Free();

        if (!empty($userId)) {
            $failedloginlog = '['.date('c').'] - Web Login '.$userMail.' - Browser '.substr($_SERVER['HTTP_USER_AGENT'], 0, 250).' - IP '.$_SERVER['REMOTE_ADDR'].PHP_EOL;
            file_put_contents(B1GMAIL_DIR.'logs/b1gmail_failedlogins.log', $failedloginlog, FILE_APPEND);
        }
    }
}
// register plugin
$plugins->registerPlugin('BMPlugin_logFailedLogins');