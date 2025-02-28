<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al, 2022 b1gMail.eu
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

// extract version
$databaseStructureVersion = '1.70';
define('DATABASE_STRUCT_HASH', '75ea517ee222f527fefbfff660189edf03225e0f2bd25082bead50c7b43fd08d');

// structure
$databaseStructure = file_get_contents(__DIR__.'/database.struct.json');
if(hash('sha256', $databaseStructure) != DATABASE_STRUCT_HASH) {
	die('Hash mismatch, please upload database.struct.json again');
}