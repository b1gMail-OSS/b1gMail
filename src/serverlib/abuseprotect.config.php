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

$apTypes = array(
	BMAP_SEND_RECP_LIMIT			=> array(
		'title'					=> $lang_admin['ap_type1'],
		'defaultPoints'			=> 5,
		'prefs'					=> array()
	),
	BMAP_SEND_FREQ_LIMIT			=> array(
		'title'					=> $lang_admin['ap_type2'],
		'defaultPoints'			=> 25,
		'prefs'					=> array()
	),
	BMAP_SEND_RECP_BLOCKED			=> array(
		'title'					=> $lang_admin['ap_type3'],
		'defaultPoints'			=> 15,
		'prefs'					=> array()
	),
	BMAP_SEND_RECP_LOCAL_INVALID	=> array(
		'title'					=> $lang_admin['ap_type4'],
		'defaultPoints'			=> 10,
		'prefs'					=> array()
	),
	BMAP_SEND_RECP_DOMAIN_INVALID	=> array(
		'title'					=> $lang_admin['ap_type5'],
		'defaultPoints'			=> 10,
		'prefs'					=> array()
	),
	BMAP_SEND_WITHOUT_RECEIVE		=> array(
		'title'					=> $lang_admin['ap_type6'],
		'defaultPoints'			=> 20,
		'prefs'					=> array(
			'interval'				=> array(
				'title'				=> $lang_admin['limit_interval_m'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '60'
			)
		)
	),
	BMAP_SEND_FAST					=> array(
		'title'					=> $lang_admin['ap_type7'],
		'defaultPoints'			=> 20,
		'prefs'					=> array(
			'interval'			=> array(
				'title'				=> $lang_admin['min_resend_interval_s'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '5'
			)
		)
	),
	BMAP_RECV_FREQ_LIMIT			=> array(
		'title'					=> $lang_admin['ap_type21'],
		'defaultPoints'			=> 5,
		'prefs'					=> array(
			'amount'				=> array(
				'title'				=> $lang_admin['limit_amount_count'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '50'
			),
			'interval'				=> array(
				'title'				=> $lang_admin['limit_interval_m'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '5'
			)
		)
	),
	BMAP_RECV_TRAFFIC_LIMIT			=> array(
		'title'					=> $lang_admin['ap_type22'],
		'defaultPoints'			=> 5,
		'prefs'					=> array(
			'amount'				=> array(
				'title'				=> $lang_admin['limit_amount_mb'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '100'
			),
			'interval'				=> array(
				'title'				=> $lang_admin['limit_interval_m'].':',
				'type'				=> FIELD_TEXT,
				'default'			=> '5'
			)
		)
	)
);
