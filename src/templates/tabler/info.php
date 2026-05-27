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

$templateInfo = array(
	'title'			=> 'Tabler',
	'author'		=> 'OneSystems GmbH',
	'website'		=> 'https://www.onesystems.ch/',
	'for_b1gmail'	=> B1GMAIL_VERSION,

	'prefs'	=> array(
		'loginStyle'	=> array(
			'title'		=> 'Login-Seite:',
			'type'		=> FIELD_DROPDOWN,
			'options'	=> array(
				'cover'		=> 'Kompakt mit Cover-Bild (Tabler)',
				'center'	=> 'Kompakt zentriert (Tabler)',
				'msp'		=> 'MSP (Infos & Registrierung)',
			),
			'default'	=> 'cover'
		),
		'splashImage'	=> array(
			'title'		=> $lang_admin['splashimage'] . ':',
			'type'		=> FIELD_DROPDOWN,
			'options'	=> array(
				'login_bg_1.png'	=> 'b1gMail Dark',
				'login_bg_2.png'	=> 'b1gMail Light',
				'login_bg_3.png'	=> 'b1gMail Blue',
			),
			'default'	=> 'login_bg_3.png'
		),
		'hideSignup'	=> array(
			'title'		=> $lang_admin['hidesignup'] . '?',
			'type'		=> FIELD_CHECKBOX,
			'default'	=> false
		),
		'prefsLayout'	=> array(
			'title'		=> $lang_admin['prefslayout'] . ':',
			'type'		=> FIELD_DROPDOWN,
			'options'	=> array('onecolumn'		=> $lang_admin['onecolumn'],
									'twocolumns'	=> $lang_admin['twocolumns']),
			'default'	=> 'onecolumn'
		),
		'showUserEmail'	=> array(
			'title'		=> $lang_admin['showuseremail'] . '?',
			'type'		=> FIELD_CHECKBOX,
			'default'	=> false
		),
		'showCheckboxes'=> array(
			'title'		=> $lang_admin['showcheckboxes'] . '?',
			'type'		=> FIELD_CHECKBOX,
			'default'	=> false
		),
		'mailListPreviewLines'	=> array(
			'title'		=> 'Mail-Liste: Body-Vorschau',
			'type'		=> FIELD_DROPDOWN,
			'options'	=> array(
				'0'	=> 'Aus (nur Betreff)',
				'1'	=> '1 Zeile',
				'2'	=> '2 Zeilen',
			),
			'default'	=> '2'
		),
		'enableDarkMode'	=> array(
			'title'		=> 'Dark Mode umschaltbar?',
			'type'		=> FIELD_CHECKBOX,
			'default'	=> true
		)
	)
);
