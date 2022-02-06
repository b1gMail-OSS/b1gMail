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

if(PHPNumVersion() < 540)
{
	DisplayError(0x18, 'PHP version too old', 'b1gMail DAV features require PHP 5.4 or newer.',
		sprintf("Installed PHP version:\n%s", phpversion()),
		__FILE__,
		__LINE__);
	exit();
}

include(B1GMAIL_DIR . 'serverlib/dav-real.inc.php');
