<?php
/*
 * b1gMail Postfix Transport List Creator
 * Copyright (c) 2022 b1gMail.eu
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
@ini_set('display_errors',0); // Don't display error messages on screen
require '../src/serverlib/init.inc.php';
$domains = GetDomainList();
function getDomainAliasesFromQuery($res,$domains)
{
    while ($row = $res->FetchArray()) {
        if ($row['saliase'] != '') {
            $domainList2 = explode(':', $row['saliase']);
            foreach ($domainList2 as $domain) {
                if (!in_array($domain, $domains)) {
                    $domains[] = $domain;
                }
            }
        }
    }
    return $domains;
}
$res = $db->Query('SELECT saliase FROM {pre}gruppen WHERE saliase IS NOT NULL;'); // Get custom alias domains from groups
$domains = getDomainAliasesFromQuery($res,$domains);
$res = $db->Query('SELECT saliase FROM {pre}users WHERE saliase IS NOT NULL;'); // Get custom alias domains from users
$domains = getDomainAliasesFromQuery($res,$domains);

foreach ($domains as $domain) {
    echo $domain.' b1gmailtransport:dummy'."\n";
}
