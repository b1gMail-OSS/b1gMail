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

if (!defined('B1GMAIL_INIT')) {
    die('Directly calling this file is not supported');
}

include_once B1GMAIL_DIR.'serverlib/organizer.collections.inc.php';

/*
 * constants
 */
define('ADDRESS_PRIVATE', 1);
define('ADDRESS_WORK', 2);

/**
 * addressbook interface class.
 */
class BMAddressbook
{
    private $_userID;
    private $_sharedAddressbooks;
    private $_groupCache = [];
    /**
     * F5: Flag set by Change() when a requested addressbook move was
     * refused because source and destination belong to different owners.
     * Callers can query wasLastMoveBlocked() right after Change() to
     * surface a "move not permitted across owners" notice in the UI.
     */
    private $_lastMoveBlocked = false;
    private $_exportFields = 'vorname AS Firstname, nachname AS Lastname, anrede AS Salutation, position AS Position, firma AS Company, strassenr AS Street, plz AS ZIP, ort as City, land AS Country, email AS EMail, tel AS Phone, handy AS Mobile, fax AS Fax, work_strassenr AS workStreet, work_plz AS workZIP, work_ort AS workCity, work_land AS workCountry, work_email AS workEMail, work_tel AS workPhone, work_handy AS workMobile, work_fax AS workFax, web AS Homepage, kommentar AS Comment, CASE geburtsdatum WHEN 0 THEN \'\' ELSE FROM_UNIXTIME(geburtsdatum, \'%Y-%m-%d\') END AS Birthday';

    /**
     * constructor.
     *
     * @param int $userID User ID
     *
     * @return BMAddressbook
     */
    public function __construct($userID)
    {
        $this->_userID = (int) $userID;
        bmOrganizerEnsureCollections();
    }

    /**
     * Get all address books of the user.
     *
     * @return array
     */
    public function GetAddressbooks()
    {
        global $db, $lang_user;

        $result = [];
        $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}addressbooks WHERE `user`=? ORDER BY `is_default` DESC, `title` ASC',
            $this->_userID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            if (empty($row['is_default'])) {
                $row['dav_uri'] = bmOrganizerUpgradeGeneratedDavUri($this->_userID, (int) $row['id'], $row['title'], $row['dav_uri'], 'book', '{pre}addressbooks');
            }
            bmOrganizerHydrateCollectionTitle($row, $lang_user['addressbook']);
            $result[(int) $row['id']] = $row;
        }
        $res->Free();

        if (count($result) === 0) {
            $this->EnsureDefaultAddressbook();
            $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}addressbooks WHERE `user`=? ORDER BY `is_default` DESC, `title` ASC',
                $this->_userID);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                if (empty($row['is_default'])) {
                    $row['dav_uri'] = bmOrganizerUpgradeGeneratedDavUri($this->_userID, (int) $row['id'], $row['title'], $row['dav_uri'], 'book', '{pre}addressbooks');
                }
                bmOrganizerHydrateCollectionTitle($row, $lang_user['addressbook']);
                $result[(int) $row['id']] = $row;
            }
            $res->Free();
        }

        return $result;
    }

    /**
     * Address books shared with this user.
     *
     * @return array
     */
    public function GetSharedAddressbooks()
    {
        if ($this->_sharedAddressbooks === null) {
            $this->_sharedAddressbooks = bmOrganizerListSharedWith(BM_ORGANIZER_SHARE_BOOK, $this->_userID);
        }

        return $this->_sharedAddressbooks;
    }

    /**
     * Own or shared address book.
     *
     * @param int $id
     *
     * @return array|false
     */
    public function GetAccessibleAddressbook($id)
    {
        $own = $this->GetAddressbookByID($id);
        if ($own !== false) {
            $own['shared'] = false;
            $own['share_access'] = 'owner';

            return $own;
        }
        $shared = $this->GetSharedAddressbooks();
        $id = (int) $id;

        return isset($shared[$id]) ? $shared[$id] : false;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function CanWriteAddressbook($id)
    {
        $book = $this->GetAccessibleAddressbook($id);
        if ($book === false) {
            return false;
        }

        return empty($book['shared']) || $book['share_access'] === BM_ORGANIZER_ACCESS_WRITE;
    }

    /**
     * Get an address book.
     *
     * @param int $id
     *
     * @return array|false
     */
    public function GetAddressbookByID($id)
    {
        global $db, $lang_user;

        $res = $db->Query('SELECT `id`,`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid` FROM {pre}addressbooks WHERE `user`=? AND `id`=?',
            $this->_userID,
            (int) $id);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        bmOrganizerHydrateCollectionTitle($row, $lang_user['addressbook']);

        return $row;
    }

    /**
     * ID of the default address book.
     *
     * @return int
     */
    public function GetDefaultAddressbookID()
    {
        global $db;

        $res = $db->Query('SELECT `id` FROM {pre}addressbooks WHERE `user`=? AND `is_default`=1 LIMIT 1',
            $this->_userID);
        if ($res->RowCount() === 1) {
            list($id) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return (int) $id;
        }
        $res->Free();

        return $this->EnsureDefaultAddressbook();
    }

    /**
     * Resolve an address book ID, falling back to the default.
     *
     * @param int $addressbookID
     *
     * @return int
     */
    public function ResolveAddressbookID($addressbookID)
    {
        $addressbookID = (int) $addressbookID;
        if ($addressbookID > 0 && $this->GetAccessibleAddressbook($addressbookID) !== false) {
            return $addressbookID;
        }

        return $this->GetDefaultAddressbookID();
    }

    /**
     * Create the default address book if missing.
     *
     * @return int
     */
    public function EnsureDefaultAddressbook()
    {
        global $db;

        $res = $db->Query('SELECT `id` FROM {pre}addressbooks WHERE `user`=? AND `is_default`=1 LIMIT 1',
            $this->_userID);
        if ($res->RowCount() === 1) {
            list($id) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return (int) $id;
        }
        $res->Free();

        $db->Query('INSERT INTO {pre}addressbooks(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) VALUES(?,?,?,?,?,?)',
            $this->_userID,
            '',
            0,
            1,
            'main',
            '');

        return (int) $db->InsertId();
    }

    /**
     * Add an address book.
     *
     * @param string $title
     * @param string $davURI
     * @param string $davUID
     *
     * @return int
     */
    public function AddAddressbook($title, $davURI = '', $davUID = '')
    {
        global $db;

        $this->EnsureDefaultAddressbook();

        $db->Query('INSERT INTO {pre}addressbooks(`user`,`title`,`color`,`is_default`,`dav_uri`,`dav_uid`) VALUES(?,?,?,?,?,?)',
            $this->_userID,
            $title,
            0,
            0,
            $davURI,
            $davUID);
        $id = (int) $db->InsertId();
        if ($id > 0 && $davURI === '') {
            $db->Query('UPDATE {pre}addressbooks SET `dav_uri`=? WHERE `id`=? AND `user`=?',
                bmOrganizerUniqueCollectionDavUri($this->_userID, $title, 'book', $id),
                $id,
                $this->_userID);
        }

        return $id;
    }

    /**
     * Update an address book.
     *
     * @param int    $id
     * @param string $title
     *
     * @return bool
     */
    public function UpdateAddressbook($id, $title)
    {
        global $db, $lang_user;

        $existing = $this->GetAddressbookByID($id);
        if ($existing === false) {
            return false;
        }
        $title = bmOrganizerCollectionTitleForStorage($title, $existing['is_default'], $lang_user['addressbook']);

        $db->Query('UPDATE {pre}addressbooks SET `title`=? WHERE `id`=? AND `user`=?',
            $title,
            (int) $id,
            $this->_userID);

        return $db->AffectedRows() == 1;
    }

    /**
     * Delete an address book (not the default). Contacts and groups move to the default book.
     *
     * @param int $id
     *
     * @return bool
     */
    public function DeleteAddressbook($id, $deleteContacts = false)
    {
        global $db;

        $book = $this->GetAddressbookByID($id);
        if ($book === false || !empty($book['is_default'])) {
            return false;
        }

        $id = (int) $id;
        if ($deleteContacts) {
            $res = $db->Query('SELECT `id` FROM {pre}adressen WHERE `user`=? AND `addressbook_id`=?',
                $this->_userID,
                $id);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                $this->Delete($row['id']);
            }
            $res->Free();
            $res = $db->Query('SELECT `id` FROM {pre}adressen_gruppen WHERE `user`=? AND `addressbook_id`=?',
                $this->_userID,
                $id);
            while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
                $this->DeleteGroup($row['id']);
            }
            $res->Free();
        } else {
            $defaultID = $this->GetDefaultAddressbookID();
            $db->Query('UPDATE {pre}adressen SET `addressbook_id`=? WHERE `user`=? AND `addressbook_id`=?',
                $defaultID,
                $this->_userID,
                $id);
            $db->Query('UPDATE {pre}adressen_gruppen SET `addressbook_id`=? WHERE `user`=? AND `addressbook_id`=?',
                $defaultID,
                $this->_userID,
                $id);
        }

        $db->Query('DELETE FROM {pre}addressbooks WHERE `id`=? AND `user`=? AND `is_default`=0',
            $id,
            $this->_userID);

        if ($db->AffectedRows() == 1) {
            bmOrganizerDeleteCollectionShares(BM_ORGANIZER_SHARE_BOOK, $id);

            return true;
        }

        return false;
    }

    /**
     * lookup address book entry by email.
     *
     * @param string $email Email address to look up
     *
     * @return int
     */
    public function LookupEmail($email)
    {
        global $db;

        if (is_array($email)) {
            $res = $db->Query('SELECT COUNT(*) FROM {pre}adressen WHERE user=? AND (email IN ? OR work_email IN ?)',
                $this->_userID,
                $email,
                $email);
            list($count) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return $count > 0;
        } else {
            $res = $db->Query('SELECT id FROM {pre}adressen WHERE user=? AND (email=? OR work_email=?) LIMIT 1',
                $this->_userID,
                $email,
                $email);
            if ($res->RowCount() == 0) {
                return 0;
            }
            list($result) = $res->FetchArray(MYSQLI_NUM);
            $res->Free();

            return $result;
        }
    }

    /**
     * look up text in addressbook (e.g. for auto completion).
     *
     * @param string $text Text to look up
     *
     * @return array
     */
    public function Lookup($text)
    {
        global $db;

        $addresses = [];
        $addr = [];

        $res = $db->Query('SELECT id,vorname,nachname,email,work_email,default_address FROM {pre}adressen WHERE '
                            .'user=? AND ('
                            .sprintf('email LIKE \'%s%%\' OR work_email LIKE \'%s%%\' OR CONCAT(vorname,\' \',nachname) LIKE \'%s%%\' '
                                    .'OR CONCAT(nachname,\', \',vorname) LIKE \'%s%%\'',
                                    $db->Escape($text), $db->Escape($text), $db->Escape($text), $db->Escape($text))
                            .')',
                            $this->_userID);
        while ($row = $res->FetchArray()) {
            if (trim($row['email']) != '') {
                $addr[] = ['email' => $row['email'], 'vorname' => $row['vorname'], 'nachname' => $row['nachname']];
            }
            if (trim($row['work_email']) != '') {
                $addr[] = ['email' => $row['work_email'], 'vorname' => $row['vorname'], 'nachname' => $row['nachname']];
            }
        }
        $res->Free();

        $groups = $this->GetGroupList();
        foreach ($groups as $group) {
            if ($group['members'] == 0) {
                continue;
            }

            $addr[] = ['email' => sprintf('%d@contact.groups', $group['id']), 'vorname' => '', 'nachname' => $group['title']];
        }

        foreach ($addr as $email) {
            if (strtolower(substr($email['email'], 0, strlen($text))) == strtolower($text)
                || strtolower(substr($email['vorname'].' '.$email['nachname'], 0, strlen($text))) == strtolower($text)
                || strtolower(substr($email['nachname'].', '.$email['vorname'], 0, strlen($text))) == strtolower($text)) {
                $match = $email['email'];
                $name = trim($email['vorname'].' '.$email['nachname']);

                if (strtolower(substr($email['nachname'].', '.$email['vorname'], 0, strlen($text))) == strtolower($text)
                    && $email['vorname'] != '') {
                    $name = trim($email['nachname'].', '.$email['vorname']);
                }

                if (trim($match) != '') {
                    $addresses[] = str_replace(';', ',', sprintf('"%s" <%s>', $name, $match));
                }
            }
        }

        return $addresses;
    }

    /**
     * get group title.
     *
     * @param int $groupID
     *
     * @return string
     */
    public function GetGroupTitle($groupID)
    {
        global $db;

        // lookup in cache first
        if (isset($this->_groupCache[$groupID])) {
            return $this->_groupCache[$groupID];
        }

        // get title
        $groupTitle = '-';
        $res = $db->Query('SELECT title FROM {pre}adressen_gruppen WHERE user=? AND id=?',
            $this->_userID,
            $groupID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $groupTitle = $row['title'];
        }
        $res->Free();

        // cache & return
        $this->_groupCache[$groupID] = $groupTitle;

        return $groupTitle;
    }

    /**
     * find address by first / last name and returns ID (> 0) on success.
     *
     * @param string $vorname       First name
     * @param string $nachname     Last name
     * @param int    $addressbookID Address book ID, or -1 for any
     *
     * @return int
     */
    public function FindAddress($vorname, $nachname, $addressbookID = -1)
    {
        global $db;

        $result = 0;
        $sql = 'SELECT id FROM {pre}adressen WHERE vorname=? AND nachname=? AND user=?';
        $params = [$vorname, $nachname, $this->_userID];
        if ((int) $addressbookID !== -1) {
            $params[] = $this->ResolveAddressbookID($addressbookID);
            $sql .= ' AND addressbook_id=?';
        }
        $sql .= ' LIMIT 1';
        $res = $db->Query($sql, ...$params);
        while ($row = $res->FetchArray(MYSQLI_NUM)) {
            $result = $row[0];
        }
        $res->Free();

        return $result;
    }

    /**
     * get addressbook entries.
     *
     * @param string $letter        Letter (or *)
     * @param string $sortColumn    Sort column
     * @param string $sortOrder     Sort order
     * @param bool   $groupByLetter Create grouped output array?
     *
     * @return array
     */
    public function GetAddressbook($letter, $groupID = -1, $sortColumn = 'nachname', $sortOrder = 'ASC', $groupByLetter = false, $addressbookID = 0)
    {
        global $db;

        $bookFilter = '';
        $ownerId = $this->_userID;
        if ((int) $addressbookID !== -1) {
            $addressbookID = $this->ResolveAddressbookID($addressbookID);
            $book = $this->GetAccessibleAddressbook($addressbookID);
            if ($book !== false) {
                $ownerId = (int) $book['user'];
            }
            $bookFilter = ' AND {pre}adressen.addressbook_id='.(int) $addressbookID.' ';
        }

        // letter => where clause
        if ($letter == '9') {
            $whereClause = ' AND (';
            for ($i = 0; $i <= 9; ++$i) {
                $whereClause .= sprintf('{pre}adressen.nachname LIKE \'%s%%\' OR ', $i);
            }
            $whereClause = substr($whereClause, 0, -4).') ';
        } elseif (preg_match('/^[a-zA-Z]$/', $letter)) {
            $whereClause = sprintf(' AND {pre}adressen.nachname LIKE \'%s%%\' ', $letter);
        } else {
            $whereClause = '';
        }

        $whereClause .= $bookFilter;

        // query
        $result = [];

        // group => where clause
        if ($groupID > 0) {
            $res = $db->Query('SELECT {pre}adressen.* FROM {pre}adressen,{pre}adressen_gruppen_member WHERE {pre}adressen.user=?'.$whereClause.' AND {pre}adressen.id={pre}adressen_gruppen_member.adresse AND {pre}adressen_gruppen_member.gruppe=? ORDER BY '
                                .'{pre}adressen.'.$sortColumn.' '.$sortOrder,
                                $ownerId,
                                $groupID);
        } else {
            $res = $db->Query('SELECT * FROM {pre}adressen WHERE user=?'.$whereClause.' ORDER BY '
                                .$sortColumn.' '.$sortOrder,
                                $ownerId);
        }
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            if ($groupByLetter) {
                if (strlen($row['nachname']) > 0) {
                    $letter = strtoupper(substr($row['nachname'], 0, 1));
                } elseif (strlen($row['firma']) > 0) {
                    $letter = strtoupper(substr($row['firma'], 0, 1));
                } else {
                    $letter = '#';
                }

                if (!(ord($letter) >= ord('A') && ord($letter) <= ord('Z'))) {
                    $letter = '#';
                }

                if (!isset($result[$letter])) {
                    $result[$letter] = [$row['id'] => $row];
                } else {
                    $result[$letter][$row['id']] = $row;
                }
            } else {
                $result[$row['id']] = $row;
            }
        }
        $res->Free();

        if ($groupByLetter) {
            ksort($result);
        }

        // return
        return $result;
    }

    /**
     * get group list.
     *
     * @param int $contactID Fetch list for contact
     *
     * @return array
     */
    public function GetGroupList($contactID = 0, $addressbookID = 0)
    {
        global $db;

        $bookFilterSql = '';
        $ownerId = $this->_userID;
        $params = [];
        if ((int) $addressbookID !== -1) {
            $addressbookID = $this->ResolveAddressbookID($addressbookID);
            $book = $this->GetAccessibleAddressbook($addressbookID);
            if ($book !== false) {
                $ownerId = (int) $book['user'];
            }
            $bookFilterSql = ' AND `addressbook_id`=?';
            $params[] = $addressbookID;
        }
        array_unshift($params, $ownerId);

        // query
        $result = [];
        $res = $db->Query('SELECT id,title,`dav_uri`,`dav_uid`,`addressbook_id` FROM {pre}adressen_gruppen WHERE user=?'.$bookFilterSql.' ORDER BY title ASC',
            ...$params);
        while ($row = $res->FetchArray()) {
            $result[$row['id']] = $row;
        }
        $res->Free();

        // for contact?
        if ($contactID > 0) {
            $res = $db->Query('SELECT gruppe FROM {pre}adressen_gruppen_member WHERE adresse=?',
                $contactID);
            while ($row = $res->FetchArray()) {
                if (isset($result[$row['gruppe']])) {
                    $result[$row['gruppe']]['member'] = true;
                }
            }
            $res->Free();
        } else {
            // get member count
            foreach ($result as $id => $val) {
                $res = $db->Query('SELECT COUNT(*) FROM {pre}adressen_gruppen_member WHERE gruppe=?',
                    $id);
                list($result[$id]['members']) = $res->FetchArray();
                $res->Free();
            }
        }

        // return
        return $result;
    }

    /**
     * check if a group with this title exists.
     *
     * @param string $title Title
     *
     * @return bool
     */
    public function GroupExists($title, $addressbookID = 0)
    {
        global $db;

        $res = $db->Query('SELECT COUNT(*) FROM {pre}adressen_gruppen WHERE title=? AND user=? AND `addressbook_id`=?',
            $title,
            $this->_userID,
            $this->ResolveAddressbookID($addressbookID));
        list($count) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        return $count > 0;
    }

    /**
     * add a group.
     *
     * @param string $title Title
     *
     * @return int
     */
    public function GroupAdd($title, $davURI = '', $davUID = '', $addressbookID = 0)
    {
        global $db;

        $addressbookID = $this->ResolveAddressbookID($addressbookID);

        $db->Query('INSERT INTO {pre}adressen_gruppen(title,user,`dav_uri`,`dav_uid`,`addressbook_id`) VALUES(?,?,?,?,?)',
            $title,
            $this->_userID,
            $davURI,
            $davUID,
            $addressbookID);

        $groupID = $db->InsertID();
        ChangelogAdded(BMCL_TYPE_CONTACTGROUP, $groupID, time());

        return $groupID;
    }

    /**
     * change user picture.
     *
     * @param int    $contactID
     * @param string $pictureFile
     * @param string $mimeType
     *
     * @return bool
     */
    public function ChangePicture($contactID, $pictureFile, $mimeType, $isFile = true)
    {
        global $db;

        $pictureData = serialize([
            'mimeType' => $mimeType,
            'data' => $isFile ? getFileContents($pictureFile) : $pictureFile,
        ]);
        $db->Query('UPDATE {pre}adressen SET picture=? WHERE id=? AND user=?',
            $pictureData,
            (int) $contactID,
            $this->_userID);

        if ($db->AffectedRows()) {
            ChangelogUpdated(BMCL_TYPE_CONTACT, $contactID, time());

            return true;
        }

        return false;
    }

    /**
     * change a contact.
     *
     * @param string $firma          Company
     * @param string $vorname        First name
     * @param string $nachname       Last name
     * @param string $strassenr      Street / No
     * @param string $plz            ZIP
     * @param string $ort            City
     * @param string $land           Country
     * @param string $tel            Phone
     * @param string $fax            Fax
     * @param string $handy          Cellphone
     * @param string $email          E-Mail
     * @param string $work_strassenr Street / No
     * @param string $work_plz       ZIP
     * @param string $work_ort       City
     * @param string $work_land      Country
     * @param string $work_tel       Phone
     * @param string $work_fax       Fax
     * @param string $work_handy     Cellphone
     * @param string $work_email     E-Mail
     * @param string $anrede         Salutation
     * @param string $position       Position
     * @param string $web            WWW address
     * @param string $kommentar      Comment
     * @param int    $geburtsdatum   Birthday
     * @param int    $default        Default address
     * @param array  $groups         Groups
     *
     * @return bool
     */
    public function Change($contactID, $firma, $vorname, $nachname, $strassenr,
                    $plz, $ort, $land, $tel, $fax, $handy,
                    $email, $work_strassenr, $work_plz, $work_ort,
                    $work_land, $work_tel, $work_fax, $work_handy,
                    $work_email, $anrede, $position, $web, $kommentar,
                    $geburtsdatum, $default = ADDRESS_PRIVATE, $groups = [], $addressbookID = 0)
    {
        global $db;

        $existing = $this->GetContact($contactID);
        if ($existing === false || !$this->CanWriteAddressbook((int) $existing['addressbook_id'])) {
            return false;
        }

        $addressbookID = $this->ResolveAddressbookID($addressbookID ? $addressbookID : $existing['addressbook_id']);
        if (!$this->CanWriteAddressbook($addressbookID)) {
            $addressbookID = (int) $existing['addressbook_id'];
        }

        // F5: Contacts carry both an addressbook_id and a user (=owner) column,
        // and every listing/query keys off the addressbook's owner. Moving a
        // contact into a book owned by a different user leaves the contact
        // with (user=oldOwner, addressbook_id=<newOwner's book>) — neither
        // party can see it afterwards, and the original owner silently loses
        // the record. That's also an implicit ownership-transfer vector if we
        // were to fix it by rewriting `user`: a shared-with-write account
        // could move a foreign owner's contacts into a book it owns and keep
        // them permanently.
        //
        // Correct behavior: forbid moves that would change the owning user.
        // Same-owner moves (my book → my other book, or foreign owner A's
        // book → foreign owner A's other book with my write access) stay
        // allowed. Users wanting a true owner change must copy + delete
        // explicitly.
        // Reset per-call state before the check.
        $this->_lastMoveBlocked = false;
        if ((int) $addressbookID !== (int) $existing['addressbook_id']) {
            $srcBook = $this->GetAccessibleAddressbook((int) $existing['addressbook_id']);
            $dstBook = $this->GetAccessibleAddressbook((int) $addressbookID);
            if ($srcBook === false || $dstBook === false
                || (int) $srcBook['user'] !== (int) $dstBook['user']) {
                // Keep the contact in its original book — the rest of the
                // Change() call will still apply field edits. The caller can
                // check wasLastMoveBlocked() and surface a warning.
                $this->_lastMoveBlocked = true;
                $addressbookID = (int) $existing['addressbook_id'];
            }
        }

        // change contact
        $db->Query('UPDATE {pre}adressen SET '
                    .'firma=?, vorname=?, nachname=?, strassenr=?, plz=?, ort=?, land=?, tel=?, fax=?, handy=?, email=?, '
                    .'work_strassenr=?, work_plz=?, work_ort=?, work_land=?, work_tel=?, work_fax=?, work_handy=?, work_email=?, web=?, kommentar=?, '
                    .'default_address=?, anrede=?, position=?, geburtsdatum=?, addressbook_id=? '
                    .'WHERE id=? AND user=?',
                    $firma,
                    $vorname,
                    $nachname,
                    $strassenr,
                    $plz,
                    $ort,
                    $land,
                    $tel,
                    $fax,
                    $handy,
                    $email,
                    $work_strassenr,
                    $work_plz,
                    $work_ort,
                    $work_land,
                    $work_tel,
                    $work_fax,
                    $work_handy,
                    $work_email,
                    $web,
                    $kommentar,
                    $default,
                    $anrede,
                    $position,
                    $geburtsdatum,
                    $addressbookID,
                    (int) $contactID,
                    $existing['user']);
        $affRows = $db->AffectedRows();

        // groups
        if ($groups !== false) {
            $this->DeContactGroup($contactID);
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $this->ContactGroup($contactID, (int) $group);
                }
            }
        }

        if ($affRows) {
            ChangelogUpdated(BMCL_TYPE_CONTACT, $contactID, time());

            return true;
        }

        return false;
    }

    /**
     * invalidate self complete invitation.
     *
     * @param int    $contactID Contact ID
     * @param string $key       Key
     *
     * @return bool
     */
    public function InvalidateSelfCompleteInvitation($contactID, $key)
    {
        global $db;

        $db->Query('UPDATE {pre}adressen SET invitationCode=? WHERE id=? AND invitationCode=? AND LENGTH(invitationCode)=32',
            '',
            $contactID,
            $key);

        return $db->AffectedRows() == 1;
    }

    /**
     * send address book self completion invitation.
     *
     * @param int $contactID   Contact ID
     * @param int $addressType Address type (mail recipient)
     *
     * @return bool
     */
    public function SendSelfCompleteInvitation($contactID, $addressType)
    {
        global $db, $userRow, $thisUser, $bm_prefs, $lang_custom;

        // fetch mail address
        $res = $db->Query('SELECT email,work_email,invitationCode FROM {pre}adressen WHERE id=? AND user=?',
            $contactID,
            $this->_userID);
        if ($res->RowCount() != 1) {
            return false;
        }
        list($eMail, $work_eMail, $invitationCode) = $res->FetchArray(MYSQLI_NUM);
        $res->Free();

        // already sent?
        //if(trim($invitationCode) != '')
        //	return(false);

        // generate invitation code
        $invitationCode = GenerateRandomKey('addressBookInvitationCode');

        // set code
        $db->Query('UPDATE {pre}adressen SET invitationCode=? WHERE id=? AND user=?',
            $invitationCode,
            $contactID,
            $this->_userID);

        // send mail
        $vars = [
            'vorname' => $userRow['vorname'],
            'nachname' => $userRow['nachname'],
            'link' => sprintf('%sindex.php?action=completeAddressBookEntry&contact=%d&key=%s',
                $bm_prefs['selfurl'],
                $contactID,
                $invitationCode),
        ];
        if (SystemMail($thisUser->GetDefaultSender(),
            $eMailTo = ExtractMailAddress($addressType == ADDRESS_PRIVATE ? $eMail : $work_eMail),
            $lang_custom['selfcomp_sub'],
            'selfcomp_text',
            $vars)) {
            // log
            PutLog(sprintf('User <%s> (%d) invited <%s> to complete his/her address book entry (contact id: %d, IP: %s)',
                $userRow['email'],
                $this->_userID,
                $eMailTo,
                $contactID,
                $_SERVER['REMOTE_ADDR']),
                PRIO_NOTE,
                __FILE__,
                __LINE__);

            return true;
        } else {
            return false;
        }
    }

    /**
     * add a contact.
     *
     * @param string $firma          Company
     * @param string $vorname        First name
     * @param string $nachname       Last name
     * @param string $strassenr      Street / No
     * @param string $plz            ZIP
     * @param string $ort            City
     * @param string $land           Country
     * @param string $tel            Phone
     * @param string $fax            Fax
     * @param string $handy          Cellphone
     * @param string $email          E-Mail
     * @param string $work_strassenr Street / No
     * @param string $work_plz       ZIP
     * @param string $work_ort       City
     * @param string $work_land      Country
     * @param string $work_tel       Phone
     * @param string $work_fax       Fax
     * @param string $work_handy     Cellphone
     * @param string $work_email     E-Mail
     * @param string $anrede         Salutation
     * @param string $position       Position
     * @param string $web            WWW address
     * @param string $kommentar      Comment
     * @param int    $geburtsdatum   Birthday
     * @param int    $default        Default address
     * @param array  $groups         Groups
     * @param string $pictureFile    Picture file
     * @param string $pictureMime    Picture mime type
     *
     * @return int
     */
    public function AddContact($firma, $vorname, $nachname, $strassenr,
                        $plz, $ort, $land, $tel, $fax, $handy,
                        $email, $work_strassenr, $work_plz, $work_ort,
                        $work_land, $work_tel, $work_fax, $work_handy,
                        $work_email, $anrede, $position, $web, $kommentar,
                        $geburtsdatum, $default = ADDRESS_PRIVATE,	$groups = [],
                        $pictureFile = false, $pictureMime = false, $davURI = '', $davUID = '', $addressbookID = 0)
    {
        global $db;

        $addressbookID = $this->ResolveAddressbookID($addressbookID);
        if (!$this->CanWriteAddressbook($addressbookID)) {
            return 0;
        }
        $book = $this->GetAccessibleAddressbook($addressbookID);
        $ownerId = $book !== false ? (int) $book['user'] : $this->_userID;

        // add contact
        $db->Query('INSERT INTO {pre}adressen(user,firma,vorname,nachname,strassenr,plz,ort,land,tel,fax,handy,email,work_strassenr,work_plz,work_ort,work_land,work_tel,work_fax,work_handy,work_email,web,kommentar,default_address,anrede,position,geburtsdatum,dav_uri,dav_uid,addressbook_id) VALUES '
                            .'(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                            $ownerId,
                            $firma,
                            $vorname,
                            $nachname,
                            $strassenr,
                            $plz,
                            $ort,
                            $land,
                            $tel,
                            $fax,
                            $handy,
                            $email,
                            $work_strassenr,
                            $work_plz,
                            $work_ort,
                            $work_land,
                            $work_tel,
                            $work_fax,
                            $work_handy,
                            $work_email,
                            $web,
                            $kommentar,
                            $default,
                            $anrede,
                            $position,
                            $geburtsdatum,
                            $davURI,
                            $davUID,
                            $addressbookID);
        $contactID = $db->InsertID();

        // groups
        if ($contactID > 0 && count($groups) > 0) {
            foreach ($groups as $group) {
                $this->ContactGroup($contactID, (int) $group);
            }
        }

        // picture
        if ($pictureFile !== false) {
            $this->ChangePicture($contactID, $pictureFile, $pictureMime);
        }

        ChangelogAdded(BMCL_TYPE_CONTACT, $contactID, time());

        // return
        return $contactID;
    }

    /**
     * associate contact with group.
     *
     * F6: The historical implementation was hard-coded to the caller
     * ($this->_userID) for both `contact.user` and `group.user`. That
     * broke every CardDAV group-membership update in a shared book:
     * Bob (write-shared with Alice's book) couldn't add Alice's
     * contacts to Alice's groups because the SQL guard demanded that
     * both rows belong to Bob.
     *
     * The optional $ownerID parameter lets the caller override the
     * ownership context — CardDAV's updateGroupMembers() resolves the
     * book owner and passes it here.  Legacy callers (web UI) do not
     * pass anything and keep the safe "must be my own contact + my
     * own group" semantics.
     *
     * @param int      $contactID Contact ID
     * @param int      $groupID   Group ID
     * @param int|null $ownerID   Owner of both contact and group; when
     *                            NULL, defaults to $this->_userID.
     *
     * @return bool
     */
    public function ContactGroup($contactID, $groupID, $ownerID = null)
    {
        global $db;

        $contactID = (int) $contactID;
        $groupID = (int) $groupID;
        if ($contactID <= 0 || $groupID <= 0) {
            return false;
        }

        $effectiveOwner = ($ownerID === null) ? (int)$this->_userID : (int)$ownerID;

        // For non-owner callers, verify the calling user actually has
        // write access to a book that houses this group — otherwise a
        // shared-with-read (or completely unrelated) user could sneak
        // members into groups of a foreign owner.
        if ($effectiveOwner !== (int)$this->_userID) {
            $groupBookID = $this->_bookIDForGroup($groupID, $effectiveOwner);
            if ($groupBookID === 0 || !$this->CanWriteAddressbook($groupBookID)) {
                return false;
            }
        }

        $res = $db->Query('SELECT a.id FROM {pre}adressen a, {pre}adressen_gruppen g '
            .'WHERE a.id=? AND a.user=? AND g.id=? AND g.user=? AND a.addressbook_id=g.addressbook_id',
            $contactID,
            $effectiveOwner,
            $groupID,
            $effectiveOwner);
        if ($res->RowCount() !== 1) {
            $res->Free();

            return false;
        }
        $res->Free();

        $db->Query('REPLACE INTO {pre}adressen_gruppen_member(adresse,gruppe) VALUES(?,?)',
            $contactID,
            $groupID);
        if ($db->AffectedRows() == 1) {
            ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());

            return true;
        }

        return false;
    }

    /**
     * Look up the addressbook_id that houses a specific group. Used
     * by ContactGroup() when checking permissions for a cross-owner
     * (shared-book) invocation.
     *
     * @param int $groupID
     * @param int $ownerID
     *
     * @return int Book ID or 0 when the group doesn't exist for that owner.
     */
    private function _bookIDForGroup($groupID, $ownerID)
    {
        global $db;
        $res = $db->Query('SELECT `addressbook_id` FROM {pre}adressen_gruppen WHERE `id`=? AND `user`=?',
            (int)$groupID, (int)$ownerID);
        $row = $res->FetchArray(MYSQLI_NUM);
        $res->Free();
        return is_array($row) ? (int)$row[0] : 0;
    }

    /**
     * de-associate contact with group.
     *
     * F6 mirror of ContactGroup(): accept an optional $ownerID so
     * CardDAV can drop members from groups housed in a foreign,
     * write-shared book. The legacy web-UI code path (no $ownerID)
     * keeps the "only my own groups" check via GetGroupList().
     *
     * @param int      $contactID Contact ID
     * @param int      $groupID   Group ID
     * @param int|null $ownerID   Group owner override. When NULL, the
     *                            call is validated against the caller's
     *                            own group list.
     *
     * @return bool
     */
    public function DeContactGroup2($contactID, $groupID, $ownerID = null)
    {
        global $db;

        if ($ownerID === null) {
            $groups = $this->GetGroupList();
            if (!isset($groups[$groupID])) {
                return false;
            }
        } else {
            // Cross-owner path: caller must have write access to the
            // book that houses the group. Otherwise a shared-with-read
            // (or unrelated) caller could rip members out of a foreign
            // owner's groups.
            $groupBookID = $this->_bookIDForGroup($groupID, (int)$ownerID);
            if ($groupBookID === 0 || !$this->CanWriteAddressbook($groupBookID)) {
                return false;
            }
        }

        $db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE `adresse`=? AND `gruppe`=?',
            $contactID,
            $groupID);
        if ($db->AffectedRows() == 1) {
            ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());

            return true;
        }

        return false;
    }

    /**
     * remove contact from all groups.
     *
     * @param int $contactID Contact ID
     *
     * @return bool
     */
    public function DeContactGroup($contactID)
    {
        global $db;

        $res = $db->Query('SELECT `gruppe` FROM {pre}adressen_gruppen_member WHERE `adresse`=?',
            $contactID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $row['gruppe'], time());
        }
        $res->Free();

        $db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE adresse=?',
            $contactID);

        return $db->AffectedRows() == 1;
    }

    /**
     * delete a contact.
     *
     * @param int $contactID Contact ID
     *
     * @return bool
     */
    public function Delete($contactID)
    {
        global $db;

        $contact = $this->GetContact($contactID);
        if ($contact === false || !$this->CanWriteAddressbook((int) $contact['addressbook_id'])) {
            return false;
        }

        // F9: audit cross-owner deletes performed via a write-shared book.
        bmShareAuditLog($this->_userID, (int) $contact['user'],
            'contact_delete', (int) $contactID,
            trim(($contact['vorname'] ?? '') . ' ' . ($contact['nachname'] ?? '')));

        // delete contact
        $db->Query('DELETE FROM {pre}adressen WHERE id=? AND user=?',
            (int) $contactID,
            $contact['user']);

        // remove from groups
        if ($db->AffectedRows() == 1) {
            $this->DeContactGroup($contactID);

            ChangelogDeleted(BMCL_TYPE_CONTACT, $contactID, time());

            return true;
        }

        return false;
    }

    /**
     * delete a group.
     *
     * @param int $groupID Group ID
     *
     * @return bool
     */
    public function DeleteGroup($groupID)
    {
        global $db;

        $db->Query('DELETE FROM {pre}adressen_gruppen WHERE user=? AND id=?',
            $this->_userID,
            (int) $groupID);

        if ($db->AffectedRows() == 1) {
            $db->Query('DELETE FROM {pre}adressen_gruppen_member WHERE gruppe=?',
                (int) $groupID);

            ChangelogDeleted(BMCL_TYPE_CONTACTGROUP, $groupID, time());

            return true;
        }

        return false;
    }

    /**
     * fetch contact from database.
     *
     * @param int $contactID Contact ID
     *
     * @return array
     */
    public function GetContact($contactID)
    {
        global $db;

        $res = $db->Query('SELECT * FROM {pre}adressen WHERE id=?',
            $contactID);
        if ($res->RowCount() == 0) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ($this->GetAccessibleAddressbook((int) $row['addressbook_id']) === false) {
            return false;
        }

        return $row;
    }

    /**
     * get contact data for self complete invitation.
     *
     * @param int    $contactID Contact ID
     * @param string $key       Invitation code
     *
     * @return array
     */
    public function GetContactForSelfCompleteInvitation($contactID, $key)
    {
        global $db;

        $res = $db->Query('SELECT * FROM {pre}adressen WHERE id=? AND LENGTH(invitationCode)=32 AND invitationCode=?',
            $contactID,
            $key);
        if ($res->RowCount() == 0) {
            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();

        return $row;
    }

    /**
     * fetch group from database.
     *
     * @param int $groupID Group ID
     *
     * @return array
     */
    public function GetGroup($groupID)
    {
        global $db;

        $res = $db->Query('SELECT * FROM {pre}adressen_gruppen WHERE id=?',
            $groupID);
        if ($res->RowCount() == 0) {
            $res->Free();

            return false;
        }
        $row = $res->FetchArray(MYSQLI_ASSOC);
        $res->Free();
        if ($this->GetAccessibleAddressbook((int) $row['addressbook_id']) === false) {
            return false;
        }

        return $row;
    }

    /**
     * change group.
     *
     * @param int    $groupID Group ID
     * @param string $title   Title
     *
     * @return bool
     */
    public function ChangeGroup($groupID, $title)
    {
        global $db;

        $db->Query('UPDATE {pre}adressen_gruppen SET title=? WHERE id=? AND user=?',
            $title,
            (int) $groupID,
            $this->_userID);

        if ($db->AffectedRows() == 1) {
            ChangelogUpdated(BMCL_TYPE_CONTACTGROUP, $groupID, time());

            return true;
        }

        return false;
    }

    /**
     * export contast as VCF.
     *
     * @param int $contactID Contact ID
     *
     * @return string
     */
    public function ExportContact($contactID)
    {
        $contact = $this->GetContact($contactID);
        if ($contact) {
            if (!class_exists('VCardBuilder')) {
                include B1GMAIL_DIR.'serverlib/vcard.class.php';
            }
            $vcardBuilder = _new('VCardBuilder', [$contact]);

            return $vcardBuilder->Build();
        }

        return false;
    }

    /**
     * export certain contacts as CSV.
     *
     * @param array $contactIDs List of contacts (identified by ID)
     */
    public function ExportContacts($contactIDs)
    {
        global $db;

        $res = $db->Query('SELECT '.$this->_exportFields.' FROM {pre}adressen WHERE user=? AND id IN ? ORDER BY id ASC',
            $this->_userID,
            $contactIDs);
        $res->ExportCSV();
        $res->Free();
    }

    /**
     * export all contacts of certain groups as CSV.
     *
     * @param array $groupIDs List of groups (identified by ID)
     */
    public function ExportGroupContacts($groupIDs)
    {
        global $db;

        $contactIDs = [];
        $res = $db->Query('SELECT adresse FROM {pre}adressen_gruppen_member WHERE gruppe IN ?',
            $groupIDs);
        while ($row = $res->FetchArray(MYSQLI_NUM)) {
            $contactIDs[] = $row[0];
        }
        $res->Free();

        $this->ExportContacts($contactIDs);
    }

    /**
     * get name/mail for certain groups.
     *
     * @param array $groupIDs List of groups (identified by ID)
     *
     * @return array
     */
    public function GetGroupContactMails($groupIDs)
    {
        global $db;

        $result = [];
        $res = $db->Query('SELECT {pre}adressen.vorname AS vorname,{pre}adressen.nachname AS nachname,{pre}adressen.email AS email,{pre}adressen.work_email AS work_email,{pre}adressen.default_address AS default_address FROM {pre}adressen,{pre}adressen_gruppen_member WHERE {pre}adressen_gruppen_member.gruppe IN ? AND {pre}adressen.user=? AND {pre}adressen.id={pre}adressen_gruppen_member.adresse',
            $groupIDs,
            $this->_userID);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $result[] = $row;
        }
        $res->Free();

        return $result;
    }

    /**
     * get list of group members.
     *
     * F6: The original filter (`adressen.user = $this->_userID`) hides
     * every member of a foreign-owned group from a shared-with-write
     * caller, which then makes updateGroupMembers() in CardDAV recompute
     * the diff against an empty "oldMembers" set and mass-re-insert
     * (or mass-delete) memberships on every sync. The optional
     * $ownerID override lets CardDAV request the true member list
     * for the book owner's groups.
     *
     * @param int      $groupID ID of group
     * @param int|null $ownerID Owner context override; defaults to
     *                          $this->_userID.
     *
     * @return array
     */
    public function GetGroupMembers($groupID, $ownerID = null)
    {
        global $db;

        $effectiveOwner = ($ownerID === null) ? (int)$this->_userID : (int)$ownerID;

        $result = [];
        $res = $db->Query('SELECT {pre}adressen.`id` AS `id`, {pre}adressen.`dav_uri` AS `dav_uri`, {pre}adressen.`dav_uid` AS `dav_uid` '
            .'FROM {pre}adressen '
            .'INNER JOIN {pre}adressen_gruppen_member '
            .'ON {pre}adressen_gruppen_member.`adresse`={pre}adressen.`id` '
            .'WHERE {pre}adressen_gruppen_member.`gruppe`=? '
            .'AND {pre}adressen.`user`=?',
            $groupID,
            $effectiveOwner);
        while ($row = $res->FetchArray(MYSQLI_ASSOC)) {
            $result[$row['id']] = $row;
        }
        $res->Free();

        return $result;
    }

    /**
     * export whole addressbook.
     *
     * @param string $lineBreakChar Line break char
     * @param string $quoteChar     Quote char
     * @param string $sepChar       Seperator char
     * @param int    $addressbookID Address book (0 = default)
     */
    public function ExportAddressbook($lineBreakChar, $quoteChar, $sepChar, $addressbookID = 0)
    {
        global $db;

        $addressbookID = $this->ResolveAddressbookID($addressbookID);
        $res = $db->Query('SELECT '.$this->_exportFields.' FROM {pre}adressen WHERE user=? AND addressbook_id=? ORDER BY id ASC',
            $this->_userID,
            $addressbookID);
        $res->ExportCSV($lineBreakChar, $quoteChar, $sepChar);
        $res->Free();
    }

    /**
     * F5: Did the last Change() call refuse a cross-owner move?
     *
     * Reset at the top of every Change() call, so it always reflects the
     * outcome of the most recent contact update. Consumers should read
     * this immediately after Change() returns.
     *
     * @return bool
     */
    public function wasLastMoveBlocked()
    {
        return $this->_lastMoveBlocked;
    }
}
