<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

define('BM_ORGANIZER_SHARE_CAL', 'calendar');
define('BM_ORGANIZER_SHARE_BOOK', 'addressbook');
define('BM_ORGANIZER_SHARE_MAIL', 'mailfolder');
define('BM_ORGANIZER_SHARE_MAILBOX', 'mailbox');
define('BM_ORGANIZER_SHARE_MAILSYS', 'mailsys');
define('BM_ORGANIZER_SHARE_WEBDISK', 'webdisk');
define('BM_ORGANIZER_SHARE_WDFOLDER', 'wdfolder');
define('BM_ORGANIZER_SHARE_TASKLIST', 'tasklist');
define('BM_ORGANIZER_SHARE_TASKLISTDEF', 'tasklistdef');
define('BM_ORGANIZER_SHARE_TASK', 'task');
define('BM_ORGANIZER_SHARE_NOTE', 'note');
define('BM_ORGANIZER_ACCESS_READ', 'read');
define('BM_ORGANIZER_ACCESS_WRITE', 'write');
define('TASKLIST_SHARED_ITEMS', -1);
define('TASKLIST_SHARE_DEFAULT_BASE', -400000000);

if (!function_exists('bmOrganizerEnsureTable')) {
    include_once B1GMAIL_DIR.'serverlib/organizer.collections.inc.php';
}

/**
 * @param int $folderID
 * @return bool
 */
function bmIsSystemMailFolder($folderID)
{
    return in_array((int) $folderID, [
        FOLDER_INBOX,
        FOLDER_OUTBOX,
        FOLDER_DRAFTS,
        FOLDER_SPAM,
        FOLDER_TRASH,
    ], true);
}

/**
 * @param int $ownerId
 * @param int $folderID
 * @return int
 */
function bmEncodeSharedSysFolder($ownerId, $folderID)
{
    $slots = [
        FOLDER_INBOX => 0,
        FOLDER_OUTBOX => 1,
        FOLDER_DRAFTS => 2,
        FOLDER_SPAM => 3,
        FOLDER_TRASH => 4,
    ];
    if (!isset($slots[$folderID])) {
        return 0;
    }

    return FOLDER_SHARE_SYS_BASE - ((int) $ownerId * 8 + $slots[$folderID]);
}

/**
 * @param int $virtualId
 * @return array|false
 */
function bmDecodeSharedSysFolder($virtualId)
{
    $virtualId = (int) $virtualId;
    if ($virtualId >= FOLDER_SHARE_SYS_BASE) {
        return false;
    }

    $packed = FOLDER_SHARE_SYS_BASE - $virtualId;
    if ($packed < 0) {
        return false;
    }

    $ownerId = (int) floor($packed / 8);
    $slot = $packed % 8;
    $folders = [
        0 => FOLDER_INBOX,
        1 => FOLDER_OUTBOX,
        2 => FOLDER_DRAFTS,
        3 => FOLDER_SPAM,
        4 => FOLDER_TRASH,
    ];
    if (!isset($folders[$slot]) || $ownerId <= 0) {
        return false;
    }

    return ['ownerId' => $ownerId, 'realFolder' => $folders[$slot]];
}

/**
 * Group flags for user-to-user sharing (calendar, mail, todo, ...).
 */
function bmOrganizerEnsureGroupShareColumns()
{
    global $mysql;

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $table = $mysql['prefix'].'gruppen';
    $cols = [
        'share_calendar',
        'share_addr',
        'share_mail',
        'share_todo',
        'share_notes',
        'share_webdisk',
    ];
    foreach ($cols as $col) {
        bmOrganizerEnsureColumn($table, $col,
            'ALTER TABLE `{table}` ADD `'.$col.'` enum(\'yes\',\'no\') NOT NULL DEFAULT \'yes\'');
    }
}

/**
 * @param string $kind calendar|addressbook|mail|todo|notes|webdisk
 * @return bool
 */
function bmOrganizerGroupCanShare($kind)
{
    global $groupRow;

    bmOrganizerEnsureGroupShareColumns();

    $map = [
        'calendar' => 'share_calendar',
        'addressbook' => 'share_addr',
        'mail' => 'share_mail',
        'todo' => 'share_todo',
        'notes' => 'share_notes',
        'webdisk' => 'share_webdisk',
    ];
    if (!isset($map[$kind]) || !is_array($groupRow)) {
        return false;
    }
    $col = $map[$kind];
    if (!isset($groupRow[$col])) {
        return true;
    }

    return $groupRow[$col] === 'yes';
}

/**
 * @param string $type
 * @return string|false
 */
function bmOrganizerShareKindForType($type)
{
    if ($type === BM_ORGANIZER_SHARE_CAL) {
        return 'calendar';
    }
    if ($type === BM_ORGANIZER_SHARE_BOOK) {
        return 'addressbook';
    }
    if ($type === BM_ORGANIZER_SHARE_MAIL
        || $type === BM_ORGANIZER_SHARE_MAILBOX
        || $type === BM_ORGANIZER_SHARE_MAILSYS) {
        return 'mail';
    }
    if ($type === BM_ORGANIZER_SHARE_TASKLIST
        || $type === BM_ORGANIZER_SHARE_TASKLISTDEF
        || $type === BM_ORGANIZER_SHARE_TASK) {
        return 'todo';
    }
    if ($type === BM_ORGANIZER_SHARE_NOTE) {
        return 'notes';
    }
    if ($type === BM_ORGANIZER_SHARE_WEBDISK
        || $type === BM_ORGANIZER_SHARE_WDFOLDER) {
        return 'webdisk';
    }

    return false;
}

/**
 * Whether the owner's group currently allows this share kind.
 * Existing share rows stay in the database; inactive shares are ignored in UI and DAV.
 *
 * @param int    $ownerId
 * @param string $kind
 * @return bool
 */
function bmOrganizerOwnerGroupCanShare($ownerId, $kind)
{
    global $db, $userRow, $groupRow;

    $ownerId = (int) $ownerId;
    if ($ownerId <= 0) {
        return false;
    }
    if (isset($userRow['id']) && (int) $userRow['id'] === $ownerId) {
        return bmOrganizerGroupCanShare($kind);
    }

    bmOrganizerEnsureGroupShareColumns();
    $map = [
        'calendar' => 'share_calendar',
        'addressbook' => 'share_addr',
        'mail' => 'share_mail',
        'todo' => 'share_todo',
        'notes' => 'share_notes',
        'webdisk' => 'share_webdisk',
    ];
    if (!isset($map[$kind])) {
        return false;
    }
    $col = $map[$kind];

    static $ownerFlags = [];
    if (!array_key_exists($ownerId, $ownerFlags)) {
        $res = $db->Query('SELECT g.`share_calendar`,g.`share_addr`,g.`share_mail`,g.`share_todo`,g.`share_notes`,g.`share_webdisk` '
            .'FROM {pre}users u INNER JOIN {pre}gruppen g ON g.`id`=u.`gruppe` WHERE u.`id`=?',
            $ownerId);
        if ($res->RowCount() !== 1) {
            $res->Free();
            $ownerFlags[$ownerId] = false;
        } else {
            $ownerFlags[$ownerId] = $res->FetchArray(MYSQLI_ASSOC);
            $res->Free();
        }
    }
    if ($ownerFlags[$ownerId] === false) {
        return false;
    }
    $row = $ownerFlags[$ownerId];
    if (!isset($row[$col])) {
        return true;
    }

    return $row[$col] === 'yes';
}

/**
 * @param string $type
 * @param int    $ownerId
 * @return bool
 */
function bmOrganizerShareActiveForOwner($type, $ownerId)
{
    $kind = bmOrganizerShareKindForType($type);
    if ($kind === false) {
        return true;
    }

    return bmOrganizerOwnerGroupCanShare($ownerId, $kind);
}

/**
 * @param int $ownerId
 * @return int
 */
function bmEncodeSharedDefaultTaskList($ownerId)
{
    return TASKLIST_SHARE_DEFAULT_BASE - (int) $ownerId;
}

/**
 * @param int $virtualId
 * @return int|false
 */
function bmDecodeSharedDefaultTaskList($virtualId)
{
    $virtualId = (int) $virtualId;
    if ($virtualId >= TASKLIST_SHARE_DEFAULT_BASE) {
        return false;
    }
    $ownerId = TASKLIST_SHARE_DEFAULT_BASE - $virtualId;

    return $ownerId > 0 ? $ownerId : false;
}

/**
 * Ensure the organizer share table exists.
 */
function bmOrganizerEnsureShares()
{
    global $mysql, $db;

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $prefix = $mysql['prefix'];
    bmOrganizerEnsureTable($prefix.'organizer_shares',
        'CREATE TABLE `{table}` ('
        .'`id` int(11) NOT NULL AUTO_INCREMENT,'
        .'`type` varchar(16) NOT NULL,'
        .'`collection_id` int(11) NOT NULL,'
        .'`owner_id` int(11) NOT NULL,'
        .'`target_id` int(11) NOT NULL,'
        .'`access` varchar(8) NOT NULL DEFAULT \'read\','
        .'`created` int(11) NOT NULL DEFAULT 0,'
        .'`notify_email` tinyint(4) NOT NULL DEFAULT 0,'
        .'`notify_push` tinyint(4) NOT NULL DEFAULT 0,'
        .'PRIMARY KEY (`id`),'
        .'UNIQUE KEY `share_unique` (`type`,`collection_id`,`target_id`),'
        .'KEY `target_id` (`target_id`),'
        .'KEY `owner_id` (`owner_id`)'
        .')');

    bmOrganizerEnsureColumn($prefix.'organizer_shares', 'notify_email',
        'ALTER TABLE `{table}` ADD `notify_email` tinyint(4) NOT NULL DEFAULT 0');
    bmOrganizerEnsureColumn($prefix.'organizer_shares', 'notify_push',
        'ALTER TABLE `{table}` ADD `notify_push` tinyint(4) NOT NULL DEFAULT 0');

    $calTable = $db->Query('SHOW TABLES LIKE ?', $prefix.'calendars');
    $hasCalendars = $calTable && $calTable->RowCount() > 0;
    if ($calTable) {
        $calTable->Free();
    }
    if ($hasCalendars) {
        bmOrganizerEnsureColumn($prefix.'calendars', 'notify_email',
            'ALTER TABLE `{table}` ADD `notify_email` tinyint(4) NOT NULL DEFAULT 0');
        bmOrganizerEnsureColumn($prefix.'calendars', 'notify_push',
            'ALTER TABLE `{table}` ADD `notify_push` tinyint(4) NOT NULL DEFAULT 0');
    }

    bmOrganizerEnsureGroupShareColumns();
}

/**
 * @param string $access
 *
 * @return string
 */
function bmOrganizerNormalizeShareAccess($access)
{
    return $access === BM_ORGANIZER_ACCESS_WRITE
        ? BM_ORGANIZER_ACCESS_WRITE
        : BM_ORGANIZER_ACCESS_READ;
}

/**
 * @param string $type
 *
 * @return string|false
 */
function bmOrganizerShareTable($type)
{
    if ($type === BM_ORGANIZER_SHARE_CAL) {
        return '{pre}calendars';
    }
    if ($type === BM_ORGANIZER_SHARE_BOOK) {
        return '{pre}addressbooks';
    }

    return false;
}

/**
 * Shares granted by the owner for a collection.
 *
 * @param string $type
 * @param int    $collectionId
 * @param int    $ownerId
 *
 * @return array
 */
function bmOrganizerListShares($type, $collectionId, $ownerId)
{
    global $db;

    bmOrganizerEnsureShares();

    $result = [];
    $res = $db->Query('SELECT s.`id`,s.`target_id`,s.`access`,s.`created`,s.`notify_email`,s.`notify_push`,u.`email`,u.`vorname`,u.`nachname` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}users u ON u.`id`=s.`target_id` '
        .'WHERE s.`type`=? AND s.`collection_id`=? AND s.`owner_id`=? AND s.`target_id`!=s.`owner_id` '
        .'ORDER BY u.`email` ASC',
        $type,
        (int) $collectionId,
        (int) $ownerId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $result[(int) $row['id']] = $row;
    }
    $res->Free();

    return $result;
}

/**
 * User IDs a collection is shared with (excluding deleted accounts).
 *
 * @param string $type
 * @param int    $collectionId
 * @param int    $ownerId
 *
 * @return int[]
 */
function bmOrganizerListShareeIds($type, $collectionId, $ownerId)
{
    global $db;

    if (!bmOrganizerShareActiveForOwner($type, $ownerId)) {
        return [];
    }

    bmOrganizerEnsureShares();

    $ids = [];
    $res = $db->Query('SELECT s.`target_id` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}users u ON u.`id`=s.`target_id` '
        .'WHERE s.`type`=? AND s.`collection_id`=? AND s.`owner_id`=? AND u.`gesperrt`!=\'delete\'',
        $type,
        (int) $collectionId,
        (int) $ownerId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $ids[] = (int) $row['target_id'];
    }
    $res->Free();

    return $ids;
}

/**
 * Collections shared with a user.
 *
 * @param string $type
 * @param int    $targetId
 *
 * @return array
 */
function bmOrganizerListSharedWith($type, $targetId)
{
    global $db, $lang_user;

    bmOrganizerEnsureShares();

    $table = bmOrganizerShareTable($type);
    if ($table === false) {
        return [];
    }

    $fallback = $type === BM_ORGANIZER_SHARE_CAL
        ? $lang_user['calendar']
        : $lang_user['addressbook'];

    $result = [];
    $res = $db->Query('SELECT c.`id`,c.`user`,c.`title`,c.`color`,c.`is_default`,c.`dav_uri`,c.`dav_uid`,'
        .'s.`access` AS `share_access`,u.`email` AS `owner_email`,u.`vorname` AS `owner_firstname`,u.`nachname` AS `owner_lastname` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN '.$table.' c ON c.`id`=s.`collection_id` '
        .'INNER JOIN {pre}users u ON u.`id`=s.`owner_id` '
        .'WHERE s.`type`=? AND s.`target_id`=? AND u.`gesperrt`!=\'delete\' '
        .'ORDER BY c.`title` ASC',
        $type,
        (int) $targetId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (!bmOrganizerShareActiveForOwner($type, $row['user'])) {
            continue;
        }
        bmOrganizerHydrateCollectionTitle($row, $fallback);
        $row['shared'] = true;
        $result[(int) $row['id']] = $row;
    }
    $res->Free();

    return $result;
}

/**
 * @param int $targetId
 * @return array
 */
function bmOrganizerListSharedTaskLists($targetId)
{
    global $db;

    bmOrganizerEnsureShares();

    $result = [];
    $res = $db->Query('SELECT t.`tasklistid`,t.`title`,t.`userid`,s.`access` AS `share_access`,u.`email` AS `owner_email` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}tasklists t ON t.`tasklistid`=s.`collection_id` '
        .'INNER JOIN {pre}users u ON u.`id`=s.`owner_id` '
        .'WHERE s.`type`=? AND s.`target_id`=? AND u.`gesperrt`!=\'delete\' '
        .'ORDER BY t.`title` ASC',
        BM_ORGANIZER_SHARE_TASKLIST,
        (int) $targetId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_TASKLIST, $row['userid'])) {
            continue;
        }
        $row['shared'] = 1;
        $row['can_leave'] = 1;
        $result[(int) $row['tasklistid']] = $row;
    }
    $res->Free();

    $res = $db->Query('SELECT s.`owner_id`,s.`access` AS `share_access`,u.`email` AS `owner_email` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}users u ON u.`id`=s.`owner_id` '
        .'WHERE s.`type`=? AND s.`target_id`=? AND u.`gesperrt`!=\'delete\'',
        BM_ORGANIZER_SHARE_TASKLISTDEF,
        (int) $targetId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_TASKLISTDEF, $row['owner_id'])) {
            continue;
        }
        $vid = bmEncodeSharedDefaultTaskList((int) $row['owner_id']);
        $row['tasklistid'] = $vid;
        $row['userid'] = (int) $row['owner_id'];
        $row['shared'] = 1;
        $row['can_leave'] = 1;
        $row['is_default_share'] = 1;
        $result[$vid] = $row;
    }
    $res->Free();

    return $result;
}

/**
 * @param int $targetId
 * @return array
 */
function bmOrganizerListSharedTasks($targetId)
{
    global $db;

    bmOrganizerEnsureShares();

    $result = [];
    $res = $db->Query('SELECT t.`id`,t.`user`,t.`beginn`,t.`faellig`,t.`akt_status`,t.`titel`,t.`priority`,t.`erledigt`,t.`comments`,t.`tasklistid`,'
        .'s.`access` AS `share_access`,u.`email` AS `owner_email` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}tasks t ON t.`id`=s.`collection_id` '
        .'INNER JOIN {pre}users u ON u.`id`=s.`owner_id` '
        .'WHERE s.`type`=? AND s.`target_id`=? AND u.`gesperrt`!=\'delete\'',
        BM_ORGANIZER_SHARE_TASK,
        (int) $targetId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_TASK, $row['user'])) {
            continue;
        }
        $row['shared'] = 1;
        $row['can_leave'] = 1;
        $result[(int) $row['id']] = $row;
    }
    $res->Free();

    return $result;
}

/**
 * @param int $targetId
 * @return array
 */
function bmOrganizerListSharedNotes($targetId)
{
    global $db;

    bmOrganizerEnsureShares();

    $result = [];
    $res = $db->Query('SELECT n.`id`,n.`user`,n.`date`,n.`priority`,n.`text`,'
        .'s.`access` AS `share_access`,u.`email` AS `owner_email` '
        .'FROM {pre}organizer_shares s '
        .'INNER JOIN {pre}notes n ON n.`id`=s.`collection_id` '
        .'INNER JOIN {pre}users u ON u.`id`=s.`owner_id` '
        .'WHERE s.`type`=? AND s.`target_id`=? AND u.`gesperrt`!=\'delete\' '
        .'ORDER BY n.`date` DESC',
        BM_ORGANIZER_SHARE_NOTE,
        (int) $targetId);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if (!bmOrganizerShareActiveForOwner(BM_ORGANIZER_SHARE_NOTE, $row['user'])) {
            continue;
        }
        $row['shared'] = 1;
        $row['can_leave'] = 1;
        $result[(int) $row['id']] = $row;
    }
    $res->Free();

    return $result;
}

/**
 * @param string $type
 * @param int    $collectionId
 * @param int    $userId
 *
 * @return string|false
 */
function bmOrganizerShareAccessRow($type, $collectionId, $userId)
{
    global $db;

    bmOrganizerEnsureShares();
    $res = $db->Query('SELECT `access`,`owner_id` FROM {pre}organizer_shares WHERE `type`=? AND `collection_id`=? AND `target_id`=?',
        $type,
        (int) $collectionId,
        (int) $userId);
    if ($res->RowCount() !== 1) {
        $res->Free();

        return false;
    }
    $share = $res->FetchArray(MYSQLI_ASSOC);
    $res->Free();
    if (!bmOrganizerShareActiveForOwner($type, $share['owner_id'])) {
        return false;
    }

    return bmOrganizerNormalizeShareAccess($share['access']);
}

/**
 * @param string $type
 * @param int    $collectionId
 * @param int    $userId
 *
 * @return string|false owner|read|write
 */
function bmOrganizerShareAccess($type, $collectionId, $userId)
{
    global $db;

    if ($type === BM_ORGANIZER_SHARE_WEBDISK) {
        if ((int) $collectionId === (int) $userId && (int) $collectionId > 0) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_WDFOLDER) {
        $res = $db->Query('SELECT `user` FROM {pre}diskfolders WHERE `id`=?',
            (int) $collectionId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ((int) $row['user'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_MAILSYS) {
        $decoded = bmDecodeSharedSysFolder((int) $collectionId);
        if ($decoded && (int) $decoded['ownerId'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_MAILBOX) {
        if ((int) $collectionId === (int) $userId && (int) $collectionId > 0) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_MAIL) {
        $res = $db->Query('SELECT `userid` FROM {pre}folders WHERE `id`=?',
            (int) $collectionId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ((int) $row['userid'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_TASKLISTDEF) {
        if ((int) $collectionId === (int) $userId && (int) $collectionId > 0) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_TASKLIST) {
        $res = $db->Query('SELECT `userid` FROM {pre}tasklists WHERE `tasklistid`=?',
            (int) $collectionId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ((int) $row['userid'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_TASK) {
        $res = $db->Query('SELECT `user` FROM {pre}tasks WHERE `id`=?',
            (int) $collectionId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ((int) $row['user'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    if ($type === BM_ORGANIZER_SHARE_NOTE) {
        $res = $db->Query('SELECT `user` FROM {pre}notes WHERE `id`=?',
            (int) $collectionId);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ((int) $row['user'] === (int) $userId) {
            return 'owner';
        }

        return bmOrganizerShareAccessRow($type, $collectionId, $userId);
    }

    $table = bmOrganizerShareTable($type);
    if ($table === false) {
        return false;
    }

    $res = $db->Query('SELECT `user` FROM '.$table.' WHERE `id`=?',
        (int) $collectionId);
    if ($res->RowCount() !== 1) {
        $res->Free();

        return false;
    }
    $row = $res->FetchArray(MYSQLI_ASSOC);
    $res->Free();
    if ((int) $row['user'] === (int) $userId) {
        return 'owner';
    }

    return bmOrganizerShareAccessRow($type, $collectionId, $userId);
}

/**
 * @param string $type
 * @param int    $collectionId
 * @param int    $ownerId
 * @param int    $targetId
 * @param string $access
 *
 * @return int|string share id or error key
 */
function bmOrganizerAddShare($type, $collectionId, $ownerId, $targetId, $access, $notifyEmail = 0, $notifyPush = 0)
{
    global $db;

    bmOrganizerEnsureShares();

    $ownerId = (int) $ownerId;
    $targetId = (int) $targetId;
    $collectionId = (int) $collectionId;
    $access = bmOrganizerNormalizeShareAccess($access);
    $notifyEmail = $notifyEmail ? 1 : 0;
    $notifyPush = $notifyPush ? 1 : 0;

    if ($targetId <= 0) {
        return 'sharenotfound';
    }
    if ($targetId === $ownerId) {
        return 'sharewithself';
    }
    if (bmOrganizerShareAccess($type, $collectionId, $ownerId) !== 'owner') {
        return 'sharenotfound';
    }

    $res = $db->Query('SELECT `id` FROM {pre}organizer_shares WHERE `type`=? AND `collection_id`=? AND `target_id`=?',
        $type,
        $collectionId,
        $targetId);
    if ($res->RowCount() > 0) {
        $res->Free();

        return 'shareexists';
    }
    $res->Free();

    $db->Query('INSERT INTO {pre}organizer_shares(`type`,`collection_id`,`owner_id`,`target_id`,`access`,`created`,`notify_email`,`notify_push`) VALUES(?,?,?,?,?,?,?,?)',
        $type,
        $collectionId,
        $ownerId,
        $targetId,
        $access,
        time(),
        $notifyEmail,
        $notifyPush);

    return (int) $db->InsertId();
}

/**
 * @param string $type
 * @param int    $shareId
 * @param int    $ownerId
 *
 * @return bool
 */
function bmOrganizerRemoveShare($type, $shareId, $ownerId)
{
    global $db;

    bmOrganizerEnsureShares();

    $db->Query('DELETE FROM {pre}organizer_shares WHERE `id`=? AND `type`=? AND `owner_id`=?',
        (int) $shareId,
        $type,
        (int) $ownerId);

    return $db->AffectedRows() == 1;
}

/**
 * Invitee leaves a share without involving the owner.
 *
 * @param string $type
 * @param int    $collectionId
 * @param int    $targetId
 *
 * @return bool
 */
function bmOrganizerLeaveShare($type, $collectionId, $targetId)
{
    global $db;

    bmOrganizerEnsureShares();

    $db->Query('DELETE FROM {pre}organizer_shares WHERE `type`=? AND `collection_id`=? AND `target_id`=?',
        $type,
        (int) $collectionId,
        (int) $targetId);

    return $db->AffectedRows() == 1;
}

/**
 * Drop all shares for a collection (when it is deleted).
 *
 * @param string $type
 * @param int    $collectionId
 */
function bmOrganizerDeleteCollectionShares($type, $collectionId)
{
    global $db;

    bmOrganizerEnsureShares();
    $db->Query('DELETE FROM {pre}organizer_shares WHERE `type`=? AND `collection_id`=?',
        $type,
        (int) $collectionId);
}

/**
 * Drop all shares involving a user (account deletion).
 *
 * @param int $userId
 */
function bmOrganizerDeleteUserShares($userId)
{
    global $db;

    bmOrganizerEnsureShares();
    $db->Query('DELETE FROM {pre}organizer_shares WHERE `owner_id`=? OR `target_id`=?',
        (int) $userId,
        (int) $userId);
}

/**
 * Resolve a local user by email or alias.
 *
 * @param string $email
 *
 * @return int
 */
function bmOrganizerShareTargetUserId($email)
{
    $email = trim($email);
    if ($email === '') {
        return 0;
    }

    $encoded = EncodeEMail($email);
    $id = (int) BMUser::GetID($encoded, true);
    if ($id > 0) {
        return $id;
    }

    $decoded = DecodeEMail($email);
    if ($decoded !== $encoded) {
        return (int) BMUser::GetID($decoded, true);
    }

    return 0;
}

/**
 * @param mixed $value
 *
 * @return int
 */
function bmOrganizerNotifyFlag($value)
{
    return !empty($value) && $value !== '0' && $value !== 'no' ? 1 : 0;
}

/**
 * Owner notification prefs for a calendar.
 *
 * @param int $calendarId
 * @param int $ownerId
 *
 * @return array{notify_email:int,notify_push:int}
 */
function bmOrganizerGetOwnerNotify($calendarId, $ownerId)
{
    global $db;

    bmOrganizerEnsureShares();

    $res = $db->Query('SELECT `notify_email`,`notify_push` FROM {pre}calendars WHERE `id`=? AND `user`=?',
        (int) $calendarId,
        (int) $ownerId);
    if ($res->RowCount() !== 1) {
        $res->Free();

        return ['notify_email' => 0, 'notify_push' => 0];
    }
    $row = $res->FetchArray(MYSQLI_ASSOC);
    $res->Free();

    return [
        'notify_email' => bmOrganizerNotifyFlag($row['notify_email']),
        'notify_push' => bmOrganizerNotifyFlag($row['notify_push']),
    ];
}

/**
 * @param int $calendarId
 * @param int $ownerId
 * @param int $notifyEmail
 * @param int $notifyPush
 */
function bmOrganizerSetOwnerNotify($calendarId, $ownerId, $notifyEmail, $notifyPush)
{
    global $db;

    bmOrganizerEnsureShares();
    $db->Query('UPDATE {pre}calendars SET `notify_email`=?, `notify_push`=? WHERE `id`=? AND `user`=?',
        $notifyEmail ? 1 : 0,
        $notifyPush ? 1 : 0,
        (int) $calendarId,
        (int) $ownerId);
}

/**
 * @param string $type
 * @param int    $shareId
 * @param int    $ownerId
 * @param int    $notifyEmail
 * @param int    $notifyPush
 */
function bmOrganizerSetShareNotify($type, $shareId, $ownerId, $notifyEmail, $notifyPush)
{
    global $db;

    bmOrganizerEnsureShares();
    $db->Query('UPDATE {pre}organizer_shares SET `notify_email`=?, `notify_push`=? WHERE `id`=? AND `type`=? AND `owner_id`=?',
        $notifyEmail ? 1 : 0,
        $notifyPush ? 1 : 0,
        (int) $shareId,
        $type,
        (int) $ownerId);
}

/**
 * Notify opted-in users when a shared calendar changes.
 *
 * @param int    $calendarId
 * @param int    $actorUserId
 * @param array  $date
 * @param string $action add|change|delete
 */
function bmOrganizerNotifyCalendarActivity($calendarId, $actorUserId, $date, $action)
{
    global $db, $bm_prefs, $lang_user;

    $calendarId = (int) $calendarId;
    $actorUserId = (int) $actorUserId;
    if ($calendarId <= 0 || !in_array($action, ['add', 'change', 'delete'], true)) {
        return;
    }

    bmOrganizerEnsureShares();

    $res = $db->Query('SELECT `id`,`user`,`title`,`notify_email`,`notify_push` FROM {pre}calendars WHERE `id`=?',
        $calendarId);
    if ($res->RowCount() !== 1) {
        $res->Free();

        return;
    }
    $cal = $res->FetchArray(MYSQLI_ASSOC);
    $res->Free();
    bmOrganizerHydrateCollectionTitle($cal, isset($lang_user['calendar']) ? $lang_user['calendar'] : 'Calendar');

    $ownerId = (int) $cal['user'];
    $recipients = [];

    if ($ownerId !== $actorUserId
        && (bmOrganizerNotifyFlag($cal['notify_email']) || bmOrganizerNotifyFlag($cal['notify_push']))) {
        $recipients[$ownerId] = [
            'notify_email' => bmOrganizerNotifyFlag($cal['notify_email']),
            'notify_push' => bmOrganizerNotifyFlag($cal['notify_push']),
        ];
    }

    foreach (bmOrganizerListShares(BM_ORGANIZER_SHARE_CAL, $calendarId, $ownerId) as $share) {
        $targetId = (int) $share['target_id'];
        if ($targetId <= 0 || $targetId === $actorUserId) {
            continue;
        }
        $emailOn = bmOrganizerNotifyFlag($share['notify_email']);
        $pushOn = bmOrganizerNotifyFlag($share['notify_push']);
        if (!$emailOn && !$pushOn) {
            continue;
        }
        $recipients[$targetId] = [
            'notify_email' => $emailOn,
            'notify_push' => $pushOn,
        ];
    }

    if (count($recipients) === 0) {
        return;
    }

    $actor = _new('BMUser', [$actorUserId]);
    $actorEmail = (is_array($actor->_row) && !empty($actor->_row['email']))
        ? $actor->_row['email']
        : '';

    $title = isset($date['title']) ? $date['title'] : '';
    $location = isset($date['location']) ? $date['location'] : '';
    $start = isset($date['startdate']) ? (int) $date['startdate'] : 0;
    $end = isset($date['enddate']) ? (int) $date['enddate'] : 0;
    $dateId = isset($date['id']) ? (int) $date['id'] : 0;
    $phraseKey = 'notify_calshare_'.$action;

    foreach ($recipients as $userId => $prefs) {
        $user = _new('BMUser', [$userId]);
        if (!is_array($user->_row) || $user->_row['gesperrt'] === 'delete') {
            continue;
        }

        if (!empty($prefs['notify_push'])) {
            if ($action === 'delete' || $dateId <= 0) {
                $link = 'organizer.calendar.php?date='.$start;
                $flags = NOTIFICATION_FLAG_USELANG;
            } else {
                $link = sprintf('showCalendarDate(%d,%d,%d,false)', $dateId, $start, $end);
                $flags = NOTIFICATION_FLAG_USELANG | NOTIFICATION_FLAG_JSLINK;
            }
            $user->PostNotification($phraseKey,
                [HTMLFormat($cal['title']), HTMLFormat($title)],
                $link,
                '%%tpldir%%images/li/notify_calendar.png',
                $start > 0 ? $start : time(),
                0,
                $flags,
                '::calendarShare');
        }

        if (!empty($prefs['notify_email']) && !empty($user->_row['email'])) {
            $actionLabel = GetPhraseForUser($userId, 'lang_user', 'calshare_action_'.$action);
            $vars = [
                'action' => $actionLabel,
                'title' => $title,
                'calendar' => $cal['title'],
                'location' => $location,
                'date' => $start > 0 ? date('d.m.Y', $start) : '',
                'time' => $start > 0 ? date('H:i', $start) : '',
                'actor' => $actorEmail,
            ];
            $subject = GetPhraseForUser($userId, 'lang_custom', 'clndr_share_subject');
            SystemMail($bm_prefs['passmail_abs'],
                $user->_row['email'],
                $subject,
                'clndr_share_msg',
                $vars,
                $userId);
        }
    }
}

/**
 * Tell the invitee that a collection was shared with them.
 *
 * @param string $type
 * @param array  $collection
 * @param int    $ownerId
 * @param int    $targetId
 * @param string $access
 */
function bmOrganizerNotifyShareInvite($type, $collection, $ownerId, $targetId, $access)
{
    global $bm_prefs;

    $targetId = (int) $targetId;
    $ownerId = (int) $ownerId;
    $collectionId = isset($collection['id']) ? (int) $collection['id'] : 0;
    if ($targetId <= 0 || $ownerId <= 0) {
        return;
    }
    if ($type === BM_ORGANIZER_SHARE_MAILSYS) {
        if ($collectionId === 0 || !bmDecodeSharedSysFolder($collectionId)) {
            return;
        }
    } elseif ($type === BM_ORGANIZER_SHARE_TASKLISTDEF) {
        if ($collectionId <= 0) {
            return;
        }
    } elseif ($collectionId <= 0) {
        return;
    }

    $target = _new('BMUser', [$targetId]);
    if (!is_array($target->_row) || $target->_row['gesperrt'] === 'delete') {
        return;
    }

    $owner = _new('BMUser', [$ownerId]);
    $ownerEmail = (is_array($owner->_row) && !empty($owner->_row['email']))
        ? DecodeEMail($owner->_row['email'])
        : '';
    $title = isset($collection['title']) ? $collection['title'] : '';
    // Icon-Dateiname wird via user.class.php:GetNotifications() zu einem faIcon
    // gemappt (siehe $notificationFaIcons dort). Pro Share-Typ das richtige Icon:
    //   notify_calendar.png    → fa-calendar → ti-calendar
    //   notify_email.png       → fa-envelope-o → ti-mail
    //   notify_webdisk.png     → fa-cloud → ti-cloud
    //   notify_addressbook.png → fa-address-book-o → ti-address-book
    //   notify_todo.png        → fa-tasks → ti-list-check
    //   notify_notes.png       → fa-sticky-note-o → ti-notes
    if ($type === BM_ORGANIZER_SHARE_CAL) {
        $phrase = 'notify_orgshare_cal';
        $link = 'organizer.calendar.php?calendar='.$collectionId;
        $kindKey = 'calendar';
        $icon = '%%tpldir%%images/li/notify_calendar.png';
    } elseif ($type === BM_ORGANIZER_SHARE_MAILBOX) {
        $phrase = 'notify_orgshare_mailbox';
        $link = 'email.php?folder='.(FOLDER_SHARE_SYS_BASE - ($ownerId * 8));
        $kindKey = 'mailbox';
        $icon = '%%tpldir%%images/li/notify_email.png';
    } elseif ($type === BM_ORGANIZER_SHARE_MAILSYS || $type === BM_ORGANIZER_SHARE_MAIL) {
        $phrase = 'notify_orgshare_mail';
        $link = 'email.php?folder='.$collectionId;
        $kindKey = 'folder';
        $icon = '%%tpldir%%images/li/notify_email.png';
    } elseif ($type === BM_ORGANIZER_SHARE_WEBDISK) {
        $phrase = 'notify_orgshare_webdisk';
        $link = 'webdisk.php?folder='.(WEBDISK_SHARE_ROOT_BASE - $ownerId);
        $kindKey = 'webdisk';
        $icon = '%%tpldir%%images/li/notify_webdisk.png';
    } elseif ($type === BM_ORGANIZER_SHARE_WDFOLDER) {
        $phrase = 'notify_orgshare_wdfolder';
        $link = 'webdisk.php?folder='.$collectionId;
        $kindKey = 'folder';
        $icon = '%%tpldir%%images/li/notify_webdisk.png';
    } elseif ($type === BM_ORGANIZER_SHARE_TASKLIST || $type === BM_ORGANIZER_SHARE_TASKLISTDEF) {
        $phrase = 'notify_orgshare_todo';
        $link = 'organizer.todo.php?taskListID='.$collectionId;
        $kindKey = 'tasklists';
        $icon = '%%tpldir%%images/li/notify_todo.png';
    } elseif ($type === BM_ORGANIZER_SHARE_TASK) {
        $phrase = 'notify_orgshare_task';
        $link = 'organizer.todo.php?action=editTask&id='.$collectionId;
        $kindKey = 'task';
        $icon = '%%tpldir%%images/li/notify_todo.png';
    } elseif ($type === BM_ORGANIZER_SHARE_NOTE) {
        $phrase = 'notify_orgshare_note';
        $link = 'organizer.notes.php?show='.$collectionId;
        $kindKey = 'note';
        $icon = '%%tpldir%%images/li/notify_notes.png';
    } else {
        $phrase = 'notify_orgshare_book';
        $link = 'organizer.addressbook.php?addressbook='.$collectionId;
        $kindKey = 'addressbook';
        $icon = '%%tpldir%%images/li/notify_addressbook.png';
    }

    $target->PostNotification($phrase,
        [HTMLFormat($ownerEmail), HTMLFormat($title)],
        $link,
        $icon,
        0,
        0,
        NOTIFICATION_FLAG_USELANG,
        '::organizerShare');

    if (empty($target->_row['email'])) {
        return;
    }

    $accessLabel = GetPhraseForUser($targetId, 'lang_user',
        $access === BM_ORGANIZER_ACCESS_WRITE ? 'access_write' : 'access_read');
    $kind = GetPhraseForUser($targetId, 'lang_user', $kindKey);
    $vars = [
        'owner' => $ownerEmail,
        'title' => $title,
        'access' => $accessLabel,
        'kind' => $kind,
    ];
    $subject = GetPhraseForUser($targetId, 'lang_custom', 'orgshare_invite_subject');
    SystemMail($bm_prefs['passmail_abs'],
        $target->_row['email'],
        $subject,
        'orgshare_invite_msg',
        $vars,
        $targetId);
}
