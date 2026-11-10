<?php
/*
 * b1gMail Login Failed Notification
 * Copyright (c) b1gMail.eu Project
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
class BMPlugin_LoginFailed extends BMPlugin
{
    public function __construct()
    {

        $this->name = 'Login Failed Notification';
        $this->author = 'b1gMail Project';
        $this->web = 'http://www.b1gmail.eu';

        $this->version = '1.0.1';
        $this->designedfor = '7.4.1';

        $this->type = BMPLUGIN_DEFAULT;
    }

    // Install
    public function Install()
    {
        PutLog(sprintf('Plugin %s v%s installed',
			$this->name,
			$this->version),
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

        return true;
    }

    // Uninstall
    public function Uninstall()
    {
        PutLog(sprintf('Plugin %s v%s uninstalled',
			$this->name,
			$this->version),
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

        return true;
    }

    // Phrases
    public function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
    {
        if (strpos($lang, 'deutsch') !== false) {
            $lang_admin['text_loginfailed_sub'] = 'Fehlgeschlagener Login - Betreff';
            $lang_admin['text_loginfailed_text'] = 'Fehlgeschlagener Login - Text';
            $lang_custom['loginfailed_sub'] = 'System Meldung: Fehlgeschlagener Login!';
            $lang_custom['loginfailed_text'] = 'Hallo %%vorname%% %%nachname%%,'."\n\n"
                                                        .'jemand hat versucht, sich am %%datum%% um %%uhrzeit%% Zugang zu deinem Postfach (Client: %%agent%%, IP-Adresse: %%ipaddr%%, E-Mail: %%email%%) zu verschaffen.'."\n"
                                                        .'Warst du das? Falls ja, kannst du diese E-Mail ignorieren.'."\n\n"
                                                        .'(Diese E-Mail wurde automatisch erstellt)';
        } else {
            $lang_admin['text_loginfailed_sub'] = 'Login failed - Subject';
            $lang_admin['text_loginfailed_text'] = 'Login failed - Text';
            $lang_custom['loginfailed_sub'] = 'System message: Login Failed!';
            $lang_custom['loginfailed_text'] = 'Hello %%vorname%% %%nachname%%,'."\n\n"
                                                        .'anyone have tried to connect on %%datum%% - %%uhrzeit%% with your Mailbox with the IP Address: %%ipaddr%%, User-Agent: %%agent%% and Email %%email%%.'."\n"
                                                        .'Was that you? So you could ignore this message.'."\n\n"
                                                        .'(This message has been automatically generated)';
        }
    }

    // If someone logs in to the system incorrectly
    public function OnLoginFailed($userMail, $password, $reason)
    {
        global $db, $lang_custom;

        // Check whether the email address used during login belongs to a user; if so, send a notification
        $res = $db->Query('(SELECT `id` FROM bm60_users WHERE email=?) UNION (SELECT `user` FROM bm60_aliase WHERE email=? AND login=\'yes\')', $userMail, $userMail);
        $row = $res->FetchArray();
        $userId = $row['id'];
        $res->Free();

        if (!empty($userId)) {
            // Get First- and Surname from user
            $res = $db->Query('(SELECT `vorname`, `nachname` FROM bm60_users WHERE id=?)', $userId);
            $row = $res->FetchArray();
            $res->Free();

            $from = GetPostmasterMail();

            // Email notification variables
            $vars = [
                            'vorname' => $row['vorname'],
                            'nachname' => $row['nachname'],
                            'datum' => date('d.m.Y'),
                            'uhrzeit' => date('H:i'),
                            'agent' => $_SERVER['HTTP_USER_AGENT'],
                            'ipaddr' => $_SERVER['REMOTE_ADDR'],
                            'email' => $userMail,
                        ];

            // Sent Mail to User
            SystemMail($from, $userMail, $lang_custom['loginfailed_sub'], 'loginfailed_text', $vars, $forUser = -1);
        }
    }
}

// register plugin
$plugins->registerPlugin('BMPlugin_LoginFailed');