<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
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

require './serverlib/init.inc.php';
require './serverlib/pop3gateway.class.php';
include './serverlib/userpop3gateway.class.php';
include './serverlib/calendar.class.php';

// try to prevent abortion
header('Connection: close');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: Wed, 04 Aug 2004 14:46:00 GMT');
@set_time_limit(0);

// output status
if (!isset($_REQUEST['out']) || $_REQUEST['out'] == 'text') {
    $str = microtime().' - OK';
    //	if(!SERVER_IIS && !DEBUG)
    //		header('Content-Length: ' . strlen($str));
    echo $str;
} elseif (isset($_REQUEST['out']) && $_REQUEST['out'] == 'img') {
    header('Content-Type: image/gif');
    if (!SERVER_IIS) {
        header('Content-Length: '.filesize('res/dummy.gif'));
    }
    readfile('res/dummy.gif');
}
flush();

// fetch user POP3 mails?
if (isset($_REQUEST['sid']) && RequestPrivileges(PRIVILEGES_USER, true)) {
    @session_write_close();
    $userPOP3Gateway = _new('BMUserPOP3Gateway', [$userRow['id'], &$thisUser]);
    $userPOP3Gateway->Run();
}

// check if interval time passed
if ($bm_prefs['last_cron'] < time() - $bm_prefs['cron_interval']) {
    // set up lock
    function ReleaseCronLock()
    {
        global $lockFP;

        flock($lockFP, LOCK_UN);
        fclose($lockFP);
    }
    $lockFileName = B1GMAIL_DIR.'temp/cron.lock';
    $lockFP = fopen($lockFileName, 'w+');
    if (!flock($lockFP, LOCK_EX | LOCK_NB)) {
        exit();
    }
    register_shutdown_function('ReleaseCronLock');

    require './serverlib/cron.inc.php';

    // update last cron run time
    $db->Query('UPDATE {pre}prefs SET last_cron=?',
        time());

    // clean up expired action tokens
    CleanupActionTokens();

    // clean up mail send stats
    CleanupSendStats();

    // clean up mail receive stats
    CleanupRecvStats();

    // clean up saved logins
    CleanupSavedLogins();

    // clean up safe codes
    CleanupSafeCodes();

    // clean up temp file
    CleanupTempFiles();

    // clean up parse cache
    if ($bm_prefs['cache_type'] == CACHE_B1GMAIL) {
        $cacheManager->CleanUp();
    }

    // clean up aliases
    CleanupAliases();

    // clean up cert mails
    CleanupCertMails();

    // clean up webdisk locks
    CleanupWebdiskLocks();

    // clean up notifications
    CleanupNotifications();

    // abuse protect cron
    AbuseCron();

    // reset webdisk traffic
    ResetWebdiskTraffic();

    // send birthday notifications
    ProcessBirthdayNotifications();

    // auto-delete users who never logged in
    ProcessNoSignupAutoDel();

    // delete old mail delivery status entries
    CleanupMailDeliveryStatus();

    // send calendar notifications
    BMCalendar::ProcessNotifications();

    // fetch POP3 mails
    if ($bm_prefs['receive_method'] == 'pop3') {
        $pop3Gateway = _new('BMPOP3Gateway');
        $pop3Gateway->Run();
    }

    // plugin cron
    ModuleFunction('OnCron');

    // store time
    if ($bm_prefs['last_storetime_cron'] < time() - STORETIME_CRON_INTERVAL) {
        // process
        StoreTimeCron();

        // update last store time cron run time
        $db->query('UPDATE {pre}prefs SET last_storetime_cron=?',
            time());
    }

    // auto archive logs
    AutoArchiveLogs();

    // update last cron run time
    $db->Query('UPDATE {pre}prefs SET last_cron=?',
        time());
}
