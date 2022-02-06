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
define('BMS_LIC_INVALID', 				0);		// license status contants
define('BMS_LIC_VALID', 				1);
define('BMS_LIC_EXPIRED', 				2);
define('BMS_LIC_CHECKING',				3);
define('BMS_LIC_FEATURE_POP3', 			1);		// feature constants
define('BMS_LIC_FEATURE_IMAP', 			2);
define('BMS_LIC_FEATURE_STATS',			4);
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
		$this->version				= '1.150';
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
		$databaseStructure =                      // checksum: 16f0f236904779f7a54e4291197d7fc6
				'YToxNjp7czoyNjoiYm02MF9ibXNfYXBuc19zdWJzY3JpcHRpb24iO2E6Mjp7czo2OiJmaWVsZHM'
			. 'iO2E6NTp7aTowO2E6Njp7aTowO3M6MTQ6InN1YnNjcmlwdGlvbmlkIjtpOjE7czo3OiJpbnQoMT'
			. 'EpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZ'
			. 'W1lbnQiO31pOjE7YTo2OntpOjA7czo2OiJ1c2VyaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6Mjtz'
			. 'OjI6Ik5PIjtpOjM7czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k'
			. '6MDtzOjEwOiJhY2NvdW50X2lkIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTy'
			. 'I7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czoxMjoiZGV2aWNlX'
			. '3Rva2VuIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtp'
			. 'OjQ7TjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo4OiJzdWJ0b3BpYyI7aToxO3M6MTI6InZ'
			. 'hcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9fX'
			. 'M6NzoiaW5kZXhlcyI7YToyOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MTQ6InN1YnNjcmlwd'
			. 'GlvbmlkIjt9czo2OiJ1c2VyaWQiO2E6MTp7aTowO3M6NjoidXNlcmlkIjt9fX1zOjMzOiJibTYw'
			. 'X2Jtc19hcG5zX3N1YnNjcmlwdGlvbl9mb2xkZXIiO2E6Mjp7czo2OiJmaWVsZHMiO2E6Mjp7aTo'
			. 'wO2E6Njp7aTowO3M6ODoiZm9sZGVyaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIj'
			. 'tpOjM7czozOiJQUkkiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTthOjY6e2k6MDtzOjE0O'
			. 'iJzdWJzY3JpcHRpb25pZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6'
			. 'IlBSSSI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToxOntzOjc6IlB'
			. 'SSU1BUlkiO2E6Mjp7aTowO3M6ODoiZm9sZGVyaWQiO2k6MTtzOjE0OiJzdWJzY3JpcHRpb25pZC'
			. 'I7fX19czoyMjoiYm02MF9ibXNfZGVsaXZlcnlydWxlcyI7YToyOntzOjY6ImZpZWxkcyI7YTo4O'
			. 'ntpOjA7YTo2OntpOjA7czoxNDoiZGVsaXZlcnlydWxlaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6'
			. 'MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI'
			. '7fWk6MTthOjY6e2k6MDtzOjk6Im1haWxfdHlwZSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6Mj'
			. 'tzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6M'
			. 'DtzOjEyOiJydWxlX3N1YmplY3QiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7'
			. 'aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo0OiJydWx'
			. 'lIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7Tj'
			. 'tpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo2OiJ0YXJnZXQiO2k6MTtzOjEwOiJ0aW55aW50K'
			. 'DQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjU7'
			. 'YTo2OntpOjA7czoxMjoidGFyZ2V0X3BhcmFtIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI'
			. '7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjY7YTo2OntpOjA7czo1Oi'
			. 'JmbGFncyI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6N'
			. 'DtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzOjM6InBvcyI7aToxO3M6NzoiaW50'
			. 'KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM'
			. '6NzoiaW5kZXhlcyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MTQ6ImRlbGl2ZXJ5cn'
			. 'VsZWlkIjt9fX1zOjE0OiJibTYwX2Jtc19kbnNibCI7YToyOntzOjY6ImZpZWxkcyI7YTo1OntpO'
			. 'jA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6Mztz'
			. 'OjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M'
			. '6NDoiaG9zdCI7aToxO3M6MTE6InZhcmNoYXIoNjQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIj'
			. 'tpOjQ7TjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxNDoiY2xhc3NpZmljYXRpb24iO2k6M'
			. 'TtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIzIjtp'
			. 'OjU7czowOiIiO31pOjM7YTo2OntpOjA7czozOiJwb3MiO2k6MTtzOjc6ImludCgxMSkiO2k6Mjt'
			. 'zOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MD'
			. 'tzOjQ6InR5cGUiO2k6MTtzOjI2OiJlbnVtKCdpcHY0JywnaXB2NicsJ2JvdGgnKSI7aToyO3M6M'
			. 'joiTk8iO2k6MztzOjA6IiI7aTo0O3M6NDoiaXB2NCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhl'
			. 'cyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MjoiaWQiO319fXM6MTk6ImJtNjBfYm1'
			. 'zX2V2ZW50cXVldWUiO2E6Mjp7czo2OiJmaWVsZHMiO2E6NTp7aTowO2E6Njp7aTowO3M6NzoiZX'
			. 'ZlbnRpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O'
			. '047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6NjoidXNlcmlkIjtp'
			. 'OjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU'
			. '7czowOiIiO31pOjI7YTo2OntpOjA7czo0OiJ0eXBlIjtpOjE7czoxMDoidGlueWludCg0KSI7aT'
			. 'oyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozO2E6Njp7a'
			. 'TowO3M6NjoicGFyYW0xIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoi'
			. 'IjtpOjQ7TjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo2OiJwYXJhbTIiO2k6MTtzOjc6Iml'
			. 'udCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fX1zOjc6Im'
			. 'luZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjc6ImV2ZW50aWQiO319fXM6M'
			. 'TY6ImJtNjBfYm1zX2ZhaWxiYW4iO2E6Mjp7czo2OiJmaWVsZHMiO2E6ODp7aTowO2E6Njp7aTow'
			. 'O3M6MjoiaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k'
			. '6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthOjY6e2k6MDtzOjI6ImlwIjtpOj'
			. 'E7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiTVVMIjtpOjQ7czoxOiIwIjtpO'
			. 'jU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMDoiZW50cnlfZGF0ZSI7aToxO3M6NzoiaW50KDEx'
			. 'KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozO2E'
			. '6Njp7aTowO3M6MTI6ImJhbm5lZF91bnRpbCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6MTE6I'
			. 'mxhc3RfdXBkYXRlIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtp'
			. 'OjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjU7YTo2OntpOjA7czo4OiJhdHRlbXB0cyI7aToxO3M'
			. '6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NT'
			. 'tzOjA6IiI7fWk6NjthOjY6e2k6MDtzOjQ6InR5cGUiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpO'
			. 'jI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjc7YTo2Ontp'
			. 'OjA7czozOiJpcDYiO2k6MTtzOjg6ImNoYXIoMzIpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiTVV'
			. 'MIjtpOjQ7TjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjM6e3M6NzoiUFJJTUFSWSI7YT'
			. 'oxOntpOjA7czoyOiJpZCI7fXM6MjoiaXAiO2E6MTp7aTowO3M6MjoiaXAiO31zOjM6ImlwNiI7Y'
			. 'ToxOntpOjA7czozOiJpcDYiO319fXM6MTc6ImJtNjBfYm1zX2dyZXlsaXN0IjthOjI6e3M6Njoi'
			. 'ZmllbGRzIjthOjU6e2k6MDthOjY6e2k6MDtzOjI6ImlkIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI'
			. '7czoyOiJOTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZW1lbnQiO3'
			. '1pOjE7YTo2OntpOjA7czoyOiJpcCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6M'
			. 'ztzOjM6Ik1VTCI7aTo0O047aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6NDoidGltZSI7aTox'
			. 'O3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt'
			. '9aTozO2E6Njp7aTowO3M6OToiY29uZmlybWVkIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO'
			. '3M6MzoiaXA2IjtpOjE7czo4OiJjaGFyKDMyKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6Ik1VTCI7'
			. 'aTo0O047aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YTozOntzOjc6IlBSSU1BUlkiO2E6MTp'
			. '7aTowO3M6MjoiaWQiO31zOjM6ImlwNiI7YToxOntpOjA7czozOiJpcDYiO31zOjI6ImlwIjthOj'
			. 'E6e2k6MDtzOjI6ImlwIjt9fX1zOjE2OiJibTYwX2Jtc19pbWFwdWlkIjthOjI6e3M6NjoiZmllb'
			. 'GRzIjthOjI6e2k6MDthOjY6e2k6MDtzOjc6ImltYXB1aWQiO2k6MTtzOjc6ImludCgxMSkiO2k6'
			. 'MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI'
			. '7fWk6MTthOjY6e2k6MDtzOjY6Im1haWxpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjM6Ik1VTCI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7Y'
			. 'ToyOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6NzoiaW1hcHVpZCI7fXM6NjoibWFpbGlkIjth'
			. 'OjE6e2k6MDtzOjY6Im1haWxpZCI7fX19czoxMzoiYm02MF9ibXNfbG9ncyI7YToyOntzOjY6ImZ'
			. 'pZWxkcyI7YTo1OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9a'
			. 'ToxO2E6Njp7aTowO3M6MTA6ImlDb21wb25lbnQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7'
			. 'czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA'
			. '7czo5OiJpU2V2ZXJpdHkiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3'
			. 'M6MzoiTVVMIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czo1OiJpRGF0Z'
			. 'SI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6Ik1VTCI7aTo0O3M6MToi'
			. 'MCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6Nzoic3pFbnRyeSI7aToxO3M6MTI6InZhcmN'
			. 'oYXIoMjU1KSI7aToyO3M6MzoiWUVTIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fX1zOj'
			. 'c6ImluZGV4ZXMiO2E6Mzp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjI6ImlkIjt9czo5OiJpU'
			. '2V2ZXJpdHkiO2E6MTp7aTowO3M6OToiaVNldmVyaXR5Ijt9czo1OiJpRGF0ZSI7YToxOntpOjA7'
			. 'czo1OiJpRGF0ZSI7fX19czoxNjoiYm02MF9ibXNfbWlsdGVycyI7YToyOntzOjY6ImZpZWxkcyI'
			. '7YTo3OntpOjA7YTo2OntpOjA7czo4OiJtaWx0ZXJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3'
			. 'M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9a'
			. 'ToxO2E6Njp7aTowO3M6NToidGl0bGUiO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6'
			. 'Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjg6Imhvc3R'
			. 'uYW1lIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOj'
			. 'Q7czo5OiJsb2NhbGhvc3QiO2k6NTtzOjA6IiI7fWk6MzthOjY6e2k6MDtzOjQ6InBvcnQiO2k6M'
			. 'TtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtz'
			. 'OjA6IiI7fWk6NDthOjY6e2k6MDtzOjU6ImZsYWdzIjtpOjE7czoxMDoidGlueWludCg0KSI7aTo'
			. 'yO3M6MjoiTk8iO2k6MztzOjM6Ik1VTCI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo1O2E6Nj'
			. 'p7aTowO3M6MTQ6ImRlZmF1bHRfYWN0aW9uIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6M'
			. 'joiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiMTE2IjtpOjU7czowOiIiO31pOjY7YTo2OntpOjA7'
			. 'czozOiJwb3MiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDt'
			. 'zOjE6IjAiO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6Mjp7czo3OiJQUklNQVJZIjthOj'
			. 'E6e2k6MDtzOjg6Im1pbHRlcmlkIjt9czo1OiJmbGFncyI7YToxOntpOjA7czo1OiJmbGFncyI7f'
			. 'X19czoxMzoiYm02MF9ibXNfbW9kcyI7YToyOntzOjY6ImZpZWxkcyI7YTo4OntpOjA7YTo2Ontp'
			. 'OjA7czo4OiJmaWxlbmFtZSI7aToxO3M6MTE6InZhcmNoYXIoNjQpIjtpOjI7czoyOiJOTyI7aTo'
			. 'zO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czowOiIiO31pOjE7YTo2OntpOjA7czo0OiJuYW1lIjtpOj'
			. 'E7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7c'
			. 'zowOiIiO31pOjI7YTo2OntpOjA7czo1OiJ0aXRsZSI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7'
			. 'aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M'
			. '6NzoidmVyc2lvbiI7aToxO3M6MTE6InZhcmNoYXIoMzIpIjtpOjI7czoyOiJOTyI7aTozO3M6MD'
			. 'oiIjtpOjQ7TjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo2OiJhdXRob3IiO2k6MTtzOjEyO'
			. 'iJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7'
			. 'fWk6NTthOjY6e2k6MDtzOjE0OiJhdXRob3Jfd2Vic2l0ZSI7aToxO3M6MTI6InZhcmNoYXIoMTI'
			. '4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo2O2E6Njp7aT'
			. 'owO3M6MTA6InVwZGF0ZV91cmwiO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k6MjtzOjI6Ik5PI'
			. 'jtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzOjY6ImFjdGl2ZSI7'
			. 'aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTt'
			. 'zOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjE6e2k6MDtzOjg6Im'
			. 'ZpbGVuYW1lIjt9fX1zOjE0OiJibTYwX2Jtc19wcmVmcyI7YToyOntzOjY6ImZpZWxkcyI7YToxM'
			. 'Dg6e2k6MDthOjY6e2k6MDtzOjI6ImlkIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7'
			. 'aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czoxNDoiYXV0b19pbmNyZW1lbnQiO31pOjE7YTo2Ont'
			. 'pOjA7czoxMjoicG9wM2dyZWV0aW5nIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyOi'
			. 'JOTyI7aTozO3M6MDoiIjtpOjQ7czozMjoiYjFnTWFpbFNlcnZlciBQT1AzIHNlcnZpY2UgcmVhZ'
			. 'HkiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjEyOiJpbWFwZ3JlZXRpbmciO2k6MTtzOjEy'
			. 'OiJ2YXJjaGFyKDI1NSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjMyOiJiMWdNYWl'
			. 'sU2VydmVyIElNQVAgc2VydmljZSByZWFkeSI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6Nz'
			. 'oibGljZW5zZSI7aToxO3M6MTE6InZhcmNoYXIoNTYpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiI'
			. 'jtpOjQ7TjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo4OiJsb2dsZXZlbCI7aToxO3M6Njoi'
			. 'aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiI3IjtpOjU7czowOiIiO31'
			. 'pOjU7YTo2OntpOjA7czo4OiJsaWNzdGF0ZSI7aToxO3M6NjoiaW50KDQpIjtpOjI7czoyOiJOTy'
			. 'I7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjY7YTo2OntpOjA7czoxMToib'
			. 'GljZmVhdHVyZXMiO2k6MTtzOjY6ImludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0'
			. 'O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo3O2E6Njp7aTowO3M6MTU6ImxpY192YWxpZF91bnRpbCI'
			. '7aToxO3M6NjoiaW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOj'
			. 'U7czowOiIiO31pOjg7YTo2OntpOjA7czoxMjoiY29yZV92ZXJzaW9uIjtpOjE7czoxMjoidmFyY'
			. '2hhcigyNTUpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjk7'
			. 'YTo2OntpOjA7czo3OiJhbHRwb3AzIjtpOjE7czo2OiJpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM'
			. '7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTA7YTo2OntpOjA7czo3OiJtaW5wb3'
			. 'AzIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwI'
			. 'jtpOjU7czowOiIiO31pOjExO2E6Njp7aTowO3M6NzoibWluaW1hcCI7aToxO3M6NzoiaW50KDEx'
			. 'KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToxMjt'
			. 'hOjY6e2k6MDtzOjEyOiJzbXRwZ3JlZXRpbmciO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO2k6Mj'
			. 'tzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjMyOiJiMWdNYWlsU2VydmVyIFNNVFAgc2VydmljZ'
			. 'SByZWFkeSI7aTo1O3M6MDoiIjt9aToxMzthOjY6e2k6MDtzOjE5OiJzbXRwX2dyZWV0aW5nX2Rl'
			. 'bGF5IjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M'
			. '6MToiNSI7aTo1O3M6MDoiIjt9aToxNDthOjY6e2k6MDtzOjEzOiJncmV5X2ludGVydmFsIjtpOj'
			. 'E7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiI2MCI7aTo1O'
			. '3M6MDoiIjt9aToxNTthOjY6e2k6MDtzOjE0OiJncmV5X3dhaXRfdGltZSI7aToxO3M6NzoiaW50'
			. 'KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6NToiMjg4MDAiO2k6NTtzOjA6IiI'
			. '7fWk6MTY7YTo2OntpOjA7czoxNDoiZ3JleV9nb29kX3RpbWUiO2k6MTtzOjc6ImludCgxMSkiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjY6IjI1OTIwMCI7aTo1O3M6MDoiIjt9aToxN'
			. 'zthOjY6e2k6MDtzOjEyOiJncmV5X2VuYWJsZWQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7'
			. 'czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIxIjtpOjU7czowOiIiO31pOjE4O2E6Njp7aTo'
			. 'wO3M6MTc6InNtdHBfYXV0aF9lbmFibGVkIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6Mj'
			. 'oiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aToxOTthOjY6e2k6MDtzO'
			. 'jEyOiJwb3AzX3RpbWVvdXQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czow'
			. 'OiIiO2k6NDtzOjM6IjM2MCI7aTo1O3M6MDoiIjt9aToyMDthOjY6e2k6MDtzOjEyOiJpbWFwX3R'
			. 'pbWVvdXQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOj'
			. 'Q6IjM2MDAiO2k6NTtzOjA6IiI7fWk6MjE7YTo2OntpOjA7czoxMjoic210cF90aW1lb3V0IjtpO'
			. 'jE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiI2MCI7aTo1'
			. 'O3M6MDoiIjt9aToyMjthOjY6e2k6MDtzOjE0OiJxdWV1ZV9pbnRlcnZhbCI7aToxO3M6NzoiaW5'
			. '0KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiMzAwIjtpOjU7czowOiIiO3'
			. '1pOjIzO2E6Njp7aTowO3M6MTQ6InF1ZXVlX2xpZmV0aW1lIjtpOjE7czo3OiJpbnQoMTEpIjtpO'
			. 'jI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo2OiIyNTkyMDAiO2k6NTtzOjA6IiI7fWk6MjQ7'
			. 'YTo2OntpOjA7czoxMToicXVldWVfcmV0cnkiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5'
			. 'PIjtpOjM7czowOiIiO2k6NDtzOjQ6IjM2MDAiO2k6NTtzOjA6IiI7fWk6MjU7YTo2OntpOjA7cz'
			. 'oxNToic210cF9zaXplX2xpbWl0IjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO'
			. '3M6MDoiIjtpOjQ7czo4OiIxMDQ4NTc2MCI7aTo1O3M6MDoiIjt9aToyNjthOjY6e2k6MDtzOjIw'
			. 'OiJzbXRwX3JlY2lwaWVudF9saW1pdCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k'
			. '6MztzOjA6IiI7aTo0O3M6MjoiMjUiO2k6NTtzOjA6IiI7fWk6Mjc7YTo2OntpOjA7czoxNjoic2'
			. '10cF9lcnJvcl9kZWxheSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7c'
			. 'zowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6Mjg7YTo2OntpOjA7czoyMDoic210cF9l'
			. 'cnJvcl9zb2Z0bGltaXQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M'
			. '6MDoiIjtpOjQ7czoyOiIxMCI7aTo1O3M6MDoiIjt9aToyOTthOjY6e2k6MDtzOjIwOiJzbXRwX2'
			. 'Vycm9yX2hhcmRsaW1pdCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7c'
			. 'zowOiIiO2k6NDtzOjI6IjIwIjtpOjU7czowOiIiO31pOjMwO2E6Njp7aTowO3M6ODoicGhwX3Bh'
			. 'dGgiO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDt'
			. 'zOjEyOiIvdXNyL2Jpbi9waHAiO2k6NTtzOjA6IiI7fWk6MzE7YTo2OntpOjA7czoxNToib3V0Ym'
			. '91bmRfdGFyZ2V0IjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6I'
			. 'iI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozMjthOjY6e2k6MDtzOjIyOiJvdXRib3VuZF9z'
			. 'ZW5kbWFpbF9wYXRoIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M'
			. '6MDoiIjtpOjQ7czoxODoiL3Vzci9zYmluL3NlbmRtYWlsIjtpOjU7czowOiIiO31pOjMzO2E6Nj'
			. 'p7aTowO3M6MjQ6Im91dGJvdW5kX3NtdHBfcmVsYXlfaG9zdCI7aToxO3M6MTI6InZhcmNoYXIoM'
			. 'TI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6OToibG9jYWxob3N0IjtpOjU7czow'
			. 'OiIiO31pOjM0O2E6Njp7aTowO3M6MjQ6Im91dGJvdW5kX3NtdHBfcmVsYXlfcG9ydCI7aToxO3M'
			. '6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MjoiMjUiO2k6NTtzOj'
			. 'A6IiI7fWk6MzU7YTo2OntpOjA7czoxNDoiaW1hcF9pZGxlX3BvbGwiO2k6MTtzOjc6ImludCgxM'
			. 'SkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjUiO2k6NTtzOjA6IiI7fWk6MzY7'
			. 'YTo2OntpOjA7czoxMzoicXVldWVfdGltZW91dCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6Mjo'
			. 'iTk8iO2k6MztzOjA6IiI7aTo0O3M6MjoiNjAiO2k6NTtzOjA6IiI7fWk6Mzc7YTo2OntpOjA7cz'
			. 'oxNToic210cF9yZXZlcnNlZG5zIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO'
			. '2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTozODthOjY6e2k6MDtzOjIyOiJv'
			. 'dXRib3VuZF9hZGRfc2lnbmF0dXJlIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8'
			. 'iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozOTthOjY6e2k6MDtzOjIyOi'
			. 'JvdXRib3VuZF9zaWduYXR1cmVfc2VwIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyO'
			. 'iJOTyI7aTozO3M6MDoiIjtpOjQ7czo2NToiX19fX19fX19fX19fX19fX19fX19fX19fX19fX19f'
			. 'X19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX19fX18iO2k6NTtzOjA6IiI7fWk6NDA7YTo'
			. '2OntpOjA7czoxMzoiY29yZV9mZWF0dXJlcyI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk'
			. '8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo0MTthOjY6e2k6MDtzOjE2O'
			. 'iJpbWFwX2ZvbGRlcl9zZW50IjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7'
			. 'aTozO3M6MDoiIjtpOjQ7czo0OiJTZW50IjtpOjU7czowOiIiO31pOjQyO2E6Njp7aTowO3M6MTY'
			. '6ImltYXBfZm9sZGVyX3NwYW0iO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIj'
			. 'tpOjM7czowOiIiO2k6NDtzOjQ6IlNwYW0iO2k6NTtzOjA6IiI7fWk6NDM7YTo2OntpOjA7czoxO'
			. 'DoiaW1hcF9mb2xkZXJfZHJhZnRzIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJO'
			. 'TyI7aTozO3M6MDoiIjtpOjQ7czo2OiJEcmFmdHMiO2k6NTtzOjA6IiI7fWk6NDQ7YTo2OntpOjA'
			. '7czoxNzoiaW1hcF9mb2xkZXJfdHJhc2giO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOj'
			. 'I6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjU6IlRyYXNoIjtpOjU7czowOiIiO31pOjQ1O2E6Njp7a'
			. 'TowO3M6MjQ6Im91dGJvdW5kX3NtdHBfcmVsYXlfYXV0aCI7aToxO3M6MTA6InRpbnlpbnQoNCki'
			. 'O2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDY7YTo'
			. '2OntpOjA7czoyNDoib3V0Ym91bmRfc210cF9yZWxheV91c2VyIjtpOjE7czoxMjoidmFyY2hhci'
			. 'gxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjQ3O2E6N'
			. 'jp7aTowO3M6MjQ6Im91dGJvdW5kX3NtdHBfcmVsYXlfcGFzcyI7aToxO3M6MTI6InZhcmNoYXIo'
			. 'MTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo0ODthOjY'
			. '6e2k6MDtzOjExOiJmdHBncmVldGluZyI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6Mj'
			. 'oiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo0OTthOjY6e2k6MDtzOjExOiJmd'
			. 'HBfdGltZW91dCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0'
			. 'O047aTo1O3M6MDoiIjt9aTo1MDthOjY6e2k6MDtzOjE1OiJsb2dzX2F1dG9kZWxldGUiO2k6MTt'
			. 'zOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOj'
			. 'U7czowOiIiO31pOjUxO2E6Njp7aTowO3M6MjA6ImxvZ3NfYXV0b2RlbGV0ZV9kYXlzIjtpOjE7c'
			. 'zo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiIzMSI7aTo1O3M6'
			. 'MDoiIjt9aTo1MjthOjY6e2k6MDtzOjIzOiJsb2dzX2F1dG9kZWxldGVfYXJjaGl2ZSI7aToxO3M'
			. '6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NT'
			. 'tzOjA6IiI7fWk6NTM7YTo2OntpOjA7czoyMDoibG9nc19hdXRvZGVsZXRlX2xhc3QiO2k6MTtzO'
			. 'jc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6'
			. 'IiI7fWk6NTQ7YTo2OntpOjA7czoyMToiaW5ib3VuZF9yZXVzZV9wcm9jZXNzIjtpOjE7czoxMDo'
			. 'idGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aT'
			. 'o1NTthOjY6e2k6MDtzOjEyOiJmYWlsYmFuX3RpbWUiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzO'
			. 'jI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IjMwMCI7aTo1O3M6MDoiIjt9aTo1NjthOjY6e2k6'
			. 'MDtzOjE1OiJmYWlsYmFuX2JhbnRpbWUiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjt'
			. 'pOjM7czowOiIiO2k6NDtzOjQ6IjM2MDAiO2k6NTtzOjA6IiI7fWk6NTc7YTo2OntpOjA7czoxMz'
			. 'oiZmFpbGJhbl90eXBlcyI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7c'
			. 'zowOiIiO2k6NDtzOjI6IjIzIjtpOjU7czowOiIiO31pOjU4O2E6Njp7aTowO3M6MTY6ImZhaWxi'
			. 'YW5fYXR0ZW1wdHMiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k'
			. '6NDtzOjI6IjE1IjtpOjU7czowOiIiO31pOjU5O2E6Njp7aTowO3M6MTU6InJhbmRvbV9xdWV1ZV'
			. '9pZCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzO'
			. 'jE6IjAiO2k6NTtzOjA6IiI7fWk6NjA7YTo2OntpOjA7czoyNToicmVjZWl2ZWRfaGVhZGVyX25v'
			. 'X2V4cG9zZSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k'
			. '6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NjE7YTo2OntpOjA7czoxMjoiY29udHJvbF9hZGRyIj'
			. 'tpOjE7czoxMToidmFyY2hhcig2NCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjk6I'
			. 'jEyNy4wLjAuMSI7aTo1O3M6MDoiIjt9aTo2MjthOjY6e2k6MDtzOjEyOiJjb250cm9sX3BvcnQi'
			. 'O2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k'
			. '6NTtzOjA6IiI7fWk6NjM7YTo2OntpOjA7czoxNDoiY29udHJvbF9zZWNyZXQiO2k6MTtzOjExOi'
			. 'J2YXJjaGFyKDMyKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9a'
			. 'To2NDthOjY6e2k6MDtzOjIwOiJpbWFwX2lkbGVfbXlzcWxjbG9zZSI7aToxO3M6MTA6InRpbnlp'
			. 'bnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk'
			. '6NjU7YTo2OntpOjA7czoxNjoicXVldWVfbXlzcWxjbG9zZSI7aToxO3M6MTA6InRpbnlpbnQoNC'
			. 'kiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6NjY7Y'
			. 'To2OntpOjA7czoxNToiaW1hcF9teXNxbGNsb3NlIjtpOjE7czoxMDoidGlueWludCg0KSI7aToy'
			. 'O3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTo2NzthOjY6e2k'
			. '6MDtzOjIzOiJpbWFwX2ludGVsbGlnZW50Zm9sZGVycyI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6Njg7YTo2O'
			. 'ntpOjA7czoxNToic210cF9jaGVja19oZWxvIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6'
			. 'MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo2OTthOjY6e2k6MDt'
			. 'zOjI0OiJzbXRwX3JlamVjdF9ub3JldmVyc2VkbnMiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOj'
			. 'I7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjcwO2E6Njp7a'
			. 'TowO3M6MTU6ImluYm91bmRfaGVhZGVycyI7aToxO3M6NDoidGV4dCI7aToyO3M6MjoiTk8iO2k6'
			. 'MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo3MTthOjY6e2k6MDtzOjE2OiJvdXRib3VuZF9'
			. 'oZWFkZXJzIjtpOjE7czo0OiJ0ZXh0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7TjtpOj'
			. 'U7czowOiIiO31pOjcyO2E6Njp7aTowO3M6MTA6InNwZl9lbmFibGUiO2k6MTtzOjEwOiJ0aW55a'
			. 'W50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31p'
			. 'OjczO2E6Njp7aTowO3M6MjM6InNwZl9kaXNhYmxlX2dyZXlsaXN0aW5nIjtpOjE7czoxMDoidGl'
			. 'ueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIj'
			. 't9aTo3NDthOjY6e2k6MDtzOjE2OiJzcGZfcmVqZWN0X21haWxzIjtpOjE7czoxMDoidGlueWlud'
			. 'Cg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTo3'
			. 'NTthOjY6e2k6MDtzOjE3OiJzcGZfaW5qZWN0X2hlYWRlciI7aToxO3M6MTA6InRpbnlpbnQoNCk'
			. 'iO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6NzY7YT'
			. 'o2OntpOjA7czoxMzoicXVldWVfdGhyZWFkcyI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiT'
			. 'k8iO2k6MztzOjA6IiI7aTo0O3M6MToiNSI7aTo1O3M6MDoiIjt9aTo3NzthOjY6e2k6MDtzOjE2'
			. 'OiJxdWV1ZV9tYXh0aHJlYWRzIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M'
			. '6MDoiIjtpOjQ7czoyOiIyNSI7aTo1O3M6MDoiIjt9aTo3ODthOjY6e2k6MDtzOjE0OiJ1c2VyX3'
			. 'Nob3dsb2dpbiI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO'
			. '2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6Nzk7YTo2OntpOjA7czoxNToidXNlcl9wb3Azc2Vy'
			. 'dmVyIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ'
			. '7TjtpOjU7czowOiIiO31pOjgwO2E6Njp7aTowO3M6MTM6InVzZXJfcG9wM3BvcnQiO2k6MTtzOj'
			. 'c6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjM6IjExMCI7aTo1O3M6M'
			. 'DoiIjt9aTo4MTthOjY6e2k6MDtzOjE1OiJ1c2VyX3NtdHBzZXJ2ZXIiO2k6MTtzOjEyOiJ2YXJj'
			. 'aGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6ODI'
			. '7YTo2OntpOjA7czoxMzoidXNlcl9zbXRwcG9ydCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6Mj'
			. 'oiTk8iO2k6MztzOjA6IiI7aTo0O3M6MzoiNTg3IjtpOjU7czowOiIiO31pOjgzO2E6Njp7aTowO'
			. '3M6MTU6InVzZXJfaW1hcHNlcnZlciI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6Mjoi'
			. 'Tk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aTo4NDthOjY6e2k6MDtzOjEzOiJ1c2V'
			. 'yX2ltYXBwb3J0IjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOj'
			. 'Q7czozOiIxNDMiO2k6NTtzOjA6IiI7fWk6ODU7YTo2OntpOjA7czoyMToidXNlcl9jaG9zZXBvc'
			. 'DNmb2xkZXJzIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7'
			. 'aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTo4NjthOjY6e2k6MDtzOjEyOiJwb3AzX2ZvbGRlcnM'
			. 'iO2k6MTtzOjEyOiJ2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOj'
			. 'Y6IjAsLTEyOCI7aTo1O3M6MDoiIjt9aTo4NzthOjY6e2k6MDtzOjIwOiJvdXRib3VuZF9zbXRwX'
			. '3VzZXRscyI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6'
			. 'NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6ODg7YTo2OntpOjA7czoxMjoidXNlcl9wb3Azc3NsIjt'
			. 'pOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMC'
			. 'I7aTo1O3M6MDoiIjt9aTo4OTthOjY6e2k6MDtzOjEyOiJ1c2VyX2ltYXBzc2wiO2k6MTtzOjEwO'
			. 'iJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czow'
			. 'OiIiO31pOjkwO2E6Njp7aTowO3M6MTI6InVzZXJfc210cHNzbCI7aToxO3M6MTA6InRpbnlpbnQ'
			. 'oNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OT'
			. 'E7YTo2OntpOjA7czoxNjoiaW1hcF9hdXRvZXhwdW5nZSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO'
			. '2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OTI7YTo2'
			. 'OntpOjA7czoxMDoiaW1hcF9saW1pdCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MzoiWUVTIjt'
			. 'pOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OTM7YTo2OntpOjA7czoxOToidX'
			. 'Nlcl9jaG9zZWltYXBsaW1pdCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpO'
			. 'jM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fWk6OTQ7YTo2OntpOjA7czoxNDoic210'
			. 'cF9ob3BfbGltaXQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k'
			. '6NDtzOjI6IjUwIjtpOjU7czowOiIiO31pOjk1O2E6Njp7aTowO3M6MjE6InNtdHBfYXV0aF9ub1'
			. '9yZWNlaXZlZCI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO'
			. '2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6OTY7YTo2OntpOjA7czoxNToic3NsX2NpcGhlcl9s'
			. 'aXN0IjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ'
			. '7czoyNToiSElHSDohRFNTOiFhTlVMTEBTVFJFTkdUSCI7aTo1O3M6MDoiIjt9aTo5NzthOjY6e2'
			. 'k6MDtzOjIxOiJpbWFwX3VpZHNfaW5pdGlhbGl6ZWQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpO'
			. 'jI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjk4O2E6Njp7'
			. 'aTowO3M6MTE6ImFwbnNfZW5hYmxlIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8'
			. 'iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo5OTthOjY6e2k6MDtzOjk6Im'
			. 'FwbnNfaG9zdCI7aToxO3M6MTI6InZhcmNoYXIoMTI4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6I'
			. 'iI7aTo0O3M6MjI6ImdhdGV3YXkucHVzaC5hcHBsZS5jb20iO2k6NTtzOjA6IiI7fWk6MTAwO2E6'
			. 'Njp7aTowO3M6OToiYXBuc19wb3J0IjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTo'
			. 'zO3M6MDoiIjtpOjQ7czo0OiIyMTk1IjtpOjU7czowOiIiO31pOjEwMTthOjY6e2k6MDtzOjE2Oi'
			. 'JhcG5zX2NlcnRpZmljYXRlIjtpOjE7czo0OiJ0ZXh0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiI'
			. 'jtpOjQ7TjtpOjU7czowOiIiO31pOjEwMjthOjY6e2k6MDtzOjE1OiJhcG5zX3ByaXZhdGVrZXki'
			. 'O2k6MTtzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI'
			. '7fWk6MTAzO2E6Njp7aTowO3M6MjE6Im91dGJvdW5kX3NtdHBfdXNlZGFuZSI7aToxO3M6MTA6In'
			. 'RpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6I'
			. 'iI7fWk6MTA0O2E6Njp7aTowO3M6MjM6Im91dGJvdW5kX3NtdHBfdXNlZG5zc2VjIjtpOjE7czox'
			. 'MDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M'
			. '6MDoiIjt9aToxMDU7YTo2OntpOjA7czoxNjoic3NsX2NpcGhlcnN1aXRlcyI7aToxO3M6MTI6In'
			. 'ZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6NzQ6IlRMU19BRVNfM'
			. 'jU2X0dDTV9TSEEzODQ6VExTX0NIQUNIQTIwX1BPTFkxMzA1X1NIQTI1NjpUTFNfQUVTXzEyOF9H'
			. 'Q01fU0hBMjU2IjtpOjU7czowOiIiO31pOjEwNjthOjY6e2k6MDtzOjE1OiJzc2xfbWluX3ZlcnN'
			. 'pb24iO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6Ij'
			. 'AiO2k6NTtzOjA6IiI7fWk6MTA3O2E6Njp7aTowO3M6MTU6InNzbF9tYXhfdmVyc2lvbiI7aToxO'
			. '3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6'
			. 'MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToxOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MjoiaWQ'
			. 'iO319fXM6MTQ6ImJtNjBfYm1zX3F1ZXVlIjthOjI6e3M6NjoiZmllbGRzIjthOjE4OntpOjA7YT'
			. 'o2OntpOjA7czoyOiJpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6I'
			. 'lBSSSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6Njoi'
			. 'YWN0aXZlIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo'
			. '0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6NDoidHlwZSI7aToxO3M6MTA6In'
			. 'RpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6I'
			. 'iI7fWk6MzthOjY6e2k6MDtzOjQ6ImRhdGUiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5P'
			. 'IjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjQ6InN'
			. 'pemUiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NT'
			. 'tzOjA6IiI7fWk6NTthOjY6e2k6MDtzOjQ6ImZyb20iO2k6MTtzOjEyOiJ2YXJjaGFyKDI1NSkiO'
			. '2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7fWk6NjthOjY6e2k6MDtz'
			. 'OjI6InRvIjtpOjE7czoxMjoidmFyY2hhcigyNTUpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjt'
			. 'pOjQ7TjtpOjU7czowOiIiO31pOjc7YTo2OntpOjA7czo5OiJ0b19kb21haW4iO2k6MTtzOjEyOi'
			. 'J2YXJjaGFyKDEyOCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7f'
			. 'Wk6ODthOjY6e2k6MDtzOjg6ImF0dGVtcHRzIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6'
			. 'MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo5O2E6Njp7aTowO3M'
			. '6MTI6Imxhc3RfYXR0ZW1wdCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOj'
			. 'A6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aToxMDthOjY6e2k6MDtzOjExOiJsYXN0X3N0Y'
			. 'XR1cyI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToi'
			. 'MCI7aTo1O3M6MDoiIjt9aToxMTthOjY6e2k6MDtzOjE2OiJsYXN0X3N0YXR1c19jb2RlIjtpOjE'
			. '7czoxMToidmFyY2hhcigxMCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOj'
			. 'A6IiI7fWk6MTI7YTo2OntpOjA7czoyMDoibGFzdF9kaWFnbm9zdGljX2NvZGUiO2k6MTtzOjEyO'
			. 'iJ2YXJjaGFyKDI1NSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtOO2k6NTtzOjA6IiI7'
			. 'fWk6MTM7YTo2OntpOjA7czo5OiJzbXRwX3VzZXIiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI'
			. '6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTQ7YTo2OntpOjA7cz'
			. 'oxNjoibGFzdF9zdGF0dXNfaW5mbyI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiT'
			. 'k8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aToxNTthOjY6e2k6MDtzOjc6ImRlbGV0'
			. 'ZWQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo'
			. 'xOiIwIjtpOjU7czowOiIiO31pOjE2O2E6Njp7aTowO3M6MTI6ImIxZ21haWxfdXNlciI7aToxO3'
			. 'M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6M'
			. 'DoiIjt9aToxNzthOjY6e2k6MDtzOjE2OiJkZWxpdmVyeXN0YXR1c2lkIjtpOjE7czo3OiJpbnQo'
			. 'MTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO319czo'
			. '3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7czoyOiJpZCI7fX19czoxOD'
			. 'oiYm02MF9ibXNfc210cHN0YXRzIjthOjI6e3M6NjoiZmllbGRzIjthOjQ6e2k6MDthOjY6e2k6M'
			. 'DtzOjY6InN0YXRpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBS'
			. 'SSI7aTo0O047aTo1O3M6MTQ6ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6NjoidXN'
			. 'lcmlkIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiTVVMIjtpOjQ7cz'
			. 'oxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMDoicmVjaXBpZW50cyI7aToxO3M6N'
			. 'zoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoi'
			. 'Ijt9aTozO2E6Njp7aTowO3M6NDoidGltZSI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8'
			. 'iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToyOn'
			. 'tzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6Njoic3RhdGlkIjt9czo2OiJ1c2VyaWQiO2E6MTp7a'
			. 'TowO3M6NjoidXNlcmlkIjt9fX1zOjE0OiJibTYwX2Jtc19zdGF0cyI7YToyOntzOjY6ImZpZWxk'
			. 'cyI7YTo1OntpOjA7YTo2OntpOjA7czo0OiJkYXRlIjtpOjE7czo0OiJkYXRlIjtpOjI7czoyOiJ'
			. 'OTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7czowOiIiO31pOjE7YTo2OntpOjA7czo5OiJjb2'
			. '1wb25lbnQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiUFJJI'
			. 'jtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjI7YTo2OntpOjA7czoxMToiY29ubmVjdGlvbnMi'
			. 'O2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k'
			. '6NTtzOjA6IiI7fWk6MzthOjY6e2k6MDtzOjI6ImluIjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7cz'
			. 'oyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7c'
			. 'zozOiJvdXQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtz'
			. 'OjE6IjAiO2k6NTtzOjA6IiI7fX1zOjc6ImluZGV4ZXMiO2E6MTp7czo3OiJQUklNQVJZIjthOjI'
			. '6e2k6MDtzOjQ6ImRhdGUiO2k6MTtzOjk6ImNvbXBvbmVudCI7fX19czoxNjoiYm02MF9ibXNfc3'
			. 'VibmV0cyI7YToyOntzOjY6ImZpZWxkcyI7YTo0OntpOjA7YTo2OntpOjA7czoyOiJpZCI7aToxO'
			. '3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6'
			. 'ImF1dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6MjoiaXAiO2k6MTtzOjExOiJ2YXJjaGF'
			. 'yKDQ4KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O047aTo1O3M6MDoiIjt9aToyO2E6Nj'
			. 'p7aTowO3M6NDoibWFzayI7aToxO3M6MTE6InZhcmNoYXIoNDgpIjtpOjI7czoyOiJOTyI7aTozO'
			. '3M6MDoiIjtpOjQ7TjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7czoxNDoiY2xhc3NpZmljYXRp'
			. 'b24iO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo'
			. 'xOiIxIjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOn'
			. 'tpOjA7czoyOiJpZCI7fX19fQ==';
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

			$db->Query('INSERT INTO {pre}bms_prefs(id,licstate,core_version,outbound_target,php_path) VALUES(1,0,?,2,?)',
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
			$_lang_admin['bms_licerror']			= 'Ihr Lizenzschl&uuml;ssel ist ung&uuml;ltig oder abgelaufen.';
			$_lang_admin['bms_adminplugin']			= 'Admin-Plugin';
			$_lang_admin['bms_core']				= 'Core';
			$_lang_admin['bms_licstatus']			= 'Lizenz-Status';
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
			$_lang_admin['bms_licerror']			= 'Your license key is invalid or has expired.';
			$_lang_admin['bms_adminplugin']			= 'admin plugin';
			$_lang_admin['bms_core']				= 'Core';
			$_lang_admin['bms_licstatus']			= 'License status';
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

		// license
		if($this->prefs['licstate'] != BMS_LIC_VALID && $this->prefs['licstate'] != BMS_LIC_CHECKING)
			$notices[] = array('type'	=> 'warning',
								'text'	=> 'b1gMailServer: ' . $lang_admin['bms_licerror'],
								'link'	=> $this->_adminLink() . '&action=common&');

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

		// hide unavailable tabs
		if(($this->prefs['licfeatures'] & BMS_LIC_FEATURE_POP3) == 0)
			unset($tabs[2]);
		if(($this->prefs['licfeatures'] & BMS_LIC_FEATURE_IMAP) == 0)
			unset($tabs[3]);
		if(($this->prefs['licfeatures'] & BMS_LIC_FEATURE_STATS) == 0)
			unset($tabs[7]);

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
				if(trim($_REQUEST['license']) != $this->prefs['license'])
					$db->Query('UPDATE {pre}bms_prefs SET license=?, licstate=?',
						trim(strtoupper($_REQUEST['license'])),
						BMS_LIC_CHECKING);
				$this->prefs = $this->_getPrefs();
				$tpl->assign('bms_prefs', $this->prefs);
			}

			// reset ban list?
			if(isset($_REQUEST['resetBanList']))
			{
				$db->Query('TRUNCATE TABLE {pre}bms_failban');
			}

			// features
			$features = array();
			if($this->prefs['licfeatures'] & BMS_LIC_FEATURE_POP3)
				$features[] = $lang_admin['pop3'];
			if($this->prefs['licfeatures'] & BMS_LIC_FEATURE_IMAP)
				$features[] = $lang_admin['imap'];
			if($this->prefs['licfeatures'] & BMS_LIC_FEATURE_STATS)
				$features[] = $lang_admin['stats'];

			// ban list
			$res = $db->Query('SELECT COUNT(*) FROM {pre}bms_failban WHERE `banned_until`>=?',
				time());
			list($banCount) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// assign
			$tpl->assign('queueRunning',	$this->_isQueueRunning());
			$tpl->assign('banCount',		$banCount);
			$tpl->assign('features',		implode(', ', $features));
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
