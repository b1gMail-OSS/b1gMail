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

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

class BMOrganizerState extends BMSessionState
{
	public $addressbook;
	public $calendar;
	public $todo;

	/**
	 * Convert a b1gMail-internal timestamp to a real UTC unix timestamp for
	 * CalDAV/CardDAV output.
	 *
	 * Historical background: b1gMail stores organizer timestamps
	 * (startdate, enddate, beginn, faellig, ...) as REAL UTC unix epochs.
	 * The web UI achieves this by calling SetTimeZoneByOffsetSeconds() at
	 * login, which sets PHP's default timezone to the user's local zone.
	 * Under that timezone, mktime($h,$m,...) with the user's wall-clock
	 * yields the real UTC epoch for the local wall-clock time.
	 *
	 * The DAV entry points (interface/caldav.php etc.) do NOT go through
	 * that login path — see BMAuthBackend — so PHP's default timezone stays
	 * whatever php.ini set it to (typically UTC on production). That is
	 * actually the correct/desired state for the CalDAV serializer, because
	 * DTSTART/DTEND already have their own timezone info (Z suffix or
	 * TZID=...) attached via VObject.
	 *
	 * Therefore this is an identity function: the stored startdate IS the
	 * UTC epoch, so we pass it straight through to VObject.
	 *
	 * Previous bug: this function used to add the user's TZ offset, which
	 * shifted every DAV-visible date by (offset) into the future. Combined
	 * with fromUTC() being a no-op, that also caused DAV-created events to
	 * appear (offset) too late in the b1gMail web UI.
	 */
	public function toUTC($date)
	{
		return $date;
	}

	/**
	 * Convert a real UTC unix timestamp (from a CalDAV/CardDAV client) into
	 * the b1gMail-internal storage convention. See toUTC() — startdate is
	 * already a real UTC epoch, so this is an identity function too.
	 */
	public function fromUTC($date)
	{
		return $date;
	}

	public function getDisplayName()
	{
		return($this->userRow['vorname'] . ' ' . $this->userRow['nachname']);
	}

	public function getPrincipalURI()
	{
		return('principals/' . $this->userRow['email']);
	}

	public function genUID($davUID, $str)
	{
		if(!empty($davUID))
			return $davUID;
		$uid = md5($str);
		return(substr($uid, 0, 8)
			. '-' . substr($uid, 8, 4)
			. '-' . substr($uid, 12, 4)
			. '-' . substr($uid, 16, 4)
			. '-' . substr($uid, 20, 12));
	}

	function getLastModified($itemIDs, $itemType)
	{
		global $db;

		$result = array();
		$noArray = false;

		if(!is_array($itemIDs))
		{
			$noArray = true;
			$itemIDs = array($itemIDs);
		}

		$res = $db->Query('SELECT `itemid`,`created`,`updated` FROM {pre}changelog WHERE `itemtype`=? AND `itemid` IN ?',
			$itemType,
			$itemIDs);
		while($row = $res->FetchArray())
		{
			$result[ $row['itemid'] ] = max($row['updated'], $row['created']);
		}
		$res->Free();

		return($noArray ? array_pop($result) : $result);
	}

	/**
	 * Compute a CalDAV/CardDAV "CS:getctag" for a collection.
	 *
	 * The ctag is an opaque, changes-on-any-collection-mutation token. Clients
	 * (Thunderbird, macOS Calendar, DAVx5, ...) query it via a cheap PROPFIND
	 * on the collection URI to decide whether they need to run a full
	 * PROPFIND depth=1 + per-item GET to reconcile changes. Without a stable
	 * yet mutating ctag, Thunderbird will not detect server-side additions
	 * or deletions when item ETags happen to stay stable.
	 *
	 * The formula uses (a) the live item count from the primary table and
	 * (b) the MAX(created,updated) from the changelog:
	 *   - added/removed items → live count changes → ctag changes
	 *   - modified items      → changelog max_lm changes → ctag changes
	 *   - remove+add of same → both change → ctag changes
	 *
	 * $collectionID must be the b1gMail primary key (calendar_id / tasklistid
	 * / addressbook id). $itemType is one of the BMCL_TYPE_* constants.
	 *
	 * Returns a short opaque string or NULL if we don't know how to compute
	 * one for this itemType (in which case the caller should not advertise
	 * a ctag).
	 */
	public function getCollectionCtag($collectionID, $itemType)
	{
		global $db;

		$collectionID = (int)$collectionID;
		$userID = (int)$this->userRow['id'];

		if($itemType == BMCL_TYPE_CALENDAR)
		{
			$sql = 'SELECT COUNT(d.id) AS cnt, IFNULL(MAX(GREATEST(cl.created,cl.updated)),0) AS maxlm '
				. 'FROM {pre}dates d '
				. 'LEFT JOIN {pre}changelog cl ON cl.itemtype=? AND cl.itemid=d.id '
				. 'WHERE d.calendar_id=?';
			$res = $db->Query($sql, BMCL_TYPE_CALENDAR, $collectionID);
		}
		else if($itemType == BMCL_TYPE_TODO)
		{
			$sql = 'SELECT COUNT(t.id) AS cnt, IFNULL(MAX(GREATEST(cl.created,cl.updated)),0) AS maxlm '
				. 'FROM {pre}tasks t '
				. 'LEFT JOIN {pre}changelog cl ON cl.itemtype=? AND cl.itemid=t.id '
				. 'WHERE t.user=? AND t.tasklistid=?';
			$res = $db->Query($sql, BMCL_TYPE_TODO, $userID, $collectionID);
		}
		else if($itemType == BMCL_TYPE_CONTACT)
		{
			$sql = 'SELECT COUNT(a.id) AS cnt, IFNULL(MAX(GREATEST(cl.created,cl.updated)),0) AS maxlm '
				. 'FROM {pre}adressen a '
				. 'LEFT JOIN {pre}changelog cl ON cl.itemtype=? AND cl.itemid=a.id '
				. 'WHERE a.user=? AND a.addressbook_id=?';
			$res = $db->Query($sql, BMCL_TYPE_CONTACT, $userID, $collectionID);
		}
		else
		{
			return(null);
		}

		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$cnt   = isset($row['cnt'])   ? (int)$row['cnt']   : 0;
		$maxLm = isset($row['maxlm']) ? (int)$row['maxlm'] : 0;

		return(md5($collectionID . ':' . $cnt . ':' . $maxLm));
	}

	public function getProdID()
	{
		return('-//b1gMail Project//b1gMail ' . B1GMAIL_VERSION . '//EN');
	}
}

class BMPrincipalBackend extends Sabre\DAVACL\PrincipalBackend\AbstractBackend
{
	function getPrincipalsByPrefix($prefixPath)
	{
		global $os;

		$result = array();

		if($prefixPath == 'principals' && !empty($os->userRow['email']))
		{
			$result[] = array('uri' => $os->getPrincipalURI(),
				'{DAV:}displayname' => $os->getDisplayName());
		}

		return($result);
	}

	/**
	 * Resolve a principal URI to a principal descriptor.
	 *
	 * IMPORTANT: This method MUST return a principal even when the request is
	 * not yet authenticated. HTTP Basic Auth clients (Thunderbird, macOS
	 * Calendar, Nautilus, curl -u, ...) commonly perform "opportunistic auth":
	 * they hit a new URL WITHOUT credentials, expect a 401 challenge, then
	 * retry with the Authorization header. If we return NULL here for an
	 * unauthenticated request, Sabre throws NotFound (404) BEFORE the DAVACL
	 * plugin can raise NotAuthenticated (401), so the client never learns it
	 * needs to send credentials — and CalDAV/CardDAV discovery breaks.
	 *
	 * We therefore always return a lightweight placeholder principal for any
	 * well-formed principals/<name> path. This is safe because:
	 *  - the placeholder contains no data derived from the user database
	 *    (no info leak);
	 *  - the DAVACL plugin will reject any subsequent property/collection
	 *    access with 401 when the caller is not authenticated;
	 *  - once authenticated, we return the real principal for the current
	 *    user (which is the only case existing code paths need anyway).
	 */
	function getPrincipalByPath($path)
	{
		global $os;

		if(strpos($path, 'principals/') !== 0)
			return;

		$requestedEmail = rtrim(substr($path, strlen('principals/')), '/');
		if($requestedEmail === '')
			return;

		// Authenticated user requesting their own principal — return the
		// canonical, database-backed descriptor.
		if(!empty($os->userRow['email']) && $requestedEmail === $os->userRow['email'])
		{
			return(array('id' => $os->userRow['id'],
				'uri' => $os->getPrincipalURI(),
				'{DAV:}displayname' => $os->getDisplayName()));
		}

		// Anything else: minimal placeholder so DAVACL can enforce auth
		// with a proper 401 challenge instead of us leaking a 404.
		return(array('id' => 0,
			'uri' => 'principals/' . $requestedEmail,
			'{DAV:}displayname' => $requestedEmail));
	}

	function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch)
	{
	}

	function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
	{
		$result = array();

		if($prefixPath == 'principals')
		{
			foreach($searchProperties as $property=>$value)
			{
				if($property == '{DAV:}displayname')
				{
					if(stripos($os->getDisplayName(), $value) !== false)
						$result[] = 'principals/' .  $os->userRow['email'];
				}
				else
					return($result);
			}
		}

		return($result);
	}

	function getGroupMemberSet($principal)
	{
		return(array());
	}

	function getGroupMembership($principal)
	{
		return(array());
	}

	function setGroupMemberSet($principal, array $members)
	{
	}
}
