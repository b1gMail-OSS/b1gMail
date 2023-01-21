<?php
/*
 * b1gMailServer admin plugin
 * (c) 2021 Patrick Schlangen et al
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

define('BMS_LOG_NOTICE', 				1);		// priority constants
define('BMS_LOG_WARNING', 				2);
define('BMS_LOG_ERROR', 				4);
define('BMS_LOG_DEBUG', 				8);
define('BMS_CMP_CORE',					1);		// components
define('BMS_CMP_POP3',					2);
define('BMS_CMP_IMAP',					4);
define('BMS_CMP_HTTP',					8);
define('BMS_CMP_FTP',					9);
define('BMS_CMP_SMTP',					16);
define('BMS_CMP_MSGQUEUE',				32);
define('BMS_CMP_PLUGIN',				64);
define('BMS_EVENT_STOREMAIL',			1);		// events
define('BMS_EVENT_DELETEMAIL',			2);
define('BMS_EVENT_MOVEMAIL',			3);
define('BMS_EVENT_CHANGEMAILFLAGS',		4);
define('BMS_CORE_FEATURE_TLS',			1);		// core features
define('BMS_CORE_FEATURE_ALTERMIME',	2);
define('BMS_FAILBAN_POP3LOGIN',			1);		// failban types
define('BMS_FAILBAN_IMAPLOGIN',			2);
define('BMS_FAILBAN_SMTPLOGIN',			4);
define('BMS_FAILBAN_SMTPRCPT',			8);
define('BMS_FAILBAN_FTPLOGIN',			16);
define('BMS_EVENTQUEUE_MAX',			500);

if(!defined('MYSQLI_NUM'))
	define('MYSQLI_NUM',			MYSQL_NUM);
if(!defined('MYSQLI_ASSOC'))
	define('MYSQLI_ASSOC',			MYSQL_ASSOC);
if(!defined('MYSQLI_BOTH'))
	define('MYSQLI_BOTH',			MYSQL_BOTH);

/**
 * plugin interface
 *
 */
class B1GMailServerAdmin extends BMPlugin
{
	/**
	 * b1gMailServer prefs
	 *
	 * @var array
	 */
	var $prefs;

	/**
	 * constructor
	 *
	 * @return B1GMailServerAdmin
	 */
	function __construct()
	{
		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'b1gMailServer Administration PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.151';
		$this->website				= 'https://www.b1gmail.org/';
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'b1gMailServer';
		$this->admin_page_icon		= 'bms_logo.png';

		// group options
		$this->RegisterGroupOption('wdhttpadsig',
			FIELD_TEXTAREA,
			'Webdisk-Werbung-Code:',
			'',
			'');
		$this->RegisterGroupOption('minpop3',
			FIELD_TEXT,
			'Min. POP3-Sitzg.-Abst.:',
			'',
			0);
		$this->RegisterGroupOption('smtp_sendercheck',
			FIELD_DROPDOWN,
			'SMTP-Absender-Check?',
			array('no' => 'Nein', 'mailfrom' => 'Nur MAIL FROM', 'full' => 'MAIL FROM und From-Header'),
			'mailfrom');
		$this->RegisterGroupOption('require_weblogin',
			FIELD_CHECKBOX,
			'POP3/IMAP/SMTP erfordert erstmaligen Web-Login?',
			'',
			true);
		$this->RegisterGroupOption('weblogin_interval',
			FIELD_TEXT,
			'POP3/IMAP/SMTP erfordert Web-Login-Intervall (Tage):',
			'',
			0);

		list($vMajor, $vMinor) = explode('.', B1GMAIL_VERSION);
		if($vMajor == 7 && $vMinor < 4)
		{
			$this->RegisterGroupOption('smtplimit_count',
				FIELD_TEXT,
				'SMTP-Limit (Mails):',
				'',
				30);
			$this->RegisterGroupOption('smtplimit_time',
				FIELD_TEXT,
				'SMTP-Limit (Minuten):',
				'',
				60);
		}
	}

	/**
	 * installer
	 *
	 */
	function Install()
	{
		global $db, $bm_prefs;

		// get tables
		$haveGreylistTable = false;
		$res = $db->Query('SHOW TABLES');
		while($row = $res->FetchArray(MYSQLI_NUM))
		{
			if($row[0] == 'bm60_bms_greylist')
			{
				$haveGreylistTable = true;
				break;
			}
		}
		$res->Free();

		// check table
		if($haveGreylistTable)
		{
			$greylistTableOldFormat = true;

			$res = $db->Query('SHOW FIELDS FROM bm60_bms_greylist');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if($row['Field'] == 'id')
				{
					$greylistTableOldFormat = false;
					break;
				}
			}
			$res->Free();

			if($greylistTableOldFormat)
				$db->Query('ALTER TABLE bm60_bms_greylist DROP PRIMARY KEY, ADD COLUMN `id` int(11) NOT NULL auto_increment FIRST, ADD PRIMARY KEY (`id`)');
		}

		// db struct
		$databaseStructure =                      // checksum: 5244e8b8ad5dcada6b144890896a2b2c
				'YToxNjp7czoyNjoiYm02MF9ibXNfYXBuc19zdWJzY3JpcHRpb24iO2E6Mjp7czo2OiJmaWVsZHM'
			. 'iO2E6NTp7aTowO2E6Njp7aTowO3M6MTQ6InN1YnNjcmlwdGlvbmlkIjtpOjE7czozOiJpbnQiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVud'
			. 'CI7fWk6MTthOjY6e2k6MDtzOjY6InVzZXJpZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7'
			. 'aTozO3M6MzoiTVVMIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMDo'
			. 'iYWNjb3VudF9pZCI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOj'
			. 'A6IiI7aTo0O047aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6MTI6ImRldmljZV90b2tlbiI7a'
			. 'ToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1'
			. 'O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6ODoic3VidG9waWMiO2k6MTtzOjEyOiJ2YXJjaGFyKDE'
			. 'yOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fX1zOjc6ImluZG'
			. 'V4ZXMiO2E6Mjp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjE0OiJzdWJzY3JpcHRpb25pZCI7f'
			. 'XM6NjoidXNlcmlkIjthOjE6e2k6MDtzOjY6InVzZXJpZCI7fX19czozMzoiYm02MF9ibXNfYXBu'
			. 'c19zdWJzY3JpcHRpb25fZm9sZGVyIjthOjI6e3M6NjoiZmllbGRzIjthOjI6e2k6MDthOjY6e2k'
			. '6MDtzOjg6ImZvbGRlcmlkIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUk'
			. 'kiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTthOjY6e2k6MDtzOjE0OiJzdWJzY3JpcHRpb'
			. '25pZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MzoiUFJJIjtpOjQ7czoxOiIw'
			. 'IjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToyOntpOjA'
			. '7czo4OiJmb2xkZXJpZCI7aToxO3M6MTQ6InN1YnNjcmlwdGlvbmlkIjt9fX1zOjIyOiJibTYwX2'
			. 'Jtc19kZWxpdmVyeXJ1bGVzIjthOjI6e3M6NjoiZmllbGRzIjthOjg6e2k6MDthOjY6e2k6MDtzO'
			. 'jE0OiJkZWxpdmVyeXJ1bGVpZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6Mzoi'
			. 'UFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZW1lbnQiO31pOjE7YTo2OntpOjA7czo5OiJ'
			. 'tYWlsX3R5cGUiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6ND'
			. 'tzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjEyOiJydWxlX3N1YmplY3QiO2k6M'
			. 'TtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtz'
			. 'OjA6IiI7fWk6MzthOjY6e2k6MDtzOjQ6InJ1bGUiO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k'
			. '6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOj'
			. 'Y6InRhcmdldCI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O'
			. '3M6MToiMCI7aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6MTI6InRhcmdldF9wYXJhbSI7aTox'
			. 'O3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M'
			. '6MDoiIjt9aTo2O2E6Njp7aTowO3M6NToiZmxhZ3MiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOj'
			. 'I6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzO'
			. 'jM6InBvcyI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIw'
			. 'IjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA'
			. '7czoxNDoiZGVsaXZlcnlydWxlaWQiO319fXM6MTQ6ImJtNjBfYm1zX2Ruc2JsIjthOjI6e3M6Nj'
			. 'oiZmllbGRzIjthOjU6e2k6MDthOjY6e2k6MDtzOjI6ImlkIjtpOjE7czozOiJpbnQiO2k6MjtzO'
			. 'jI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6'
			. 'MTthOjY6e2k6MDtzOjQ6Imhvc3QiO2k6MTtzOjExOiJ2YXJjaGFyKDY0KSI7aToyO3M6MjoiTk8'
			. 'iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6MTQ6ImNsYXNzaW'
			. 'ZpY2F0aW9uIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7c'
			. 'zoxOiIzIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czozOiJwb3MiO2k6MTtzOjM6ImludCI7'
			. 'aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp'
			. '7aTowO3M6NDoidHlwZSI7aToxO3M6MjY6ImVudW0oJ2lwdjQnLCdpcHY2JywnYm90aCcpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo0OiJpcHY0IjtpOjU7czowOiIiO319czo3OiJpb'
			. 'mRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7czoyOiJpZCI7fX19czoxOToiYm02'
			. 'MF9ibXNfZXZlbnRxdWV1ZSI7YToyOntzOjY6ImZpZWxkcyI7YTo1OntpOjA7YTo2OntpOjA7czo'
			. '3OiJldmVudGlkIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6ND'
			. 'tOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthOjY6e2k6MDtzOjY6InVzZXJpZCI7a'
			. 'ToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czow'
			. 'OiIiO31pOjI7YTo2OntpOjA7czo0OiJ0eXBlIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJ'
			. 'OTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo2Oi'
			. 'JwYXJhbTEiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O'
			. '3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6NjoicGFyYW0yIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6'
			. 'Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo'
			. '3OiJQUklNQVJZIjthOjE6e2k6MDtzOjc6ImV2ZW50aWQiO319fXM6MTY6ImJtNjBfYm1zX2ZhaW'
			. 'xiYW4iO2E6Mjp7czo2OiJmaWVsZHMiO2E6ODp7aTowO2E6Njp7aTowO3M6MjoiaWQiO2k6MTtzO'
			. 'jM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9f'
			. 'aW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6MjoiaXAiO2k6MTtzOjM6ImludCI7aToyO3M6Mjo'
			. 'iTk8iO2k6MztzOjM6Ik1VTCI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3'
			. 'M6MTA6ImVudHJ5X2RhdGUiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7a'
			. 'To0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6MTI6ImJhbm5lZF91bnRpbCI7'
			. 'aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czo'
			. 'wOiIiO31pOjQ7YTo2OntpOjA7czoxMToibGFzdF91cGRhdGUiO2k6MTtzOjM6ImludCI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO'
			. '3M6ODoiYXR0ZW1wdHMiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIi'
			. 'O2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NjthOjY6e2k6MDtzOjQ6InR5cGUiO2k6MTtzOjc'
			. '6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6Ii'
			. 'I7fWk6NzthOjY6e2k6MDtzOjM6ImlwNiI7aToxO3M6ODoiY2hhcigzMikiO2k6MjtzOjI6Ik5PI'
			. 'jtpOjM7czozOiJNVUwiO2k6NDtOO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6Mzp7czo3'
			. 'OiJQUklNQVJZIjthOjE6e2k6MDtzOjI6ImlkIjt9czoyOiJpcCI7YToxOntpOjA7czoyOiJpcCI'
			. '7fXM6MzoiaXA2IjthOjE6e2k6MDtzOjM6ImlwNiI7fX19czoxNzoiYm02MF9ibXNfZ3JleWxpc3'
			. 'QiO2E6Mjp7czo2OiJmaWVsZHMiO2E6NTp7aTowO2E6Njp7aTowO3M6MjoiaWQiO2k6MTtzOjM6I'
			. 'mludCI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5j'
			. 'cmVtZW50Ijt9aToxO2E6Njp7aTowO3M6MjoiaXAiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8'
			. 'iO2k6MztzOjM6Ik1VTCI7aTo0O047aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6NDoidGltZS'
			. 'I7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO'
			. '31pOjM7YTo2OntpOjA7czo5OiJjb25maXJtZWQiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6'
			. 'Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjM'
			. '6ImlwNiI7aToxO3M6ODoiY2hhcigzMikiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJNVUwiO2k6ND'
			. 'tOO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6Mzp7czo3OiJQUklNQVJZIjthOjE6e2k6M'
			. 'DtzOjI6ImlkIjt9czozOiJpcDYiO2E6MTp7aTowO3M6MzoiaXA2Ijt9czoyOiJpcCI7YToxOntp'
			. 'OjA7czoyOiJpcCI7fX19czoxNjoiYm02MF9ibXNfaW1hcHVpZCI7YToyOntzOjY6ImZpZWxkcyI'
			. '7YToyOntpOjA7YTo2OntpOjA7czo3OiJpbWFwdWlkIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik'
			. '5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthO'
			. 'jY6e2k6MDtzOjY6Im1haWxpZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6Mzoi'
			. 'TVVMIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjI6e3M6NzoiUFJ'
			. 'JTUFSWSI7YToxOntpOjA7czo3OiJpbWFwdWlkIjt9czo2OiJtYWlsaWQiO2E6MTp7aTowO3M6Nj'
			. 'oibWFpbGlkIjt9fX1zOjEzOiJibTYwX2Jtc19sb2dzIjthOjI6e3M6NjoiZmllbGRzIjthOjU6e'
			. '2k6MDthOjY6e2k6MDtzOjI6ImlkIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czoz'
			. 'OiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthOjY6e2k6MDtzOjE'
			. 'wOiJpQ29tcG9uZW50IjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIj'
			. 'tpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czo5OiJpU2V2ZXJpdHkiO2k6M'
			. 'TtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6'
			. 'NTtzOjA6IiI7fWk6MzthOjY6e2k6MDtzOjU6ImlEYXRlIjtpOjE7czozOiJpbnQiO2k6MjtzOjI'
			. '6Ik5PIjtpOjM7czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MD'
			. 'tzOjc6InN6RW50cnkiO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k6MjtzOjM6IllFUyI7aTozO'
			. '3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjM6e3M6NzoiUFJJTUFS'
			. 'WSI7YToxOntpOjA7czoyOiJpZCI7fXM6OToiaVNldmVyaXR5IjthOjE6e2k6MDtzOjk6ImlTZXZ'
			. 'lcml0eSI7fXM6NToiaURhdGUiO2E6MTp7aTowO3M6NToiaURhdGUiO319fXM6MTY6ImJtNjBfYm'
			. '1zX21pbHRlcnMiO2E6Mjp7czo2OiJmaWVsZHMiO2E6Nzp7aTowO2E6Njp7aTowO3M6ODoibWlsd'
			. 'GVyaWQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1'
			. 'O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6NToidGl0bGUiO2k6MTtzOjE'
			. 'yOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6Ii'
			. 'I7fWk6MjthOjY6e2k6MDtzOjg6Imhvc3RuYW1lIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpO'
			. 'jI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo5OiJsb2NhbGhvc3QiO2k6NTtzOjA6IiI7fWk6'
			. 'MzthOjY6e2k6MDtzOjQ6InBvcnQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA'
			. '6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6NToiZmxhZ3MiO2k6MT'
			. 'tzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6N'
			. 'TtzOjA6IiI7fWk6NTthOjY6e2k6MDtzOjE0OiJkZWZhdWx0X2FjdGlvbiI7aToxO3M6NzoidGlu'
			. 'eWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiMTE2IjtpOjU7czowOiIiO31'
			. 'pOjY7YTo2OntpOjA7czozOiJwb3MiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOj'
			. 'A6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToyOntzOjc6IlBSS'
			. 'U1BUlkiO2E6MTp7aTowO3M6ODoibWlsdGVyaWQiO31zOjU6ImZsYWdzIjthOjE6e2k6MDtzOjU6'
			. 'ImZsYWdzIjt9fX1zOjEzOiJibTYwX2Jtc19tb2RzIjthOjI6e3M6NjoiZmllbGRzIjthOjg6e2k'
			. '6MDthOjY6e2k6MDtzOjg6ImZpbGVuYW1lIjtpOjE7czoxMToidmFyY2hhcig2NCkiO2k6MjtzOj'
			. 'I6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MTthOjY6e2k6MDtzOjQ6I'
			. 'm5hbWUiO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6'
			. 'NDtOO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjU6InRpdGxlIjtpOjE7czoxMjoidmFyY2h'
			. 'hcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjM7YT'
			. 'o2OntpOjA7czo3OiJ2ZXJzaW9uIjtpOjE7czoxMToidmFyY2hhcigzMikiO2k6MjtzOjI6Ik5PI'
			. 'jtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjY6ImF1dGhvciI7'
			. 'aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo'
			. '1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6MTQ6ImF1dGhvcl93ZWJzaXRlIjtpOjE7czoxMjoidm'
			. 'FyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pO'
			. 'jY7YTo2OntpOjA7czoxMDoidXBkYXRlX3VybCI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToy'
			. 'O3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo3O2E6Njp7aTowO3M6Njo'
			. 'iYWN0aXZlIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7Tj'
			. 'tpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7c'
			. 'zo4OiJmaWxlbmFtZSI7fX19czoxNDoiYm02MF9ibXNfcHJlZnMiO2E6Mjp7czo2OiJmaWVsZHMi'
			. 'O2E6MTA0OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI'
			. '7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZW1lbnQiO31pOjE7YTo2On'
			. 'tpOjA7czoxMjoicG9wM2dyZWV0aW5nIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyO'
			. 'iJOTyI7aTozO3M6MDoiIjtpOjQ7czozMjoiYjFnTWFpbFNlcnZlciBQT1AzIHNlcnZpY2UgcmVh'
			. 'ZHkiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjEyOiJpbWFwZ3JlZXRpbmciO2k6MTtzOjE'
			. 'yOiJ2YXJjaGFyKDI1NSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjMyOiJiMWdNYW'
			. 'lsU2VydmVyIElNQVAgc2VydmljZSByZWFkeSI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6O'
			. 'DoibG9nbGV2ZWwiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6'
			. 'MToiNyI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6MTI6ImNvcmVfdmVyc2lvbiI7aToxO3M'
			. '6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MD'
			. 'oiIjt9aTo1O2E6Njp7aTowO3M6NzoiYWx0cG9wMyI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOT'
			. 'yI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjY7YTo2OntpOjA7czo3OiJt'
			. 'aW5wb3AzIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjA'
			. 'iO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzOjc6Im1pbmltYXAiO2k6MTtzOjM6ImludCI7aT'
			. 'oyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo4O2E6Njp7a'
			. 'TowO3M6MTI6InNtdHBncmVldGluZyI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6Mjoi'
			. 'Tk8iO2k6MztzOjA6IiI7aTo0O3M6MzI6ImIxZ01haWxTZXJ2ZXIgU01UUCBzZXJ2aWNlIHJlYWR'
			. '5IjtpOjU7czowOiIiO31pOjk7YTo2OntpOjA7czoxOToic210cF9ncmVldGluZ19kZWxheSI7aT'
			. 'oxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiNSI7aTo1O'
			. '3M6MDoiIjt9aToxMDthOjY6e2k6MDtzOjEzOiJncmV5X2ludGVydmFsIjtpOjE7czozOiJpbnQi'
			. 'O2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjYwIjtpOjU7czowOiIiO31pOjExO2E'
			. '6Njp7aTowO3M6MTQ6ImdyZXlfd2FpdF90aW1lIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIj'
			. 'tpOjM7czowOiIiO2k6NDtzOjU6IjI4ODAwIjtpOjU7czowOiIiO31pOjEyO2E6Njp7aTowO3M6M'
			. 'TQ6ImdyZXlfZ29vZF90aW1lIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIi'
			. 'O2k6NDtzOjY6IjI1OTIwMCI7aTo1O3M6MDoiIjt9aToxMzthOjY6e2k6MDtzOjEyOiJncmV5X2V'
			. 'uYWJsZWQiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOj'
			. 'E6IjEiO2k6NTtzOjA6IiI7fWk6MTQ7YTo2OntpOjA7czoxNzoic210cF9hdXRoX2VuYWJsZWQiO'
			. '2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6'
			. 'NTtzOjA6IiI7fWk6MTU7YTo2OntpOjA7czoxMjoicG9wM190aW1lb3V0IjtpOjE7czozOiJpbnQ'
			. 'iO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IjM2MCI7aTo1O3M6MDoiIjt9aToxNj'
			. 'thOjY6e2k6MDtzOjEyOiJpbWFwX3RpbWVvdXQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO'
			. '2k6MztzOjA6IiI7aTo0O3M6NDoiMzYwMCI7aTo1O3M6MDoiIjt9aToxNzthOjY6e2k6MDtzOjEy'
			. 'OiJzbXRwX3RpbWVvdXQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo'
			. '0O3M6MjoiNjAiO2k6NTtzOjA6IiI7fWk6MTg7YTo2OntpOjA7czoxNDoicXVldWVfaW50ZXJ2YW'
			. 'wiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiMzAwIjtpO'
			. 'jU7czowOiIiO31pOjE5O2E6Njp7aTowO3M6MTQ6InF1ZXVlX2xpZmV0aW1lIjtpOjE7czozOiJp'
			. 'bnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjY6IjI1OTIwMCI7aTo1O3M6MDoiIjt'
			. '9aToyMDthOjY6e2k6MDtzOjExOiJxdWV1ZV9yZXRyeSI7aToxO3M6MzoiaW50IjtpOjI7czoyOi'
			. 'JOTyI7aTozO3M6MDoiIjtpOjQ7czo0OiIzNjAwIjtpOjU7czowOiIiO31pOjIxO2E6Njp7aTowO'
			. '3M6MTU6InNtdHBfc2l6ZV9saW1pdCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6'
			. 'MDoiIjtpOjQ7czo4OiIxMDQ4NTc2MCI7aTo1O3M6MDoiIjt9aToyMjthOjY6e2k6MDtzOjIwOiJ'
			. 'zbXRwX3JlY2lwaWVudF9saW1pdCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MD'
			. 'oiIjtpOjQ7czoyOiIyNSI7aTo1O3M6MDoiIjt9aToyMzthOjY6e2k6MDtzOjE2OiJzbXRwX2Vyc'
			. 'm9yX2RlbGF5IjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7'
			. 'czoxOiIxIjtpOjU7czowOiIiO31pOjI0O2E6Njp7aTowO3M6MjA6InNtdHBfZXJyb3Jfc29mdGx'
			. 'pbWl0IjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOi'
			. 'IxMCI7aTo1O3M6MDoiIjt9aToyNTthOjY6e2k6MDtzOjIwOiJzbXRwX2Vycm9yX2hhcmRsaW1pd'
			. 'CI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MjoiMjAi'
			. 'O2k6NTtzOjA6IiI7fWk6MjY7YTo2OntpOjA7czo4OiJwaHBfcGF0aCI7aToxO3M6MTI6InZhcmN'
			. 'oYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MTI6Ii91c3IvYmluL3BocC'
			. 'I7aTo1O3M6MDoiIjt9aToyNzthOjY6e2k6MDtzOjE1OiJvdXRib3VuZF90YXJnZXQiO2k6MTtzO'
			. 'jc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6'
			. 'IiI7fWk6Mjg7YTo2OntpOjA7czoyMjoib3V0Ym91bmRfc2VuZG1haWxfcGF0aCI7aToxO3M6MTI'
			. '6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MTg6Ii91c3Ivc2'
			. 'Jpbi9zZW5kbWFpbCI7aTo1O3M6MDoiIjt9aToyOTthOjY6e2k6MDtzOjI0OiJvdXRib3VuZF9zb'
			. 'XRwX3JlbGF5X2hvc3QiO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7'
			. 'czowOiIiO2k6NDtzOjk6ImxvY2FsaG9zdCI7aTo1O3M6MDoiIjt9aTozMDthOjY6e2k6MDtzOjI'
			. '0OiJvdXRib3VuZF9zbXRwX3JlbGF5X3BvcnQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2'
			. 'k6MztzOjA6IiI7aTo0O3M6MjoiMjUiO2k6NTtzOjA6IiI7fWk6MzE7YTo2OntpOjA7czoxNDoia'
			. 'W1hcF9pZGxlX3BvbGwiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0'
			. 'O3M6MToiNSI7aTo1O3M6MDoiIjt9aTozMjthOjY6e2k6MDtzOjEzOiJxdWV1ZV90aW1lb3V0Ijt'
			. 'pOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjYwIjtpOjU7cz'
			. 'owOiIiO31pOjMzO2E6Njp7aTowO3M6MTU6InNtdHBfcmV2ZXJzZWRucyI7aToxO3M6NzoidGlue'
			. 'WludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aToz'
			. 'NDthOjY6e2k6MDtzOjIyOiJvdXRib3VuZF9hZGRfc2lnbmF0dXJlIjtpOjE7czo3OiJ0aW55aW5'
			. '0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM1O2'
			. 'E6Njp7aTowO3M6MjI6Im91dGJvdW5kX3NpZ25hdHVyZV9zZXAiO2k6MTtzOjEyOiJ2YXJjaGFyK'
			. 'DI1NSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjY1OiJfX19fX19fX19fX19fX19f'
			. 'X19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fXyI7aTo1O3M'
			. '6MDoiIjt9aTozNjthOjY6e2k6MDtzOjEzOiJjb3JlX2ZlYXR1cmVzIjtpOjE7czozOiJpbnQiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6Mzc7YTo2O'
			. 'ntpOjA7czoxNjoiaW1hcF9mb2xkZXJfc2VudCI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToy'
			. 'O3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6NDoiU2VudCI7aTo1O3M6MDoiIjt9aTozODthOjY'
			. '6e2k6MDtzOjE2OiJpbWFwX2ZvbGRlcl9zcGFtIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo0OiJTcGFtIjtpOjU7czowOiIiO31pOjM5O2E6N'
			. 'jp7aTowO3M6MTg6ImltYXBfZm9sZGVyX2RyYWZ0cyI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7'
			. 'aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6NjoiRHJhZnRzIjtpOjU7czowOiIiO31pOjQ'
			. 'wO2E6Njp7aTowO3M6MTc6ImltYXBfZm9sZGVyX3RyYXNoIjtpOjE7czoxMjoidmFyY2hhcigxMj'
			. 'gpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo1OiJUcmFzaCI7aTo1O3M6MDoiIjt9a'
			. 'To0MTthOjY6e2k6MDtzOjI0OiJvdXRib3VuZF9zbXRwX3JlbGF5X2F1dGgiO2k6MTtzOjc6InRp'
			. 'bnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk'
			. '6NDI7YTo2OntpOjA7czoyNDoib3V0Ym91bmRfc210cF9yZWxheV91c2VyIjtpOjE7czoxMjoidm'
			. 'FyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pO'
			. 'jQzO2E6Njp7aTowO3M6MjQ6Im91dGJvdW5kX3NtdHBfcmVsYXlfcGFzcyI7aToxO3M6MTI6InZh'
			. 'cmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo'
			. '0NDthOjY6e2k6MDtzOjExOiJmdHBncmVldGluZyI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aT'
			. 'oyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo0NTthOjY6e2k6MDtzO'
			. 'jExOiJmdHBfdGltZW91dCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtp'
			. 'OjQ7TjtpOjU7czowOiIiO31pOjQ2O2E6Njp7aTowO3M6MTU6ImxvZ3NfYXV0b2RlbGV0ZSI7aTo'
			. 'xO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3'
			. 'M6MDoiIjt9aTo0NzthOjY6e2k6MDtzOjIwOiJsb2dzX2F1dG9kZWxldGVfZGF5cyI7aToxO3M6M'
			. 'zoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiIzMSI7aTo1O3M6MDoiIjt9'
			. 'aTo0ODthOjY6e2k6MDtzOjIzOiJsb2dzX2F1dG9kZWxldGVfYXJjaGl2ZSI7aToxO3M6NzoidGl'
			. 'ueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aT'
			. 'o0OTthOjY6e2k6MDtzOjIwOiJsb2dzX2F1dG9kZWxldGVfbGFzdCI7aToxO3M6MzoiaW50IjtpO'
			. 'jI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjUwO2E6Njp7'
			. 'aTowO3M6MjE6ImluYm91bmRfcmV1c2VfcHJvY2VzcyI7aToxO3M6NzoidGlueWludCI7aToyO3M'
			. '6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo1MTthOjY6e2k6MDtzOjEyOi'
			. 'JmYWlsYmFuX3RpbWUiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O'
			. '3M6MzoiMzAwIjtpOjU7czowOiIiO31pOjUyO2E6Njp7aTowO3M6MTU6ImZhaWxiYW5fYmFudGlt'
			. 'ZSI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo0OiIzNjAwIjt'
			. 'pOjU7czowOiIiO31pOjUzO2E6Njp7aTowO3M6MTM6ImZhaWxiYW5fdHlwZXMiO2k6MTtzOjc6In'
			. 'RpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjIzIjtpOjU7czowOiIiO'
			. '31pOjU0O2E6Njp7aTowO3M6MTY6ImZhaWxiYW5fYXR0ZW1wdHMiO2k6MTtzOjM6ImludCI7aToy'
			. 'O3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MjoiMTUiO2k6NTtzOjA6IiI7fWk6NTU7YTo2Ont'
			. 'pOjA7czoxNToicmFuZG9tX3F1ZXVlX2lkIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTy'
			. 'I7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjU2O2E6Njp7aTowO3M6MjU6I'
			. 'nJlY2VpdmVkX2hlYWRlcl9ub19leHBvc2UiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5P'
			. 'IjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NTc7YTo2OntpOjA7czoxMjo'
			. 'iY29udHJvbF9hZGRyIjtpOjE7czoxMToidmFyY2hhcig2NCkiO2k6MjtzOjI6Ik5PIjtpOjM7cz'
			. 'owOiIiO2k6NDtzOjk6IjEyNy4wLjAuMSI7aTo1O3M6MDoiIjt9aTo1ODthOjY6e2k6MDtzOjEyO'
			. 'iJjb250cm9sX3BvcnQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0'
			. 'O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo1OTthOjY6e2k6MDtzOjE0OiJjb250cm9sX3NlY3JldCI'
			. '7aToxO3M6MTE6InZhcmNoYXIoMzIpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOj'
			. 'U7czowOiIiO31pOjYwO2E6Njp7aTowO3M6MjA6ImltYXBfaWRsZV9teXNxbGNsb3NlIjtpOjE7c'
			. 'zo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIxIjtpOjU7czow'
			. 'OiIiO31pOjYxO2E6Njp7aTowO3M6MTY6InF1ZXVlX215c3FsY2xvc2UiO2k6MTtzOjc6InRpbnl'
			. 'pbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6Nj'
			. 'I7YTo2OntpOjA7czoxNToiaW1hcF9teXNxbGNsb3NlIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7c'
			. 'zoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIxIjtpOjU7czowOiIiO31pOjYzO2E6Njp7aTow'
			. 'O3M6MjM6ImltYXBfaW50ZWxsaWdlbnRmb2xkZXJzIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czo'
			. 'yOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjY0O2E6Njp7aTowO3'
			. 'M6MTU6InNtdHBfY2hlY2tfaGVsbyI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6M'
			. 'ztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo2NTthOjY6e2k6MDtzOjI0OiJzbXRw'
			. 'X3JlamVjdF9ub3JldmVyc2VkbnMiO2k6MTtzOjc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM'
			. '7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NjY7YTo2OntpOjA7czoxNToiaW5ib3'
			. 'VuZF9oZWFkZXJzIjtpOjE7czo0OiJ0ZXh0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7T'
			. 'jtpOjU7czowOiIiO31pOjY3O2E6Njp7aTowO3M6MTY6Im91dGJvdW5kX2hlYWRlcnMiO2k6MTtz'
			. 'OjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6Njg'
			. '7YTo2OntpOjA7czoxMDoic3BmX2VuYWJsZSI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo2OTthOjY6e2k6MDtzOjIzO'
			. 'iJzcGZfZGlzYWJsZV9ncmV5bGlzdGluZyI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8i'
			. 'O2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTo3MDthOjY6e2k6MDtzOjE2OiJ'
			. 'zcGZfcmVqZWN0X21haWxzIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MD'
			. 'oiIjtpOjQ7czoxOiIxIjtpOjU7czowOiIiO31pOjcxO2E6Njp7aTowO3M6MTc6InNwZl9pbmplY'
			. '3RfaGVhZGVyIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7'
			. 'czoxOiIxIjtpOjU7czowOiIiO31pOjcyO2E6Njp7aTowO3M6MTM6InF1ZXVlX3RocmVhZHMiO2k'
			. '6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiNSI7aTo1O3M6MD'
			. 'oiIjt9aTo3MzthOjY6e2k6MDtzOjE2OiJxdWV1ZV9tYXh0aHJlYWRzIjtpOjE7czozOiJpbnQiO'
			. '2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjI1IjtpOjU7czowOiIiO31pOjc0O2E6'
			. 'Njp7aTowO3M6MTQ6InVzZXJfc2hvd2xvZ2luIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJ'
			. 'OTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIxIjtpOjU7czowOiIiO31pOjc1O2E6Njp7aTowO3M6MT'
			. 'U6InVzZXJfcG9wM3NlcnZlciI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO'
			. '2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo3NjthOjY6e2k6MDtzOjEzOiJ1c2VyX3Bv'
			. 'cDNwb3J0IjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IjE'
			. 'xMCI7aTo1O3M6MDoiIjt9aTo3NzthOjY6e2k6MDtzOjE1OiJ1c2VyX3NtdHBzZXJ2ZXIiO2k6MT'
			. 'tzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzO'
			. 'jA6IiI7fWk6Nzg7YTo2OntpOjA7czoxMzoidXNlcl9zbXRwcG9ydCI7aToxO3M6MzoiaW50Ijtp'
			. 'OjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czozOiI1ODciO2k6NTtzOjA6IiI7fWk6Nzk7YTo'
			. '2OntpOjA7czoxNToidXNlcl9pbWFwc2VydmVyIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjgwO2E6Njp7aTowO3M6M'
			. 'TM6InVzZXJfaW1hcHBvcnQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7'
			. 'aTo0O3M6MzoiMTQzIjtpOjU7czowOiIiO31pOjgxO2E6Njp7aTowO3M6MjE6InVzZXJfY2hvc2V'
			. 'wb3AzZm9sZGVycyI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aT'
			. 'o0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTo4MjthOjY6e2k6MDtzOjEyOiJwb3AzX2ZvbGRlcnMiO'
			. '2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjY6'
			. 'IjAsLTEyOCI7aTo1O3M6MDoiIjt9aTo4MzthOjY6e2k6MDtzOjIwOiJvdXRib3VuZF9zbXRwX3V'
			. 'zZXRscyI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MT'
			. 'oiMSI7aTo1O3M6MDoiIjt9aTo4NDthOjY6e2k6MDtzOjEyOiJ1c2VyX3BvcDNzc2wiO2k6MTtzO'
			. 'jc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6'
			. 'IiI7fWk6ODU7YTo2OntpOjA7czoxMjoidXNlcl9pbWFwc3NsIjtpOjE7czo3OiJ0aW55aW50Ijt'
			. 'pOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjg2O2E6Nj'
			. 'p7aTowO3M6MTI6InVzZXJfc210cHNzbCI7aToxO3M6NzoidGlueWludCI7aToyO3M6MjoiTk8iO'
			. '2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo4NzthOjY6e2k6MDtzOjE2OiJp'
			. 'bWFwX2F1dG9leHB1bmdlIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDo'
			. 'iIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjg4O2E6Njp7aTowO3M6MTA6ImltYXBfbGltaX'
			. 'QiO2k6MTtzOjM6ImludCI7aToyO3M6MzoiWUVTIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6N'
			. 'TtzOjA6IiI7fWk6ODk7YTo2OntpOjA7czoxOToidXNlcl9jaG9zZWltYXBsaW1pdCI7aToxO3M6'
			. 'NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDo'
			. 'iIjt9aTo5MDthOjY6e2k6MDtzOjE0OiJzbXRwX2hvcF9saW1pdCI7aToxO3M6MzoiaW50IjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiI1MCI7aTo1O3M6MDoiIjt9aTo5MTthOjY6e'
			. '2k6MDtzOjIxOiJzbXRwX2F1dGhfbm9fcmVjZWl2ZWQiO2k6MTtzOjc6InRpbnlpbnQiO2k6Mjtz'
			. 'OjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OTI7YTo2OntpOjA'
			. '7czoxNToic3NsX2NpcGhlcl9saXN0IjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyOi'
			. 'JOTyI7aTozO3M6MDoiIjtpOjQ7czoyNToiSElHSDohRFNTOiFhTlVMTEBTVFJFTkdUSCI7aTo1O'
			. '3M6MDoiIjt9aTo5MzthOjY6e2k6MDtzOjIxOiJpbWFwX3VpZHNfaW5pdGlhbGl6ZWQiO2k6MTtz'
			. 'Ojc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA'
			. '6IiI7fWk6OTQ7YTo2OntpOjA7czoxMToiYXBuc19lbmFibGUiO2k6MTtzOjc6InRpbnlpbnQiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OTU7YTo2O'
			. 'ntpOjA7czo5OiJhcG5zX2hvc3QiO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5P'
			. 'IjtpOjM7czowOiIiO2k6NDtzOjIyOiJnYXRld2F5LnB1c2guYXBwbGUuY29tIjtpOjU7czowOiI'
			. 'iO31pOjk2O2E6Njp7aTowO3M6OToiYXBuc19wb3J0IjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik'
			. '5PIjtpOjM7czowOiIiO2k6NDtzOjQ6IjIxOTUiO2k6NTtzOjA6IiI7fWk6OTc7YTo2OntpOjA7c'
			. 'zoxNjoiYXBuc19jZXJ0aWZpY2F0ZSI7aToxO3M6NDoidGV4dCI7aToyO3M6MjoiTk8iO2k6Mztz'
			. 'OjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo5ODthOjY6e2k6MDtzOjE1OiJhcG5zX3ByaXZhdGV'
			. 'rZXkiO2k6MTtzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOj'
			. 'A6IiI7fWk6OTk7YTo2OntpOjA7czoyMToib3V0Ym91bmRfc210cF91c2VkYW5lIjtpOjE7czo3O'
			. 'iJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIi'
			. 'O31pOjEwMDthOjY6e2k6MDtzOjIzOiJvdXRib3VuZF9zbXRwX3VzZWRuc3NlYyI7aToxO3M6Nzo'
			. 'idGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIj'
			. 't9aToxMDE7YTo2OntpOjA7czoxNjoic3NsX2NpcGhlcnN1aXRlcyI7aToxO3M6MTI6InZhcmNoY'
			. 'XIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6NzQ6IlRMU19BRVNfMjU2X0dD'
			. 'TV9TSEEzODQ6VExTX0NIQUNIQTIwX1BPTFkxMzA1X1NIQTI1NjpUTFNfQUVTXzEyOF9HQ01fU0h'
			. 'BMjU2IjtpOjU7czowOiIiO31pOjEwMjthOjY6e2k6MDtzOjE1OiJzc2xfbWluX3ZlcnNpb24iO2'
			. 'k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6M'
			. 'DoiIjt9aToxMDM7YTo2OntpOjA7czoxNToic3NsX21heF92ZXJzaW9uIjtpOjE7czozOiJpbnQi'
			. 'O2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fX1zOjc6Iml'
			. 'uZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjI6ImlkIjt9fX1zOjE0OiJibT'
			. 'YwX2Jtc19xdWV1ZSI7YToyOntzOjY6ImZpZWxkcyI7YToxOTp7aTowO2E6Njp7aTowO3M6Mjoia'
			. 'WQiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6'
			. 'MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6NjoiYWN0aXZlIjtpOjE7czo3OiJ'
			. '0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO3'
			. '1pOjI7YTo2OntpOjA7czo0OiJ0eXBlIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7a'
			. 'TozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo0OiJkYXRl'
			. 'IjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTt'
			. 'zOjA6IiI7fWk6NDthOjY6e2k6MDtzOjQ6InNpemUiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo1O2E6Njp7aTowO3M6NDoiZnJvbSI7a'
			. 'ToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1'
			. 'O3M6MDoiIjt9aTo2O2E6Njp7aTowO3M6MjoidG8iO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k'
			. '6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzOj'
			. 'k6InRvX2RvbWFpbiI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzO'
			. 'jA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo4O2E6Njp7aTowO3M6ODoiYXR0ZW1wdHMiO2k6MTtz'
			. 'Ojc6InRpbnlpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA'
			. '6IiI7fWk6OTthOjY6e2k6MDtzOjEyOiJsYXN0X2F0dGVtcHQiO2k6MTtzOjM6ImludCI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToxMDthOjY6e2k6M'
			. 'DtzOjExOiJsYXN0X3N0YXR1cyI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoi'
			. 'IjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjExO2E6Njp7aTowO3M6MTY6Imxhc3Rfc3RhdHV'
			. 'zX2NvZGUiO2k6MTtzOjExOiJ2YXJjaGFyKDEwKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aT'
			. 'o0O047aTo1O3M6MDoiIjt9aToxMjthOjY6e2k6MDtzOjIwOiJsYXN0X2RpYWdub3N0aWNfY29kZ'
			. 'SI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047'
			. 'aTo1O3M6MDoiIjt9aToxMzthOjY6e2k6MDtzOjk6InNtdHBfdXNlciI7aToxO3M6MzoiaW50Ijt'
			. 'pOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjE0O2E6Nj'
			. 'p7aTowO3M6MTY6Imxhc3Rfc3RhdHVzX2luZm8iO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k6M'
			. 'jtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MTU7YTo2OntpOjA7czo3'
			. 'OiJkZWxldGVkIjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ'
			. '7czoxOiIwIjtpOjU7czowOiIiO31pOjE2O2E6Njp7aTowO3M6MTI6ImIxZ21haWxfdXNlciI7aT'
			. 'oxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowO'
			. 'iIiO31pOjE3O2E6Njp7aTowO3M6MTY6ImRlbGl2ZXJ5c3RhdHVzaWQiO2k6MTtzOjM6ImludCI7'
			. 'aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToxODthOjY'
			. '6e2k6MDtzOjU6ImZsYWdzIjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2'
			. 'k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZI'
			. 'jthOjE6e2k6MDtzOjI6ImlkIjt9fX1zOjE4OiJibTYwX2Jtc19zbXRwc3RhdHMiO2E6Mjp7czo2'
			. 'OiJmaWVsZHMiO2E6NDp7aTowO2E6Njp7aTowO3M6Njoic3RhdGlkIjtpOjE7czozOiJpbnQiO2k'
			. '6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudC'
			. 'I7fWk6MTthOjY6e2k6MDtzOjY6InVzZXJpZCI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7a'
			. 'TozO3M6MzoiTVVMIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMDoi'
			. 'cmVjaXBpZW50cyI7aToxO3M6MzoiaW50IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo'
			. 'xOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo0OiJ0aW1lIjtpOjE7czozOiJpbnQiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fX1zOjc6ImluZ'
			. 'GV4ZXMiO2E6Mjp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjY6InN0YXRpZCI7fXM6NjoidXNl'
			. 'cmlkIjthOjE6e2k6MDtzOjY6InVzZXJpZCI7fX19czoxNDoiYm02MF9ibXNfc3RhdHMiO2E6Mjp'
			. '7czo2OiJmaWVsZHMiO2E6NTp7aTowO2E6Njp7aTowO3M6NDoiZGF0ZSI7aToxO3M6NDoiZGF0ZS'
			. 'I7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MDoiIjt9aToxO2E6Njp7a'
			. 'TowO3M6OToiY29tcG9uZW50IjtpOjE7czo3OiJ0aW55aW50IjtpOjI7czoyOiJOTyI7aTozO3M6'
			. 'MzoiUFJJIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMToiY29ubmV'
			. 'jdGlvbnMiO2k6MTtzOjM6ImludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMC'
			. 'I7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6MjoiaW4iO2k6MTtzOjM6ImludCI7aToyO3M6M'
			. 'joiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6'
			. 'Mzoib3V0IjtpOjE7czozOiJpbnQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjA'
			. 'iO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjI6e2k6MD'
			. 'tzOjQ6ImRhdGUiO2k6MTtzOjk6ImNvbXBvbmVudCI7fX19czoxNjoiYm02MF9ibXNfc3VibmV0c'
			. 'yI7YToyOntzOjY6ImZpZWxkcyI7YTo0OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6Mzoi'
			. 'aW50IjtpOjI7czoyOiJOTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmN'
			. 'yZW1lbnQiO31pOjE7YTo2OntpOjA7czoyOiJpcCI7aToxO3M6MTE6InZhcmNoYXIoNDgpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czo0O'
			. 'iJtYXNrIjtpOjE7czoxMToidmFyY2hhcig0OCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6'
			. 'NDtOO2k6NTtzOjA6IiI7fWk6MzthOjY6e2k6MDtzOjE0OiJjbGFzc2lmaWNhdGlvbiI7aToxO3M'
			. '6NzoidGlueWludCI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MD'
			. 'oiIjt9fXM6NzoiaW5kZXhlcyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MjoiaWQiO'
			. '319fX0=';
		$databaseStructure = unserialize(base64_decode($databaseStructure));

		// sync struct
		SyncDBStruct($databaseStructure);

		// insert prefs row?
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_prefs');
		list($rowCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($rowCount == 0)
		{
			$phpPath = '/usr/bin/php';

			if(SERVER_WINDOWS)
			{
				$possiblePHPPaths = array(
					'c:\\php\\php-cli.exe',
					'c:\\php\\php5-cli.exe',
					'c:\\php\\php4-cli.exe',
					'c:\\php\\php.exe',
					'c:\\php\\php5.exe',
					'c:\\php\\php4.exe',
					'c:\\php\\php-cgi.exe',
					'c:\\php\\php5-cgi.exe',
					'c:\\php\\php4-cgi.exe',
					'c:\\program files\\php\\php.exe',
					'c:\\program files\\php5\\php.exe',
					'c:\\program files\\php4\\php.exe',
					'c:\\program files\\php\\bin\\php.exe',
					'c:\\program files\\php5\\bin\\php.exe',
					'c:\\program files\\php4\\bin\\php.exe',
					'c:\\program files (x86)\\php\\php.exe',
					'c:\\program files (x86)\\php5\\php.exe',
					'c:\\program files (x86)\\php4\\php.exe',
					'c:\\program files (x86)\\php\\bin\\php.exe',
					'c:\\program files (x86)\\php5\\bin\\php.exe',
					'c:\\program files (x86)\\php4\\bin\\php.exe'
				);

				foreach($possiblePHPPaths as $possiblePHPPath)
					if(@file_exists($possiblePHPPath))
					{
						$phpPath = $possiblePHPPath;
						break;
					}
			}
			else
			{
				if(($path = @exec('php-config --php-binary'))
				   && trim($path) != ''
				   && @file_exists($path)
				   && is_executable($path))
				{
					$phpPath = $path;
				}
				else
				{
					$possiblePHPPaths = array(
						'/usr/bin/php',
						'/usr/bin/php-cli',
						'/usr/bin/php5-cli',
						'/usr/bin/php4-cli',
						'/usr/bin/php5',
						'/usr/bin/php4',
						'/usr/bin/php-cgi',
						'/usr/bin/php5-cgi',
						'/usr/bin/php4-cgi'
					);

					foreach($possiblePHPPaths as $possiblePHPPath)
						if(@file_exists($possiblePHPPath))
						{
							$phpPath = $possiblePHPPath;
							break;
						}
				}
			}

			$db->Query('INSERT INTO {pre}bms_prefs(id,core_version,outbound_target,php_path) VALUES(1,?,2,?)',
				'',
				$phpPath);

			// 192.168.0.0
			$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
				'192.168.0.0',
				'16',
				2);

			// 127.0.0.0
			$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
				'127.0.0.0',
				'24',
				2);

			// ::1
			$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
				'::1',
				'128',
				2);

			// server IP
			if(isset($_SERVER['SERVER_ADDR']) && !empty($_SERVER['SERVER_ADDR'])
				&& $_SERVER['SERVER_ADDR'] != '127.0.0.1')
			{
				$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
					$_SERVER['SERVER_ADDR'],
					'255.255.255.255',
					2);
			}

			// server name
			$hostName = @gethostbyname($_SERVER['SERVER_ADDR']);
			if($hostName && trim($hostName) != '' && $hostName != $_SERVER['SERVER_ADDR'])
			{
				$db->Query('UPDATE {pre}prefs SET `b1gmta_host`=?',
						   $hostName);
			}
		}

		// update subnet table data
		$db->Query('UPDATE {pre}bms_subnets SET `ip`=inet_ntoa(`ip`),`mask`=inet_ntoa(`mask`) WHERE NOT(`ip` LIKE \'%.%\' OR `ip` LIKE \'%:%\')');

		//  ipv6 ::1
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_subnets WHERE `ip`=?', '::1');
		list($ipv6Count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($ipv6Count == 0)
		{
			$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
				'::1',
				'128',
				2);
		}

		// set server names?
		$db->Query('UPDATE {pre}bms_prefs SET `user_pop3server`=? WHERE `user_pop3server`=\'\'',
			$bm_prefs['b1gmta_host']);
		$db->Query('UPDATE {pre}bms_prefs SET `user_imapserver`=? WHERE `user_imapserver`=\'\'',
			$bm_prefs['b1gmta_host']);
		$db->Query('UPDATE {pre}bms_prefs SET `user_smtpserver`=? WHERE `user_smtpserver`=\'\'',
			$bm_prefs['b1gmta_host']);

		// set up triggers
		$db->Query('DROP TRIGGER IF EXISTS `bms_imapseen_update`');
		$db->Query('DROP TRIGGER IF EXISTS `bms_imapuid_update`');
		$db->Query('DROP TRIGGER IF EXISTS `bms_imapuid_insert`');
		$db->Query('DROP TRIGGER IF EXISTS `bms_imapuid_delete`');
		$db->Query('CREATE TRIGGER `bms_imapseen_update` BEFORE UPDATE ON `{pre}mails` '
					. 'FOR EACH ROW '
					. 'BEGIN '
					. '    IF NEW.`folder` <> OLD.`folder` THEN '
					. '        SET NEW.`flags`=(NEW.`flags`&(~32)); '
					. '    END IF; '
					. 'END');
		$db->Query('CREATE TRIGGER `bms_imapuid_update` AFTER UPDATE ON `{pre}mails` '
					. 'FOR EACH ROW '
					. 'BEGIN '
					. '    IF NEW.`folder` <> OLD.`folder` THEN '
					. '        DELETE FROM `bm60_bms_imapuid` WHERE `bm60_bms_imapuid`.`mailid`=OLD.`id`; '
					. '        INSERT INTO `bm60_bms_imapuid`(`mailid`) VALUES(OLD.`id`); '
					. '    END IF; '
					. 'END');
		$db->Query('CREATE TRIGGER `bms_imapuid_insert` AFTER INSERT ON `bm60_mails` '
					. 'FOR EACH ROW '
					. '    INSERT INTO `bm60_bms_imapuid`(`mailid`) VALUES(NEW.`id`)');
		$db->Query('CREATE TRIGGER `bms_imapuid_delete` AFTER DELETE ON `bm60_mails` '
					. 'FOR EACH ROW '
					. '    DELETE FROM `bm60_bms_imapuid` WHERE `bm60_bms_imapuid`.`mailid`=OLD.`id`');

		// log
		PutLog(sprintf('%s v%s installed',
			$this->name,
			$this->version),
			PRIO_PLUGIN,
			__FILE__,
			__LINE__);

		return(true);
	}

	/**
	 * initialize language-dependent phrases
	 *
	 */
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		$_lang_custom = $_lang_user = $_lang_client = $_lang_admin = array();

		if($lang == 'deutsch')
		{
			// german
			$_lang_admin['bms_milters']				= 'Mail-Filter (Milter)';
			$_lang_admin['bms_defaultaction']		= 'Standard-Aktion';
			$_lang_admin['bms_milter_tempfail']		= 'Temp. abweisen';
			$_lang_admin['bms_milter_accept']		= 'Akzeptieren';
			$_lang_admin['bms_milter_reject']		= 'Abweisen';
			$_lang_admin['bms_milter_nonauth']		= 'Nicht auth.';
			$_lang_admin['bms_milter_auth']			= 'Authentifiziert';
			$_lang_admin['bms_tcp']					= 'TCP/IP';
			$_lang_admin['bms_local']				= 'Unix-Socket';
			$_lang_admin['bms_reject_norevdns']		= 'Anonyme Clients mit fehlendem Reverse-DNS ablehnen';
			$_lang_admin['bms_control_addr_help']	= 'Die hier angegebene IP-Adresse vom Warteschleifen-Server muss vom Webserver aus erreichbar sein.\n\nEs wird dringend empfohlen, keine öffentlich erreichbare IP-Adresse zu verwenden.\n\nSollten b1gMailServer und Webserver auf dem gleichen Server betrieben werden, sollte diese Einstellung auf 127.0.0.1 belassen werden.\n\nNach Änderung muss der Warteschleifen-Dienst manuell per SSH/RDP neu gestartet werden.';
			$_lang_admin['bms_control_addr']		= 'Kontroll-Kanal-Interface';
			$_lang_admin['bms_apnsqueuerestartnote']= '&Auml;nderungen der Push-Konfiguration werden erst nach Neustart des Warteschleifen-Dienstes wirksam!';
			$_lang_admin['bms_certerr_format']		= 'Zertifikat oder Private Key haben ein ung&uuml;ltiges Format.';
			$_lang_admin['bms_certerr_purpose']		= 'Das Zertifikat ist nicht als SSL-Client-Zertifikat geeignet.';
			$_lang_admin['bms_certerr_pkcheck']		= 'Der Private Key passt nicht zum Zertifikat.';
			$_lang_admin['bms_certuidcn']			= 'Zerifikat-UID/-CN';
			$_lang_admin['bms_certimport']			= 'Zertifikat importieren';
			$_lang_admin['bms_nocertset']			= 'kein Zertifikat hinterlegt';
			$_lang_admin['bms_certpk']				= 'Zertifikat / Private Key';
			$_lang_admin['bms_certificate']			= 'Zertifikat';
			$_lang_admin['bms_privatekey']			= '<em>und</em> Private Key';
			$_lang_admin['bms_serverport']			= 'Server / Port';
			$_lang_admin['bms_setvaliduntil']		= 'Hinterlegt, g&uuml;ltig bis';
			$_lang_admin['bms_notset']				= 'Nicht hinterlegt';
			$_lang_admin['bms_apns']				= 'Apple Push-Service';
			$_lang_admin['bms_apnsnote']			= 'zur Aktivierung muss zuerst ein Zertifikat hinterlegt werden';
			$_lang_admin['bms_pushcertificate']		= 'Push-Zertifikat';
			$_lang_admin['bms_tls_ssl']				= 'Sicherheit';
			$_lang_admin['bms_ssl_cipher_list']		= 'SSL-/TLS-Cipher-Liste';
			$_lang_admin['bms_ssl_ciphersuites']		= 'TLSv1.3-Cipher-Suites';
			$_lang_admin['bms_ssl_minmaxversion']		= 'Protokoll-Version min/max';
			$_lang_admin['bms_tlsarecord']			= 'Vorgeschlagener TLSA-Record';
			$_lang_admin['bms_mysqlconnection']		= 'MySQL-Verbindung';
			$_lang_admin['bms_closeduringidle']		= 'Zwischen IDLE-Polls freigeben';
			$_lang_admin['bms_closewhenidle']		= 'Bei Inaktivit&auml;t freigeben';
			$_lang_admin['bms_running']				= 'Gestartet';
			$_lang_admin['bms_not_running']			= 'Nicht gestartet';
			$_lang_admin['bms_greeting']			= 'Begr&uuml;&szlig;ung';
			$_lang_admin['bms_timeout']				= 'Timeout';
			$_lang_admin['bms_smtp_auth']			= 'SMTP-Authentifizierung';
			$_lang_admin['bms_untrusted_limits']	= 'Einschr&auml;nkungen f&uuml;r nicht-vertrauensw&uuml;rdige Verbindungen';
			$_lang_admin['bms_greetingdelay']		= 'Verbindungs-Verz&ouml;gerung';
			$_lang_admin['bms_error_delay']			= 'Verz&ouml;gerung bei Fehler';
			$_lang_admin['bms_error_softlimit']		= 'Fehler-Soft-Limit';
			$_lang_admin['bms_error_hardlimit']		= 'Fehler-Hard-Limit';
			$_lang_admin['bms_greylisting']			= 'Grey-Listing';
			$_lang_admin['bms_grey_interval']		= 'Intervall';
			$_lang_admin['bms_grey_wait_time']		= 'Reconnect-Wartezeit';
			$_lang_admin['bms_grey_good_time']		= 'Ablauf nach';
			$_lang_admin['bms_minutes']				= 'Minuten';
			$_lang_admin['bms_hours']				= 'Stunden';
			$_lang_admin['bms_list']				= 'Liste';
			$_lang_admin['bms_peer_classification']	= 'Verbindungs-Klassifizierung';
			$_lang_admin['bms_subnet_rules']		= 'Subnetz-Regeln';
			$_lang_admin['bms_dnsbl_rules']			= 'DNSBL-Regeln';
			$_lang_admin['bms_msgqueue']			= 'E-Mail-Verarbeitung';
			$_lang_admin['bms_queue']				= 'Warteschleife';
			$_lang_admin['bms_queue_prefs']			= 'Warteschleifen-Einstellungen';
			$_lang_admin['bms_queue_interval']		= 'Verarbeitungs-Intervall';
			$_lang_admin['bms_queue_retry']			= 'E-Mail-Zustellversuch-Intervall';
			$_lang_admin['bms_queue_lifetime']		= 'Maximale E-Mail-Lebensdauer';
			$_lang_admin['bms_queue_timeout']		= 'Verarbeitungs-Timeout';
			$_lang_admin['bms_inbound']				= 'Eingehende E-Mails';
			$_lang_admin['bms_php_path']			= 'PHP-Pfad';
			$_lang_admin['bms_outbound']			= 'Ausgehende E-Mails';
			$_lang_admin['bms_processing']			= 'Verarbeitung';
			$_lang_admin['bms_redirecttosendmail']	= 'An Sendmail &uuml;bergeben';
			$_lang_admin['bms_redirecttosmtprelay']	= 'An SMTP-Relay-Server &uuml;bergeben';
			$_lang_admin['bms_queuerestartnote']	= '&Auml;nderungen werden erst nach Neustart des Warteschleifen-Dienstes wirksam!';
			$_lang_admin['bms_subnet']				= 'Subnetz';
			$_lang_admin['bms_classification']		= 'Klassifizierung';
			$_lang_admin['bms_origin_default']		= 'Nicht vertrauensw&uuml;rdig (Standard)';
			$_lang_admin['bms_origin_trusted']		= 'Vertrauensw&uuml;rdig';
			$_lang_admin['bms_origin_dialup']		= 'DialUp-Host';
			$_lang_admin['bms_origin_reject']		= 'Verbindung ablehnen';
			$_lang_admin['bms_dnsbl']				= 'DNSBL';
			$_lang_admin['bms_logging']				= 'Logging';
			$_lang_admin['bms_logging_debug']		= 'Debug-Meldungen loggen';
			$_lang_admin['bms_logging_notices']		= 'Hinweise loggen';
			$_lang_admin['bms_logging_warnings']	= 'Warnungen loggen';
			$_lang_admin['bms_logging_errors']		= 'Fehler loggen';
			$_lang_admin['bms_validating']			= 'Wird gepr&uuml;ft...';
			$_lang_admin['bms_valid']				= 'G&uuml;ltig';
			$_lang_admin['bms_invalid']				= 'Ung&uuml;ltig';
			$_lang_admin['bms_expired']				= 'Abgelaufen';
			$_lang_admin['bms_until']				= 'bis zum';
			$_lang_admin['bms_last_attempt']		= 'Letzter Zustell-Versuch';
			$_lang_admin['bms_attempts']			= 'Zustell-Versuch(e)';
			$_lang_admin['bms_inbound1']			= 'Eingehende E-Mail';
			$_lang_admin['bms_outbound1']			= 'Ausgehende E-Mail';
			$_lang_admin['bms_enqueued_by']			= 'Eingeliefert von';
			$_lang_admin['bms_component']			= 'Komponente';
			$_lang_admin['bms_minpop3']				= 'Minimaler Sitzungs-Abstand';
			$_lang_admin['bms_idle_poll']			= 'Internes IDLE-Poll-Intervall';
			$_lang_admin['bms_adminplugin']			= 'Admin-Plugin';
			$_lang_admin['bms_core']				= 'Core';
			$_lang_admin['bms_debugmode']			= 'Das Logging von Debug-Meldungen ist derzeit aktiviert. Es wird dringend empfohlen, Debug-Logging in Produktiv-Umgebungen zu deaktivieren!';
			$_lang_admin['bms_queueentries']		= 'Warteschleifen-Eintr&auml;ge';
			$_lang_admin['bms_pop3today']			= 'POP3-Sitzungen (heute)';
			$_lang_admin['bms_imaptoday']			= 'IMAP-Sitzungen (heute)';
			$_lang_admin['bms_smtptoday']			= 'SMTP-Sitzungen (heute)';
			$_lang_admin['bms_pop3traffic']			= 'POP3-Traffic (heute)';
			$_lang_admin['bms_imaptraffic']			= 'IMAP-Traffic (heute)';
			$_lang_admin['bms_smtptraffic']			= 'SMTP-Traffic (heute)';
			$_lang_admin['bms_stat_pop3sessions']	= 'POP3-Sitzungen';
			$_lang_admin['bms_stat_pop3traffic']	= 'POP3-Traffic (MB)';
			$_lang_admin['bms_stat_imapsessions']	= 'IMAP-Sitzungen';
			$_lang_admin['bms_stat_imaptraffic']	= 'IMAP-Traffic (MB)';
			$_lang_admin['bms_stat_smtpsessions']	= 'SMTP-Sitzungen';
			$_lang_admin['bms_stat_smtptraffic']	= 'SMTP-Traffic (MB)';
			$_lang_admin['bms_altpop3']				= 'Alternativ-Weiterleitung';
			$_lang_admin['bms_toport']				= 'zu Port';
			$_lang_admin['bms_reversedns']			= 'Reverse-DNS-Lookup';
			$_lang_admin['bms_reset_stats']			= 'Statistiken zur&uuml;cksetzen';
			$_lang_admin['bms_real_reset']			= 'Sollen die Sitzungs-Statistiken wirklich zur�ckgesetzt werden?';
			$_lang_admin['bms_greylist']			= 'Grey-List';
			$_lang_admin['bms_ip']					= 'IP-Adresse';
			$_lang_admin['bms_grey_date']			= 'Letzte Verbindung';
			$_lang_admin['bms_grey_confirmed']		= 'Best&auml;tigt?';
			$_lang_admin['bms_add_signature']		= 'Gruppen-Signatur anh&auml;ngen';
			$_lang_admin['bms_signature_sep']		= 'Signatur-Trennung';
			$_lang_admin['bms_feature_tls']			= 'TLS-Support';
			$_lang_admin['bms_feature_sig']			= 'Signatur-Support';
			$_lang_admin['bms_foldernames']			= 'Ordner-Namen';
			$_lang_admin['bms_folder_inbox']		= 'Posteingang';
			$_lang_admin['bms_folder_sent']			= 'Postausgang';
			$_lang_admin['bms_folder_spam']			= 'Spam';
			$_lang_admin['bms_folder_drafts']		= 'Entw&uuml;rfe';
			$_lang_admin['bms_folder_trash']		= 'Papierkorb';
			$_lang_admin['bms_deliverself']			= 'Mails selbst ausliefern';
			$_lang_admin['bms_flushqueue']			= 'Alle Eintr&auml;ge abarbeiten';
			$_lang_admin['bms_failed']				= 'Fehlgeschlagen';
			$_lang_admin['bms_arch']				= 'Architektur';
			$_lang_admin['bms_updatesdesc']			= 'Klicken Sie auf den folgenden Button, um nach verf&uuml;gbaren Updates (z.B. wichtige Sicherheits-Aktualisierungen) f&uuml;r Ihre b1gMailServer-Plugins zu suchen.';
			$_lang_admin['bms_origin_nogrey']		= 'Grey-Listing deaktivieren';
			$_lang_admin['bms_origin_nogreyandban']	= 'Grey-Listing und IP-Bann deaktivieren';
			$_lang_admin['bms_reuseprocess']		= 'PHP-Prozesse wiederverwenden';
			$_lang_admin['bms_failban']				= 'Automatischer IP-Bann';
			$_lang_admin['bms_fb_activatefor']		= '&Uuml;berwachen';
			$_lang_admin['bms_fb_1']				= 'fehlerhafte POP3-Login-Versuche';
			$_lang_admin['bms_fb_2']				= 'fehlerhafte IMAP-Login-Versuche';
			$_lang_admin['bms_fb_4']				= 'fehlerhafte SMTP-Login-Versuche';
			$_lang_admin['bms_fb_8']				= 'fehlerhafte SMTP-Empf&auml;nger';
			$_lang_admin['bms_fb_16']				= 'fehlerhafte FTP-Login-Versuche';
			$_lang_admin['bms_fb_attempts']			= 'Erlaubte Versuche';
			$_lang_admin['bms_fb_time']				= 'Im Zeitraum';
			$_lang_admin['bms_fb_bantime']			= 'Bannen f&uuml;r';
			$_lang_admin['bms_random_queue_id']		= 'Zuf&auml;llige Queue-ID';
			$_lang_admin['bms_received_header']		= '\'Received\'-Header';
			$_lang_admin['bms_dont_expose']			= 'b1gMailServer verstecken';
			$_lang_admin['bms_restartqueue']		= 'Warteschleife neustarten';
			$_lang_admin['bms_reallyrestartqueue']	= 'Soll der Warteschleifen-Dienst wirklich neu gestartet werden?';
			$_lang_admin['bms_queueitemgone']		= 'Der Warteschleifen-Eintrag ist nicht mehr vorhanden. M&ouml;glicherweise wurde er mittlerweile abgearbeitet oder hat das Ende seine maximale Lebensdauer erreicht.';
			$_lang_admin['bms_fb_entrydate']		= 'Erster Versuch';
			$_lang_admin['bms_fb_lastupdate']		= 'Letzter Versuch';
			$_lang_admin['bms_fb_type']				= 'Grund';
			$_lang_admin['bms_fb_banneduntil']		= 'Gebannt bis';
			$_lang_admin['bms_fb_type_0']			= 'Unbekannt';
			$_lang_admin['bms_fb_type_1']			= 'POP3-Login';
			$_lang_admin['bms_fb_type_2']			= 'IMAP-Login';
			$_lang_admin['bms_fb_type_4']			= 'SMTP-Login';
			$_lang_admin['bms_fb_type_8']			= 'SMTP-RCPT';
			$_lang_admin['bms_fb_type_16']			= 'FTP-Login';
			$_lang_admin['bms_logging_autodelete']	= 'Auto-Archivierung';
			$_lang_admin['bms_enableolder']			= 'Aktivieren f&uuml;r Eintr&auml;ge &auml;lter als';
			$_lang_admin['bms_intfolders']			= 'Intelligente Ordner';
			$_lang_admin['bms_systemuser']			= 'System';
			$_lang_admin['bms_adminuser']			= 'Administrator';
			$_lang_admin['bms_enqueued_for']		= 'Eingeliefert f&uuml;r';
			$_lang_admin['bms_smtp_user']			= 'SMTP-User';
			$_lang_admin['bms_checkhelo']			= 'Mails ablehnen, wenn HELO-Hostname abweicht';
			$_lang_admin['bms_headers']				= 'Kopfzeilen';
			$_lang_admin['bms_download']			= 'Herunterladen';
			$_lang_admin['bms_sizelimitissue']		= 'Das SMTP-Mailgr&ouml;&szlig;enlimit ist kleiner als das b1gMail-Mailgr&ouml;&szlig;enlimit (Einstellungen &raquo; E-Mail &raquo; Empfang).';
			$_lang_admin['bms_ownheaders']			= 'Eigene Header';
			$_lang_admin['bms_headersnote']			= 'Ung&uuml;ltige Eingaben in diesen Feldern k&ouml;nnen besch&auml;digte E-Mails verursachen.';
			$_lang_admin['bms_clearqueue']			= 'Alle Eintr&auml;ge l&ouml;schen';
			$_lang_admin['bms_clearquestion']		= 'M&ouml;chten Sie wirklich ALLE Warteschleifen-Eintr&auml;ge l&ouml;schen?';
			$_lang_admin['bms_delbyattr']			= 'Alle Eintr&auml;ge mit gew&auml;hltem Attribut l&ouml;schen';
			$_lang_admin['bms_reallydelbyattr']		= 'Sollen wirklich alle Eintr&auml;ge, die das gew&auml;hlte Attribut besitzen, inklusive diesem Eintrag, gel&ouml;scht werden?';
			$_lang_admin['bms_deletedmsg']			= 'Es wurden <strong>%d</strong> Warteschleifen-Eintr&auml;ge gel&ouml;scht.';
			$_lang_admin['bms_spf']					= 'Sender Policy Framework (SPF)';
			$_lang_admin['bms_spf_onpass']			= 'Bei erfolgreicher Pr&uuml;fung';
			$_lang_admin['bms_spf_onfail']			= 'Bei fehlgeschlagener Pr&uuml;fung';
			$_lang_admin['bms_spf_injectheader']	= 'SPF-Header schreiben';
			$_lang_admin['bms_spf_disgrey']			= 'Grey-Listing deaktivieren';
			$_lang_admin['bms_spf_reject']			= 'E-Mail ablehnen';
			$_lang_admin['bms_queue_threads']		= 'Threads (initial / maximal)';
			$_lang_admin['bms_threads']				= 'Threads';
			$_lang_admin['bms_userarea']			= 'Benutzer-Bereich';
			$_lang_admin['bms_usershowlogin']		= 'Login-Daten anzeigen';
			$_lang_admin['bms_pop3server']			= 'POP3-Server/-Port';
			$_lang_admin['bms_smtpserver']			= 'SMTP-Server/-Port';
			$_lang_admin['bms_imapserver']			= 'IMAP-Server/-Port';
			$_lang_admin['bms_folderstofetch']		= 'Abzurufende Ordner';
			$_lang_admin['bms_user_chosepop3folders'] = 'Benutzer kann Ordner w&auml;hlen';
			$_lang_admin['bms_userfolders'] 		= 'Vom Benutzer erstellte Ordner';
			$_lang_admin['bms_usetls'] 				= 'Nach M&ouml;glichkeit TLS verwenden';
			$_lang_admin['bms_usednssecdane'] 		= 'DNSSEC und DANE-Verifizierung aktivieren';
			$_lang_admin['bms_helo_check'] 			= 'HELO-Hostname-Check';
			$_lang_admin['bms_helo_disabled']		= 'Deaktiviert';
			$_lang_admin['bms_helo_exact']			= 'Aktiviert (exakt)';
			$_lang_admin['bms_helo_fuzzy']			= 'Aktiviert (nur Domain)';
			$_lang_admin['bms_advanced']			= 'Erweitert';
			$_lang_admin['bms_deliveryrules']		= 'Auslieferungs-Regeln';
			$_lang_admin['bms_rule']				= 'Regel';
			$_lang_admin['bms_target']				= 'Ziel';
			$_lang_admin['bms_param']				= 'Parameter';
			$_lang_admin['bms_sender']				= 'Absender';
			$_lang_admin['bms_recpdomain']			= 'Empf.-Domain';
			$_lang_admin['bms_recipient']			= 'Empf&auml;nger';
			$_lang_admin['bms_target_0']			= 'Lokal zustellen';
			$_lang_admin['bms_target_3']			= 'An zust. MX ausliefern';
			$_lang_admin['bms_flag_ci']				= 'Gro&szlig;/klein ign.';
			$_lang_admin['bms_flag_regexp']			= 'Regul&auml;rer Ausdruck';
			$_lang_admin['bms_autoexpunge']			= 'Auto-Expunge';
			$_lang_admin['bms_imaplimit']			= 'Ordner-Gr&ouml;&szlig;en-Limitierung';
			$_lang_admin['bms_user_choseimaplimit']	= 'Durch Benutzer &auml;nderbar';
			$_lang_admin['bms_zerolimit']			= '0 = kein Limit';
			$_lang_admin['bms_hop_limit']			= 'Mail-Hop-Limit';
			$_lang_admin['bms_recipient_limit']		= 'Mail-Empf&auml;nger-Limit';
			$_lang_admin['bms_auth_no_received']	= '\'Received\'-Header bei auth. Clients unterdr&uuml;cken';

			$_lang_user['bms_userarea']				= 'POP3/IMAP/SMTP';
			$_lang_user['prefs_d_bms_userarea']		= 'Verwalten Sie Ihre E-Mail-Client-Zugriffsm&ouml;glichkeiten.';
			$_lang_user['bms_userlogin'] 			= 'Login-Daten';
			$_lang_user['bms_userloginnote']		= 'Verwenden Sie die folgenden Zugangsdaten, um Ihr E-Mail-Konto mit einem E-Mail-Client wie Outlook, Thunderbird oder Apple Mail abzurufen.';
			$_lang_user['bms_pop3server']			= 'POP3-Server';
			$_lang_user['bms_imapserver']			= 'IMAP-Server';
			$_lang_user['bms_smtpserver']			= 'SMTP-Server';
			$_lang_user['bms_pwnote']				= '(bekannt; wie beim Web-Login)';
			$_lang_user['bms_folderstofetch']		= 'Per POP3 abzurufende Ordner';
			$_lang_user['bms_folderstofetchnote']	= 'Hier k&ouml;nnen Sie ausw&auml;hlen, welche Ordner beim POP3-Abruf ber&uuml;cksichtigt werden sollen. Um selbst angelegte Ordner individuell ein- oder auszuschlie&szlig;en, deaktivieren Sie einfach das H&auml;kchen bei &quot;Alle selbst erstellten Ordner&quot;.';
			$_lang_user['bms_userfolders']			= 'Alle selbst erstellen Ordner';
			$_lang_user['bms_folderssaved']			= 'Die abzurufenden Ordner wurden erfolgreich gespeichert. Die Einstellungen werden bei der n&auml;chsten POP3-Sitzung ber&uuml;cksichtigt.';
			$_lang_user['bms_sslport']				= 'Verbindungssicherheit: SSL';
			$_lang_user['bms_imaplimit']			= 'Beschr&auml;nkung der IMAP-Ordner-Gr&ouml;&szlig;e';
			$_lang_user['bms_imaplimitnote']		= 'Sie k&ouml;nnen die Anzahl der E-Mails, die pro IMAP-Ordner maximal angezeigt werden, limitieren. Dies kann z.B. n&uuml;tzlich sein, wenn Sie sehr viele E-Mails haben und Ihr Postfach per mobilem Endger&auml;t abrufen.';
			$_lang_user['bms_limit']				= 'Beschr&auml;nkung';
			$_lang_user['bms_emails']				= 'E-Mails';
			$_lang_user['bms_nolimit']				= 'Unbeschr&auml;nkt';
			$_lang_user['bms_imaplimitsaved']		= 'Die IMAP-Ordner-Beschr&auml;nkung wurde erfolgreich gespeichert. Die Einstellungen werden bei der n&auml;chsten IMAP-Sitzung ber&uuml;cksichtigt.';
		}
		else
		{
			// english
			$_lang_admin['bms_milters']				= 'Mail filters (Milters)';
			$_lang_admin['bms_defaultaction']		= 'Default action';
			$_lang_admin['bms_milter_tempfail']		= 'Temp. reject';
			$_lang_admin['bms_milter_accept']		= 'Accept';
			$_lang_admin['bms_milter_reject']		= 'Reject';
			$_lang_admin['bms_milter_nonauth']		= 'Not auth.';
			$_lang_admin['bms_milter_auth']			= 'Authenticated';
			$_lang_admin['bms_tcp']					= 'TCP/IP';
			$_lang_admin['bms_local']				= 'Unix socket';
			$_lang_admin['bms_reject_norevdns']		= 'Reject anonymous clients without reverse DNS';
			$_lang_admin['bms_control_addr_help']	= 'The IP address of the queue server you enter here has to be accessible from the web server.\n\nIt is strongly discouraged to use an IP address which is accessible from the internet.\n\nIn case you run b1gMailServer and your web server on the same machine, you should leave this setting at 127.0.0.1.\n\nAfter changing this setting, the queue service needs to be restarted manually via SSH/RDP.';
			$_lang_admin['bms_control_addr']		= 'Control channel interface';
			$_lang_admin['bms_apnsqueuerestartnote']= 'You need to restart the queue service after saving changes to push settings!';
			$_lang_admin['bms_certerr_format']		= 'The format of the certificate and/or private key is invalid.';
			$_lang_admin['bms_certerr_purpose']		= 'The certificate is not suitable as SSL client certificate.';
			$_lang_admin['bms_certerr_pkcheck']		= 'The private key does not match the certificate.';
			$_lang_admin['bms_certuidcn']			= 'Certificate UID/CN';
			$_lang_admin['bms_certimport']			= 'Import certificate';
			$_lang_admin['bms_nocertset']			= 'no certificate set';
			$_lang_admin['bms_certpk']				= 'Certificate / private key';
			$_lang_admin['bms_certificate']			= 'Certificate';
			$_lang_admin['bms_privatekey']			= '<em>and</em> private key';
			$_lang_admin['bms_serverport']			= 'Server / Port';
			$_lang_admin['bms_setvaliduntil']		= 'Set, valid until';
			$_lang_admin['bms_notset']				= 'Not set';
			$_lang_admin['bms_apns']				= 'Apple Push Service';
			$_lang_admin['bms_apnsnote']			= 'to enable, please set a certificate first';
			$_lang_admin['bms_pushcertificate']		= 'Push certificate';
			$_lang_admin['bms_tls_ssl']				= 'Security';
			$_lang_admin['bms_ssl_cipher_list']		= 'SSL/TLS cipher list';
			$_lang_admin['bms_ssl_ciphersuites']		= 'TLSv1.3 cipher suites';
			$_lang_admin['bms_ssl_minmaxversion']		= 'Protocol version min/max';
			$_lang_admin['bms_tlsarecord']			= 'Suggested TLSA record';
			$_lang_admin['bms_mysqlconnection']		= 'MySQL connection';
			$_lang_admin['bms_closeduringidle']		= 'Release between IDLE polls';
			$_lang_admin['bms_closewhenidle']		= 'Release during inactivity';
			$_lang_admin['bms_running']				= 'Running';
			$_lang_admin['bms_not_running']			= 'Not running';
			$_lang_admin['bms_greeting']			= 'Greeting';
			$_lang_admin['bms_timeout']				= 'Timeout';
			$_lang_admin['bms_smtp_auth']			= 'SMTP authentication';
			$_lang_admin['bms_untrusted_limits']	= 'Restrictions for untrustworthy peers';
			$_lang_admin['bms_greetingdelay']		= 'Greeting delay';
			$_lang_admin['bms_error_delay']			= 'Error delay';
			$_lang_admin['bms_error_softlimit']		= 'Error soft limit';
			$_lang_admin['bms_error_hardlimit']		= 'Error hard limit';
			$_lang_admin['bms_greylisting']			= 'Greylisting';
			$_lang_admin['bms_grey_interval']		= 'Interval';
			$_lang_admin['bms_grey_wait_time']		= 'Reconnect wait time';
			$_lang_admin['bms_grey_good_time']		= 'Expiriation in';
			$_lang_admin['bms_minutes']				= 'minutes';
			$_lang_admin['bms_hours']				= 'hours';
			$_lang_admin['bms_list']				= 'List';
			$_lang_admin['bms_peer_classification']	= 'Peer classification';
			$_lang_admin['bms_subnet_rules']		= 'Subnet rules';
			$_lang_admin['bms_dnsbl_rules']			= 'DNSBL rules';
			$_lang_admin['bms_msgqueue']			= 'E-mail processing';
			$_lang_admin['bms_queue']				= 'Queue';
			$_lang_admin['bms_queue_prefs']			= 'Queue preferences';
			$_lang_admin['bms_queue_interval']		= 'Processing interval';
			$_lang_admin['bms_queue_retry']			= 'Delivery-attempt interval';
			$_lang_admin['bms_queue_lifetime']		= 'Queue entry lifetime';
			$_lang_admin['bms_queue_timeout']		= 'Processing timeout';
			$_lang_admin['bms_inbound']				= 'Incoming e-mails';
			$_lang_admin['bms_php_path']			= 'PHP path';
			$_lang_admin['bms_outbound']			= 'Outgoing e-mails';
			$_lang_admin['bms_processing']			= 'Processing';
			$_lang_admin['bms_redirecttosendmail']	= 'Pass to sendmail';
			$_lang_admin['bms_redirecttosmtprelay']	= 'Pass to SMTP relay server';
			$_lang_admin['bms_queuerestartnote']	= 'You need to restart the queue service after saving your changes!';
			$_lang_admin['bms_subnet']				= 'Subnet';
			$_lang_admin['bms_classification']		= 'Classification';
			$_lang_admin['bms_origin_default']		= 'Untrustworthy (default)';
			$_lang_admin['bms_origin_trusted']		= 'Trustworthy';
			$_lang_admin['bms_origin_dialup']		= 'Dialup host';
			$_lang_admin['bms_origin_reject']		= 'Reject connection';
			$_lang_admin['bms_dnsbl']				= 'DNSBL';
			$_lang_admin['bms_logging']				= 'Logging';
			$_lang_admin['bms_logging_debug']		= 'Log debug messages';
			$_lang_admin['bms_logging_notices']		= 'Log notices';
			$_lang_admin['bms_logging_warnings']	= 'Log warnings';
			$_lang_admin['bms_logging_errors']		= 'Log errors';
			$_lang_admin['bms_validating']			= 'Validating...';
			$_lang_admin['bms_valid']				= 'Valid';
			$_lang_admin['bms_invalid']				= 'Invalid';
			$_lang_admin['bms_expired']				= 'Expired';
			$_lang_admin['bms_until']				= 'until';
			$_lang_admin['bms_last_attempt']		= 'Last delivery attempt';
			$_lang_admin['bms_attempts']			= 'delivery attempt(s)';
			$_lang_admin['bms_inbound1']			= 'Incoming E-mail';
			$_lang_admin['bms_outbound1']			= 'Outgoing E-mail';
			$_lang_admin['bms_enqueued_by']			= 'Submitted by';
			$_lang_admin['bms_component']			= 'Component';
			$_lang_admin['bms_minpop3']				= 'Minimum session interval';
			$_lang_admin['bms_idle_poll']			= 'Internal IDLE poll interval';
			$_lang_admin['bms_adminplugin']			= 'admin plugin';
			$_lang_admin['bms_core']				= 'Core';
			$_lang_admin['bms_debugmode']			= 'Debug logging is activated. We strongly recommend to disable debug logging in production environments!';
			$_lang_admin['bms_queueentries']		= 'Queue entries';
			$_lang_admin['bms_pop3today']			= 'POP3 sessions (today)';
			$_lang_admin['bms_imaptoday']			= 'IMAP sessions (today)';
			$_lang_admin['bms_smtptoday']			= 'SMTP sessions (today)';
			$_lang_admin['bms_pop3traffic']			= 'POP3 traffic (today)';
			$_lang_admin['bms_imaptraffic']			= 'IMAP traffic (today)';
			$_lang_admin['bms_smtptraffic']			= 'SMTP traffic (today)';
			$_lang_admin['bms_stat_pop3sessions']	= 'POP3 sessions';
			$_lang_admin['bms_stat_pop3traffic']	= 'POP3 traffic (MB)';
			$_lang_admin['bms_stat_imapsessions']	= 'IMAP sessions';
			$_lang_admin['bms_stat_imaptraffic']	= 'IMAP traffic (MB)';
			$_lang_admin['bms_stat_smtpsessions']	= 'SMTP sessions';
			$_lang_admin['bms_stat_smtptraffic']	= 'SMTP traffic (MB)';
			$_lang_admin['bms_altpop3']				= 'Alt. redirection';
			$_lang_admin['bms_toport']				= 'to port';
			$_lang_admin['bms_reversedns']			= 'Reverse DNS lookup';
			$_lang_admin['bms_reset_stats']			= 'Reset statistics';
			$_lang_admin['bms_real_reset']			= 'Do you really want to reset the session statistics?';
			$_lang_admin['bms_feature_tls']			= 'TLS support';
			$_lang_admin['bms_feature_sig']			= 'Signature support';
			$_lang_admin['bms_greylist']			= 'Greylist';
			$_lang_admin['bms_ip']					= 'IP address';
			$_lang_admin['bms_grey_date']			= 'Last connection';
			$_lang_admin['bms_grey_confirmed']		= 'Confirmed?';
			$_lang_admin['bms_add_signature']		= 'Append group signature';
			$_lang_admin['bms_signature_sep']		= 'Signature separation string';
			$_lang_admin['bms_foldernames']			= 'Folder names';
			$_lang_admin['bms_folder_inbox']		= 'Inbox';
			$_lang_admin['bms_folder_sent']			= 'Outbox';
			$_lang_admin['bms_folder_spam']			= 'Spam';
			$_lang_admin['bms_folder_drafts']		= 'Drafts';
			$_lang_admin['bms_folder_trash']		= 'Trash';
			$_lang_admin['bms_deliverself']			= 'Use built-in delivery';
			$_lang_admin['bms_flushqueue']			= 'Flush queue';
			$_lang_admin['bms_failed']				= 'Failed';
			$_lang_admin['bms_arch']				= 'Architecture';
			$_lang_admin['bms_updatesdesc']			= 'Please click the following button to check for updates (e.g. important security updates) for your b1gMailServer plugins.';
			$_lang_admin['bms_origin_nogrey']		= 'Disable greylisting';
			$_lang_admin['bms_origin_nogreyandban']	= 'Disable greylisting and IP ban';
			$_lang_admin['bms_reuseprocess']		= 'Re-use PHP processes';
			$_lang_admin['bms_failban']				= 'Automatic IP ban';
			$_lang_admin['bms_fb_activatefor']		= 'Monitor';
			$_lang_admin['bms_fb_1']				= 'failed POP3 logins';
			$_lang_admin['bms_fb_2']				= 'failed IMAP logins';
			$_lang_admin['bms_fb_4']				= 'failed SMTP logins';
			$_lang_admin['bms_fb_8']				= 'failed SMTP \'RCPT TO\'s';
			$_lang_admin['bms_fb_16']				= 'failed FTP logins';
			$_lang_admin['bms_fb_attempts']			= 'Allowed attempts';
			$_lang_admin['bms_fb_time']				= 'In timeframe';
			$_lang_admin['bms_fb_bantime']			= 'Ban for';
			$_lang_admin['bms_random_queue_id']		= 'Random queue ID';
			$_lang_admin['bms_received_header']		= '\'Received\' header';
			$_lang_admin['bms_dont_expose']			= 'Do not expose b1gMailServer';
			$_lang_admin['bms_restartqueue']		= 'Restart queue service';
			$_lang_admin['bms_reallyrestartqueue']	= 'Do you really want to restart the queue service?';
			$_lang_admin['bms_queueitemgone']		= 'The queue item is not available anymore. Maybe it has been processed in the meantime or exceeded it\'s lifetime.';
			$_lang_admin['bms_fb_entrydate']		= 'First attempt';
			$_lang_admin['bms_fb_lastupdate']		= 'Last attempt';
			$_lang_admin['bms_fb_type']				= 'Reason';
			$_lang_admin['bms_fb_banneduntil']		= 'Banned until';
			$_lang_admin['bms_fb_type_0']			= 'Unknown';
			$_lang_admin['bms_fb_type_1']			= 'POP3 login';
			$_lang_admin['bms_fb_type_2']			= 'IMAP login';
			$_lang_admin['bms_fb_type_4']			= 'SMTP login';
			$_lang_admin['bms_fb_type_8']			= 'SMTP RCPT';
			$_lang_admin['bms_fb_type_16']			= 'FTP login';
			$_lang_admin['bms_logging_autodelete']	= 'Auto archiving';
			$_lang_admin['bms_enableolder']			= 'Enable for entries older than';
			$_lang_admin['bms_intfolders']			= 'Intelligent folders';
			$_lang_admin['bms_systemuser']			= 'System';
			$_lang_admin['bms_adminuser']			= 'Administrator';
			$_lang_admin['bms_enqueued_for']		= 'Enqueued for';
			$_lang_admin['bms_smtp_user']			= 'SMTP user';
			$_lang_admin['bms_checkhelo']			= 'Reject mails when actual hostname differs from HELO host';
			$_lang_admin['bms_headers']				= 'Headers';
			$_lang_admin['bms_download']			= 'Download';
			$_lang_admin['bms_sizelimitissue']		= 'The SMTP mail size limit is smaller than the b1gMail mail size limit (Preferences &raquo; Email &raquo; Receiving).';
			$_lang_admin['bms_ownheaders']			= 'Own headers';
			$_lang_admin['bms_headersnote']			= 'Invalid input in these fields can cause broken emails.';
			$_lang_admin['bms_clearqueue']			= 'Clear queue';
			$_lang_admin['bms_clearquestion']		= 'Do you really want to delete ALL queue entries?';
			$_lang_admin['bms_delbyattr']			= 'Delete all entries with the same selected attribute';
			$_lang_admin['bms_reallydelbyattr']		= 'Do you really want to delete all entries which share the same selected attribute, including this entry?';
			$_lang_admin['bms_deletedmsg']			= '<strong>%d</strong> queue entries have been deleted.';
			$_lang_admin['bms_spf']					= 'Sender Policy Framework (SPF)';
			$_lang_admin['bms_spf_onpass']			= 'On pass';
			$_lang_admin['bms_spf_onfail']			= 'On fail';
			$_lang_admin['bms_spf_injectheader']	= 'Write SPF header';
			$_lang_admin['bms_spf_disgrey']			= 'Disable greylisting';
			$_lang_admin['bms_spf_reject']			= 'Reject email';
			$_lang_admin['bms_queue_threads']		= 'Threads (initial / maximum)';
			$_lang_admin['bms_threads']				= 'Threads';
			$_lang_admin['bms_userarea']			= 'User area';
			$_lang_admin['bms_usershowlogin']		= 'Show login details';
			$_lang_admin['bms_pop3server']			= 'POP3 server/port';
			$_lang_admin['bms_smtpserver']			= 'SMTP server/port';
			$_lang_admin['bms_imapserver']			= 'IMAP server/port';
			$_lang_admin['bms_folderstofetch']		= 'Folders to fetch';
			$_lang_admin['bms_user_chosepop3folders'] = 'User can choose folders';
			$_lang_admin['bms_userfolders'] 		= 'User-created folders';
			$_lang_admin['bms_usetls'] 				= 'Use TLS when possible';
			$_lang_admin['bms_usednssecdane'] 		= 'Enable DNSSEC and DANE validation';
			$_lang_admin['bms_helo_check'] 			= 'HELO hostname check';
			$_lang_admin['bms_helo_disabled']		= 'Disabled';
			$_lang_admin['bms_helo_exact']			= 'Enabled (exact)';
			$_lang_admin['bms_helo_fuzzy']			= 'Enabled (domain only)';
			$_lang_admin['bms_advanced']			= 'Advanced';
			$_lang_admin['bms_deliveryrules']		= 'Delivery rules';
			$_lang_admin['bms_rule']				= 'Rule';
			$_lang_admin['bms_target']				= 'Target';
			$_lang_admin['bms_param']				= 'Parameter';
			$_lang_admin['bms_sender']				= 'Sender';
			$_lang_admin['bms_recpdomain']			= 'Recp. domain';
			$_lang_admin['bms_recipient']			= 'Recipient';
			$_lang_admin['bms_target_0']			= 'Deliver locally';
			$_lang_admin['bms_target_3']			= 'Deliver to responsible MX';
			$_lang_admin['bms_flag_ci']				= 'Case-insensitive';
			$_lang_admin['bms_flag_regexp']			= 'Regular expression';
			$_lang_admin['bms_autoexpunge']			= 'Auto expunge';
			$_lang_admin['bms_imaplimit']			= 'Folder size limit';
			$_lang_admin['bms_user_choseimaplimit']	= 'Adjustable by user';
			$_lang_admin['bms_zerolimit']			= '0 = no limit';
			$_lang_admin['bms_hop_limit']			= 'Mail hop limit';
			$_lang_admin['bms_recipient_limit']		= 'Mail recipient limit';
			$_lang_admin['bms_auth_no_received']	= 'Suppress \'Received\' header for authenticated clients';

			$_lang_user['bms_userarea']				= 'POP3/IMAP/SMTP';
			$_lang_user['prefs_d_bms_userarea']		= 'Manage your email client access methods.';
			$_lang_user['bms_userlogin'] 			= 'Login information';
			$_lang_user['bms_userloginnote']		= 'Use the following login information to set up your email account in an email client like Outlook, Thunderbird or Apple Mail.';
			$_lang_user['bms_pop3server']			= 'POP3 server';
			$_lang_user['bms_imapserver']			= 'IMAP server';
			$_lang_user['bms_smtpserver']			= 'SMTP server';
			$_lang_user['bms_pwnote']				= '(same as your web login password)';
			$_lang_user['bms_folderstofetch']		= 'Folders to fetch using POP3';
			$_lang_user['bms_folderstofetchnote']	= 'Here you can define which folders should be fetched using POP3. To individually select/deselect custom folders, just uncheck the item &quot;All user-created folders&quot;.';
			$_lang_user['bms_userfolders']			= 'All user-created folders';
			$_lang_user['bms_folderssaved']			= 'The folders have been saved successfully. The new settings will become active in your next POP3 session.';
			$_lang_user['bms_sslport']				= 'connection security: SSL';
			$_lang_user['bms_imaplimit']			= 'IMAP folder size limit';
			$_lang_user['bms_imaplimitnote']		= 'You can limit the count of emails which will be displayed in each IMAP folder. This can be useful in case you have a large amount of emails and use your IMAP account on a mobile device.';
			$_lang_user['bms_limit']				= 'Limit';
			$_lang_user['bms_emails']				= 'Emails';
			$_lang_user['bms_nolimit']				= 'Unlimited';
			$_lang_user['bms_imaplimitsaved']		= 'The IMAP folder size limit has been saved successfully. The new setting will become active in you next IMAP session.';
		}

		// convert charset
		global $currentCharset;
		$arrays = array('admin', 'client', 'user', 'custom');
		foreach($arrays as $array)
		{
		   $destArray = sprintf('lang_%s', $array);
		   $srcArray  = '_' . $destArray;

		   if(!isset($$srcArray))
			  continue;

		   foreach($$srcArray as $key=>$val)
		   {
			  if(function_exists('CharsetDecode') && !in_array(strtolower($currentCharset), array('iso-8859-1', 'iso-8859-15')))
				 $val = CharsetDecode($val, 'iso-8859-15');
			  ${$destArray}[$key] = $val;
		   }
		}
	}

	/**
	 * fetch DB prefs
	 *
	 * @return array
	 */
	function _getPrefs()
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}bms_prefs LIMIT 1');
		$prefs = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($prefs);
	}

	/**
	 * notify subscribed iOS devices via APNS when a new email is stored
	 *
	 */
	function AfterStoreMail($mailID, &$mail, &$mailbox)
	{
		global $db;

		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		if($this->prefs['apns_enable'] != 1)
			return;

		$res = $db->Query('SELECT {pre}bms_apns_subscription.`subscriptionid` FROM {pre}bms_apns_subscription '
			. 'INNER JOIN {pre}bms_apns_subscription_folder '
			. 'ON {pre}bms_apns_subscription_folder.`subscriptionid`={pre}bms_apns_subscription.`subscriptionid` '
			. 'INNER JOIN {pre}mails '
			. 'ON {pre}mails.`folder`={pre}bms_apns_subscription_folder.`folderid` AND {pre}mails.`userid`={pre}bms_apns_subscription.`userid` '
			. 'WHERE {pre}mails.`id`=?',
			$mailID);
		if($res->RowCount() > 0)
		{
			if($fp = $this->_openControlChannel())
			{
				while($row = $res->FetchArray(MYSQLI_NUM))
				{
					$subscriptionID = $row[0];
					$response = $this->_queueControlCommand($fp, sprintf('APNS_NOTIFY %d', $subscriptionID));

					if(!$response || $response[0] != '+')
					{
						PutLog(sprintf('APNS_NOTIFY command failed for subscription #%d, mail #%d: %s',
							$subscriptionID,
							$mailID,
							$response),
							PRIO_WARNING,
							__FILE__,
							__LINE__);
						continue;
					}

					PutLog(sprintf('Sent APNS_NOTIFY for subscription #%d because of mail #%d',
						$subscriptionID,
						$mailID),
						PRIO_DEBUG,
						__FILE__,
						__LINE__);
				}

				$this->_closeControlChannel($fp);
			}
			else
			{
				PutLog(sprintf('Failed to open control channel to send APNS_NOTIFY command for mail #%d - is the bms-queue service running?',
					$mailID),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
			}
		}
		$res->Free();
	}

	/**
	 * cron tasks
	 *
	 */
	function OnCron()
	{
		global $db;

		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		$db->Query('DELETE FROM {pre}bms_greylist WHERE (`time`<?) OR (`confirmed`=0 AND `time`<?)',
			time()-$this->prefs['grey_good_time'],
			time()-$this->prefs['grey_wait_time']);

		$db->Query('DELETE FROM {pre}bms_failban WHERE `last_update`<?',
			time()-$this->prefs['failban_time']);

		$db->Query('DELETE FROM {pre}bms_smtpstats WHERE `time`<?',
			time()-TIME_ONE_DAY);

		$this->ProcessEventQueue();

		// auto archive logs?
		if($this->prefs['logs_autodelete'] == 1
			&& $this->prefs['logs_autodelete_days'] >= 1
			&& $this->prefs['logs_autodelete_last'] < time()-86400)
		{
			$db->Query('UPDATE {pre}bms_prefs SET `logs_autodelete_last`=?',
				time());
			$date = time() - TIME_ONE_DAY*$this->prefs['logs_autodelete_days'];
			if($this->_archiveLogs($date, $this->prefs['logs_autodelete_archive'] == 1, false, $count))
			{
				PutLog(sprintf('Auto-archived %d b1gMailServer log entries',
					$count),
					PRIO_NOTE,
					__FILE__,
					__LINE__);
			}
		}
	}

	/**
	 * process BMS event queue
	 *
	 */
	function ProcessEventQueue()
	{
		global $db;

		$currentUserID = $currentUserObject = $currentMailbox = false;
		$processedEvents = array();

		// process events
		$res = $db->Query('SELECT `eventid`,`userid`,`type`,`param1`,`param2` FROM {pre}bms_eventqueue ORDER BY `userid` ASC,`eventid` ASC LIMIT ' . BMS_EVENTQUEUE_MAX);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['userid'] != $currentUserID)
			{
				$currentUserID = $row['userid'];
				$currentUserObject = _new('BMUser', array($currentUserID));

				if(!is_object($currentUserObject) || !is_array($currentUserObject->_row))
				{
					$currentUserID = 0;
					continue;
				}

				$currentMailbox = _new('BMMailbox', array($currentUserID, $currentUserObject->_row['email'], $currentUserObject));
			}

			if(!is_object($currentUserObject) || !is_object($currentMailbox))
				continue;

			switch($row['type'])
			{
			case BMS_EVENT_STOREMAIL:
				if(is_object($mail = $currentMailbox->GetMail((int)$row['param1'])))
				{
					ModuleFunction('AfterStoreMail',
		 				array($mail->id, &$mail, &$currentMailbox));

					if($mail->_row['folder'] == FOLDER_SPAM)
						$currentMailbox->SetSpamStatus((int)$row['param1'], true);
				}
		 		break;

			case BMS_EVENT_DELETEMAIL:
				if(method_exists($currentMailbox, 'DeleteMailFromSearchIndex'))
					$currentMailbox->DeleteMailFromSearchIndex((int)$row['param1']);
				ModuleFunction('AfterDeleteMail', array((int)$row['param1'], &$currentMailbox));
				break;

			case BMS_EVENT_MOVEMAIL:
				if((int)$row['param2'] == FOLDER_SPAM)
					$currentMailbox->SetSpamStatus((int)$row['param1'], true);
				ModuleFunction('AfterMoveMails', array(array((int)$row['param1']), (int)$row['param2'], &$currentMailbox));
				break;

			case BMS_EVENT_CHANGEMAILFLAGS:
				ModuleFunction('AfterChangeMailFlags', array((int)$row['param1'], (int)$row['param2'], &$currentMailbox));
				break;

			default:
				PutLog(sprintf('%s: Unknown event type (%d)',
					$this->name,
					$row['type']),
					PRIO_WARNING,
					__FILE__,
					__LINE__);
			}

			$processedEvents[] = $row['eventid'];
		}
		$res->Free();

		// delete processed events
		if(count($processedEvents) > 0)
			$db->Query('DELETE FROM {pre}bms_eventqueue WHERE `eventid` IN ?',
				$processedEvents);
	}

	/**
	 * connect to queue control channel
	 *
	 * @return type resource
	 */
	function _openControlChannel()
	{
		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		$addr		= $this->prefs['control_addr'];
		if(ip2long($addr) === false)
			$addr 	= '127.0.0.1';
		$port		= $this->prefs['control_port'];
		$secret		= $this->prefs['control_secret'];

		if(strlen($secret) != 32 || $port <= 0)
			return(false);

		$errNo = 0;
		$errStr = '';

		$fp = @fsockopen($addr, $port, $errNo, $errStr, 5);

		if($fp)
		{
			$response = $this->_queueControlCommand($fp, 'AUTH ' . $secret);
			if($response && $response[0] == '+')
				return($fp);

			$this->_closeControlChannel($fp);
		}

		return(false);
	}

	/**
	 * send command to queue control channel
	 *
	 * @param resource $fp Connection
	 * @param string $cmd Command
	 * @return string string Response
	 */
	function _queueControlCommand(&$fp, $cmd)
	{
		if(@fprintf($fp, $cmd . "\r\n"))
		{
			$response = @fgets2($fp);

			if(is_string($response))
				return(trim($response));
		}

		return(false);
	}

	/**
	 * close queue control channel
	 *
	 * @param type $fp resource
	 */
	function _closeControlChannel(&$fp)
	{
		$this->_queueControlCommand($fp, 'QUIT');
		fclose($fp);
	}

	/**
	 * check if queue service is running
	 *
	 */
	function _isQueueRunning()
	{
		// queue service running?
		$queueRunning = false;
		if($fp = $this->_openControlChannel())
		{
			$this->_closeControlChannel($fp);
			$queueRunning = true;
		}

		return($queueRunning);
	}

	/**
	 * admin notices
	 *
	 */
	function getNotices()
	{
		global $db, $lang_admin, $bm_prefs;
		$notices = array();

		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		// debug logging?
		if(($this->prefs['loglevel'] & BMS_LOG_DEBUG) != 0)
			$notices[] = array('type'	=> 'info',
								'text'	=> 'b1gMailServer: ' . $lang_admin['bms_debugmode'],
								'link'	=> $this->_adminLink() . '&action=common&');

		// many logs?
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_logs');
		list($logCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();
		if($logCount > 250000)
			$notices[] = array('type'	=> 'info',
								'text'	=> 'b1gMailServer: ' . $lang_admin['manylogs'],
								'link'	=> $this->_adminLink() . '&action=logs&');

		// do limits make sense?
		if($this->prefs['smtp_size_limit'] < $bm_prefs['mailmax'])
			$notices[] = array('type'	=> 'warning',
								'text'	=> 'b1gMailServer: ' . $lang_admin['bms_sizelimitissue'],
								'link'	=> $this->_adminLink() . '&action=smtp&');


		return($notices);
	}

	/**
	 * admin handler
	 *
	 */
	function AdminHandler()
	{
		global $tpl, $plugins, $lang_admin;

		// reads prefs
		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		// default action
		if(!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'overview';

		// tabs
		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['overview'],
				'icon'		=> '../plugins/templates/images/bms_logo.png',
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['action'] == 'overview'
			),
			1 => array(
				'title'		=> $lang_admin['common'],
				'relIcon'	=> 'ico_prefs_common.png',
				'link'		=> $this->_adminLink() . '&action=common&',
				'active'	=> $_REQUEST['action'] == 'common'
			),
			2 => array(
				'title'		=> $lang_admin['pop3'],
				'icon'		=> '../plugins/templates/images/bms_pop3.png',
				'link'		=> $this->_adminLink() . '&action=pop3&',
				'active'	=> $_REQUEST['action'] == 'pop3'
			),
			3 => array(
				'title'		=> $lang_admin['imap'],
				'link'		=> $this->_adminLink() . '&action=imap&',
				'icon'		=> '../plugins/templates/images/bms_imap.png',
				'active'	=> $_REQUEST['action'] == 'imap'
			),
			4 => array(
				'title'		=> $lang_admin['smtp'],
				'link'		=> $this->_adminLink() . '&action=smtp&',
				'icon'		=> '../plugins/templates/images/bms_smtp.png',
				'active'	=> $_REQUEST['action'] == 'smtp'
			),
			5 => array(
				'title'		=> $lang_admin['bms_msgqueue'],
				'icon'		=> '../plugins/templates/images/bms_queue.png',
				'link'		=> $this->_adminLink() . '&action=msgqueue&',
				'active'	=> $_REQUEST['action'] == 'msgqueue'
			),
			6 => array(
				'title'		=> $lang_admin['plugins'],
				'relIcon'	=> 'plugin32.png',
				'link'		=> $this->_adminLink() . '&action=plugins&',
				'active'	=> $_REQUEST['action'] == 'plugins'
			),
			7 => array(
				'title'		=> $lang_admin['stats'],
				'relIcon'	=> 'stats32.png',
				'link'		=> $this->_adminLink() . '&action=stats&',
				'active'	=> $_REQUEST['action'] == 'stats'
			),
			8 => array(
				'title'		=> $lang_admin['logs'],
				'relIcon'	=> 'filter.png',
				'link'		=> $this->_adminLink() . '&action=logs&',
				'active'	=> $_REQUEST['action'] == 'logs'
			)
		);

		// assign
		$tpl->assign('tabHeaderText',	'b1gMailServer');
		$tpl->assign('tabs', 			$tabs);
		$tpl->assign('bms_prefs', 		$this->prefs);

		// call page function
		if($_REQUEST['action'] == 'overview')
			$this->_overviewPage();
		if($_REQUEST['action'] == 'common')
			$this->_commonPage();
		if($_REQUEST['action'] == 'pop3')
			$this->_pop3Page();
		if($_REQUEST['action'] == 'imap')
			$this->_imapPage();
		if($_REQUEST['action'] == 'smtp')
			$this->_smtpPage();
		if($_REQUEST['action'] == 'msgqueue')
			$this->_msgqueuePage();
		else if($_REQUEST['action'] == 'plugins')
			$this->_pluginsPage();
		else if($_REQUEST['action'] == 'stats')
			$this->_statsPage();
		else if($_REQUEST['action'] == 'logs')
			$this->_logsPage();
		else if($_REQUEST['action'] == 'lookupIP'
			&& isset($_REQUEST['ip']))
		{
			$hostName = @gethostbyaddr($_REQUEST['ip']);
			if(!$hostName || $hostName == $_REQUEST['ip'])
				$hostName = $lang_admin['unknown'];
			echo $_REQUEST['ip'] . '/' . $hostName;
			exit();
		}
	}

	/**
	 * common page
	 *
	 */
	function _commonPage()
	{
		global $db, $tpl, $lang_admin, $bm_prefs, $currentLanguage;

		if(!isset($_REQUEST['do']))
		{
			// save?
			if(isset($_REQUEST['save']) && IsPOSTRequest())
			{
				$logLevel = 0;
				$failbanTypes = 0;

				if(isset($_REQUEST['loglevel']) && is_array($_REQUEST['loglevel']))
					foreach($_REQUEST['loglevel'] as $key=>$val)
						$logLevel |= $key;

				if(isset($_REQUEST['failban_types']) && is_array($_REQUEST['failban_types']))
					foreach($_REQUEST['failban_types'] as $key=>$val)
						$failbanTypes |= $key;

				$sslMinVersion = (int)$_REQUEST['ssl_min_version'];
				$sslMaxVersion = (int)$_REQUEST['ssl_max_version'];
				if($sslMaxVersion < $sslMinVersion && $sslMaxVersion !== 0)
					list($sslMinVersion, $sslMaxVersion) = array($sslMaxVersion, $sslMinVersion);

				// update
				$db->Query('UPDATE {pre}bms_prefs SET loglevel=?,failban_types=?,failban_time=?,failban_bantime=?,failban_attempts=?,logs_autodelete=?,logs_autodelete_days=?,logs_autodelete_archive=?,user_showlogin=?,user_pop3server=?,user_pop3port=?,user_smtpserver=?,user_smtpport=?,user_imapserver=?,user_imapport=?,user_pop3ssl=?,user_smtpssl=?,user_imapssl=?,ssl_cipher_list=?,ssl_ciphersuites=?,ssl_min_version=?,ssl_max_version=?',
					$logLevel,
					$failbanTypes,
					(int)$_REQUEST['failban_time'],
					(int)$_REQUEST['failban_bantime'],
					max((int)$_REQUEST['failban_attempts'], 1),
					isset($_REQUEST['logs_autodelete']) ? 1 : 0,
					max(1, (int)$_REQUEST['logs_autodelete_days']),
					isset($_REQUEST['logs_autodelete_archive']) ? 1 : 0,
					isset($_REQUEST['user_showlogin']) ? 1 : 0,
					$_REQUEST['user_pop3server'],
					(int)$_REQUEST['user_pop3port'],
					$_REQUEST['user_smtpserver'],
					(int)$_REQUEST['user_smtpport'],
					$_REQUEST['user_imapserver'],
					(int)$_REQUEST['user_imapport'],
					isset($_REQUEST['user_pop3ssl']) ? 1 : 0,
					isset($_REQUEST['user_smtpssl']) ? 1 : 0,
					isset($_REQUEST['user_imapssl']) ? 1 : 0,
					trim($_REQUEST['ssl_cipher_list']),
					trim($_REQUEST['ssl_ciphersuites']),
					$sslMinVersion,
					$sslMaxVersion);
				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			// reset ban list?
			if(isset($_REQUEST['resetBanList']))
			{
				$db->Query('TRUNCATE TABLE {pre}bms_failban');
			}

			// ban list
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_failban WHERE `banned_until`>=?',
				time());
			list($banCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// assign
			$tpl->assign('queueRunning',	$this->_isQueueRunning());
			$tpl->assign('banCount',		$banCount);
			$tpl->assign('pageURL', 		$this->_adminLink());
			$tpl->assign('page', 			$this->_templatePath('bms.admin.common.tpl'));
		}
		else if($_REQUEST['do'] == 'banlist')
		{
			// delete?
			if(isset($_REQUEST['delete']) && is_array($_REQUEST['delete']))
			{
				$db->Query('DELETE FROM {pre}bms_failban WHERE `id` IN ?',
					$_REQUEST['delete']);
			}

			// get banlist entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_failban WHERE `banned_until`>=?',
				time());
			list($banCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// page options
			$perPage 	= 50;
			$pageCount 	= ceil($banCount / $perPage);
			$pageNo		= isset($_REQUEST['page'])
							? max(1, min($pageCount, (int)$_REQUEST['page']))
							: 1;
			$startPos	= ($pageNo-1) * $perPage;

			// get entries
			$banlist  = array();
			$res = $db->Query('SELECT `id`,`ip`,`ip6`,`entry_date`,`banned_until`,`last_update`,`attempts`,`type` FROM {pre}bms_failban WHERE `banned_until`>=? ORDER BY `banned_until` ASC LIMIT '
				. $startPos . ',' . $perPage,
				time());
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$types = array();
				for($i=0; $i<=4; $i++)
				{
					$val = (1<<$i);
					if(($row['type'] & $val) != 0)
					{
						$types[] = $lang_admin['bms_fb_type_'.$val];
					}
				}

				$row['type_text'] = implode(', ', $types);
				$row['ip'] = long2ip($this->_htons($row['ip']));
				$row['ip6'] = $this->_dbIP6($row['ip6']);
				$banlist[$row['id']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('pageNo',		$pageNo);
			$tpl->assign('pageCount',	$pageCount);
			$tpl->assign('banlist',		$banlist);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.common.banlist.tpl'));
		}
		else if($_REQUEST['do'] == 'tlsaRecord')
		{
			if($fp = $this->_openControlChannel())
			{
				$tlsaResponse = $this->_queueControlCommand($fp, 'GET_TLSARECORD');
				$this->_closeControlChannel($fp);

				if(strlen($tlsaResponse) > 4 && substr($tlsaResponse, 0, 3) == '+OK')
					echo(trim(substr($tlsaResponse, 4)));
			}
			exit();
		}
	}

	/**
	 * overview page
	 *
	 */
	function _overviewPage()
	{
		global $db, $tpl, $currentLanguage;

		//
		// fetch stats
		//
		$startDate = mktime(0, 0, 0);

		// pop3
		$res = $db->Query('SELECT `connections`,`in`,`out` FROM {pre}bms_stats WHERE `component`=? AND `date`=CURDATE()',
			BMS_CMP_POP3);
		if($res->RowCount() == 0)
			$pop3Today = $pop3In = $pop3Out = 0;
		else
			list($pop3Today, $pop3In, $pop3Out) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// imap
		$res = $db->Query('SELECT `connections`,`in`,`out` FROM {pre}bms_stats WHERE `component`=? AND `date`=CURDATE()',
			BMS_CMP_IMAP);
		if($res->RowCount() == 0)
			$imapToday = $imapIn = $imapOut = 0;
		else
			list($imapToday, $imapIn, $imapOut) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// smtp
		$res = $db->Query('SELECT `connections`,`in`,`out` FROM {pre}bms_stats WHERE `component`=? AND `date`=CURDATE()',
			BMS_CMP_SMTP,
			$startDate);
		if($res->RowCount() == 0)
			$smtpToday = $smtpIn = $smtpOut = 0;
		else
			list($smtpToday, $smtpIn, $smtpOut) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// queue entries
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_queue WHERE `deleted`=0');
		list($queueEntries) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// inbound queue entries
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_queue WHERE `type`=0 AND `deleted`=0');
		list($queueInbound) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// outbound queue entries
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_queue WHERE `type`=1 AND `deleted`=0');
		list($queueOutbound) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// running?
		$queueRunning = false;
		$threadCount = 0;
		if($fp = $this->_openControlChannel())
		{
			$queueRunning = true;
			$threadResponse = $this->_queueControlCommand($fp, 'GET_THREADCOUNT');
			$this->_closeControlChannel($fp);

			if(strlen($threadResponse) > 4 && substr($threadResponse, 0, 3) == '+OK')
				$threadCount = (int)trim(substr($threadResponse, 4));
		}

		// assign
		$tpl->assign('queueRunning',	$queueRunning);
		$tpl->assign('threadCount',		$threadCount);
		$tpl->assign('pop3Today',		$pop3Today);
		$tpl->assign('imapToday',		$imapToday);
		$tpl->assign('smtpToday',		$smtpToday);
		$tpl->assign('pop3Traffic',		$pop3In + $pop3Out);
		$tpl->assign('imapTraffic',		$imapIn + $imapOut);
		$tpl->assign('smtpTraffic',		$smtpIn + $smtpOut);
		$tpl->assign('queueEntries',	$queueEntries);
		$tpl->assign('queueInbound',	$queueInbound);
		$tpl->assign('queueOutbound',	$queueOutbound);
		$tpl->assign('notices',			$this->getNotices());
		$tpl->assign('adminVersion',	$this->version);
		$tpl->assign('coreVersion',		explode(' ', $this->prefs['core_version'])[0]);
		$tpl->assign('pageURL', 		$this->_adminLink());
		$tpl->assign('lang',			$currentLanguage);
		$tpl->assign('page', 			$this->_templatePath('bms.admin.overview.tpl'));
	}

	/**
	 * pop3 page
	 *
	 */
	function _pop3Page()
	{
		global $db, $tpl;

		// save?
		if(isset($_REQUEST['save']) && IsPOSTRequest())
		{
			$pop3Folders = implode(',', $_POST['pop3_folders']);
			$db->Query('UPDATE {pre}bms_prefs SET pop3greeting=?,pop3_timeout=?,altpop3=?,user_chosepop3folders=?,pop3_folders=?',
				$_REQUEST['pop3greeting'],
				(int)$_REQUEST['pop3_timeout'],
				isset($_REQUEST['altpop3_enable']) ? (int)$_REQUEST['altpop3_port'] : 0,
				isset($_REQUEST['user_chosepop3folders']) ? 1 : 0,
				$pop3Folders);
			$this->prefs = $this->_getPrefs();
			$tpl->assign('bms_prefs', $this->prefs);
		}

		// prepare pop3 folders array
		$pop3Folders = array();
		$pop3FolderIDs = explode(',', $this->prefs['pop3_folders']);
		foreach($pop3FolderIDs as $folderID)
			$pop3Folders[ str_replace('-', 'm', $folderID) ] = true;

		// assign
		$tpl->assign('pop3Folders',	$pop3Folders);
		$tpl->assign('pageURL', 	$this->_adminLink());
		$tpl->assign('page', 		$this->_templatePath('bms.admin.pop3.tpl'));
	}

	/**
	 * imap page
	 *
	 */
	function _imapPage()
	{
		global $db, $tpl, $lang_admin;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'prefs';

		if($_REQUEST['do'] == 'prefs')
		{
			// save?
			if(isset($_REQUEST['save']) && IsPOSTRequest())
			{
				$db->Query('UPDATE {pre}bms_prefs SET imapgreeting=?,imap_timeout=?,imap_idle_poll=?,imap_idle_mysqlclose=?,imap_mysqlclose=?,imap_intelligentfolders=?,imap_folder_sent=?,imap_folder_spam=?,imap_folder_drafts=?,imap_folder_trash=?,imap_autoexpunge=?,user_choseimaplimit=?,imap_limit=?,apns_enable=?,apns_host=?,apns_port=?',
					$_REQUEST['imapgreeting'],
					(int)$_REQUEST['imap_timeout'],
					max(1, (int)$_REQUEST['imap_idle_poll']),
					isset($_REQUEST['imap_idle_mysqlclose']) ? 1 : 0,
					isset($_REQUEST['imap_mysqlclose']) ? 1 : 0,
					isset($_REQUEST['imap_intelligentfolders']) ? 1 : 0,
					$_REQUEST['imap_folder_sent'],
					$_REQUEST['imap_folder_spam'],
					$_REQUEST['imap_folder_drafts'],
					$_REQUEST['imap_folder_trash'],
					isset($_REQUEST['imap_autoexpunge']) ? 1 : 0,
					isset($_REQUEST['user_choseimaplimit']) ? 1 : 0,
					(int)$_REQUEST['imap_limit'],
					isset($_REQUEST['apns_enable']) ? 1 : 0,
					$_REQUEST['apns_host'],
					(int)$_REQUEST['apns_port']);
				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			// apns status
			$apnsSet = false;
			$apnsValid = false;
			if(function_exists('openssl_x509_parse'))
			{
				$cert = @openssl_x509_parse($this->prefs['apns_certificate']);
				if($cert)
				{
					$apnsSet = true;
					$apnsValid = $cert['validTo_time_t'] > time();
					$tpl->assign('apnsValidUntil', $cert['validTo_time_t']);
				}
			}
			$tpl->assign('apnsSet', $apnsSet);
			$tpl->assign('apnsValid', $apnsValid);

			// assign
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.imap.tpl'));
		}

		else if($_REQUEST['do'] == 'apns')
		{
			$stopIt = false;

			if(isset($_REQUEST['import']))
			{
				$certData = $keyData = '';

				// pem?
				if(isset($_FILES['cert_pem'])
					&& $_FILES['cert_pem']['error'] == 0
					&& $_FILES['cert_pem']['size'] > 5)
				{
					// request temp file
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// move uploaded file to temp file
					if(move_uploaded_file($_FILES['cert_pem']['tmp_name'], $tempFileName))
						$certData = getFileContents($tempFileName);

					ReleaseTempFile(0, $tempFileID);
				}

				// key?
				if(isset($_FILES['cert_key'])
					&& $_FILES['cert_key']['error'] == 0
					&& $_FILES['cert_key']['size'] > 5)
				{
					// request temp file
					$tempFileID = RequestTempFile(0);
					$tempFileName = TempFileName($tempFileID);

					// move uploaded file to temp file
					if(move_uploaded_file($_FILES['cert_key']['tmp_name'], $tempFileName))
						$keyData = getFileContents($tempFileName);

					ReleaseTempFile(0, $tempFileID);
				}

				$success = false;
				$error = 'format';

				if($certData && $keyData && strlen($certData) > 5 && strlen($keyData) > 5)
				{
					$certData = str_replace(' TRUSTED ', ' ', $certData);
					$cert = @openssl_x509_read(trim($certData));

					if($cert)
					{
						if(@openssl_x509_check_private_key($cert, $keyData))
						{
							$certInfo = openssl_x509_parse($cert);

							// check purpose
							$sslClient = false;
							foreach($certInfo['purposes'] as $purpose)
							{
								if($purpose[2] == 'sslclient'
									&& $purpose[0])
									$sslClient = true;
							}

							if($sslClient)
							{
								$db->Query('UPDATE {pre}bms_prefs SET `apns_certificate`=?,`apns_privatekey`=?',
									$certData,
									$keyData);
								$this->prefs = $this->_getPrefs();
								$success = true;
							}
							else
								$error = 'purpose';
						}
						else
							$error = 'pkcheck';

						openssl_x509_free($cert);
					}
				}

				// display result on error
				if(!$success)
				{
					$tpl->assign('msgTitle', $lang_admin['error']);
					$tpl->assign('msgText', $lang_admin['bms_certerr_'.$error]);
					$tpl->assign('msgIcon', 'error32');
					$tpl->assign('page', 'msg.tpl');
					$stopIt = true;
				}
			}

			if(!$stopIt)
			{
				if(function_exists('openssl_x509_parse'))
				{
					$cert = @openssl_x509_parse($this->prefs['apns_certificate']);
					if($cert)
					{
						$tpl->assign('validCert', $cert['validTo_time_t'] > time());
						$tpl->assign('certInfo', $cert);
					}
				}

				// assign
				$tpl->assign('pageURL', 	$this->_adminLink());
				$tpl->assign('page', 		$this->_templatePath('bms.admin.imap.apns.tpl'));
			}
		}
	}


	/**
	 * get stat data
	 *
	 * @param string $types Stat type
	 * @param int $time Stat time
	 * @return array
	 */
	function _getStatData($type, $time)
	{
		global $db;

		$componentTable = array(
			'pop3'		=> BMS_CMP_POP3,
			'imap'		=> BMS_CMP_IMAP,
			'smtp'		=> BMS_CMP_SMTP
		);

		// load class, if needed
		if(!class_exists('BMCalendar'))
			include(B1GMAIL_DIR . 'serverlib/calendar.class.php');

		// component
		$component = $componentTable[substr($type, 0, 4)];

		// pepare result array
		$result = array();
		for($i=1; $i<=BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); $i++)
			$result[(int)$i] = array($type => 0);

		// traffic?
		if(substr($type, -7) == 'traffic')
		{
			for($i=1; $i<=BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); $i++)
			{
				$res = $db->Query('SELECT `in`,`out` FROM {pre}bms_stats WHERE `component`=? AND `date`=?',
					$component,
					sprintf('%04d-%02d-%02d', date('Y', $time), date('m', $time), $i));
				if($res->RowCount() == 0)
					$inTraffic = $outTraffic = 0;
				else
					list($inTraffic, $outTraffic) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				$result[(int)$i] = array($type => round(($inTraffic+$outTraffic)/1024/1024, 2));
			}
		}

		// sessions
		else if(substr($type, -8) == 'sessions')
		{
			for($i=1; $i<=BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); $i++)
			{
				$res = $db->Query('SELECT `connections` FROM {pre}bms_stats WHERE `component`=? AND `date`=?',
					$component,
					sprintf('%04d-%02d-%02d', date('Y', $time), date('m', $time), $i));
				if($res->RowCount() == 0)
					$count = 0;
				else
					list($count) = $res->FetchArray(MYSQLI_NUM);
				$res->Free();

				$result[(int)$i] = array($type => $count);
			}
		}

		return($result);
	}

	/**
	 * plugins page
	 *
	 */
	function _pluginsPage()
	{
		global $db, $tpl, $lang_admin;

		if(!isset($_REQUEST['do'])
			|| in_array($_REQUEST['do'], array('activatePlugin', 'deactivatePlugin')))
		{
			// activate?
			if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'activatePlugin')
				$db->Query('UPDATE bm60_bms_mods SET `active`=1 WHERE `filename`=?',
					$_REQUEST['filename']);
			if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'deactivatePlugin')
				$db->Query('UPDATE bm60_bms_mods SET `active`=0 WHERE `filename`=?',
					$_REQUEST['filename']);

			// fetch
			$plugins = array();
			$res = $db->Query('SELECT `filename`,`name`,`title`,`version`,`author`,`author_website`,`update_url`,`active` FROM bm60_bms_mods ORDER BY `active` DESC, `title` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
				$plugins[$row['filename']] = $row;
			$res->Free();

			// assign
			$tpl->assign('updateCheck',	isset($_REQUEST['updateCheck']));
			$tpl->assign('plugins',		$plugins);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.plugins.tpl'));
		}

		else if($_REQUEST['do'] == 'updateCheck')
		{
			$res = $db->Query('SELECT `version`,`update_url`,`author_website`,`name` FROM bm60_bms_mods WHERE `filename`=?',
				$_REQUEST['filename']);
			$res->RowCount() == 1 || die('Invalid plugin');
			$row = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			// abuse the plugin base class to do the update check
			$plugin = new BMPlugin();
			$plugin->internal_name 	= 'bMS::' . $row['name'];
			$plugin->version 		= $row['version'];
			$plugin->update_url		= $row['update_url'];

			$latestVersion = '';
			$resultCode = $plugin->CheckForUpdates($latestVersion);

			printf('%s;%d;%s;%s',
				$_REQUEST['filename'],
				$resultCode,
				$latestVersion,
				$row['author_website']);

			exit();
		}
	}

	/**
	 * stats page
	 *
	 */
	function _statsPage()
	{
		global $db, $tpl, $lang_admin;

		if(!class_exists('BMChart'))
			include(B1GMAIL_DIR . 'serverlib/chart.class.php');

		// reset?
		if(isset($_REQUEST['do']) && $_REQUEST['do']=='reset')
		{
			$db->Query('TRUNCATE TABLE {pre}bms_stats');
		}

		// show chart?
		if(isset($_REQUEST['do']) && $_REQUEST['do']=='showChart'
			&& isset($_REQUEST['statType']) && isset($_REQUEST['time']))
		{
			$statTypeItem = $_REQUEST['statType'];
			$time = (int)$_REQUEST['time'];
			$rawData = $this->_getStatData($statTypeItem, $time);
			$data = array();

			foreach($rawData as $key=>$val)
				$data[$key] = array_pop($val);

			$chart = new BMChart(sprintf('%s (%d/%d)', $lang_admin['bms_stat_'.$statTypeItem], date('m', $time), date('Y', $time)),
				520,
				280);
			$chart->SetData($data);
			$chart->Display();
			exit();
		}

		// time?
		if(!isset($_REQUEST['timeMonth']))
			$time = mktime(0, 0, 0, date('m'), 1, date('Y'));
		else
			$time = mktime(0, 0, 0, $_REQUEST['timeMonth'], 1, $_REQUEST['timeYear']);

		// common stats
		$statTypes = array('pop3', 'imap', 'smtp');

		// stat type
		$statType = isset($_REQUEST['statType'])
					? $_REQUEST['statType']
					: $statTypes[0];

		// special types
		$statsSpecial = array(
			'pop3'		=> array('pop3sessions', 'pop3traffic'),
			'imap'		=> array('imapsessions', 'imaptraffic'),
			'smtp'		=> array('smtpsessions', 'smtptraffic')
		);
		if(isset($statsSpecial[$statType]))
			$statTypeList = $statsSpecial[$statType];
		else
			$statTypeList = array($statType);

		// build stats
		$stats = array();
		foreach($statTypeList as $statTypeItem)
		{
			$statData = $this->_getStatData($statTypeItem, $time);
			$maxVal = 0;

			foreach($statData as $val)
				if($val[$statTypeItem] > $maxVal)
					$maxVal = $val[$statTypeItem];

			if(!in_array($statTypeItem, array('pop3traffic', 'imaptraffic', 'smtptraffic')))
				if($maxVal%10 != 0)
					$maxVal += (10-$maxVal%10);

			$heights = array();
			$sum = 0;
			foreach($statData as $day=>$val)
			{
				$theVal = $val[$statTypeItem];
				$sum += $theVal;

				if($maxVal <= 0)
					$heights[$day] = 0;
				else
					$heights[$day] = round(($theVal/$maxVal)*240, 0);
			}

			$yScale = array();
			for($i=10; $i>0; $i--)
			{
				$scale = $maxVal == 0 ? '' : round($maxVal*$i/10, 2);
				$yScale[$i] = $scale;
			}

			$stats[] = array(
				'title'		=> sprintf('%s (%d/%d)', $lang_admin['bms_stat_'.$statTypeItem], date('m', $time), date('Y', $time)),
				'key'		=> $statTypeItem,
				'maxVal'	=> $maxVal,
				'yScale'	=> $yScale,
				'heights'	=> $heights,
				'data'		=> $statData,
				'count'		=> count($statData),
				'sum'		=> $sum
			);
		}

		// assign
		$tpl->assign('stats', 		$stats);
		$tpl->assign('time', 		$time);
		$tpl->assign('statType', 	$statType);
		$tpl->assign('statTypes', 	$statTypes);
		$tpl->assign('pageURL', 	$this->_adminLink());
		$tpl->assign('page', 		$this->_templatePath('bms.admin.stats.tpl'));
	}

	/**
	 * archive log entries
	 *
	 * @param int $date Date
	 * @param bool $archive Save copy to archive?
	 * @param bool $dieOnError Terminate script on error?
	 * @return boolean
	 */
	function _archiveLogs($date, $archive, $dieOnError = true, &$archivedLogEntryCount = null)
	{
		global $db;

		$componentNames = array(
			BMS_CMP_CORE		=> 'Core',
			BMS_CMP_HTTP		=> 'HTTP',
			BMS_CMP_IMAP		=> 'IMAP',
			BMS_CMP_MSGQUEUE	=> 'MSGQueue',
			BMS_CMP_POP3		=> 'POP3',
			BMS_CMP_SMTP		=> 'SMTP',
			BMS_CMP_PLUGIN		=> 'Plugin',
			BMS_CMP_FTP			=> 'FTP'
		);

		if($archive)
		{
			$fileName = B1GMAIL_REL . 'logs/b1gMailServerLog-' . time() . '.log';
			if(PHPNumVersion() >= 430 && function_exists('bzopen'))
			{
				$fp = fopen('compress.bzip2://' . $fileName . '.bz2', 'w+');
			}

			if(!isset($fp) || !$fp)
			{
				$fp = fopen($fileName, 'w+');
			}

			if(!$fp)
			{
				if($dieOnError)
				{
					DisplayError(0x15, 'Cannot create log archive file',
						'Failed to create a new log archive file. The archiving procedure has been aborted.',
						sprintf("File:\n%s", $fileName),
						__FILE__,
						__LINE__);
					die();
				}
				else
				{
					PutLog('Failed to archive b1gMailServer logs (cannot create log archive file) - log cleanup process aborted',
						PRIO_WARNING,
						__FILE__,
						__LINE__);
					return(false);
				}
			}

			fwrite($fp, '#' . "\n");
			fwrite($fp, '# b1gMailServer ' . $this->prefs['core_version'] . "\n");
			fwrite($fp, '# Log file' . "\n");
			fwrite($fp, '#' . "\n");
			fwrite($fp, '# To: ' . date('r', $date) . "\n");
			fwrite($fp, '# Generated: ' . date('r') . "\n");
			fwrite($fp, '#' . "\n");
			fwrite($fp, "\n");

			$res = $db->Query('SELECT iComponent,iSeverity,iDate,szEntry FROM {pre}bms_logs WHERE iDate<'.$date.' ORDER BY id ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				fwrite($fp, sprintf('[%s] %s [%d]: %s' . "\n",
					$componentNames[$row['iComponent']],
					date('r', $row['iDate']),
					$row['iSeverity'],
					trim($row['szEntry'])));
			}
			$res->Free();

			fclose($fp);
		}

		$db->Query('DELETE FROM {pre}bms_logs WHERE iDate<'.$date);
		$archivedLogEntryCount = $db->AffectedRows();
		return(true);
	}

	/**
	 * logs page
	 *
	 */
	function _logsPage()
	{
		global $db, $tpl;

		$prioImg = array(
			BMS_LOG_DEBUG		=> 'debug',
			BMS_LOG_ERROR		=> 'error',
			BMS_LOG_NOTICE		=> 'info',
			BMS_LOG_WARNING		=> 'warning'
		);
		$componentNames = array(
			BMS_CMP_CORE		=> 'Core',
			BMS_CMP_HTTP		=> 'HTTP',
			BMS_CMP_IMAP		=> 'IMAP',
			BMS_CMP_MSGQUEUE	=> 'MSGQueue',
			BMS_CMP_POP3		=> 'POP3',
			BMS_CMP_SMTP		=> 'SMTP',
			BMS_CMP_PLUGIN		=> 'Plugin',
			BMS_CMP_FTP			=> 'FTP'
		);
		$start = isset($_REQUEST['startDay']) ? SmartyDateTime('start')
					: (isset($_REQUEST['start']) ? (int)$_REQUEST['start']
						: mktime(0, 0, 0, date('m'), date('d'), date('Y')));
		$end = isset($_REQUEST['endDay']) ? SmartyDateTime('end') + 59
					: (isset($_REQUEST['end']) ? (int)$_REQUEST['end']
						: time());
		$component = isset($_REQUEST['component']) && is_array($_REQUEST['component'])
					? $_REQUEST['component']
					: array(1 => true, 2 => true, 4 => true, 8 => true, 16 => true, 32 => true, 64 => true);
		$prio = isset($_REQUEST['prio']) && is_array($_REQUEST['prio'])
					? $_REQUEST['prio']
					: array(1 => true, 2 => true, 4 => true);
		$addQ = isset($_REQUEST['q']) && trim($_REQUEST['q']) != ''
					? ' AND szEntry LIKE \'%' . $db->Escape($_REQUEST['q']) . '%\''
					: '';

		// get log count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_logs WHERE iDate>='.$start.' AND iDate<='.$end . ' AND iSeverity IN ? AND iComponent IN ?' . $addQ,
			array_keys($prio),
			array_keys($component));
		list($logCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// page stuff
		$itemsPerPage = 100;
		$pageNo = (isset($_REQUEST['page']))
						? (int)$_REQUEST['page']
						: 1;
		$pageCount = max(1, ceil($logCount / max(1, $itemsPerPage)));
		$pageNo = min($pageCount, max(1, $pageNo));

		/**
		 * archive?
		 */
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'archive')
		{
			$this->_archiveLogs(SmartyDateTime('date'), isset($_REQUEST['saveCopy']), true);
		}

		/**
		 * export?
		 */
		$exportMode = false;
		if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'export')
		{
			$exportMode = true;
			header('Pragma: public');
			header('Content-Type: text/plain');
			header(sprintf('Content-Disposition: attachment; filename=b1gMailServerLog-%d-%d.log',
				$start, $end));

			// header
			echo '#' . "\n";
			echo '# b1gMailServer ' . $this->prefs['core_version'] . "\n";
			echo '# Log file' . "\n";
			echo '#' . "\n";
			echo '# From: ' . date('r', $start) . "\n";
			echo '# To: ' . date('r', $end) . "\n";
			echo '# Page: ' . $pageNo . ' / ' . $pageCount . "\n";
			echo '# Generated: ' . date('r') . "\n";
			echo '#' . "\n";
			echo "\n";
		}

		$entries = array();
		$res = $db->Query('SELECT iComponent,iSeverity,iDate,szEntry FROM {pre}bms_logs WHERE iDate>='.$start.' AND iDate<='.$end.' AND iSeverity IN ? AND iComponent IN ?'.$addQ.' ORDER BY id DESC'
			. (!$exportMode ? ' LIMIT ' . ($pageNo-1)*$itemsPerPage . ',' . $itemsPerPage : ''),
			array_keys($prio),
			array_keys($component));
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($exportMode)
			{
				printf('[%s] %s [%d]: %s' . "\n",
					$componentNames[$row['iComponent']],
					date('r', $row['iDate']),
					$row['iSeverity'],
					trim($row['szEntry']));
			}
			else
			{
				$row['szEntry']			= HTMLFormat($row['szEntry']);
				if(isset($_REQUEST['q']) && trim($_REQUEST['q']) != '')
				{
					$row['szEntry']			= preg_replace('/'.str_replace('/', '\/', preg_quote($_REQUEST['q'])).'/i',
						'<font style="background-color:yellow;">$0</font>',
						$row['szEntry']);
				}
				$row['szEntry']			= preg_replace('/^\#([0-9]*)/',
					sprintf('<a href="%s&action=logs&q=%%23$1%%20-&start=%d&end=%d&sid=%s" style="color:blue;">$0</a>',
						$this->_adminLink(),
						$start,
						$end,
						session_id()),
					$row['szEntry']);

				$row['componentName']	= $componentNames[$row['iComponent']];
				$row['prioImg']			= $prioImg[$row['iSeverity']];
				$entries[] = $row;
			}
		}
		$res->Free();

		if($exportMode)
			die();

		// assign
		$prioQ = '';
		foreach($prio as $key=>$val)
			$prioQ .= '&prio[' . ((int)$key) . ']=true';
		foreach($component as $key=>$val)
			$prioQ .= '&component[' . ((int)$key) . ']=true';

		$tpl->assign('q', 			isset($_REQUEST['q']) ? $_REQUEST['q'] : '');
		$tpl->assign('ueQ', 		isset($_REQUEST['q']) ? urlencode($_REQUEST['q']) : '');
		$tpl->assign('prioQ',		$prioQ);
		$tpl->assign('prio', 		$prio);
		$tpl->assign('component', 	$component);
		$tpl->assign('start', 		$start);
		$tpl->assign('end', 		$end);
		$tpl->assign('entries', 	$entries);
		$tpl->assign('pageNo',		$pageNo);
		$tpl->assign('pageCount',	$pageCount);
		$tpl->assign('pageURL', 	$this->_adminLink());
		$tpl->assign('page', 		$this->_templatePath('bms.admin.logs.tpl'));
	}

	/**
	 * smtp page
	 *
	 */
	function _smtpPage()
	{
		global $db, $tpl;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'prefs';

		//
		// prefs
		//
		if($_REQUEST['do'] == 'prefs')
		{
			// save?
			if(isset($_REQUEST['save']) && IsPOSTRequest())
			{
				$db->Query('UPDATE {pre}bms_prefs SET smtpgreeting=?,smtp_timeout=?,smtp_auth_enabled=?,smtp_greeting_delay=?,smtp_error_delay=?,smtp_error_softlimit=?,smtp_error_hardlimit=?,grey_enabled=?,grey_interval=?,grey_wait_time=?,grey_good_time=?,smtp_reversedns=?,smtp_size_limit=?,smtp_check_helo=?,spf_enable=?,spf_inject_header=?,spf_disable_greylisting=?,spf_reject_mails=?,smtp_hop_limit=?,smtp_auth_no_received=?,smtp_reject_noreversedns=?,smtp_recipient_limit=?',
					$_REQUEST['smtpgreeting'],
					(int)$_REQUEST['smtp_timeout'],
					isset($_REQUEST['smtp_auth_enabled']) ? 1 : 0,
					(int)$_REQUEST['smtp_greeting_delay'],
					(int)$_REQUEST['smtp_error_delay'],
					(int)$_REQUEST['smtp_error_softlimit'],
					(int)$_REQUEST['smtp_error_hardlimit'],
					isset($_REQUEST['grey_enabled']) ? 1 : 0,
					(int)$_REQUEST['grey_interval'],
					(int)$_REQUEST['grey_wait_time'] * 3600,
					(int)$_REQUEST['grey_good_time'] * 3600,
					isset($_REQUEST['smtp_reversedns']) ? 1 : 0,
					(int)$_REQUEST['smtp_size_limit'] * 1024,
					$_REQUEST['smtp_check_helo'],
					isset($_REQUEST['spf_enable']) ? 1 : 0,
					isset($_REQUEST['spf_inject_header']) ? 1 : 0,
					isset($_REQUEST['spf_disable_greylisting']) ? 1 : 0,
					isset($_REQUEST['spf_reject_mails']) ? 1 : 0,
					(int)$_REQUEST['smtp_hop_limit'],
					isset($_REQUEST['smtp_auth_no_received']) ? 1 : 0,
					isset($_REQUEST['smtp_reject_noreversedns']) ? 1 : 0,
					max((int)$_REQUEST['smtp_recipient_limit'], 1));
				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			// reset grey list?
			if(isset($_REQUEST['resetGreyList']))
				$db->Query('DELETE FROM {pre}bms_greylist');

			// get grey list entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_greylist');
			list($greyCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// get subnet entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_subnets');
			list($subnetCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// get dnsbl entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_dnsbl');
			list($dnsblCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// assign
			$tpl->assign('subnetCount',	$subnetCount);
			$tpl->assign('dnsblCount',	$dnsblCount);
			$tpl->assign('greyCount',	$greyCount);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.smtp.tpl'));
		}

		//
		// subnet rules
		//
		else if($_REQUEST['do'] == 'subnetRules')
		{
			// save?
			if(isset($_REQUEST['save'])
				&& isset($_REQUEST['subnets'])
				&& is_array($_REQUEST['subnets']))
			{
				foreach($_REQUEST['subnets'] as $key=>$val)
				{
					if($key == 0 && strlen(trim($val['ip'])) > 2
						&& strlen(trim($val['mask'])) > 0)
					{
						$db->Query('INSERT INTO {pre}bms_subnets(ip,mask,classification) VALUES(?,?,?)',
							$val['ip'],
							$val['mask'],
							$val['classification']);
					}
					else if(isset($val['delete']))
					{
						$db->Query('DELETE FROM {pre}bms_subnets WHERE id=?',
							$key);
					}
					else if($key > 0
						&& strlen(trim($val['ip'])) > 2
						&& strlen(trim($val['mask'])) > 0)
					{
						$db->Query('UPDATE {pre}bms_subnets SET ip=?,mask=?,classification=? WHERE id=?',
							$val['ip'],
							$val['mask'],
							$val['classification'],
							$key);
					}
				}
			}

			// get subnets
			$subnets  = array();
			$res = $db->Query('SELECT id,ip,mask,classification FROM {pre}bms_subnets ORDER BY ip,mask');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$subnets[$row['id']] = array(
					'id'				=> $row['id'],
					'ip'				=> $row['ip'],
					'mask'				=> $row['mask'],
					'classification'	=> $row['classification']
				);
			}
			$res->Free();

			// assign
			$tpl->assign('subnets',		$subnets);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.subnetrules.tpl'));
		}

		//
		// dnsbl rules
		//
		else if($_REQUEST['do'] == 'dnsblRules')
		{
			// save?
			if(isset($_REQUEST['save'])
				&& isset($_REQUEST['dnsbls'])
				&& is_array($_REQUEST['dnsbls']))
			{
				foreach($_REQUEST['dnsbls'] as $key=>$val)
				{
					if($key == 0 && strlen(trim($val['host'])) > 4)
					{
						$db->Query('INSERT INTO {pre}bms_dnsbl(host,classification,`type`) VALUES(?,?,?)',
							$val['host'],
							$val['classification'],
							$val['type']);
					}
					else if(isset($val['delete']))
					{
						$db->Query('DELETE FROM {pre}bms_dnsbl WHERE id=?',
							$key);
					}
					else if($key > 0
						&& strlen(trim($val['host'])) > 4)
					{
						$db->Query('UPDATE {pre}bms_dnsbl SET host=?,classification=?,`type`=? WHERE id=?',
							$val['host'],
							$val['classification'],
							$val['type'],
							$key);
					}
				}
			}

			// get dnsbls
			$dnsbls  = array();
			$res = $db->Query('SELECT id,host,classification,`type` FROM {pre}bms_dnsbl ORDER BY host');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$dnsbls[$row['id']] = array(
					'id'				=> $row['id'],
					'host'				=> $row['host'],
					'classification'	=> $row['classification'],
					'type'				=> $row['type']
				);
			}
			$res->Free();

			// assign
			$tpl->assign('dnsbls',		$dnsbls);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.dnsblrules.tpl'));
		}

		//
		// greylist
		//
		else if($_REQUEST['do'] == 'greylist')
		{
			// delete?
			if(isset($_REQUEST['delete']) && is_array($_REQUEST['delete']))
			{
				$db->Query('DELETE FROM {pre}bms_greylist WHERE `id` IN ?',
					$_REQUEST['delete']);
			}

			// get greylist entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_greylist');
			list($greyCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// page options
			$perPage 	= 50;
			$pageCount 	= ceil($greyCount / $perPage);
			$pageNo		= isset($_REQUEST['page'])
							? max(1, min($pageCount, (int)$_REQUEST['page']))
							: 1;
			$startPos	= ($pageNo-1) * $perPage;

			// get entries
			$greylist  = array();
			$res = $db->Query('SELECT `id`,`ip`,`ip6`,`time`,`confirmed` FROM {pre}bms_greylist ORDER BY `time` DESC LIMIT '
				. $startPos . ',' . $perPage);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$greylist[$row['id']] = array(
					'ip'				=> long2ip($this->_htons($row['ip'])),
					'ip6'				=> $this->_dbIP6($row['ip6']),
					'time'				=> $row['time'],
					'confirmed'			=> $row['confirmed']
				);
			}
			$res->Free();

			// assign
			$tpl->assign('pageNo',		$pageNo);
			$tpl->assign('pageCount',	$pageCount);
			$tpl->assign('greylist',	$greylist);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.greylist.tpl'));
		}

		//
		// milters
		//
		else if($_REQUEST['do'] == 'milters')
		{
			// save?
			if(isset($_REQUEST['save'])
				&& isset($_POST['milters'])
				&& is_array($_POST['milters']))
			{
				foreach($_POST['milters'] as $key=>$val)
				{
					$flags = 0;
					if(isset($val['flags']) && is_array($val['flags']))
					foreach($val['flags'] as $flag)
						$flags |= $flag;

					if($key == 0 && strlen(trim($val['hostname'])) > 1)
					{
						$db->Query('INSERT INTO {pre}bms_milters(`title`,`hostname`,`port`,`flags`,`default_action`,`pos`) VALUES(?,?,?,?,?,?)',
							$val['title'],
							$val['hostname'],
							(int)$val['port'],
							$flags,
							(int)$val['default_action'],
							(int)$val['pos']);
					}
					else if(isset($val['delete']))
					{
						$db->Query('DELETE FROM {pre}bms_milters WHERE `milterid`=?',
							$key);
					}
					else if($key > 0
						&& strlen(trim($val['hostname'])) > 1)
					{
						$db->Query('UPDATE {pre}bms_milters SET `title`=?,`hostname`=?,`port`=?,`flags`=?,`default_action`=?,`pos`=? WHERE `milterid`=?',
							$val['title'],
							$val['hostname'],
							(int)$val['port'],
							$flags,
							(int)$val['default_action'],
							(int)$val['pos'],
							$key);
					}
				}
			}

			// get milters
			$lastPos = 0;
			$milters  = array();
			$res = $db->Query('SELECT `milterid`,`title`,`hostname`,`port`,`flags`,`default_action`,`pos` FROM {pre}bms_milters ORDER BY `pos` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$lastPos = $row['pos'];
				$milters[$row['milterid']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('milters',		$milters);
			$tpl->assign('nextPos',		$lastPos+10);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.milters.tpl'));
		}
	}

	/**
	 * convert IPv6 from DB format to standard format
	 *
	 * @param $str DB-formatted IP
	 * @return string
	 */
	function _dbIP6($str)
	{
		$result = '';
		$emptyGroup = false;
		for($i=strlen($str)-4; $i>=0; $i-=4)
		{
			$part = ltrim(substr($str, $i+2, 2) . substr($str, $i, 2), '0');

			if($part == '')
			{
				if(!$emptyGroup)
				{
					$result .= ':';
					$emptyGroup = true;
				}
			}
			else
			{
				$result .= ':' . $part;
				$emptyGroup = false;
			}
		}
		return(substr($result, 1));
	}

	/**
	 * change byte order
	 *
	 * @param int $ip
	 * @return int
	 */
	function _htons($ip)
	{
		return(($ip << 24) | (($ip << 8) & 0xFF0000) | (($ip >> 8) & 0xFF00) | (($ip >> 24) & 0xFF));
	}

	/**
	 * msgqueue page
	 *
	 */
	function _msgqueuePage()
	{
		global $db, $tpl, $lang_admin;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'prefs';

		$haveSignatureSupport = ($this->prefs['core_features'] & BMS_CORE_FEATURE_ALTERMIME) != 0;
		$tpl->assign('haveSignatureSupport', $haveSignatureSupport);

		//
		// prefs
		//
		if($_REQUEST['do'] == 'prefs')
		{
			// flush queue
			if(isset($_REQUEST['flushQueue']))
			{
				if($fp = $this->_openControlChannel())
				{
					$this->_queueControlCommand($fp, 'FLUSH_MSGQUEUE');
					$this->_closeControlChannel($fp);
				}
				else
				{
					$db->Query('UPDATE {pre}bms_queue SET `last_attempt`=0 WHERE `deleted`=0');
					@touch('/opt/b1gmailserver/queue/state');
				}
			}

			// clear queue
			if(isset($_REQUEST['clearQueue']))
			{
				if($fp = $this->_openControlChannel())
				{
					$this->_queueControlCommand($fp, 'CLEAR_MSGQUEUE');
					$this->_closeControlChannel($fp);
				}
				else
				{
					$db->Query('UPDATE {pre}bms_queue SET `deleted`=1 WHERE `active`=0');
					@touch('/opt/b1gmailserver/queue/state');
				}
			}

			// flush queue
			if(isset($_REQUEST['restartQueue']))
			{
				if($fp = $this->_openControlChannel())
				{
					$this->_queueControlCommand($fp, 'RESTART_MSGQUEUE');
					$this->_closeControlChannel($fp);
				}
				else
				{
					PutLog('Failed to restart b1gMailServer queue service (cannot access control channel)',
						PRIO_PLUGIN,
						__FILE__,
						__LINE__);
				}
			}

			// save?
			if(isset($_REQUEST['save']) && IsPOSTRequest())
			{
				$db->Query('UPDATE {pre}bms_prefs SET queue_mysqlclose=?,queue_interval=?,queue_retry=?,queue_lifetime=?,queue_timeout=?,queue_threads=?,queue_maxthreads=?,php_path=?,outbound_target=?,outbound_sendmail_path=?,outbound_smtp_relay_host=?,outbound_smtp_relay_port=?,outbound_smtp_relay_auth=?,outbound_smtp_relay_user=?,outbound_smtp_relay_pass=?,inbound_reuse_process=?,random_queue_id=?,received_header_no_expose=?,outbound_smtp_usetls=?,control_addr=?,outbound_smtp_usednssec=?,outbound_smtp_usedane=?',
					isset($_REQUEST['queue_mysqlclose']) ? 1 : 0,
					(int)$_REQUEST['queue_interval'],
					(int)$_REQUEST['queue_retry'] * 60,
					(int)$_REQUEST['queue_lifetime'] * 3600,
					(int)$_REQUEST['queue_timeout'],
					max(1, (int)$_REQUEST['queue_threads']),
					max(1, (int)$_REQUEST['queue_maxthreads']),
					$_REQUEST['php_path'],
					(int)$_REQUEST['outbound_target'],
					$_REQUEST['outbound_sendmail_path'],
					$_REQUEST['outbound_smtp_relay_host'],
					(int)$_REQUEST['outbound_smtp_relay_port'],
					isset($_REQUEST['outbound_smtp_relay_auth']) ? 1 : 0,
					$_REQUEST['outbound_smtp_relay_user'],
					$_REQUEST['outbound_smtp_relay_pass'],
					isset($_REQUEST['inbound_reuse_process']) ? 1 : 0,
					isset($_REQUEST['random_queue_id']) ? 1 : 0,
					isset($_REQUEST['received_header_no_expose']) ? 1 : 0,
					isset($_REQUEST['outbound_smtp_usetls']) ? 1 : 0,
					ip2long($_REQUEST['control_addr']) !== false ? $_REQUEST['control_addr'] : '127.0.0.1',
					isset($_REQUEST['outbound_smtp_usedane']) ? 1 : 0,
					isset($_REQUEST['outbound_smtp_usedane']) ? 1 : 0);
				if($haveSignatureSupport)
				{
					$db->Query('UPDATE {pre}bms_prefs SET outbound_add_signature=?,outbound_signature_sep=?',
						isset($_REQUEST['outbound_add_signature']) ? 1 : 0,
						$_REQUEST['outbound_signature_sep']);
				}
				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			// get queue entry count
			$res = $db->Query('SELECT COUNT(*),SUM(size) FROM {pre}bms_queue WHERE `deleted`=0');
			list($queueCount, $queueSize) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// get flushable entry count
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_queue WHERE `last_attempt`>=('.time().'-`attempts`*'.$this->prefs['queue_retry'].') AND `deleted`=0 AND `active`=0');
			list($flushableCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// check b1gMail version
			list($vMajor, $vMinor) = explode('.', B1GMAIL_VERSION);

			// running?
			$queueRunning = false;
			$threadCount = 0;
			if($fp = $this->_openControlChannel())
			{
				$queueRunning = true;
				$threadResponse = $this->_queueControlCommand($fp, 'GET_THREADCOUNT');
				$this->_closeControlChannel($fp);

				if(strlen($threadResponse) > 4 && substr($threadResponse, 0, 3) == '+OK')
					$threadCount = (int)trim(substr($threadResponse, 4));
			}

			// assign
			$tpl->assign('enableRestart',	$queueRunning);
			$tpl->assign('threadCount',		$threadCount);
			$tpl->assign('minV72',			$vMajor >= 7 && $vMinor >= 2);
			$tpl->assign('allowFlush',		$flushableCount > 0);
			$tpl->assign('queueCount',		$queueCount);
			$tpl->assign('queueSize',		$queueSize);
			$tpl->assign('pageURL',			$this->_adminLink());
			$tpl->assign('page',			$this->_templatePath('bms.admin.msgqueue.tpl'));
		}

		//
		// headers
		//
		else if($_REQUEST['do'] == 'headers')
		{
			if(isset($_REQUEST['save']) && isset($_POST['inbound_headers']))
			{
				$inboundHeaders = explode("\n", $_POST['inbound_headers']);
				$outboundHeaders = explode("\n", $_POST['outbound_headers']);
				$inbound_headers = $outbound_headers = '';

				foreach($inboundHeaders as $line)
				{
					if(trim($line) == '') continue;

					$line = rtrim($line);
					$inbound_headers .= $line . "\r\n";
				}

				foreach($outboundHeaders as $line)
				{
					if(trim($line) == '') continue;

					$line = rtrim($line);
					$outbound_headers .= $line . "\r\n";
				}

				$db->Query('UPDATE {pre}bms_prefs SET `inbound_headers`=?,`outbound_headers`=?',
					$inbound_headers,
					$outbound_headers);

				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			$tpl->assign('pageURL',			$this->_adminLink());
			$tpl->assign('page',			$this->_templatePath('bms.admin.msgqueue.headers.tpl'));
		}

		//
		// queue
		//
		else if($_REQUEST['do'] == 'deliveryRules')
		{
			// save?
			if(isset($_REQUEST['save'])
				&& isset($_POST['rules'])
				&& is_array($_POST['rules']))
			{
				foreach($_POST['rules'] as $key=>$val)
				{
					$flags = 0;
					if(isset($val['flags']) && is_array($val['flags']))
					foreach($val['flags'] as $flag)
						$flags |= $flag;

					if($key == 0 && strlen(trim($val['rule'])) > 1)
					{
						$db->Query('INSERT INTO {pre}bms_deliveryrules(`mail_type`,`rule_subject`,`rule`,`target`,`target_param`,`flags`,`pos`) VALUES(?,?,?,?,?,?,?)',
							$val['mail_type'],
							$val['rule_subject'],
							trim($val['rule']),
							$val['target'],
							trim($val['target_param']),
							$flags,
							$val['pos']);
					}
					else if(isset($val['delete']))
					{
						$db->Query('DELETE FROM {pre}bms_deliveryrules WHERE `deliveryruleid`=?',
							$key);
					}
					else if($key > 0
						&& strlen(trim($val['rule'])) > 1)
					{
						$db->Query('UPDATE {pre}bms_deliveryrules SET `mail_type`=?,`rule_subject`=?,`rule`=?,`target`=?,`target_param`=?,`flags`=?,`pos`=? WHERE `deliveryruleid`=?',
							$val['mail_type'],
							$val['rule_subject'],
							trim($val['rule']),
							$val['target'],
							trim($val['target_param']),
							$flags,
							$val['pos'],
							$key);
					}
				}
			}

			// get delivery rules
			$lastPos = 0;
			$rules  = array();
			$res = $db->Query('SELECT `deliveryruleid`,`mail_type`,`rule_subject`,`rule`,`target`,`target_param`,`flags`,`pos` FROM {pre}bms_deliveryrules ORDER BY `pos` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$lastPos = $row['pos'];
				$rules[$row['deliveryruleid']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('rules',		$rules);
			$tpl->assign('nextPos',		$lastPos+10);
			$tpl->assign('pageURL', 	$this->_adminLink());
			$tpl->assign('page', 		$this->_templatePath('bms.admin.deliveryrules.tpl'));
		}

		//
		// queue
		//
		else if($_REQUEST['do'] == 'queue')
		{
			// single action?
			if(isset($_REQUEST['singleAction']))
			{
				if($_REQUEST['singleAction'] == 'delete')
				{
					if($fp = $this->_openControlChannel())
					{
						$this->_queueControlCommand($fp, sprintf('DELETE_QUEUEITEM %d', $_REQUEST['singleID']));
						$this->_closeControlChannel($fp);
					}
					else
					{
						$db->Query('UPDATE {pre}bms_queue SET `deleted`=1 WHERE `id`=?',
							$_REQUEST['singleID']);
					}
				}
			}

			// mass action?
			if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] != '-'
				&& isset($_REQUEST['items']) && is_array($_REQUEST['items']))
			{
				$items = $_REQUEST['items'];

				if($_REQUEST['massAction'] == 'delete')
				{
					if($fp = $this->_openControlChannel())
					{
						foreach($items as $itemID)
						{
							$this->_queueControlCommand($fp, sprintf('DELETE_QUEUEITEM %d', $itemID));
						}
						$this->_closeControlChannel($fp);
					}
					else
					{
						$db->Query('UPDATE {pre}bms_queue SET `deleted`=1 WHERE `id` IN ?',
							$items);
					}
				}
			}

			// delete by attribute?
			if(isset($_REQUEST['delByAttr']) && isset($_POST['item_attribute']))
			{
				$deletedCount = 0;

				$res = $db->Query('SELECT * FROM {pre}bms_queue WHERE `id`=?',
					$_REQUEST['delByAttr']);
				if($res->RowCount() == 1)
				{
					$itemRow = $res->FetchArray(MYSQLI_ASSOC);
					$res->Free();

					if(isset($itemRow[$_POST['item_attribute']]))
					{
						if($fp = $this->_openControlChannel())
						{
							$res = $db->Query('SELECT `id` FROM {pre}bms_queue WHERE `'.$_POST['item_attribute'].'`=?',
								$itemRow[$_POST['item_attribute']]);
							while($row = $res->FetchArray(MYSQLI_ASSOC))
							{
								$this->_queueControlCommand($fp, sprintf('DELETE_QUEUEITEM %d', $row['id']));
								$deletedCount++;
							}
							$res->Free();

							$this->_closeControlChannel($fp);
						}
						else
						{
							$db->Query('UPDATE {pre}bms_queue SET `deleted`=1 WHERE `'.$_POST['item_attribute'].'`=?',
								$itemRow[$_POST['item_attribute']]);
							$deletedCount = $db->AffectedRows();
						}
					}
				}

				$tpl->assign('msg', sprintf($lang_admin['bms_deletedmsg'], $deletedCount));
			}

			// sort options
			$sortBy = isset($_REQUEST['sortBy'])
						? $_REQUEST['sortBy']
						: 'last_attempt';
			$sortOrder = isset($_REQUEST['sortOrder'])
							? strtolower($_REQUEST['sortOrder'])
							: 'desc';
			$perPage = max(1, isset($_REQUEST['perPage'])
							? (int)$_REQUEST['perPage']
							: 50);

			// filter stuff
			$queryAdd = '';
			$query = '';
			$start = $end = 0;
			if(isset($_REQUEST['filter']))
			{
				$typeIDs = array_keys($_REQUEST['types']);
				$queryTypes = count($typeIDs) > 0 ? implode(',', $typeIDs) : '0';
				$queryAdd = ' AND type IN(' . $queryTypes . ') ';
				$types = is_array($_REQUEST['types']) ? $_REQUEST['types'] : array();

				if(isset($_REQUEST['query']) && trim($_REQUEST['query']) != '')
				{
					$query = trim($_REQUEST['query']);
					$escQuery = $db->Escape($query);

					$queryAdd .= ' AND (`from` LIKE \'%'.$escQuery.'%\' OR `to` LIKE \'%'.$escQuery.'%\'';

					if(is_numeric($query))
						$queryAdd .= ' OR `id`=\'' . $escQuery . '\'';

					if(strlen($query) == 8 && preg_match('/^[abcdefABCDEF0-9]+$/', $query))
						$queryAdd .= ' OR `id`=\'' . hexdec($query) . '\'';

					$queryAdd .= ') ';
				}

				if(isset($_REQUEST['use_start']) && $_REQUEST['use_start'] == 'yes'
					&& isset($_REQUEST['startDay']))
				{
					$start = SmartyDateTime('start');
					$queryAdd .= ' AND `date`>=' . (int)$start . ' ';
				}

				if(isset($_REQUEST['use_end']) && $_REQUEST['use_end'] == 'yes'
					&& isset($_REQUEST['endDay']))
				{
					$end = SmartyDateTime('end');
					$queryAdd .= ' AND `date`<=' . (int)$end . ' ';
				}
			}
			else
				$types = array(0 => true, 1 => true);

			// page calculation
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_queue WHERE `deleted`=0' . $queryAdd);
			list($queueCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
			$pageCount = ceil($queueCount / $perPage);
			$pageNo = isset($_REQUEST['page'])
						? max(1, min($pageCount, (int)$_REQUEST['page']))
						: 1;
			$startPos = max(0, min($perPage*($pageNo-1), $queueCount));

			// do the query!
			$queue = array();
			$res = $db->Query('SELECT id,`date`,`type`,`from`,`to`,`size`,last_attempt,attempts,`active` FROM {pre}bms_queue WHERE `deleted`=0 ' . $queryAdd
						. 'ORDER BY `' . $sortBy . '` '
						. $sortOrder . ' '
						. 'LIMIT ' . $startPos . ',' . $perPage);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$queue[$row['id']] = array(
					'id'			=> $row['id'],
					'hexID'			=> sprintf('%08X', $row['id']),
					'typeIcon'		=> $row['type'] == 0 ? 'inbound' : 'outbound',
					'date'			=> $row['date'],
					'from'			=> $row['from'],
					'to'			=> $row['to'],
					'size'			=> $row['size'],
					'last_attempt'	=> $row['last_attempt'],
					'attempts'		=> $row['attempts'],
					'active'		=> $row['active']
				);
			}
			$res->Free();

			// assign
			$tpl->assign('queueRunning',	$this->_isQueueRunning());
			$tpl->assign('pageNo', 			$pageNo);
			$tpl->assign('pageCount', 		$pageCount);
			$tpl->assign('sortBy', 			$sortBy);
			$tpl->assign('sortOrder', 		$sortOrder);
			$tpl->assign('sortOrderInv', 	$sortOrder == 'asc' ? 'desc' : 'asc');
			$tpl->assign('perPage', 		$perPage);
			$tpl->assign('types',			$types);
			$tpl->assign('query',			$query);
			$tpl->assign('start',			$start);
			$tpl->assign('end',				$end);
			$tpl->assign('queue',			$queue);
			$tpl->assign('pageURL', 		$this->_adminLink());
			$tpl->assign('page', 			$this->_templatePath('bms.admin.queue.tpl'));
		}

		//
		// queue item details
		//
		else if($_REQUEST['do'] == 'showQueueItem' && isset($_REQUEST['id']))
		{
			// fetch
			$res = $db->Query('SELECT id,`type`,`date`,`size`,`from`,`to`,`attempts`,`last_attempt`,`last_status`,`smtp_user`,`b1gmail_user`,`last_status_info` FROM {pre}bms_queue WHERE id=?',
				(int)$_REQUEST['id']);
			if($res->RowCount() == 0)
			{
				$tpl->assign('msgIcon',		'error32');
				$tpl->assign('msgTitle',	$lang_admin['error']);
				$tpl->assign('msgText',		$lang_admin['bms_queueitemgone']);
				$tpl->assign('page',		'msg.tpl');
				return;
			}
			$item = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			// prepare
			$item['typeIcon']	= $item['type'] == 0 ? 'inbound' : 'outbound';
			$item['hexID']		= sprintf('%08X', $item['id']);
			if($item['smtp_user'] > 0)
			{
				$user = _new('BMUser', array($item['smtp_user']));
				$userRow = $user->Fetch();
				$item['smtp_user_mail'] = $userRow['email'];
			}
			if($item['b1gmail_user'] > 0)
			{
				$user = _new('BMUser', array($item['b1gmail_user']));
				$userRow = $user->Fetch();
				$item['b1gmail_user_mail'] = $userRow['email'];
			}

			// assign
			$tpl->assign('queueRunning',	$this->_isQueueRunning());
			$tpl->assign('item',			$item);
			$tpl->assign('pageURL', 		$this->_adminLink());
			$tpl->assign('page', 			$this->_templatePath('bms.admin.queue.item.tpl'));
		}

		//
		// download queue item
		//
		else if($_REQUEST['do'] == 'downloadQueueItem' && isset($_REQUEST['id']))
		{
			if($fp = $this->_openControlChannel())
			{
				$response = $this->_queueControlCommand($fp, sprintf('GET_QUEUEITEM %d', $_REQUEST['id']));
				if(trim($response) == '+OK')
				{
					header('Pragma: public');
					header('Content-Type: message/rfc822');
					header(sprintf('Content-Disposition: attachment; filename=queue-%08x.eml',
						$_REQUEST['id']));

					while(!feof($fp))
					{
						$line = rtrim(fgets2($fp), "\r\n");
						if($line == '.')
							break;
						if(strlen($line) > 1 && $line[0] == '.')
							$line = substr($line, 1);
						echo $line . "\r\n";
					}
				}
				else
					die('Failed to retrieve MSGQueue item');
				$this->_closeControlChannel($fp);
			}
			else
				die('Connection to MSGQueue failed');
			exit();
		}

		//
		// get queue item headers
		//
		else if($_REQUEST['do'] == 'getQueueItemHeaders' && isset($_REQUEST['id']))
		{
			if($fp = $this->_openControlChannel())
			{
				$response = $this->_queueControlCommand($fp, sprintf('GET_QUEUEITEM %d', $_REQUEST['id']));
				if(trim($response) == '+OK')
				{
					$passedHeaders = false;

					while(!feof($fp))
					{
						$line = rtrim(fgets2($fp), "\r\n");
						if($line == '.')
							break;
						if(strlen($line) > 1 && $line[0] == '.')
							$line = substr($line, 1);
						if(!$passedHeaders)
						{
							if($line == '')
								$passedHeaders = true;
							else
								echo $line . "\r\n";
						}
					}
				}
				else
					die('Failed to retrieve MSGQueue item');
				$this->_closeControlChannel($fp);
			}
			else
				die('Connection to MSGQueue failed');
			exit();
		}
	}

	/**
	 * Get array of supported protocols for group
	 *
	 * @param BMGroup $thisGroup
	 * @return array
	 */
	function _getProtocols($thisGroup)
	{
		$protocols = array();

		if($thisGroup->_row['pop3'] == 'yes')
			$protocols[] = 'POP3';
		if($thisGroup->_row['imap'] == 'yes')
			$protocols[] = 'IMAP';
		if($thisGroup->_row['smtp'] == 'yes')
			$protocols[] = 'SMTP';

		return($protocols);
	}

	/**
	 * User area setup
	 *
	 * @param string $file
	 * @param string $action
	 */
	function FileHandler($file, $action)
	{
		global $lang_user, $thisGroup;

		if($file == 'index.php' && stripos($_SERVER['REQUEST_URI'], '/autodiscover/autodiscover.xml') !== false)
		{
			if(!isset($this->prefs) || !is_array($this->prefs))
				$this->prefs = $this->_getPrefs();
			$this->_autoDiscover();
			exit();
		}
		else if($file == 'index.php' && stripos($_SERVER['REQUEST_URI'], '/mail/config-v1.1.xml') !== false)
		{
			if(!isset($this->prefs) || !is_array($this->prefs))
				$this->prefs = $this->_getPrefs();
			$this->_autoConfig();
			exit();
		}
		else if($file == 'prefs.php')
		{
			if(!isset($this->prefs) || !is_array($this->prefs))
				$this->prefs = $this->_getPrefs();

			$protocols = $this->_getProtocols($thisGroup);

			if(count($protocols) == 0)
				return;

			if($this->prefs['user_showlogin'] != 1
				&& ($this->prefs['user_chosepop3folders'] != 1 || $thisGroup->_row['pop3'] != 'yes')
				&& ($this->prefs['user_choseimaplimit'] != 1 || $thisGroup->_row['imap'] != 'yes'))
				return;

			$lang_user['bms_userarea'] = implode('/', $protocols);
			$GLOBALS['prefsItems']['bms_userarea'] = true;
			$GLOBALS['prefsImages']['bms_userarea'] = 'plugins/templates/images/bms_userarea48.png';
			$GLOBALS['prefsIcons']['bms_userarea'] = 'plugins/templates/images/bms_userarea16.png';
		}
	}

	/**
	 * Autodiscover/Autoconfig output generator
	 *
	 * @param string $tplFile Template file name
	 * @param string $userName User name / email address
	 */
	function _adOutput($tplFile, $userName)
	{
		global $tpl, $bm_prefs;

		$haveTLS = ($this->prefs['core_features'] & BMS_CORE_FEATURE_TLS) != 0;

		// build protocols array
		$protocols = array();
		if(!empty($userName) && ($userID = BMUser::GetID($userName, true)) > 0)
		{
			$user = _new('BMUser', array($userID));
			$group = $user->GetGroup();
			$allowedProtos = $this->_getProtocols($group);

			if(in_array('IMAP', $allowedProtos))
			{
				$protocols[] = array(
					'type' 		=> 'IMAP',
					'server'	=> $this->prefs['user_imapserver'],
					'port'		=> $this->prefs['user_imapport'],
					'ssl'		=> $this->prefs['user_imapssl'] == 1,
					'tls'		=> $haveTLS
				);
			}
			if(in_array('POP3', $allowedProtos))
			{
				$protocols[] = array(
					'type' 		=> 'POP3',
					'server'	=> $this->prefs['user_pop3server'],
					'port'		=> $this->prefs['user_pop3port'],
					'ssl'		=> $this->prefs['user_pop3ssl'] == 1,
					'tls'		=> $haveTLS
				);
			}
			if(in_array('SMTP', $allowedProtos))
			{
				$protocols[] = array(
					'type' 		=> 'SMTP',
					'server'	=> $this->prefs['user_smtpserver'],
					'port'		=> $this->prefs['user_smtpport'],
					'ssl'		=> $this->prefs['user_smtpssl'] == 1,
					'tls'		=> $haveTLS
				);
			}
		}

		// domain
		$domain = $_SERVER['HTTP_HOST'];
		if(isset($_SERVER['HTTP_X_FORWARDED_HOST']))
			$domain = $_SERVER['HTTP_X_FORWARDED_HOST'];
		$domain = preg_replace('/^(autoconfig|autodiscover)\./i', '', $domain);

		// output
		header('Content-Type: application/xml; charset=utf-8');
		$tpl->assign('serviceURL', 	$bm_prefs['selfurl']);
		$tpl->assign('serviceTitle',$bm_prefs['titel']);
		$tpl->assign('userName', 	$userName);
		$tpl->assign('protocols', 	$protocols);
		$tpl->assign('domain', 		$domain);
		$tpl->display($this->_templatePath($tplFile));
	}

	/**
	 * Autodiscover handler
	 *
	 */
	function _autoDiscover()
	{
		global $tpl, $bm_prefs;

		// get input data
		$inputData = '';
		$fp = fopen('php://input', 'r');
		while(!feof($fp))
		{
			$inputData .= fread($fp, 4096);
		}
		fclose($fp);

		// extract mailaddress
		$userName = '';
		if(preg_match('/\<EMailAddress\>(.*?)\<\/EMailAddress\>/', $inputData, $emailAddress))
			$userName = ExtractMailAddress($emailAddress[1]);

		// output
		$this->_adOutput('bms.autodiscover.tpl', $userName);
	}

	/**
	 * Autoconfig handler
	 *
	 */
	function _autoConfig()
	{
		// extract mailaddress
		$userName = '';
		if(preg_match('/emailaddress=([^&]*)/', $_SERVER['REQUEST_URI'], $emailAddress))
			$userName = ExtractMailAddress(urldecode($emailAddress[1]));

		// output
		$this->_adOutput('bms.autoconfig.tpl', $userName);
	}

	/**
	 * User area
	 *
	 * @param string $action
	 * @return bool
	 */
	function UserPrefsPageHandler($action)
	{
		global $lang_user, $tpl, $thisUser, $thisGroup, $mailbox;

		if($action != 'bms_userarea')
			return(false);

		if(!isset($this->prefs) || !is_array($this->prefs))
			$this->prefs = $this->_getPrefs();

		$protocols = $this->_getProtocols($thisGroup);

		if(count($protocols) == 0)
			return;

		if($this->prefs['user_showlogin'] != 1
			&& ($this->prefs['user_chosepop3folders'] != 1 || $thisGroup->_row['pop3'] != 'yes')
			&& ($this->prefs['user_choseimaplimit'] != 1 || $thisGroup->_row['imap'] != 'yes'))
			return;

		$lang_user['bms_userarea'] = implode('/', $protocols);

		if($thisGroup->_row['pop3'] == 'yes' && $this->prefs['user_chosepop3folders'])
		{
			if(isset($_POST['do']) && $_POST['do'] == 'savePOP3Folders')
			{
				$folderIDs = $_POST['pop3_folders'];
				if(!is_array($folderIDs))
					$folderIDs = array();
				$thisUser->SetPref('pop3Folders', implode(',', $folderIDs));

				$tpl->assign('title', $lang_user['bms_folderstofetch']);
				$tpl->assign('msg', $lang_user['bms_folderssaved']);
				$tpl->assign('backLink', 'prefs.php?action=bms_userarea&sid=' . session_id());
				$tpl->assign('pageContent', 'li/msg.tpl');
				$tpl->display('li/index.tpl');

				return(true);
			}

			$pop3FolderIDs = $thisUser->GetPref('pop3Folders');
			if($pop3FolderIDs === false)
				$pop3FolderIDs = $this->prefs['pop3_folders'];
			$pop3FolderIDs = explode(',', $pop3FolderIDs);
			$pop3Folders = array();
			foreach($pop3FolderIDs as $folderID)
				$pop3Folders[ str_replace('-', 'm', $folderID) ] = true;

			$tpl->assign('pop3Folders', $pop3Folders);
			$tpl->assign('folderList',	$mailbox->GetDropdownFolderList(-1, $null));
		}

		if($thisGroup->_row['imap'] == 'yes' && $this->prefs['user_choseimaplimit'])
		{
			if(isset($_POST['do']) && $_POST['do'] == 'saveIMAPLimit')
			{
				$thisUser->SetPref('imapLimit', (int)$_POST['imapLimit']);

				$tpl->assign('title', $lang_user['bms_imaplimit']);
				$tpl->assign('msg', $lang_user['bms_imaplimitsaved']);
				$tpl->assign('backLink', 'prefs.php?action=bms_userarea&sid=' . session_id());
				$tpl->assign('pageContent', 'li/msg.tpl');
				$tpl->display('li/index.tpl');

				return(true);
			}

			$imapLimit = $thisUser->GetPref('imapLimit');
			if($imapLimit === false)
				$imapLimit = $this->prefs['imap_limit'];

			$tpl->assign('imapLimit', $imapLimit);
		}

		$tpl->assign('bms_prefs',	$this->prefs);
		$tpl->assign('username', 	$thisUser->_row['email']);
		$tpl->assign('havePOP3', 	$thisGroup->_row['pop3'] == 'yes');
		$tpl->assign('haveIMAP',	$thisGroup->_row['imap'] == 'yes');
		$tpl->assign('haveSMTP', 	$thisGroup->_row['smtp'] == 'yes');
		$tpl->assign('pageContent', $this->_templatePath('bms.user.tpl'));
		$tpl->display('li/index.tpl');

		return(true);
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('B1GMailServerAdmin');
