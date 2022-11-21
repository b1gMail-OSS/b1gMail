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

define('ADMIN_MODE', true);
include '../serverlib/init.inc.php';
if (defined('TOOLBOX_SERVER')) {
    $toolbox_serverurl = TOOLBOX_SERVER;
}
else $toolbox_serverurl = '';

// tables
$customTextsHTML = [
//	'imprint'						=> true
];
$permsTable = [
    'overview' => $lang_admin['overview'],
    'users' => $lang_admin['users'],
    'groups' => $lang_admin['groups'],
    'workgroups' => $lang_admin['workgroups'],
    'activity' => $lang_admin['activity'],
    'abuse' => $lang_admin['abuseprotect'],
    'newsletter' => $lang_admin['newsletter'],
    'payments' => $lang_admin['payments'],
    'optimize' => $lang_admin['optimize'],
    'maintenance' => $lang_admin['maintenance'],
    'stats' => $lang_admin['stats'],
    'logs' => $lang_admin['logs'],
];
$fieldTypeTable = [
    FIELD_CHECKBOX => $lang_admin['checkbox'],
    FIELD_DROPDOWN => $lang_admin['dropdown'],
    FIELD_RADIO => $lang_admin['radio'],
    FIELD_TEXT => $lang_admin['text'],
    FIELD_DATE => $lang_admin['date'],
];
$pluginTypeTable = [
    BMPLUGIN_DEFAULT => $lang_admin['module'],
    BMPLUGIN_FILTER => $lang_admin['filter'],
    BMPLUGIN_WIDGET => $lang_admin['widget'],
];
$statusTable = [
    'yes' => $lang_admin['locked'],
    'no' => $lang_admin['active'],
    'locked' => $lang_admin['notactivated'],
    'delete' => $lang_admin['deleted'],
    'registered' => $lang_admin['registered'],
];
$statusImgTable = [
    'yes' => 'locked',
    'no' => 'active',
    'locked' => 'notactivated',
    'delete' => 'deleted',
    'registered' => 'nologin',
];
$aliasTypeTable = [
    ALIAS_RECIPIENT => $lang_admin['receive'],
    ALIAS_SENDER => $lang_admin['send'],
    ALIAS_SENDER | ALIAS_RECIPIENT => $lang_admin['send'].', '.$lang_admin['receive'],
    ALIAS_SENDER | ALIAS_PENDING => $lang_admin['notconfirmed'],
];
$ruleActionTable = [
    RECVRULE_ACTION_ISRECIPIENT => $lang_admin['isrecipient'],
    RECVRULE_ACTION_SETRECIPIENT => $lang_admin['setrecipient'],
    RECVRULE_ACTION_ADDRECIPIENT => $lang_admin['addrecipient'],
    RECVRULE_ACTION_DELETE => $lang_admin['delete'],
    RECVRULE_ACTION_BOUNCE => $lang_admin['bounce'],
    RECVRULE_ACTION_MARKSPAM => $lang_admin['markspam'],
    RECVRULE_ACTION_MARKINFECTED => $lang_admin['markinfected'],
    RECVRULE_ACTION_SETINFECTION => $lang_admin['setinfection'],
    RECVRULE_ACTION_MARKREAD => $lang_admin['markread'],
];
$ruleTypeTable = [
    RECVRULE_TYPE_INACTIVE => $lang_admin['inactive'],
    RECVRULE_TYPE_RECEIVERULE => $lang_admin['receiverule'],
    RECVRULE_TYPE_CUSTOMRULE => $lang_admin['custom'],
];
$faqRequirementTable = [
    'responder' => $lang_admin['autoresponder'],
    'forward' => $lang_admin['forward'],
    'mail2sms' => $lang_admin['mail2sms'],
    'pop3' => $lang_admin['pop3'],
    'imap' => $lang_admin['imap'],
    'webdav' => $lang_admin['webdav'],
    'wap' => $lang_admin['mobileaccess'],
    'checker' => $lang_admin['mailchecker'],
    'webdisk' => $lang_admin['webdisk'],
    'share' => $lang_admin['wdshare'],
    'syncml' => $lang_admin['syncml'],
    'organizerdav' => $lang_admin['organizerdav'],
    'ftsearch' => $lang_admin['ftsearch'],
];
$lockedTypeTable = [
    'start' => $lang_admin['startswith'],
    'mitte' => $lang_admin['contains'],
    'ende' => $lang_admin['endswith'],
    'gleich' => $lang_admin['isequal'],
];
$backupTables = [
    'prefs' => ['prefs', 'ads', 'codes', 'extensions', 'faq', 'gruppen',
                            'locked', 'mods', 'profilfelder', 'recvrules', 'smsgateways',
                            'smstypen', 'staaten', 'texts', 'workgroups', 'workgroups_member',
                            'groupoptions', ],
    'stats' => ['stats'],
    'users' => ['users', 'aliase', 'autoresponder', 'filter', 'filter_actions', 'filter_conditions',
                            'folder_conditions', 'folders', 'pop3', 'signaturen', 'smsend', 'userprefs', ],
    'organizer' => ['adressen', 'adressen_gruppen', 'adressen_gruppen_member', 'dates', 'dates_attendees',
                            'dates_groups', 'notes', 'tasks', ],
    'mails' => ['mails', 'certmails'],
    'webdisk' => ['diskfiles', 'diskfolders', 'diskprops'],
];

// files and folders that should have write permissions
$writeableFiles = [
    'admin/templates/cache/',
    'languages/',
    'logs/',
    'plugins/',
    'plugins/templates/',
    'plugins/templates/images/',
    'plugins/js/',
    'plugins/css/',
    'temp/',
    'temp/session/',
    'temp/cache/',
    'templates/'.$bm_prefs['template'].'/cache/',
];

// htaccess files that should exist
$htaccessFiles = [
    B1GMAIL_DATA_DIR.'.htaccess',
    B1GMAIL_REL.'logs/.htaccess',
    B1GMAIL_REL.'temp/.htaccess',
];

/**
 * check if admin is allowed to do sth.
 *
 * @param string $priv Privilege name
 *
 * @return bool
 */
function AdminAllowed($priv)
{
    global $adminRow;

    return $adminRow['type'] == 0 || isset($adminRow['privileges'][$priv]);
}

/**
 * require privilege.
 *
 * @param string $priv
 */
function AdminRequirePrivilege($priv)
{
    if (!AdminAllowed($priv)) {
        DisplayError(0x02, 'Unauthorized', 'You are not authorized to view or change this dataset or page. Possible reasons are too few permissions or an expired session.',
            sprintf("Requested privileges:\n%s",
                $priv),
            __FILE__,
            __LINE__);
        exit();
    }
}

/**
 * get stat data.
 *
 * @param mixed $types Stat type(s)
 * @param int   $time  Stat time
 *
 * @return array
 */
function GetStatData($types, $time)
{
    global $db;

    // load class, if needed
    if (!class_exists('BMCalendar')) {
        include B1GMAIL_DIR.'serverlib/calendar.class.php';
    }

    // types?
    if (!is_array($types)) {
        $types = [$types];
    }
    $typeList = '\''.implode('\',\'', $types).'\'';

    // pepare result array
    $result = $falseArray = $nullArray = [];
    foreach ($types as $type) {
        $nullArray[$type] = 0;
    }
    foreach ($types as $type) {
        $falseArray[$type] = false;
    }
    for ($i = 1; $i <= BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); ++$i) {
        $result[(int) $i] = (mktime(0, 0, 0, date('m', $time), $i, date('Y', $time)) > time()) ? $falseArray : $nullArray;
    }

    // fetch stats from DB
    $res = $db->Query('SELECT typ,d,SUM(anzahl) AS anzahlSum FROM {pre}stats WHERE typ IN ('.$typeList.') AND m=? AND y=? GROUP BY d ORDER BY d ASC',
        date('m', $time),
        date('Y', $time));
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $result[(int) $row['d']][$row['typ']] = in_array($row['typ'], ['wd_down', 'wd_up'])
                                                ? round($row['anzahlSum'] / 1024, 2)
                                                : $row['anzahlSum'];
    }
    $res->Free();

    return $result;
}

/**
 * get categorized space usage.
 *
 * @return array
 */
function GetCategorizedSpaceUsage()
{
    global $backupTables, $db, $mysql;

    // get table sizes
    $tableSizes = [];
    $res = $db->Query('SHOW TABLE STATUS');
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (substr($row['Name'], 0, strlen($mysql['prefix'])) == $mysql['prefix']) {
            if ($row['Engine'] == 'InnoDB') {
                $val = $row['Data_length'];
            } else {
                $val = $row['Data_length'] - $row['Data_free'];
            }
            $tableSizes[substr($row['Name'], strlen($mysql['prefix']))] = $val;
        }
    }
    $res->Free();

    // estimate sizes
    $sizes = [];
    foreach ($backupTables as $key => $tables) {
        foreach ($tables as $table) {
            if (isset($sizes[$key])) {
                $sizes[$key] += $tableSizes[$table];
            } else {
                $sizes[$key] = $tableSizes[$table];
            }
        }
    }

    // data size for mails + webdisk
    $res = $db->Query('SELECT SUM(size) FROM {pre}mails');
    list($emailSize) = $res->FetchArray(MYSQLI_NUM);
    $res->Free();
    $res = $db->Query('SELECT SUM(size) FROM {pre}diskfiles');
    list($diskSize) = $res->FetchArray(MYSQLI_NUM);
    $res->Free();
    $sizes['mails'] += $emailSize;
    $sizes['webdisk'] += $diskSize;

    // return
    return $sizes;
}

/**
 * get categorizes space usage.
 *
 * @return array
 */
function GetGroupSpaceUsage()
{
    global $db, $mysql;

    $sizes = [];

    // get groups
    $res = $db->Query('SELECT id,titel FROM {pre}gruppen ORDER BY titel ASC');
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        // get sizes
        $res2 = $db->Query('SELECT SUM(mailspace_used),SUM(diskspace_used),COUNT(*) FROM {pre}users WHERE gruppe=?',
            $row['id']);
        list($mailSpace, $diskSpace, $userCount) = $res2->FetchArray(MYSQLI_NUM);
        $res2->Free();
        $sizes[$row['id']] = [
            'title' => $row['titel'],
            'users' => $userCount,
            'size' => $mailSpace + $diskSpace,
        ];
    }
    $res->Free();

    // return
    return $sizes;
}

/**
 * load toolbox config descriptors.
 *
 * @return array
 */
function LoadTbxConfigDescriptors()
{
    global $lang_admin, $lang_user, $lang_client, $bm_prefs;

    $tbxConfig = [];
    include B1GMAIL_DIR.'serverlib/toolbox.config.php';

    function cmpTbxConfig($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        list($aMajor, $aMinor) = explode('.', $a);
        list($bMajor, $bMinor) = explode('.', $b);

        return ($aMajor * 1000 + $aMinor) - ($bMajor * 1000 + $bMinor);
    }

    uksort($tbxConfig, 'cmpTbxConfig');

    return $tbxConfig;
}

/**
 * delete an user and associated data.
 *
 * @param int $userID
 */
function DeleteUser($userID, $qAddAND = '')
{
    global $db;

    if ($userID <= 0) {
        return false;
    }

    // get mail address
    $res = $db->Query('SELECT email FROM {pre}users WHERE id=?'.$qAddAND,
        $userID);
    if ($res->RowCount() == 0) {
        return false;
    }
    list($userMail) = $res->FetchArray(MYSQLI_NUM);
    $res->Free();

    // module handler
    ModuleFunction('OnDeleteUser', [$userID]);

    // delete blobs
    $blobStorageIDs = [];
    $res = $db->Query('SELECT DISTINCT(`blobstorage`) FROM {pre}mails WHERE userid=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $blobStorageIDs[] = $row['blobstorage'];
    }
    $res->Free();
    $res = $db->Query('SELECT DISTINCT(`blobstorage`) FROM {pre}diskfiles WHERE `user`=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $blobStorageIDs[] = $row['blobstorage'];
    }
    $res->Free();
    foreach (array_unique($blobStorageIDs) as $blobStorageID) {
        BMBlobStorage::createProvider($blobStorageID, $userID)->deleteUser();
    }

    // delivery status entries
    $db->Query('DELETE FROM {pre}maildeliverystatus WHERE userid=?',
        $userID);

    // abuse points
    $db->Query('DELETE FROM {pre}abuse_points WHERE userid=?',
        $userID);

    // delete group<->member associations + groups
    $groupIDs = [];
    $res = $db->Query('SELECT id FROM {pre}adressen_gruppen WHERE user=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $groupIDs[] = $row['id'];
    }
    $res->Free();
    if (count($groupIDs) > 0) {
        $db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE gruppe IN('.implode(',', $groupIDs).')');
        $db->Query('DELETE FROM {pre}adressen_gruppen WHERE user=?',
            $userID);
    }

    // delete addresses
    $db->Query('DELETE FROM {pre}adressen WHERE user=?',
        $userID);

    // delete aliases
    $db->Query('DELETE FROM {pre}aliase WHERE user=?',
        $userID);

    // delete autoresponder
    $db->Query('DELETE FROM {pre}autoresponder WHERE userid=?',
        $userID);

    // delete calendar dates
    $dateIDs = [];
    $res = $db->Query('SELECT id FROM {pre}dates WHERE user=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $dateIDs[] = $row['id'];
    }
    $res->Free();
    if (count($dateIDs) > 0) {
        $db->Query('DELETE FROM {pre}dates_attendees WHERE date IN('.implode(',', $dateIDs).')');
        $db->Query('DELETE FROM {pre}dates WHERE user=?',
            $userID);
    }

    // delete calendar groups
    $db->Query('DELETE FROM {pre}dates_groups WHERE user=?',
        $userID);

    // delete disk props
    $db->Query('DELETE FROM {pre}diskprops WHERE user=?',
        $userID);

    // delete disk locks
    $db->Query('DELETE FROM {pre}disklocks WHERE user=?',
        $userID);

    // delete disk folders
    $db->Query('DELETE FROM {pre}diskfolders WHERE user=?',
        $userID);

    // delete disk files
    $db->Query('DELETE FROM {pre}diskfiles WHERE user=?',
        $userID);

    // delete cert mails
    $db->Query('DELETE FROM {pre}certmails WHERE user=?',
        $userID);

    // delete filters
    $filterIDs = [];
    $res = $db->Query('SELECT id FROM {pre}filter WHERE userid=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $filterIDs[] = $row['id'];
    }
    $res->Free();
    if (count($filterIDs) > 0) {
        $db->Query('DELETE FROM {pre}filter_actions WHERE filter IN('.implode(',', $filterIDs).')');
        $db->Query('DELETE FROM {pre}filter_conditions WHERE filter IN('.implode(',', $filterIDs).')');
        $db->Query('DELETE FROM {pre}filter WHERE userid=?',
            $userID);
    }

    // delete folder conditions + folders
    $folderIDs = [];
    $res = $db->Query('SELECT id FROM {pre}folders WHERE userid=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $folderIDs[] = $row['id'];
    }
    $res->Free();
    if (count($folderIDs) > 0) {
        $db->Query('DELETE FROM {pre}folder_conditions WHERE folder IN('.implode(',', $folderIDs).')');
        $db->Query('DELETE FROM {pre}folders WHERE userid=?',
            $userID);
    }

    // delete mails
    $db->Query('DELETE FROM {pre}mailnotes WHERE `mailid` IN (SELECT `id` FROM {pre}mails WHERE `userid`=?)',
        $userID);
    $db->Query('DELETE FROM {pre}mails WHERE userid=?',
        $userID);
    $db->Query('DELETE FROM {pre}attachments WHERE userid=?',
        $userID);

    // delete notes
    $db->Query('DELETE FROM {pre}notes WHERE user=?',
        $userID);

    // uid index + ext. pop3s
    $pop3IDs = [];
    $res = $db->Query('SELECT id FROM {pre}pop3 WHERE user=?',
        $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $pop3IDs[] = $row['id'];
    }
    $res->Free();
    if (count($pop3IDs) > 0) {
        $db->Query('DELETE FROM {pre}uidindex WHERE pop3 IN('.implode(',', $pop3IDs).')');
        $db->Query('DELETE FROM {pre}pop3 WHERE user=?',
            $userID);
    }

    // sigs
    $db->Query('DELETE FROM {pre}signaturen WHERE user=?',
        $userID);

    // sent sms
    $db->Query('DELETE FROM {pre}smsend WHERE user=?',
        $userID);

    // spam index
    $db->Query('DELETE FROM {pre}spamindex WHERE userid=?',
        $userID);

    // tasks
    $db->Query('DELETE FROM {pre}tasks WHERE user=?',
        $userID);

    // workgroup memberships
    $db->Query('DELETE FROM {pre}workgroups_member WHERE user=?',
        $userID);

    // certificates
    $db->Query('DELETE FROM {pre}certificates WHERE userid=?',
        $userID);

    // user prefs
    $db->Query('DELETE FROM {pre}userprefs WHERE userid=?',
        $userID);

    // search index
    $indexFileName = DataFilename($userID, 'idx', true);
    if (file_exists($indexFileName)) {
        @unlink($indexFileName);
    }

    // finally, the user record itself
    $db->Query('DELETE FROM {pre}users WHERE id=?',
        $userID);

    // log
    PutLog(sprintf('User <%s> (%d) deleted',
        $userMail,
        $userID),
        PRIO_NOTE,
        __FILE__,
        __LINE__);

    return true;
}
