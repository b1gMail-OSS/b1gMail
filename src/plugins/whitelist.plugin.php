<?php
/*
 * b1gMail whiteslist plugin
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
 * whitelist plugin.
 */
class WhitelistPlugin extends BMPlugin
{
    public function __construct()
    {
        // plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'Whitelist';
		$this->author				= 'b1gMail Project';
		$this->web					= 'https://www.b1gmail.org/';
		$this->mail					= 'info@b1gmail.org';
		$this->version				= '1.1';

        // group option
        $this->RegisterGroupOption('whitelist',
            FIELD_CHECKBOX,
            'Whitelist?');
    }

    public function OnReceiveMail(&$mail, &$mailbox, &$user)
    {
        global $db;

        // check input data
        if (!is_object($mail) || !is_object($user)) {
            PutLog('WhitelistPlugin: $mail or $user invalid', PRIO_DEBUG, __FILE__, __LINE__);

            return BM_OK;
        }

        // check if whitelist is enabled for user's group
        $userGroupID = $user->_row['gruppe'];
        if (!$this->GetGroupOptionValue('whitelist', $userGroupID)) {
            return BM_OK;
        }

        // lookup sender addresses in addressbook
        $from = ExtractMailAddresses($mail->GetHeaderValue('from'));
        $res = $db->Query('SELECT COUNT(*) FROM {pre}adressen WHERE `user`=? AND (`email` IN ? OR `work_email` IN ?)',
            $user->_id,
            $from,
            $from);
        list($addressBookEntryCount) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        // return
        if ($addressBookEntryCount > 0) {
            PutLog(sprintf('WhitelistPlugin: Accepted email for user #%d', $user->_id),
                PRIO_DEBUG,
                __FILE__,
                __LINE__);

            return BM_OK;
        } else {
            PutLog(sprintf('WhitelistPlugin: Rejected email for user #%d', $user->_id),
                PRIO_DEBUG,
                __FILE__,
                __LINE__);

            return BM_BLOCK;
        }
    }
}

/*
 * register plugin
 */
$plugins->registerPlugin('WhitelistPlugin');
