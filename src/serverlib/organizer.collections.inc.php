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
 */

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

/**
 * Ensure organizer collection tables/columns and backfill default calendars/address books.
 */
function bmOrganizerEnsureCollections()
{
    global $db, $mysql;

    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $prefix = $mysql['prefix'];

    bmOrganizerEnsureTable($prefix.'calendars',
        'CREATE TABLE `{table}` ('
        .'`id` int(11) NOT NULL AUTO_INCREMENT,'
        .'`user` int(11) NOT NULL,'
        .'`title` varchar(255) NOT NULL,'
        .'`color` tinyint(4) NOT NULL DEFAULT 0,'
        .'`is_default` tinyint(4) NOT NULL DEFAULT 0,'
        .'`dav_uri` varchar(128) NOT NULL,'
        .'`dav_uid` varchar(128) NOT NULL,'
        .'`notify_email` tinyint(4) NOT NULL DEFAULT 0,'
        .'`notify_push` tinyint(4) NOT NULL DEFAULT 0,'
        .'PRIMARY KEY (`id`),'
        .'KEY `user` (`user`)'
        .')');

    bmOrganizerEnsureTable($prefix.'addressbooks',
        'CREATE TABLE `{table}` ('
        .'`id` int(11) NOT NULL AUTO_INCREMENT,'
        .'`user` int(11) NOT NULL,'
        .'`title` varchar(255) NOT NULL,'
        .'`color` tinyint(4) NOT NULL DEFAULT 0,'
        .'`is_default` tinyint(4) NOT NULL DEFAULT 0,'
        .'`dav_uri` varchar(128) NOT NULL,'
        .'`dav_uid` varchar(128) NOT NULL,'
        .'PRIMARY KEY (`id`),'
        .'KEY `user` (`user`)'
        .')');

    bmOrganizerEnsureColumn($prefix.'dates', 'calendar_id',
        'ALTER TABLE `{table}` ADD `calendar_id` int(11) NOT NULL DEFAULT 0',
        'calendar_id');
    bmOrganizerEnsureColumn($prefix.'dates_groups', 'calendar_id',
        'ALTER TABLE `{table}` ADD `calendar_id` int(11) NOT NULL DEFAULT 0',
        'calendar_id');
    bmOrganizerEnsureColumn($prefix.'adressen', 'addressbook_id',
        'ALTER TABLE `{table}` ADD `addressbook_id` int(11) NOT NULL DEFAULT 0',
        'addressbook_id');
    bmOrganizerEnsureColumn($prefix.'adressen_gruppen', 'addressbook_id',
        'ALTER TABLE `{table}` ADD `addressbook_id` int(11) NOT NULL DEFAULT 0',
        'addressbook_id');

    $db->Query('INSERT INTO {pre}calendars(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) '
        .'SELECT `id`,\'\',0,1,\'calendar\',\'\' FROM {pre}users u '
        .'WHERE NOT EXISTS (SELECT 1 FROM {pre}calendars c WHERE c.`user`=u.`id` AND c.`is_default`=1)');

    $db->Query('UPDATE {pre}dates d '
        .'INNER JOIN {pre}calendars c ON c.`user`=d.`user` AND c.`is_default`=1 '
        .'SET d.`calendar_id`=c.`id` WHERE d.`calendar_id`=0');

    $db->Query('UPDATE {pre}dates_groups g '
        .'INNER JOIN {pre}calendars c ON c.`user`=g.`user` AND c.`is_default`=1 '
        .'SET g.`calendar_id`=c.`id` WHERE g.`calendar_id`=0');

    $db->Query('INSERT INTO {pre}addressbooks(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) '
        .'SELECT `id`,\'\',0,1,\'main\',\'\' FROM {pre}users u '
        .'WHERE NOT EXISTS (SELECT 1 FROM {pre}addressbooks b WHERE b.`user`=u.`id` AND b.`is_default`=1)');

    $db->Query('UPDATE {pre}adressen a '
        .'INNER JOIN {pre}addressbooks b ON b.`user`=a.`user` AND b.`is_default`=1 '
        .'SET a.`addressbook_id`=b.`id` WHERE a.`addressbook_id`=0');

    $db->Query('UPDATE {pre}adressen_gruppen g '
        .'INNER JOIN {pre}addressbooks b ON b.`user`=g.`user` AND b.`is_default`=1 '
        .'SET g.`addressbook_id`=b.`id` WHERE g.`addressbook_id`=0');

    bmOrganizerEnsureShares();
}

/**
 * @param string $table
 * @param string $sql
 */
function bmOrganizerEnsureTable($table, $sql)
{
    global $db;

    $res = $db->Query('SHOW TABLES LIKE ?', $table);
    $exists = $res && $res->RowCount() > 0;
    if ($res) {
        $res->Free();
    }
    if (!$exists) {
        $db->Query(str_replace('{table}', $table, $sql));
    }
}

/**
 * @param string $table
 * @param string $column
 * @param string $alterSql
 * @param string $indexName
 */
function bmOrganizerEnsureColumn($table, $column, $alterSql, $indexName = '')
{
    global $db;

    $res = $db->Query('SHOW COLUMNS FROM `'.$table.'` LIKE ?', $column);
    $exists = $res && $res->RowCount() > 0;
    if ($res) {
        $res->Free();
    }
    if ($exists) {
        return;
    }

    $db->Query(str_replace('{table}', $table, $alterSql));

    if ($indexName !== '') {
        $idx = $db->Query('SHOW INDEX FROM `'.$table.'` WHERE Key_name=?', $indexName);
        $hasIndex = $idx && $idx->RowCount() > 0;
        if ($idx) {
            $idx->Free();
        }
        if (!$hasIndex) {
            $db->Query('ALTER TABLE `'.$table.'` ADD KEY `'.$indexName.'` (`'.$column.'`)');
        }
    }
}

/**
 * Localized title for a default collection when none is stored.
 *
 * @param array  $row
 * @param string $fallback
 *
 * @return string
 */
function bmOrganizerCollectionTitle($row, $fallback)
{
    $title = isset($row['title']) ? trim((string) $row['title']) : '';
    if ($title !== '') {
        return $title;
    }
    if (!empty($row['is_default'])) {
        return $fallback;
    }

    return $fallback;
}

/**
 * Keep the stored title and set the display title (localized default if empty).
 *
 * @param array  $row
 * @param string $fallback
 */
function bmOrganizerHydrateCollectionTitle(&$row, $fallback)
{
    $row['title_raw'] = isset($row['title']) ? $row['title'] : '';
    $row['title'] = bmOrganizerCollectionTitle($row, $fallback);
}

/**
 * Title to store: default collections keep an empty title when it matches the localized fallback.
 *
 * @param string $title
 * @param mixed  $isDefault
 * @param string $fallback
 *
 * @return string
 */
function bmOrganizerCollectionTitleForStorage($title, $isDefault, $fallback)
{
    $title = trim((string) $title);
    if (!empty($isDefault) && ($title === '' || $title === $fallback)) {
        return '';
    }

    return $title;
}

/**
 * UI/CSS calendar color index (0-5) as Apple calendar-color hex.
 *
 * @param int $index
 *
 * @return string
 */
function bmOrganizerCalendarColorHex($index)
{
    $colors = [
        0 => '#3D81EB',
        1 => '#5DB747',
        2 => '#FA514A',
        3 => '#FD9530',
        4 => '#C358BF',
        5 => '#8A74D3',
    ];
    $index = (int) $index;

    return isset($colors[$index]) ? $colors[$index] : $colors[0];
}

/**
 * Closest UI color index for an Apple/CalDAV calendar-color value.
 *
 * @param mixed $value
 *
 * @return int
 */
function bmOrganizerCalendarColorFromHex($value)
{
    if (is_object($value) && method_exists($value, 'getValue')) {
        $value = $value->getValue();
    }
    $hex = ltrim(trim((string) $value), '#');
    if (strlen($hex) >= 8) {
        $hex = substr($hex, 0, 6);
    }
    if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
        return 0;
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $palette = [
        0 => [0x3D, 0x81, 0xEB],
        1 => [0x5D, 0xB7, 0x47],
        2 => [0xFA, 0x51, 0x4A],
        3 => [0xFD, 0x95, 0x30],
        4 => [0xC3, 0x58, 0xBF],
        5 => [0x8A, 0x74, 0xD3],
    ];

    $best = 0;
    $bestDist = PHP_INT_MAX;
    foreach ($palette as $i => $c) {
        $d = ($r - $c[0]) * ($r - $c[0]) + ($g - $c[1]) * ($g - $c[1]) + ($b - $c[2]) * ($b - $c[2]);
        if ($d < $bestDist) {
            $bestDist = $d;
            $best = $i;
        }
    }

    return $best;
}

/**
 * ASCII slug for a DAV collection URI (familie, firma, geschaeft).
 *
 * @param string $title
 *
 * @return string
 */
function bmOrganizerCollectionSlug($title)
{
    $title = trim((string) $title);
    $replace = [
        'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
        'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u',
        'ç' => 'c', 'ñ' => 'n',
        'æ' => 'ae', 'œ' => 'oe',
    ];
    $slug = strtr($title, $replace);
    if (function_exists('iconv')) {
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if (is_string($trans) && $trans !== '') {
            $slug = $trans;
        }
    }
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);

    return trim($slug, '-');
}

/**
 * DAV URIs already used by this user (calendars, groups, address books, task lists).
 *
 * @param int $userID
 *
 * @return array<string,bool>
 */
function bmOrganizerUsedDavUris($userID)
{
    global $db;

    $used = [
        'calendar' => true,
        'main' => true,
        'inbox' => true,
        'outbox' => true,
    ];

    $res = $db->Query('SELECT `dav_uri` FROM {pre}calendars WHERE `user`=?', $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if ($row['dav_uri'] !== '') {
            $used[$row['dav_uri']] = true;
        }
    }
    $res->Free();

    $res = $db->Query('SELECT `id`,`dav_uri` FROM {pre}dates_groups WHERE `user`=?', $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $uri = $row['dav_uri'] !== '' ? $row['dav_uri'] : 'calendar-'.$row['id'];
        $used[$uri] = true;
    }
    $res->Free();

    $res = $db->Query('SELECT `dav_uri` FROM {pre}addressbooks WHERE `user`=?', $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        if ($row['dav_uri'] !== '') {
            $used[$row['dav_uri']] = true;
        }
    }
    $res->Free();

    $res = $db->Query('SELECT `tasklistid`,`dav_uri` FROM {pre}tasklists WHERE `userid`=?', $userID);
    while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
        $uri = $row['dav_uri'] !== '' ? $row['dav_uri'] : 'tasklist-'.$row['tasklistid'];
        $used[$uri] = true;
    }
    $res->Free();

    return $used;
}

/**
 * Unique, name-based DAV URI for a calendar or address book.
 *
 * @param int    $userID
 * @param string $title
 * @param string $fallbackPrefix cal or book
 * @param int    $id
 * @param string $currentUri    URI of this row, excluded from the clash set
 *
 * @return string
 */
function bmOrganizerUniqueCollectionDavUri($userID, $title, $fallbackPrefix, $id, $currentUri = '')
{
    $used = bmOrganizerUsedDavUris($userID);
    if ($currentUri !== '') {
        unset($used[$currentUri]);
    }
    unset($used[$fallbackPrefix.'-'.(int) $id]);

    $slug = bmOrganizerCollectionSlug($title);
    if ($slug === '') {
        $slug = $fallbackPrefix.'-'.(int) $id;
    }

    $base = $slug;
    $n = 2;
    while (isset($used[$slug])) {
        $slug = $base.'-'.$n;
        ++$n;
    }

    return $slug;
}

/**
 * Replace leftover generated URIs (cal-4, book-12) with a stable name slug.
 *
 * @param int    $userID
 * @param int    $id
 * @param string $title
 * @param string $currentUri
 * @param string $fallbackPrefix
 * @param string $sqlTable        e.g. {pre}calendars
 *
 * @return string
 */
function bmOrganizerUpgradeGeneratedDavUri($userID, $id, $title, $currentUri, $fallbackPrefix, $sqlTable)
{
    global $db;

    $currentUri = (string) $currentUri;
    if ($currentUri !== '' && !preg_match('/^'.preg_quote($fallbackPrefix, '/').'-\d+$/', $currentUri)) {
        return $currentUri;
    }

    $newUri = bmOrganizerUniqueCollectionDavUri($userID, $title, $fallbackPrefix, $id, $currentUri);
    if ($newUri !== $currentUri) {
        $db->Query('UPDATE '.$sqlTable.' SET `dav_uri`=? WHERE `id`=? AND `user`=?',
            $newUri,
            (int) $id,
            $userID);
    }

    return $newUri;
}

include_once B1GMAIL_DIR.'serverlib/organizer.shares.inc.php';

