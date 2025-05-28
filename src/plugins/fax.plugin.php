<?php
/*
 * b1gMail fax plugin
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

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

define('MODFAX_PROTOCOL_EMAIL',			1);
define('MODFAX_PROTOCOL_HTTP',			2);

define('MODFAX_NUMBER_INTERNAT_00',		1);
define('MODFAX_NUMBER_INTERNAT_PLUS',	2);
define('MODFAX_NUMBER_INTERNAT_NONE',	3);
define('MODFAX_NUMBER_NAT',				4);

define('MODFAX_BLOCK_TEXT',				0);
define('MODFAX_BLOCK_PAGEBREAK',		1);
define('MODFAX_BLOCK_COVER',			2);
define('MODFAX_BLOCK_PDFFILE',			3);

define('MODFAX_REMARK_URGENT',			0);
define('MODFAX_REMARK_PLEASEREPLY',		1);
define('MODFAX_REMARK_PLEASECOMMENT',	2);
define('MODFAX_REMARK_FORREVIEW',		3);
define('MODFAX_REMARK_FORINFORMATION',	4);

define('MODFAX_STATUS_SENDING',			1);
define('MODFAX_STATUS_SENT',			2);
define('MODFAX_STATUS_ERROR',			3);

define('MODFAX_STYLE_TOPLINE',			1);
define('MODFAX_STYLE_BOTTOMLINE',		2);
define('MODFAX_STYLE_BOLD',				4);
define('MODFAX_STYLE_ITALIC',			8);
define('MODFAX_STYLE_UNDERLINED',		16);

define('MODFAX_SHOWON_FIRSTPAGE',		1);
define('MODFAX_SHOWON_OTHERPAGES',		2);
define('MODFAX_SHOWON_TOP',				4);
define('MODFAX_SHOWON_BOTTOM',			8);

define('MODFAX_STATUSMODE_NONE',		0);
define('MODFAX_STATUSMODE_EMAIL',		1);
define('MODFAX_STATUSMODE_HTTP',		2);

define('MODFAX_EVENT_SENDFAX',			1);
define('MODFAX_EVENT_FAX_OK',			2);
define('MODFAX_EVENT_FAX_ERROR',		3);
define('MODFAX_EVENT_CREDITS',			4);
define('MODFAX_EVENT_REFUNDS',			5);

// load BMHTTP class
if(!class_exists('BMHTTP'))
	include(B1GMAIL_DIR . 'serverlib/http.class.php');

/**
 * POST capable HTTP class
 *
 */
class BMHTTP_POST extends BMHTTP
{
	/**
	 * constructor
	 *
	 * @param string $url URL
	 * @return BMHTTP_POST
	 */
	function __construct($url)
	{
		BMHTTP::BMHTTP($url);
	}

	/**
	 * make post request and return response
	 *
	 * @param string $postData Data to post to the URL
	 * @param string $contentType Data content type
	 * @return string
	 */
	function DownloadToString_POST($postData, $contentType = 'application/x-www-form-urlencoded', &$headers = NULL)
	{
		$crlf = "\r\n";

		// generate request
		$req = 'POST ' . $this->_uri . ' HTTP/1.0' . $crlf
			.	'Host: ' . $this->_host . $crlf
			.	'Content-Type: ' . $contentType . $crlf
			.	'Content-Length: ' . strlen($postData) . $crlf
			.	$crlf;

		// fetch
		$this->_fp = fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port,
			$errNo, $errStr, SOCKET_TIMEOUT);
		fwrite($this->_fp, $req);
		fwrite($this->_fp, $postData);
		$response = '';
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
			$response .= fread($this->_fp, 1024);
		fclose($this->_fp);

		// split header and body
		$pos = strpos($response, $crlf . $crlf);
		if($pos === false)
			return($response);
		$header = substr($response, 0, $pos);
		$body = substr($response, $pos + 2 * strlen($crlf));

		// parse headers
		$headers = array();
		$lines = explode($crlf, $header);
		foreach($lines as $line)
			if(($pos = strpos($line, ':')) !== false)
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));

		// redirection?
		if(isset($headers['location']))
		{
			$http = new BMHTTP_POST($headers['location']);
			return($http->DownloadToString_POST($postData, $contentType));
		}
		else
		{
			return($body);
		}
	}
}

/**
 * fax plugin
 *
 */
class FaxPlugin extends BMPlugin
{
	/**
	 * fpdf path
	 *
	 * @var string
	 */
	var $_fpdfDir;

	/**
	 * plugin prefs
	 *
	 * @var array
	 */
	var $prefs;

	/**
	 * page is new & blank?
	 *
	 * @var bool
	 */
	var $_haveNewPage;

	/**
	 * need new page?
	 *
	 * @var bool
	 */
	var $_needNewPage;

	/**
	 * constructor
	 *
	 * @return FaxPlugin
	 */
	function __construct()
	{
		// fpdf dir
		$this->_fpdfDir				= B1GMAIL_DIR . 'serverlib/3rdparty/fpdf/';

		// plugin info
		$this->type					= BMPLUGIN_DEFAULT;
		$this->name					= 'b1gMail Fax PlugIn';
		$this->author				= 'b1gMail Project';
		$this->version				= '1.46';
		$this->update_url			= 'https://service.b1gmail.org/plugin_updates/';
		$this->website				= 'https://www.b1gmail.org/';

		// admin pages
		$this->admin_pages			= true;
		$this->admin_page_title		= 'Fax';
		$this->admin_page_icon		= 'modfax_logo.png';

		// group option
		$this->RegisterGroupOption('fax', FIELD_CHECKBOX, 'Fax?', '', false);
		if(method_exists('BMPlugin', 'ToolInterfaceCheckLogin'))
		{
			$this->RegisterGroupOption('tbx_fax', FIELD_CHECKBOX, 'Fax in Toolbox?', '', false);
		}
	}

	/**
	 * onload
	 *
	 */
	function OnLoad()
	{
		$this->prefs = $this->_getPrefs();
	}

	/**
	 * install function
	 *
	 * @return bool
	 */
	function Install()
	{
		global $db;

		// db struct
		$databaseStructure =
			  'YTo2OntzOjIwOiJibTYwX21vZGZheF9nYXRld2F5cyI7YToyOntzOjY6ImZpZWxkcyI7YTo5Ont'
			. 'pOjA7YTo2OntpOjA7czo5OiJmYXhnYXRlaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik'
			. '5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthO'
			. 'jY6e2k6MDtzOjU6InRpdGxlIjtpOjE7czoxMjoidmFyY2hhcigxMjgpIjtpOjI7czoyOiJOTyI7'
			. 'aTozO3M6MDoiIjtpOjQ7czowOiIiO2k6NTtzOjA6IiI7fWk6MjthOjY6e2k6MDtzOjEzOiJudW1'
			. 'iZXJfZm9ybWF0IjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6Ii'
			. 'I7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6ODoicHJvdG9jb2wiO2k6M'
			. 'TtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtp'
			. 'OjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo1OiJwcmVmcyI7aToxO3M6NDoidGV4dCI7aToyO3M'
			. '6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MDoiIjtpOjU7czowOiIiO31pOjU7YTo2OntpOjA7cz'
			. 'oxMToic3RhdHVzX21vZGUiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO'
			. '3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjY7YTo2OntpOjA7czoxMjoic3RhdHVz'
			. 'X3ByZWZzIjtpOjE7czo0OiJ0ZXh0IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czowOiI'
			. 'iO2k6NTtzOjA6IiI7fWk6NzthOjY6e2k6MDtzOjQ6InVzZXIiO2k6MTtzOjExOiJ2YXJjaGFyKD'
			. 'Y0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MDoiIjtpOjU7czowOiIiO31pOjg7Y'
			. 'To2OntpOjA7czo0OiJwYXNzIjtpOjE7czoxMToidmFyY2hhcig2NCkiO2k6MjtzOjI6Ik5PIjtp'
			. 'OjM7czowOiIiO2k6NDtzOjA6IiI7aTo1O3M6MDoiIjt9fXM6NzoiaW5kZXhlcyI7YToxOntzOjc'
			. '6IlBSSU1BUlkiO2E6MTp7aTowO3M6OToiZmF4Z2F0ZWlkIjt9fX1zOjE4OiJibTYwX21vZGZheF'
			. '9vdXRib3giO2E6Mjp7czo2OiJmaWVsZHMiO2E6MTQ6e2k6MDthOjY6e2k6MDtzOjU6ImZheGlkI'
			. 'jtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiUFJJIjtpOjQ7TjtpOjU7'
			. 'czoxNDoiYXV0b19pbmNyZW1lbnQiO31pOjE7YTo2OntpOjA7czo2OiJ1c2VyaWQiO2k6MTtzOjc'
			. '6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJNVUwiO2k6NDtzOjE6IjAiO2k6NTtzOj'
			. 'A6IiI7fWk6MjthOjY6e2k6MDtzOjk6ImZheGdhdGVpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO'
			. '3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTow'
			. 'O3M6ODoiZnJvbW5hbWUiO2k6MTtzOjExOiJ2YXJjaGFyKDY0KSI7aToyO3M6MjoiTk8iO2k6Mzt'
			. 'zOjA6IiI7aTo0O3M6MDoiIjtpOjU7czowOiIiO31pOjQ7YTo2OntpOjA7czo2OiJmcm9tbm8iO2'
			. 'k6MTtzOjExOiJ2YXJjaGFyKDY0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MDoiI'
			. 'jtpOjU7czowOiIiO31pOjU7YTo2OntpOjA7czo0OiJ0b25vIjtpOjE7czoxMToidmFyY2hhcig2'
			. 'NCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjA6IiI7aTo1O3M6MDoiIjt9aTo2O2E'
			. '6Njp7aTowO3M6NToicGFnZXMiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aT'
			. 'ozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjc7YTo2OntpOjA7czo0OiJkYXRlI'
			. 'jtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtp'
			. 'OjU7czowOiIiO31pOjg7YTo2OntpOjA7czoxMDoiZGlza2ZpbGVpZCI7aToxO3M6NzoiaW50KDE'
			. 'xKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTo5O2'
			. 'E6Njp7aTowO3M6ODoib3V0Ym94aWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpO'
			. 'jM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6MTA7YTo2OntpOjA7czo1OiJwcmlj'
			. 'ZSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE'
			. '6IjAiO2k6NTtzOjA6IiI7fWk6MTE7YTo2OntpOjA7czo4OiJyZWZ1bmRlZCI7aToxO3M6MTA6In'
			. 'RpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6I'
			. 'iI7fWk6MTI7YTo2OntpOjA7czoxMDoic3RhdHVzY29kZSI7aToxO3M6MTE6InZhcmNoYXIoMzIp'
			. 'IjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czowOiIiO2k6NTtzOjA6IiI7fWk6MTM7YTo'
			. '2OntpOjA7czo2OiJzdGF0dXMiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aT'
			. 'ozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjI6e3M6N'
			. 'zoiUFJJTUFSWSI7YToxOntpOjA7czo1OiJmYXhpZCI7fXM6NjoidXNlcmlkIjthOjE6e2k6MDtz'
			. 'OjY6InVzZXJpZCI7fX19czoyMDoiYm02MF9tb2RmYXhfcHJlZml4ZXMiO2E6Mjp7czo2OiJmaWV'
			. 'sZHMiO2E6Njp7aTowO2E6Njp7aTowO3M6ODoicHJlZml4aWQiO2k6MTtzOjc6ImludCgxMSkiO2'
			. 'k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO2k6NTtzOjE0OiJhdXRvX2luY3JlbWVud'
			. 'CI7fWk6MTthOjY6e2k6MDtzOjE0OiJjb3VudHJ5X3ByZWZpeCI7aToxO3M6MTA6InZhcmNoYXIo'
			. 'NikiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjQ5IjtpOjU7czowOiIiO31pOjI'
			. '7YTo2OntpOjA7czo2OiJwcmVmaXgiO2k6MTtzOjEwOiJ2YXJjaGFyKDYpIjtpOjI7czoyOiJOTy'
			. 'I7aTozO3M6MDoiIjtpOjQ7czowOiIiO2k6NTtzOjA6IiI7fWk6MzthOjY6e2k6MDtzOjk6ImZhe'
			. 'GdhdGVpZCI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6'
			. 'MToiMCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6MTU6InByaWNlX2ZpcnN0cGFnZSI7aTo'
			. 'xO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjMiO2'
			. 'k6NTtzOjA6IiI7fWk6NTthOjY6e2k6MDtzOjE1OiJwcmljZV9uZXh0cGFnZXMiO2k6MTtzOjEwO'
			. 'iJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIyIjtpOjU7czow'
			. 'OiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6NzoiUFJJTUFSWSI7YToxOntpOjA7czo4OiJwcmV'
			. 'maXhpZCI7fX19czoxNzoiYm02MF9tb2RmYXhfcHJlZnMiO2E6Mjp7czo2OiJmaWVsZHMiO2E6MT'
			. 'A6e2k6MDthOjY6e2k6MDtzOjEzOiJhbGxvd19vd25uYW1lIjtpOjE7czoxMDoidGlueWludCg0K'
			. 'SI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aToxO2E6'
			. 'Njp7aTowO3M6MTE6ImFsbG93X293bm5vIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6Mjo'
			. 'iTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aToyO2E6Njp7aTowO3M6OT'
			. 'oiYWxsb3dfcGRmIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6MjoiTk8iO2k6MztzOjA6I'
			. 'iI7aTo0O3M6MToiMSI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6MTI6ImRlZmF1bHRfbmFt'
			. 'ZSI7aToxO3M6MTE6InZhcmNoYXIoNjQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czo'
			. 'wOiIiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjEwOiJkZWZhdWx0X25vIjtpOjE7czoxMT'
			. 'oidmFyY2hhcig2NCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjA6IiI7aTo1O3M6M'
			. 'DoiIjt9aTo1O2E6Njp7aTowO3M6MTM6InNlbmRfc2FmZWNvZGUiO2k6MTtzOjEwOiJ0aW55aW50'
			. 'KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjY'
			. '7YTo2OntpOjA7czoyMjoiZGVmYXVsdF9jb3VudHJ5X3ByZWZpeCI7aToxO3M6MTA6InZhcmNoYX'
			. 'IoNikiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjQ5IjtpOjU7czowOiIiO31pO'
			. 'jc7YTo2OntpOjA7czoxNzoiZGVmYXVsdF9mYXhnYXRlaWQiO2k6MTtzOjc6ImludCgxMSkiO2k6'
			. 'MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6ODthOjY6e2k'
			. '6MDtzOjE1OiJyZWZ1bmRfb25fZXJyb3IiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOi'
			. 'JOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIxIjtpOjU7czowOiIiO31pOjk7YTo2OntpOjA7czoxN'
			. 'joiZGVmYXVsdF90ZW1wbGF0ZSI7aToxO3M6MTI6InZhcmNoYXIoMjU1KSI7aToyO3M6MjoiTk8i'
			. 'O2k6MztzOjA6IiI7aTo0O3M6MTg6ImE6MTp7aTowO3M6MToiMCI7fSI7aTo1O3M6MDoiIjt9fXM'
			. '6NzoiaW5kZXhlcyI7YTowOnt9fXM6MjI6ImJtNjBfbW9kZmF4X3NpZ25hdHVyZXMiO2E6Mjp7cz'
			. 'o2OiJmaWVsZHMiO2E6MTI6e2k6MDthOjY6e2k6MDtzOjExOiJzaWduYXR1cmVpZCI7aToxO3M6N'
			. 'zoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjM6IlBSSSI7aTo0O047aTo1O3M6MTQ6ImF1'
			. 'dG9faW5jcmVtZW50Ijt9aToxO2E6Njp7aTowO3M6NjoiZ3JvdXBzIjtpOjE7czoxMjoidmFyY2h'
			. 'hcigxMjgpIjtpOjI7czoyOiJOTyI7aTozO3M6MzoiTVVMIjtpOjQ7czoxOiIqIjtpOjU7czowOi'
			. 'IiO31pOjI7YTo2OntpOjA7czo2OiJwYXVzZWQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7c'
			. 'zoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO31pOjM7YTo2OntpOjA7'
			. 'czo2OiJ3ZWlnaHQiO2k6MTtzOjEwOiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDo'
			. 'iIjtpOjQ7czozOiIxMDAiO2k6NTtzOjA6IiI7fWk6NDthOjY6e2k6MDtzOjc6ImNvdW50ZXIiO2'
			. 'k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6N'
			. 'TtzOjA6IiI7fWk6NTthOjY6e2k6MDtzOjg6ImZvbnRuYW1lIjtpOjE7czoxMToidmFyY2hhcigz'
			. 'MikiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjU6ImFyaWFsIjtpOjU7czowOiIiO31'
			. 'pOjY7YTo2OntpOjA7czo4OiJmb250c2l6ZSI7aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOj'
			. 'I6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjI6IjExIjtpOjU7czowOiIiO31pOjc7YTo2OntpOjA7c'
			. 'zo1OiJhbGlnbiI7aToxO3M6NzoiY2hhcigxKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0'
			. 'O3M6MToiTCI7aTo1O3M6MDoiIjt9aTo4O2E6Njp7aTowO3M6NToic3R5bGUiO2k6MTtzOjc6Iml'
			. 'udCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjEiO2k6NTtzOjA6IiI7fW'
			. 'k6OTthOjY6e2k6MDtzOjQ6InRleHQiO2k6MTtzOjQ6InRleHQiO2k6MjtzOjI6Ik5PIjtpOjM7c'
			. 'zowOiIiO2k6NDtzOjA6IiI7aTo1O3M6MDoiIjt9aToxMDthOjY6e2k6MDtzOjY6InNob3dvbiI7'
			. 'aToxO3M6MTA6InRpbnlpbnQoNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6Ijc'
			. 'iO2k6NTtzOjA6IiI7fWk6MTE7YTo2OntpOjA7czo2OiJtYXJnaW4iO2k6MTtzOjEwOiJ0aW55aW'
			. '50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoyOiIyMCI7aTo1O3M6MDoiIjt9f'
			. 'XM6NzoiaW5kZXhlcyI7YToyOntzOjc6IlBSSU1BUlkiO2E6MTp7aTowO3M6MTE6InNpZ25hdHVy'
			. 'ZWlkIjt9czo2OiJncm91cHMiO2E6MTp7aTowO3M6NjoiZ3JvdXBzIjt9fX1zOjE3OiJibTYwX21'
			. 'vZGZheF9zdGF0cyI7YToyOntzOjY6ImZpZWxkcyI7YTo2OntpOjA7YTo2OntpOjA7czo2OiJzdG'
			. 'F0aWQiO2k6MTtzOjc6ImludCgxMSkiO2k6MjtzOjI6Ik5PIjtpOjM7czozOiJQUkkiO2k6NDtOO'
			. '2k6NTtzOjE0OiJhdXRvX2luY3JlbWVudCI7fWk6MTthOjY6e2k6MDtzOjE6ImQiO2k6MTtzOjEw'
			. 'OiJ0aW55aW50KDQpIjtpOjI7czoyOiJOTyI7aTozO3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czo'
			. 'wOiIiO31pOjI7YTo2OntpOjA7czoxOiJtIjtpOjE7czoxMDoidGlueWludCg0KSI7aToyO3M6Mj'
			. 'oiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToiMCI7aTo1O3M6MDoiIjt9aTozO2E6Njp7aTowO3M6M'
			. 'ToieSI7aToxO3M6NzoiaW50KDExKSI7aToyO3M6MjoiTk8iO2k6MztzOjA6IiI7aTo0O3M6MToi'
			. 'MCI7aTo1O3M6MDoiIjt9aTo0O2E6Njp7aTowO3M6NDoidHlwZSI7aToxO3M6MTA6InRpbnlpbnQ'
			. 'oNCkiO2k6MjtzOjI6Ik5PIjtpOjM7czowOiIiO2k6NDtzOjE6IjAiO2k6NTtzOjA6IiI7fWk6NT'
			. 'thOjY6e2k6MDtzOjU6ImNvdW50IjtpOjE7czo3OiJpbnQoMTEpIjtpOjI7czoyOiJOTyI7aTozO'
			. '3M6MDoiIjtpOjQ7czoxOiIwIjtpOjU7czowOiIiO319czo3OiJpbmRleGVzIjthOjE6e3M6Nzoi'
			. 'UFJJTUFSWSI7YToxOntpOjA7czo2OiJzdGF0aWQiO319fX0=';
		$databaseStructure = unserialize(base64_decode($databaseStructure));

		// sync struct
		SyncDBStruct($databaseStructure);

		// existing config?
		$res = $db->Query('SELECT COUNT(*) FROM {pre}modfax_prefs');
		list($prefsCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// install sample data
		if($prefsCount == 0)
		{
			$db->Query('INSERT INTO {pre}modfax_gateways(`faxgateid`,`title`,`number_format`,`protocol`,`prefs`,`status_mode`,`status_prefs`,`user`,`pass`) VALUES(\'1\',\'SMSKaufen (E-Mail)\',\'1\',\'1\',\'a:5:{s:4:\"from\";s:12:\"%%usermail%%\";s:2:\"to\";s:20:\"faximo@smskaufen.com\";s:7:\"subject\";s:66:\"%%user%%#%%pass%%#%%to%%#%%from_name%%#%%from_no%%#%%status_code%%\";s:4:\"text\";s:0:\"\";s:7:\"pdffile\";s:7:\"fax.pdf\";}\',\'2\',\'a:3:{s:10:\"code_param\";s:7:\"pers_id\";s:12:\"result_param\";s:6:\"status\";s:12:\"result_regex\";s:13:\"^Kein Fehler$\";}\',\'\',\'\')');
			$db->Query('INSERT INTO {pre}modfax_gateways(`faxgateid`,`title`,`number_format`,`protocol`,`prefs`,`status_mode`,`status_prefs`,`user`,`pass`) VALUES(\'2\',\'SMSKaufen (HTTP)\',\'1\',\'2\',\'a:3:{s:3:\"url\";s:47:\"http://www.smskaufen.com/sms/faxtmp/inbound.php\";s:7:\"request\";s:114:\"id=%%user%%&pw=%%pass%%&empfaenger=%%to%%&abs_nr=%%from_no%%&abs_name=%%from_name%%&datei=%%pdf_data_base64%%&aw=0\";s:11:\"returnvalue\";s:3:\"100\";}\',\'2\',\'a:3:{s:10:\"code_param\";s:2:\"id\";s:12:\"result_param\";s:6:\"status\";s:12:\"result_regex\";s:13:\"^Kein Fehler$\";}\',\'\',\'\')');
			$db->Query('INSERT INTO {pre}modfax_gateways(`faxgateid`,`title`,`number_format`,`protocol`,`prefs`,`status_mode`,`status_prefs`,`user`,`pass`) VALUES(\'3\',\'XaraNet\',\'1\',\'1\',\'a:5:{s:4:\"from\";s:16:\"%%answer_email%%\";s:2:\"to\";s:27:\"mail2fax-body@faxstation.de\";s:7:\"subject\";s:19:\"Fax %%status_code%%\";s:4:\"text\";s:294:\"<request>\r\n<auth>\r\n  <account>%%user%%</account>\r\n  <password>%%pass%%</password>\r\n</auth>\r\n<fax>\r\n  <fax-id>%%status_code%%</fax-id>\r\n  <to>%%to%%</to>\r\n  <from>%%from_no%%</from>\r\n  <station-id>%%from_no%%</station-id>\r\n  <retry>3</retry>\r\n  <header>%%from_name%%</header>\r\n</fax>\r\n</request>\";s:7:\"pdffile\";s:7:\"fax.pdf\";}\',\'1\',\'a:7:{s:9:\"emailfrom\";s:27:\"faxversand@faxstation.de\";s:7:\"emailto\";s:16:\"%%answer_email%%\";s:12:\"emailsubject\";s:31:\"^Fax Status Report ID ([0-9]*)$\";s:10:\"code_field\";s:7:\"subject\";s:13:\"success_field\";s:4:\"text\";s:10:\"code_regex\";s:31:\"^Fax Status Report ID ([0-9]*)$\";s:13:\"success_regex\";s:20:\"Status: SEND-SUCCESS\";}\',\'\',\'\')');
			$db->Query('INSERT INTO {pre}modfax_gateways(`faxgateid`,`title`,`number_format`,`protocol`,`prefs`,`status_mode`,`status_prefs`,`user`,`pass`) VALUES(\'4\',\'CompuTron GNetX\',\'2\',\'1\',\'a:5:{s:4:\"from\";s:16:\"%%answer_email%%\";s:2:\"to\";s:20:\"%%to%%@fax.gnetx.com\";s:7:\"subject\";s:24:\"%%pass%%:%%status_code%%\";s:4:\"text\";s:0:\"\";s:7:\"pdffile\";s:7:\"fax.pdf\";}\',\'1\',\'a:7:{s:9:\"emailfrom\";s:13:\"fax@gnetx.com\";s:7:\"emailto\";s:16:\"%%answer_email%%\";s:12:\"emailsubject\";s:27:\"^Sendebericht TNR:([0-9]*)$\";s:10:\"code_field\";s:7:\"subject\";s:13:\"success_field\";s:4:\"text\";s:10:\"code_regex\";s:27:\"^Sendebericht TNR:([0-9]*)$\";s:13:\"success_regex\";s:8:\"status:0\";}\',\'\',\'\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'1\',\'49\',\'*\',\'0\',\'3\',\'2\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'2\',\'49\',\'18\',\'0\',\'10\',\'9\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'3\',\'49\',\'90\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'4\',\'49\',\'19\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'5\',\'49\',\'13\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'6\',\'*\',\'*\',\'0\',\'5\',\'4\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'16\',\'49\',\'14\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'15\',\'49\',\'12\',\'-1\',\'3\',\'2\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'14\',\'49\',\'11\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_prefixes(`prefixid`,`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(\'13\',\'49\',\'10\',\'-1\',\'0\',\'0\')');
			$db->Query('INSERT INTO {pre}modfax_signatures(`signatureid`,`groups`,`paused`,`weight`,`counter`,`fontname`,`fontsize`,`align`,`style`,`text`,`showon`,`margin`) VALUES(\'1\',\'*\',\'0\',\'100\',\'0\',\'arial\',\'8\',\'L\',\'13\',\'powered by b1gMail\r\nhttps://www.b1gmail.eu/\',\'11\',\'25\')');
			$db->Query('INSERT INTO {pre}modfax_prefs(`allow_ownname`,`allow_ownno`,`allow_pdf`,`default_name`,`default_no`,`send_safecode`,`default_country_prefix`,`default_faxgateid`,`refund_on_error`) VALUES(\'1\',\'1\',\'1\',\'\',\'\',\'0\',\'49\',\'2\',\'1\')');
		}

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
	 * uninstall function
	 *
	 * @return bool
	 */
	function Uninstall()
	{
		return(true);
	}

	/**
	 * injects our language phrases
	 *
	 * @param array $lang_user
	 * @param array $lang_client
	 * @param array $lang_custom
	 * @param array $lang_admin
	 * @param string $lang
	 */
	function OnReadLang(&$lang_user, &$lang_client, &$lang_custom, &$lang_admin, $lang)
	{
		//
		// german
		//
		if($lang == 'deutsch')
		{
			$lang_admin['modfax_prefixes']				= 'Vorwahlen';
			$lang_admin['modfax_fpdferror']			= 'FPDF/FPDI ist nicht installiert, wird aber vom Fax-Plugin zwingend zum Versand von Faxen ben&ouml;tigt. Bitte lesen Sie die Fax-Plugin-Dokumentation, um zu erfahren, wie Sie FPDF/FPDI installieren.';
			$lang_admin['modfax_protocol']				= 'Einlieferungsweg';
			$lang_admin['modfax_email']				= 'E-Mail';
			$lang_admin['modfax_http']					= 'HTTP-POST';
			$lang_admin['modfax_emailfrom']			= 'E-Mail von';
			$lang_admin['modfax_emailto']				= 'E-Mail an';
			$lang_admin['modfax_emailsubject']			= 'E-Mail-Betreff';
			$lang_admin['modfax_emailtext']			= 'E-Mail-Text';
			$lang_admin['modfax_emailpdffile']			= 'Anhang-Dateiname';
			$lang_admin['modfax_httpurl']				= 'URL';
			$lang_admin['modfax_httprequest']			= 'POST-Request';
			$lang_admin['modfax_numberformat']			= 'Nummern-Format';
			$lang_admin['modfax_number_internat_00']	= 'International mit 00 (z.B. 004912345678)';
			$lang_admin['modfax_number_internat_plus']	= 'International mit + (z.B. +4912345678)';
			$lang_admin['modfax_number_internat_none']	= 'International ohne Prefix (z.B. 4912345678)';
			$lang_admin['modfax_number_nat']			= 'Ohne internat. Vorwahl (z.B. 012345678)';
			$lang_admin['modfax_supportsstatus']		= 'Status-Nachricht';
			$lang_admin['modfax_signatures']			= 'Signaturen';
			$lang_admin['modfax_status1']				= 'Antwort-E-Mail';
			$lang_admin['modfax_status2']				= 'HTTP-Push';
			$lang_admin['modfax_statuscode_from']		= 'Status-Code';
			$lang_admin['modfax_success_from']			= 'Erfolgs-Bedingung';
			$lang_admin['modfax_statuscode_param']		= 'Status-Code-Parameter';
			$lang_admin['modfax_result_param']			= 'Ergebnis-Parameter';
			$lang_admin['modfax_advancedmode']			= 'Erweiterter Modus';
			$lang_admin['modfax_gateways_advanced']	= 'Gateways (erweiterter Modus)';
			$lang_admin['modfax_gateways_simple']		= 'Gateways (vereinfachter Modus)';
			$lang_admin['modfax_country_prefix']		= 'Landes-Vorwahl';
			$lang_admin['modfax_prefix']				= 'Vorwahl';
			$lang_admin['modfax_price_firstpage']		= 'Preis (erste Seite)';
			$lang_admin['modfax_price_nextpages']		= 'Preis (weitere Seiten)';
			$lang_admin['modfax_forbidno']				= 'Nummern-Bereich verbieten';
			$lang_admin['modfax_addprefix']			= 'Vorwahl hinzuf&uuml;gen';
			$lang_admin['modfax_signature']			= 'Signatur';
			$lang_admin['modfax_used']					= 'Verwendet';
			$lang_admin['modfax_addsignature']			= 'Signatur hinzuf&uuml;gen';
			$lang_admin['modfax_fontsize']				= 'Schriftgr&ouml;&szlig;e';
			$lang_admin['modfax_style']				= 'Stil';
			$lang_admin['modfax_bold']					= 'Fett';
			$lang_admin['modfax_italic']				= 'Kursiv';
			$lang_admin['modfax_underlined']			= 'Unterstrichen';
			$lang_admin['modfax_fontname']				= 'Schriftart';
			$lang_admin['modfax_align']				= 'Ausrichtung';
			$lang_admin['modfax_alignleft']			= 'Linksb&uuml;ndig';
			$lang_admin['modfax_alignright']			= 'Rechtsb&uuml;ndig';
			$lang_admin['modfax_aligncenter']			= 'Zentriert';
			$lang_admin['modfax_alignjustify']			= 'Blocksatz';
			$lang_admin['modfax_line']					= 'Trenn-Linie';
			$lang_admin['modfax_top']					= 'Oben';
			$lang_admin['modfax_bottom']				= 'Unten';
			$lang_admin['modfax_showon']				= 'Anzeigen auf';
			$lang_admin['modfax_placement']			= 'Platzierung';
			$lang_admin['modfax_margin']				= 'Reservierte H&ouml;he';
			$lang_admin['modfax_firstpage']			= '1. Seite';
			$lang_admin['modfax_otherpages']			= 'Weitere Seiten';
			$lang_admin['modfax_faxtoday']				= 'Gesendete Faxe (heute)';
			$lang_admin['modfax_faxmonth']				= 'Gesendete Faxe (dieser Monat)';
			$lang_admin['modfax_faxall']				= 'Gesendete Faxe (insgesamt)';
			$lang_admin['modfax_creditstoday']			= 'Abgerechnete Cred. (heute)';
			$lang_admin['modfax_creditsmonth']			= 'Abgerechnete Cred. (dieser Monat)';
			$lang_admin['modfax_creditsall']			= 'Abgerechnete Cred. (insgesamt)';
			$lang_admin['modfax_refundstoday']			= 'R&uuml;ckerstattungen (Cred., heute)';
			$lang_admin['modfax_refundsmonth']			= 'R&uuml;ckerstattungen (Cred., dieser Monat)';
			$lang_admin['modfax_refundsall']			= 'R&uuml;ckerstattungen (Cred., insgesamt)';
			$lang_admin['modfax_errtoday']				= '&Uuml;bertragungs-Fehler (heute)';
			$lang_admin['modfax_errmonth']				= '&Uuml;bertragungs-Fehler (dieser Monat)';
			$lang_admin['modfax_errall']				= '&Uuml;bertragungs-Fehler (insgesamt)';
			$lang_admin['modfax_perms']				= 'Berechtigungen';
			$lang_admin['modfax_refund_on_error']		= 'R&uuml;ckerstattung bei Fehler';
			$lang_admin['modfax_fromname']				= 'Absender-Name';
			$lang_admin['modfax_fromno']				= 'Absender-Nr.';
			$lang_admin['modfax_allow_ownno']			= 'Eigene Absender-Nr.';
			$lang_admin['modfax_allow_ownname']		= 'Eigener Absender-Name';
			$lang_admin['modfax_allow_pdf']			= 'PDF-Anh&auml;nge';
			$lang_admin['modfax_faxes']				= 'Faxe';
			$lang_admin['modfax_credits']				= 'Credits';
			$lang_admin['modfax_stat_1']				= 'Gesendete Faxe';
			$lang_admin['modfax_stat_2']				= 'Erfolgreiche Übermittlungen';
			$lang_admin['modfax_stat_3']				= 'Fehlerhafte Übermittlungen';
			$lang_admin['modfax_stat_4']				= 'Abgerechnete Credits';
			$lang_admin['modfax_stat_5']				= 'Zurückerstattete Credits';
			$lang_admin['modfax_faxtpl']				= 'Fax-Vorlage';
			$lang_admin['modfax_block']				= 'Block';
			$lang_admin['modfax_textblock']			= 'Text-Block';
			$lang_admin['modfax_pagebreak']			= 'Seitenumbruch';
			$lang_admin['modfax_cover']				= 'Deckblatt (Kopf)';
			$lang_admin['modfax_pdffile']				= 'PDF-Anhang';
			$lang_admin['modfax_faxplugin']				= 'Fax-Plugin';

			$lang_user['modfax_txrefundtext']			= 'Rückerstattung (Fax-Versand fehlgeschlagen)';
			$lang_user['modfax_txtext']				= 'Fax-Versand';
			$lang_user['modfax_fax']					= 'Fax';
			$lang_user['modfax_send']					= 'Fax senden';
			$lang_user['modfax_outbox']				= 'Journal';
			$lang_user['modfax_fromno']				= 'Absender-Nummer';
			$lang_user['modfax_fromname']				= 'Absender-Name';
			$lang_user['modfax_next']					= 'Weiter';
			$lang_user['modfax_fontname']				= 'Schriftart';
			$lang_user['modfax_textblock']				= 'Text-Block';
			$lang_user['modfax_pagebreak']				= 'Seitenumbruch';
			$lang_user['modfax_cover']					= 'Deckblatt (Kopf)';
			$lang_user['modfax_pdffile']				= 'PDF-Anhang';
			$lang_user['modfax_fontsize']				= 'Schriftgr&ouml;&szlig;e';
			$lang_user['modfax_style']					= 'Stil';
			$lang_user['modfax_bold']					= 'Fett';
			$lang_user['modfax_italic']				= 'Kursiv';
			$lang_user['modfax_underlined']			= 'Unterstrichen';
			$lang_user['modfax_file']					= 'Datei';
			$lang_user['modfax_browse']				= 'Ausw&auml;hlen';
			$lang_user['modfax_pages']					= 'Seiten';
			$lang_user['modfax_preview']				= 'Vorschau und Preis-Berechnung';
			$lang_user['modfax_align']					= 'Ausrichtung';
			$lang_user['modfax_alignleft']				= 'Linksb&uuml;ndig';
			$lang_user['modfax_alignright']			= 'Rechtsb&uuml;ndig';
			$lang_user['modfax_aligncenter']			= 'Zentriert';
			$lang_user['modfax_alignjustify']			= 'Blocksatz';
			$lang_user['modfax_nofilesel']				= 'Noch keine Datei gew&auml;hlt';
			$lang_user['modfax_browsetext']			= 'Bitte w&auml;hlen Sie die PDF-Datei aus, die Sie an Ihr Fax anh&auml;ngen m&ouml;chten. Sie k&ouml;nnen eine Datei von Ihrem Computer oder von Ihrer Webdisk (falls vorhanden) ausw&auml;hlen.';
			$lang_user['modfax_pdferror']				= 'Die Datei ist keine gültige PDF-Datei oder verschlüsselt/geschützt.';
			$lang_user['modfax_pdfpageerror']			= 'Die PDF-Datei konnte aus unbekannten Gründen nicht verarbeitet werden.';
			$lang_user['modfax_remark']				= 'Vermerk';
			$lang_user['modfax_remark0']				= 'Dringend';
			$lang_user['modfax_remark1']				= 'Bitte um Antwort';
			$lang_user['modfax_remark2']				= 'Bitte um Stellungnahme';
			$lang_user['modfax_remark3']				= 'Zur Durchsicht';
			$lang_user['modfax_remark4']				= 'Zur Kenntnisnahme';
			$lang_user['modfax_previewtext']			= 'Ihr Fax wurde erfolgreich zum Versand vorbereitet. Im Folgenden k&ouml;nnen Sie eine Vorschau (PDF-Datei) des Faxes herunterladen und Sie sehen die Seitenanzahl des Faxes sowie den Preis (in Credits), der bei Versand f&auml;llig wird.';
			$lang_user['modfax_previewtext2']			= 'Wenn Sie das Fax absenden m&ouml;chten, klicken Sie bitte auf &quot;Fax senden&quot;. Stellen Sie vorher sicher, dass Ihr Credit-Guthaben zum Versand ausreicht.';
			$lang_user['modfax_toerr']					= 'Die angegebene Empf&auml;nger-Nummer ist ung&uuml;ltig. Bitte pr&uuml;fen Sie Ihre Eingabe und versuchen Sie es erneut.';
			$lang_user['modfax_toforbiddenerr']		= 'Es k&ouml;nnen leider keine Faxe an die angegebene Nummer bzw. an die angegebene Vorwahl gesendet werden.';
			$lang_user['modfax_sent']					= 'Das Fax wurde erfolgreich verschickt.';
			$lang_user['modfax_sendfailed']			= 'Das Fax konnte nicht gesendet werden. M&ouml;gliche Ursache ist ein zu kleines Guthaben oder ein tempor&auml;rer interner Fehler. Bitte versuchen Sie es sp&auml;ter erneut.';
			$lang_user['modfax_sentfolder']			= 'Gesendete Faxe';
			$lang_user['modfax_status1']				= 'Wird gesendet';
			$lang_user['modfax_status2']				= 'Versendet';
			$lang_user['modfax_status3']				= 'Fehler';
			$lang_user['modfax_refunded']				= 'zur&uuml;ckerstattet';
			$lang_user['modfax_filena']				= 'nicht mehr vorhanden';
			$lang_user['modfax_browsepdf']				= 'PDF-Datei ausw&auml;hlen';
			$lang_user['modfax_fromerr']				= 'Der angegebene Absender-Name bzw. die angegebene Absender-Nummer ist ung&uuml;ltig. Bitte pr&uuml;fen Sie Ihre Eingabe und versuchen Sie es erneut.';

			$lang_client['modfax_browsepdf']			= 'PDF-Datei ausw&auml;hlen';
		}

		//
		// english
		//
		else
		{
			$lang_admin['modfax_prefixes']				= 'Prefixes';
			$lang_admin['modfax_fpdferror']			= 'FPDF/FPDI is not installed properly, but is required by the Fax plugin to create faxes. Please read the documentation of the Fax plugin on how to install FPDF/FPDI.';
			$lang_admin['modfax_protocol']				= 'Submit protocol';
			$lang_admin['modfax_email']				= 'E-mail';
			$lang_admin['modfax_http']					= 'HTTP-POST';
			$lang_admin['modfax_emailfrom']			= 'E-mail from';
			$lang_admin['modfax_emailto']				= 'E-mail tio';
			$lang_admin['modfax_emailsubject']			= 'E-mail subject';
			$lang_admin['modfax_emailtext']			= 'E-mail text';
			$lang_admin['modfax_emailpdffile']			= 'Attachment file name';
			$lang_admin['modfax_httpurl']				= 'URL';
			$lang_admin['modfax_httprequest']			= 'POST-Request';
			$lang_admin['modfax_numberformat']			= 'Number format';
			$lang_admin['modfax_number_internat_00']	= 'International with 00 (e.g. 004912345678)';
			$lang_admin['modfax_number_internat_plus']	= 'International with + (e.g. +4912345678)';
			$lang_admin['modfax_number_internat_none']	= 'International without 00/+ (e.g. 4912345678)';
			$lang_admin['modfax_number_nat']			= 'Without internat. prefix (e.g.. 012345678)';
			$lang_admin['modfax_supportsstatus']		= 'Status report';
			$lang_admin['modfax_signatures']			= 'Signatures';
			$lang_admin['modfax_status1']				= 'Reply e-mail';
			$lang_admin['modfax_status2']				= 'HTTP push';
			$lang_admin['modfax_statuscode_from']		= 'Status code';
			$lang_admin['modfax_success_from']			= 'Success condition';
			$lang_admin['modfax_statuscode_param']		= 'Status code parameter';
			$lang_admin['modfax_result_param']			= 'Status parameter';
			$lang_admin['modfax_advancedmode']			= 'Advanced mode';
			$lang_admin['modfax_gateways_advanced']	= 'Gateways (simplified mode)';
			$lang_admin['modfax_gateways_simple']		= 'Gateways (advanced mode)';
			$lang_admin['modfax_country_prefix']		= 'Country prefix';
			$lang_admin['modfax_prefix']				= 'Prefix';
			$lang_admin['modfax_price_firstpage']		= 'Price (first page)';
			$lang_admin['modfax_price_nextpages']		= 'Price (further pages)';
			$lang_admin['modfax_forbidno']				= 'Forbid';
			$lang_admin['modfax_addprefix']			= 'Add prefix';
			$lang_admin['modfax_signature']			= 'Signature';
			$lang_admin['modfax_used']					= 'Used';
			$lang_admin['modfax_addsignature']			= 'Add signature';
			$lang_admin['modfax_fontsize']				= 'Font size';
			$lang_admin['modfax_style']				= 'Style';
			$lang_admin['modfax_bold']					= 'Bold';
			$lang_admin['modfax_italic']				= 'Italic';
			$lang_admin['modfax_underlined']			= 'Underlined';
			$lang_admin['modfax_fontname']				= 'Font family';
			$lang_admin['modfax_align']				= 'Alignment';
			$lang_admin['modfax_alignleft']			= 'Left';
			$lang_admin['modfax_alignright']			= 'Right';
			$lang_admin['modfax_aligncenter']			= 'Center';
			$lang_admin['modfax_alignjustify']			= 'Justify';
			$lang_admin['modfax_line']					= 'Line';
			$lang_admin['modfax_top']					= 'Top';
			$lang_admin['modfax_bottom']				= 'Bottom';
			$lang_admin['modfax_showon']				= 'Show on';
			$lang_admin['modfax_placement']			= 'Placement';
			$lang_admin['modfax_margin']				= 'Reserved height';
			$lang_admin['modfax_firstpage']			= '1. page';
			$lang_admin['modfax_otherpages']			= 'Further pages';
			$lang_admin['modfax_faxtoday']				= 'Sent faxes (today)';
			$lang_admin['modfax_faxmonth']				= 'Sent faxes (current month)';
			$lang_admin['modfax_faxall']				= 'Sent faxes (overall)';
			$lang_admin['modfax_creditstoday']			= 'Charged credits (today)';
			$lang_admin['modfax_creditsmonth']			= 'Charged credits (current month)';
			$lang_admin['modfax_creditsall']			= 'Charged credits (overall)';
			$lang_admin['modfax_refundstoday']			= 'Refunds (credits, today)';
			$lang_admin['modfax_refundsmonth']			= 'Refunds (credits, current month)';
			$lang_admin['modfax_refundsall']			= 'Refunds (credits, overall)';
			$lang_admin['modfax_errtoday']				= 'Transmission errors (today)';
			$lang_admin['modfax_errmonth']				= 'Transmission errors (current month)';
			$lang_admin['modfax_errall']				= 'Transmission errors (overall)';
			$lang_admin['modfax_perms']				= 'Permissions';
			$lang_admin['modfax_refund_on_error']		= 'Refund on error';
			$lang_admin['modfax_fromname']				= 'Sender name';
			$lang_admin['modfax_fromno']				= 'Sender no';
			$lang_admin['modfax_allow_ownno']			= 'Own sender no';
			$lang_admin['modfax_allow_ownname']		= 'Own sender name';
			$lang_admin['modfax_allow_pdf']			= 'PDF attachments';
			$lang_admin['modfax_faxes']				= 'Faxes';
			$lang_admin['modfax_credits']				= 'Credits';
			$lang_admin['modfax_stat_1']				= 'Sent faxes';
			$lang_admin['modfax_stat_2']				= 'Successful transmissions';
			$lang_admin['modfax_stat_3']				= 'Failed transmissions';
			$lang_admin['modfax_stat_4']				= 'Charged credits';
			$lang_admin['modfax_stat_5']				= 'Refunded credits';
			$lang_admin['modfax_faxtpl']				= 'Fax template';
			$lang_admin['modfax_block']				= 'Block';
			$lang_admin['modfax_textblock']			= 'Text block';
			$lang_admin['modfax_pagebreak']			= 'Page break';
			$lang_admin['modfax_cover']				= 'Cover page (head)';
			$lang_admin['modfax_pdffile']				= 'PDF attachment';
			$lang_admin['modfax_faxplugin']				= 'Fax plugin';

			$lang_user['modfax_txrefundtext']			= 'Refund (failed to send fax)';
			$lang_user['modfax_txtext']				= 'Fax';
			$lang_user['modfax_fax']					= 'Fax';
			$lang_user['modfax_send']					= 'Send fax';
			$lang_user['modfax_outbox']				= 'Outbox';
			$lang_user['modfax_fromno']				= 'Sender no';
			$lang_user['modfax_fromname']				= 'Sender name';
			$lang_user['modfax_next']					= 'Next';
			$lang_user['modfax_fontname']				= 'Font family';
			$lang_user['modfax_textblock']				= 'Text block';
			$lang_user['modfax_pagebreak']				= 'Page break';
			$lang_user['modfax_cover']					= 'Cover page (head)';
			$lang_user['modfax_pdffile']				= 'PDF attachment';
			$lang_user['modfax_fontsize']				= 'Font size';
			$lang_user['modfax_style']					= 'Style';
			$lang_user['modfax_bold']					= 'Bold';
			$lang_user['modfax_italic']				= 'Italic';
			$lang_user['modfax_underlined']			= 'Underlined';
			$lang_user['modfax_file']					= 'File';
			$lang_user['modfax_browse']				= 'Chose';
			$lang_user['modfax_pages']					= 'Pages';
			$lang_user['modfax_preview']				= 'Preview and price calculation';
			$lang_user['modfax_align']					= 'Alignment';
			$lang_user['modfax_alignleft']				= 'Left';
			$lang_user['modfax_alignright']			= 'Right';
			$lang_user['modfax_aligncenter']			= 'Center';
			$lang_user['modfax_alignjustify']			= 'Justify';
			$lang_user['modfax_nofilesel']				= 'No file selected';
			$lang_user['modfax_browsetext']			= 'Please select the PDF file you would like to attach to your fax. You can chose a file from your computer or from your webdisk (if available).';
			$lang_user['modfax_pdferror']				= 'The file is not in PDF format or encrypted/protected.';
			$lang_user['modfax_pdfpageerror']			= 'The PDF file could not be processed for unknown reasons.';
			$lang_user['modfax_remark']				= 'Remark';
			$lang_user['modfax_remark0']				= 'Urgent';
			$lang_user['modfax_remark1']				= 'Please Reply';
			$lang_user['modfax_remark2']				= 'Please Comment';
			$lang_user['modfax_remark3']				= 'For Review';
			$lang_user['modfax_remark4']				= 'For Your Information';
			$lang_user['modfax_previewtext']			= 'Your fax has been prepared for sending. Here you can download the preview for your fax (PDF file) and you can see the page count and price (in credits) of your fax.';
			$lang_user['modfax_previewtext2']			= 'If you would like to send the fax, click at &quot;Send fax&quot;. Please ensure that you have enough credits first.';
			$lang_user['modfax_toerr']					= 'The recipient no is invalid. Please check your input and try again.';
			$lang_user['modfax_toforbiddenerr']		= 'Unfortunately we cannot process faxes to this number at this time.';
			$lang_user['modfax_sent']					= 'The fax has been sent successfuly.';
			$lang_user['modfax_sendfailed']			= 'The fax could not been sent. Possible causes are too less credits or a temporary internal problem. Please try again later.';
			$lang_user['modfax_sentfolder']			= 'Sent faxes';
			$lang_user['modfax_status1']				= 'Sending';
			$lang_user['modfax_status2']				= 'Sent';
			$lang_user['modfax_status3']				= 'Error';
			$lang_user['modfax_refunded']				= 'refunded';
			$lang_user['modfax_filena']				= 'not available anymore';
			$lang_user['modfax_browsepdf']				= 'Select PDF file';
			$lang_user['modfax_fromerr']				= 'The sender name/no is invalid. Please check your input and try again.';

			$lang_client['modfax_browsepdf']			= 'Select PDF file';
		}
	}



	//
	// tool interface
	//

	/**
	 * tool interface permissions
	 *
	 * @param BMUser $user
	 * @return array
	 */
	function ToolInterfaceCheckLogin($user)
	{
		$groupID = $user->GetGroup()->_id;
		$faxEnabled = $this->GetGroupOptionValue('fax', $groupID)
						&& $this->GetGroupOptionValue('tbx_fax', $groupID);
		$result = array('faxAccess' => $faxEnabled);

		if($faxEnabled)
		{
			$result['allowOwnName'] 		= $this->prefs['allow_ownname'];
			$result['allowOwnNo'] 			= $this->prefs['allow_ownno'];

			if($result['allowOwnNo'])
			{
				$result['defaultFromNo']	= !empty($this->prefs['default_no'])
												? $this->prefs['default_no']
												: $user->_row['fax'];
			}
			else
				$result['defaultFromNo'] 	= $this->prefs['default_no'];

			if($result['allowOwnName'])
			{
				$result['defaultFromName']	= !empty($this->prefs['default_name'])
												? $this->prefs['default_name']
												: $user->_row['vorname'] . ' ' . $user->_row['nachname'];
			}
			else
				$result['defaultFromName'] 	= $this->prefs['default_name'];
		}

		return($result);
	}

	/**
	 * tool interface handler
	 *
	 * @param string $method
	 * @param array $params
	 * @param array $result
	 * @param BMToolInterface $ti
	 */
	function ToolInterfaceHandler($method, $params, &$result, &$ti)
	{
		$supportedMethods = array('GetFaxPrice', 'SendFax');

		if(!in_array($method, $supportedMethods))
			return;
		$result['status'] = 'OK';

		$userInfo = $ti->CheckLogin($params[0], $params[1]);
		if($userInfo['loginOK'] > 0 && $userInfo['plugins']['FaxPlugin']['faxAccess'])
		{
			$result['result']['status'] = 'OK';

			// getFaxPrice (params: toNo, pageCount)
			if($method == 'GetFaxPrice')
			{
				$faxGateID 	= 0;
				$to			= $this->_parseNo($params[2]);
				$price 		= $this->_calculatePrice($to, $params[3], $faxgateID);

				if($price === false)
				{
					$result['result']['status'] = 'Error';
				}
				else
				{
					$result['result']['status'] = 'OK';
					$result['result']['price'] 	= $price;
				}
			}

			// sendFax (params: fromName, fromNo, toNo, fileSize)
			else if($method == 'SendFax')
			{
				$result['result']['status'] = 'Error';

				// get params
				$fromName		= $params[2];
				$fromNo			= $params[3];
				$toNo			= $params[4];
				$fileSize		= $params[5];

				if($fileSize > 10)
				{
					// prepare params
					$fromName 		= $this->prefs['allow_ownname'] ? $fromName 	: $this->prefs['default_name'];
					$fromNo 		= $this->prefs['allow_ownno'] 	? $fromNo	 	: $this->prefs['default_no'];

					// parse 'to' number
					$to				= $this->_parseNo($toNo);
					if($to)
					{
						// get fax data
						$tempFileID 	= RequestTempFile($userInfo['userID'], time()+TIME_ONE_HOUR);
						$tempFileName 	= TempFileName($tempFileID);
						$fpIn 			= fopen('php://input', 'rb');
						$fpOut 			= fopen(TempFileName($tempFileID), 'wb');
						if($fpIn && $fpOut)
						{
							$writtenBytes = 0;
							while($writtenBytes < $fileSize && !feof($fpIn))
							{
								$buffer = fread($fpIn, 4096);
								fwrite($fpOut, $buffer);
								$writtenBytes += strlen($buffer);
							}

							fclose($fpIn);
							fclose($fpOut);

							// send fax
							$thisUser = _new('BMUser', array($userInfo['userID']));
							if($this->_sendFax($tempFileID, $fromName, $fromNo, $to, $thisUser))
							{
								$result['result']['status'] = 'OK';
							}
						}
					}
				}
			}
		}
		else
		{
			$result['result']['status'] = 'Invalid login';
		}
	}



	//
	// user interface
	//

	/**
	 * returns user pages (i.e. tab bar entries)
	 *
	 * @param bool $loggedin
	 * @return array
	 */
	function getUserPages($loggedin)
	{
		global $lang_user;

		if(!$loggedin)
			return(array());

		$faxEnabled = $this->GetGroupOptionValue('fax');
		if(!$faxEnabled)
			return(array());

		return(array(
			'fax'	=> array(
					'icon'				=> 'modfax_fax',
					'faIcon'			=> 'fa-fax',
					'link'				=> 'start.php?action=faxPlugin&sid=',
					'text'				=> $lang_user['modfax_fax'],
					'iconDir'			=> './plugins/templates/images/',
					'order'				=> 201
				)
		));
	}

	/**
	* returns new menu entries
	*
	* @return array
	*/
	function getNewMenu()
	{
		global $lang_user, $thisUser;

		$faxEnabled = $this->GetGroupOptionValue('fax');
		if(!$faxEnabled)
			return(array());

		$result = array();

		if($thisUser->SMSEnabled())
		{
			$pos = 802;
		}
		else
		{
			$pos = 901;

			$result[] = array(
				'sep'		=> true,
				'order'		=> 900
			);
		}

		$result[] = array(
			'icon'		=> 'modfax_fax',
			'faIcon'	=> 'fa-fax',
			'link'		=> 'start.php?action=faxPlugin&sid=',
			'text'		=> $lang_user['modfax_fax'],
			'iconDir'	=> './plugins/templates/images/',
			'order'		=> 901
		);

		return($result);
	}

	/**
	 * status mail capture
	 *
	 * @param BMMail $mail
	 * @param BMMailbox $mailbox
	 * @param BMUser $user
	 */
	function OnReceiveMail(&$mail, &$mailbox, &$user)
	{
		global $db;

		$from 	= ExtractMailAddresses(strtolower($mail->GetHeaderValue('from')));
		$to 	= ExtractMailAddresses(strtolower($mail->GetHeaderValue('to')));

		$res = $db->Query('SELECT `faxgateid`,`status_prefs` FROM {pre}modfax_gateways WHERE `status_mode`=?',
			MODFAX_STATUSMODE_EMAIL);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$statusPrefs = @unserialize($row['status_prefs']);
			if(!is_array($statusPrefs))
				continue;

			if(!in_array($statusPrefs['emailfrom'], $from))
				continue;

			$emailTo = str_replace('%%answer_email%%', $this->_answerEMailAddr($row['faxgateid']), $statusPrefs['emailto']);
			if(!in_array($emailTo, $to))
				continue;

			if(!preg_match('/'.$statusPrefs['emailsubject'].'/', $mail->GetHeaderValue('subject')))
				continue;

			// get status code
			$codeText = '';
			$codeRegs = array();
			if($statusPrefs['code_field'] == 'subject')
			{
				$codeText = $mail->GetHeaderValue('subject');
			}
			else
			{
				$codeText = $mail->GetTextParts();
				if(isset($codeText['text']))
					$codeText = $codeText['text'];
				else
					$codeText = $codeText['html'];
			}
			if(!preg_match('/'.$statusPrefs['code_regex'].'/', $codeText, $codeRegs)
				|| count($codeRegs) < 2)
				continue;

			// check for success
			$successText = '';
			$success = false;
			if($statusPrefs['success_field'] == 'subject')
			{
				$successText = $mail->GetHeaderValue('subject');
			}
			else
			{
				$successText = $mail->GetTextParts();
				if(isset($successText['text']))
					$successText = $successText['text'];
				else
					$successText = $successText['html'];
			}
			$success = preg_match('/'.$statusPrefs['success_regex'].'/', $successText);

			// process
			if($this->_processStatusReport($codeRegs[1], $success))
				return(BM_DELETE);
		}
		$res->Free();

		return(BM_OK);
	}

	/**
	 * user interface handler
	 *
	 * @param string $file
	 * @param string $action
	 */
	function FileHandler($file, $action)
	{
		global $tpl, $thisUser;

		if($file == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/faxPluginStatusPush/') !== false)
		{
			$this->_httpStatusPush();
			exit();
		}

		if($file != 'start.php' || $action != 'faxPlugin')
			return;

		if(!isset($thisUser) || !is_object($thisUser))
			return;

		if(!$this->GetGroupOptionValue('fax'))
			return;

		$tpl->assign('activeTab', 		'fax');
		$tpl->assign('pageToolbarFile', 'li/sms.toolbar.tpl');
		$tpl->assign('pageMenuFile',	$this->_templatePath('modfax.user.sidebar.tpl'));
		$tpl->assign('accBalance', 		$thisUser->GetBalance());

		// b1gMail 7.4 captcha concept
		if(file_exists(B1GMAIL_DIR . 'serverlib/captcha.class.php'))
		{
			if(!class_exists('BMCaptcha'))
				include(B1GMAIL_DIR . 'serverlib/captcha.class.php');
		}

		// older versions
		else
		{
			if(!class_exists('Safecode'))
				include(B1GMAIL_DIR . 'serverlib/safecode.class.php');
		}

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'compose';

		if($_REQUEST['do'] == 'compose')
			$this->_composeUserPage();
		else if($_REQUEST['do'] == 'addressBook')
			$this->_addressBookUserPage();
		else if($_REQUEST['do'] == 'outbox')
			$this->_outboxUserPage();
	}

	/**
	 * HTTP status push
	 *
	 */
	function _httpStatusPush()
	{
		global $db;

		$res = $db->Query('SELECT `faxgateid`,`status_prefs` FROM {pre}modfax_gateways WHERE `status_mode`=?',
			MODFAX_STATUSMODE_HTTP);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$statusPrefs = @unserialize($row['status_prefs']);
			if(!is_array($statusPrefs))
				continue;

			if(!isset($_REQUEST[$statusPrefs['code_param']])
				|| !isset($_REQUEST[$statusPrefs['result_param']]))
				continue;

			$statusCode = $_REQUEST[$statusPrefs['code_param']];

			$res2 = $db->Query('SELECT COUNT(*) FROM {pre}modfax_outbox WHERE `statuscode`=? AND `faxgateid`=?',
				$statusCode,
				$row['faxgateid']);
			list($outboxCount) = $res2->FetchArray(MYSQLI_NUM);
			$res2->Free();

			if($outboxCount < 1)
				continue;

			$success = preg_match('/'.$statusPrefs['result_regex'].'/', $_REQUEST[$statusPrefs['result_param']]);
			if($this->_processStatusReport($statusCode, $success))
			{
				echo('1');
				exit();
			}
		}
		$res->Free();

		echo('0');
		exit();
	}

	/**
	 * show fax address book
	 *
	 */
	function _addressBookUserPage()
	{
		global $tpl, $userRow;

		// open addressbook
		if(!class_exists('BMAddressbook'))
			include(B1GMAIL_DIR . 'serverlib/addressbook.class.php');
		$book = _new('BMAddressbook', array($userRow['id']));

		// load addresses
		$addresses = array();
		$addressBook = $book->GetAddressBook('*', -1, 'nachname', 'asc');
		foreach($addressBook as $id=>$entry)
		{
			if(trim($entry['fax']) != '' || trim($entry['work_fax']) != '')
				$addresses[] = array('firstname' 		=> $entry['vorname'],
										'lastname'		=> $entry['nachname'],
										'fax'			=> $entry['fax'],
										'work_fax'		=> $entry['work_fax'],
										'id'			=> $entry['id']);
		}

		// display page
		$tpl->assign('addresses', $addresses);
		$tpl->display($this->_templatePath('modfax.user.addressbook.tpl'));
	}

	/**
	 * show fax outbox page
	 *
	 */
	function _outboxUserPage()
	{
		global $tpl, $userRow, $db, $lang_user, $bm_prefs;

		// open webdisk
		if(!class_exists('BMWebdisk'))
			include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');
		$webdisk = _new('BMWebdisk', array($userRow['id']));

		// delete
		if(isset($_REQUEST['delete']))
		{
			// delete pdf file
			$res = $db->Query('SELECT `diskfileid` FROM {pre}modfax_outbox WHERE `userid`=? AND `faxid`=?',
				$userRow['id'],
				(int)$_REQUEST['delete']);
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				if($webdisk->GetFileInfo($row['diskfileid']) !== false)
					$webdisk->DeleteFile($row['diskfileid']);
			}
			$res->Free();

			// delete outbox entry
			$db->Query('DELETE FROM {pre}modfax_outbox WHERE `userid`=? AND `faxid`=?',
				$userRow['id'],
				(int)$_REQUEST['delete']);
		}

		// mass delete
		if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] == 'delete'
			&& isset($_REQUEST['fax']) && is_array($_REQUEST['fax']))
		{
			foreach($_REQUEST['fax'] as $faxID)
			{
				// delete pdf file
				$res = $db->Query('SELECT `diskfileid` FROM {pre}modfax_outbox WHERE `userid`=? AND `faxid`=?',
					$userRow['id'],
					(int)$faxID);
				while($row = $res->FetchArray(MYSQLI_ASSOC))
				{
					if($webdisk->GetFileInfo($row['diskfileid']) !== false)
						$webdisk->DeleteFile($row['diskfileid']);
				}
				$res->Free();

				// delete outbox entry
				$db->Query('DELETE FROM {pre}modfax_outbox WHERE `userid`=? AND `faxid`=?',
					$userRow['id'],
					(int)$faxID);
			}
		}

		// get fax count
		$res = $db->Query('SELECT COUNT(*) FROM {pre}modfax_outbox WHERE `userid`=?',
			$userRow['id']);
		list($faxCount) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		// calculate page stuff
		$entriesPerPage = (int)$bm_prefs['ordner_proseite'];
		$pageNo 		= (isset($_REQUEST['page']))
							? (int)$_REQUEST['page']
							: 1;
		$pageCount 		= max(1, ceil($faxCount / max(1, $entriesPerPage)));
		$pageNo 		= min($pageCount, max(1, $pageNo));

		// get sort info
		$sortColumns 	= array('tono', 'pages', 'date', 'status');
		$sortColumn 	= (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
							? $_REQUEST['sort']
							: 'date';
		$sortOrder 		= (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
							? $_REQUEST['order']
							: 'desc';
		$sortOrderFA = ($sortOrder=="desc")?'fa-arrow-down': 'fa-arrow-up';

		// fetch
		$outbox = array();
		$res = $db->Query('SELECT * FROM {pre}modfax_outbox WHERE `userid`=? ORDER BY `' . $sortColumn . '` ' . $sortOrder . ' LIMIT '
					. (int)(($pageNo-1) * $entriesPerPage) . ', ' . (int)$entriesPerPage,
			$userRow['id']);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['fileAvailable']	= $webdisk->GetFileInfo($row['diskfileid']) !== false;
			$row['statusText']		= $lang_user['modfax_status' . $row['status']];
			$outbox[$row['faxid']] 	= $row;
		}
		$res->Free();

		// page output
		$tpl->assign('pageNo', 			$pageNo);
		$tpl->assign('pageCount', 		$pageCount);
		$tpl->assign('sortColumn', 		$sortColumn);
		$tpl->assign('sortOrder', 		$sortOrderFA);
		$tpl->assign('sortOrderInv', 	$sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('outbox', 			$outbox);
		$tpl->assign('pageTitle',		$lang_user['modfax_outbox']);
		$tpl->assign('pageContent',		$this->_templatePath('modfax.user.outbox.tpl'));
		$tpl->display('li/index.tpl');
	}

	/**
	 * show fax compose page
	 *
	 */
	function _composeUserPage()
	{
		global $tpl, $userRow, $thisUser, $lang_user;

		$tpl->assign('pageTitle',	$lang_user['modfax_send']);

		if(!isset($_REQUEST['do2']))
		{
			if(!is_array($defaultTemplate = @unserialize($this->prefs['default_template'])))
				$defaultTemplate = array(0 => MODFAX_BLOCK_TEXT);

			if($this->prefs['send_safecode'])
			{
				if(class_exists('BMCaptcha'))
				{
					$captcha = BMCaptcha::createDefaultProvider();
					$tpl->assign('captchaHTML', $captcha->getHTML());
					$tpl->assign('captchaInfo', $captcha->getInfo());
				}
				else
				{
					$tpl->assign('codeID', 	Safecode::RequestCode());
				}
			}

			$tpl->assign('userRow', 	$userRow);
			$tpl->assign('faxPrefs', 	$this->prefs);
			$tpl->assign('defaultTpl',	$defaultTemplate);
			$tpl->assign('pageContent', $this->_templatePath('modfax.user.compose.tpl'));
			$tpl->display('li/index.tpl');
		}

		else if($_REQUEST['do2'] == 'prepareSend')
		{
			echo '<script>' . "\n";
			echo '<!--' . "\n";

			$error			= false;

			$fromName 		= $this->prefs['allow_ownname'] && isset($_REQUEST['fromname']) ? $_REQUEST['fromname'] : $this->prefs['default_name'];
			$fromNo 		= $this->prefs['allow_ownno'] && isset($_REQUEST['fromno'])		? $_REQUEST['fromno'] 	: $this->prefs['default_no'];
			$toNo			= $_REQUEST['to'];

			// check 'from' number
			if($this->prefs['allow_ownno'] && strlen(trim($fromNo)) < 4
				|| ($this->prefs['allow_ownname'] && strlen(trim($fromName)) < 2))
			{
				$error 		= $lang_user['modfax_fromerr'];
			}
			else
			{
				// parse 'to' number
				$to				= false;
				if(preg_match('/^[0-9\+]/', $toNo))
					$to			= $this->_parseNo($toNo);
				if($to)
				{
					// generate PDF file
					$pageCount 		= 0;
					$pdfTempFileID 	= $this->_generatePDF($fromName, $fromNo, $toNo, $_REQUEST['block'], $pageCount);

					// calculate price
					$faxgateID		= 0;
					$faxPrice		= $this->_calculatePrice($to, $pageCount, $faxgateID);
					if($faxPrice === false)
					{
						// number not supported
						$error		= $lang_user['modfax_toforbiddenerr'];
					}
					else
					{
						// switch to step 2
						printf('    parent.window.faxFormStep2(%d, %d, %d);' . "\n", $pdfTempFileID, $pageCount, $faxPrice);
					}
				}
				else
					$error 		= $lang_user['modfax_toerr'];
			}

			if($error)
			{
				printf('    parent.window.faxFormSetError(\'%s\');' . "\n", addslashes($error));
			}

			echo '//-->' . "\n";
			echo '</script>' . "\n";
		}

		else if($_REQUEST['do2'] == 'send'
			&& isset($_REQUEST['fileID']))
		{
			$tempFileID = (int)$_REQUEST['fileID'];

			// check if fax pdf file belongs to us
			if(!ValidTempFile($userRow['id'], $tempFileID))
				die('Invalid fileID');

			// captcha?
			$captchaOK = true;
			if($this->prefs['send_safecode'])
			{
				$captchaOK = false;

				// >= v7.4
				if(class_exists('BMCaptcha'))
				{
					$captchaOK = BMCaptcha::createDefaultProvider()->check();
				}

				// < v7.4
				else
				{
					if(strlen($code = Safecode::GetCode((int)$_REQUEST['codeID'])) >= 4
						&& trim(strtolower($_REQUEST['safecode'])) == strtolower($code))
					{
						$captchaOK = true;
					}
					Safecode::ReleaseCode((int)$_REQUEST['codeID']);
				}
			}

			// safecode?
			if(!$captchaOK)
			{
				$tpl->assign('msg', $lang_user['invalidcode']);
				$tpl->assign('pageContent', 'li/error.tpl');
			}
			else
			{
				// get params
				$fromName 		= $this->prefs['allow_ownname'] ? $_REQUEST['fromname'] : $this->prefs['default_name'];
				$fromNo 		= $this->prefs['allow_ownno'] 	? $_REQUEST['fromno'] 	: $this->prefs['default_no'];
				$toNo			= $_REQUEST['to'];
				$result 		= false;

				// parse 'to' number
				$to				= $this->_parseNo($toNo);
				if($to)
				{
					// try to send fax
					$result = $this->_sendFax($tempFileID, $fromName, $fromNo, $to, $thisUser);
				}

				// display result
				if($result)
				{
					ReleaseTempFile($userRow['id'], $tempFileID);

					$tpl->assign('accBalance', 	$thisUser->GetBalance());
					$tpl->assign('title', 		$lang_user['modfax_send']);
					$tpl->assign('msg', 		$lang_user['modfax_sent']);
					$tpl->assign('backLink', 	'start.php?action=faxPlugin&sid=' . session_id());
					$tpl->assign('pageContent', 'li/msg.tpl');
				}
				else
				{
					$tpl->assign('msg', 		$lang_user['modfax_sendfailed']);
					$tpl->assign('pageContent', 'li/error.tpl');
				}
			}

			$tpl->assign('pageTitle', $lang_user['modfax_send']);
			$tpl->display('li/index.tpl');
		}

		else if($_REQUEST['do2'] == 'downloadPreview'
			&& isset($_REQUEST['fileID']))
		{
			$tempFileID = (int)$_REQUEST['fileID'];

			// check if fax pdf file belongs to us
			if(!ValidTempFile($userRow['id'], $tempFileID))
				die('Invalid fileID');

			// send to browser
			header('Pragma: public');
			header('Content-Disposition: attachment; filename="fax-preview.pdf"');
			header('Content-Type: application/pdf');
			header(sprintf('Content-Length: %d',
				filesize(TempFileName($tempFileID))));
			SendFile(TempFileName($tempFileID));
			exit();
		}

		else if($_REQUEST['do2'] == 'getFormBlockCode')
		{
			$tpl->assign('faxPrefs',	$this->prefs);
			$tpl->assign('userRow', 	$userRow);
			$tpl->display($this->_templatePath('modfax.user.compose.block.tpl'));
		}

		else if($_REQUEST['do2'] == 'addPDFFile'
				&& isset($_REQUEST['blockID']))
		{
			$tpl->assign('title', 		$lang_user['modfax_browsepdf']);
			$tpl->assign('text', 		$lang_user['modfax_browsetext']);
			$tpl->assign('formAction', 	'start.php?action=faxPlugin&do2=uploadPDFFile&blockID=' . (int)$_REQUEST['blockID'] . '&sid=' . session_id());
			$tpl->assign('fieldName', 	'pdfFile');
			$tpl->display('li/dialog.openfile.tpl');
		}

		else if($_REQUEST['do2'] == 'uploadPDFFile'
				&& isset($_REQUEST['blockID']))
		{
			$blockID = (int)$_REQUEST['blockID'];
			$tempFileID = RequestTempFile($userRow['id'], time()+4*TIME_ONE_HOUR);
			$tempFileName = TempFileName($tempFileID);

			echo '<script type="text/javascript">' . "\n";
			echo '<!--' . "\n";

			// get uploaded file
			$file = getUploadedFile('pdfFile', $tempFileName);
			if(!$file)
			{
				ReleaseTempFile($userRow['id'], $tempFileID);
			}
			else
			{
				// get first file line
				$fp = fopen($tempFileName, 'r');
				$firstLine = fgets($fp, 128);
				fclose($fp);

				// check type + version
				if(substr($firstLine, 0, 5) == '%PDF-'
					&& sscanf($firstLine, '%%PDF-%d.%d', $pdfMajor, $pdfMinor) == 2)
				{
					$this->_ensureFPDFisLoaded();

					// get page count
					$fpdi = new Fpdi();
					$pageCount = @$fpdi->setSourceFile($tempFileName);
					unset($fpdi);

					// ok?
					if($pageCount >= 1)
					{
						echo 'parent.document.getElementById(\'block_' . $blockID . '_filename\').innerHTML = \'' . addslashes($file['name']) . '\';' . "\n";
						echo 'parent.document.getElementById(\'block_' . $blockID . '_pages\').innerHTML = \'' . $pageCount . '\';' . "\n";
						echo 'parent.document.getElementById(\'block_' . $blockID . '_fileid\').value = \'' . $tempFileID . '\';' . "\n";
					}
					else
					{
						ReleaseTempFile($userRow['id'], $tempFileID);
						echo 'alert(\'' . $lang_user['modfax_pdfpageerror'] . '\');' . "\n";
					}
				}

				else
				{
					ReleaseTempFile($userRow['id'], $tempFileID);
					echo 'alert(\'' . $lang_user['modfax_pdferror'] . '\');' . "\n";
				}
			}

			echo 'parent.hideOverlay();' . "\n";
			echo '//-->' . "\n";
			echo '</script>' . "\n";
		}
	}


	//
	// admin interface
	//

	/**
	 * get notices for ACP start page
	 *
	 * @return array
	 */
	function getNotices()
	{
		global $lang_admin;

		$result = array();
		if(!file_exists($this->_fpdfDir)
			|| !file_exists($this->_fpdfDir . 'fpdf.php')
			|| !file_exists($this->_fpdfDir . 'fpdi/src/autoload.php'))
		{
			$result[] = array(
				'type'	=> 'error',
				'text'	=> $lang_admin['modfax_fpdferror']
			);
		}

		return($result);
	}

	/**
	 * admin interface handler
	 *
	 */
	function AdminHandler()
	{
		global $tpl, $lang_admin;

		// default action
		if(!isset($_REQUEST['action']))
			$_REQUEST['action'] = 'overview';

		// tabs
		$tabs = array(
			0 => array(
				'title'		=> $lang_admin['overview'],
				'icon'		=> '../plugins/templates/images/modfax_logo.png',
				'link'		=> $this->_adminLink() . '&',
				'active'	=> $_REQUEST['action'] == 'overview'
			),
			1 => array(
				'title'		=> $lang_admin['modfax_prefixes'],
				'icon'		=> '../plugins/templates/images/modfax_prefix.png',
				'link'		=> $this->_adminLink() . '&action=prefixes&',
				'active'	=> $_REQUEST['action'] == 'prefixes'
			),
			2 => array(
				'title'		=> $lang_admin['gateways'],
				'icon'		=> '../plugins/templates/images/modfax_gateway.png',
				'link'		=> $this->_adminLink() . '&action=gateways&simple=true&',
				'active'	=> $_REQUEST['action'] == 'gateways'
			),
			3 => array(
				'title'		=> $lang_admin['modfax_signatures'],
				'icon'		=> '../plugins/templates/images/modfax_sig32.png',
				'link'		=> $this->_adminLink() . '&action=signatures&',
				'active'	=> $_REQUEST['action'] == 'signatures'
			),
			4 => array(
				'title'		=> $lang_admin['stats'],
				'icon'		=> '../plugins/templates/images/modfax_stats32.png',
				'link'		=> $this->_adminLink() . '&action=stats&',
				'active'	=> $_REQUEST['action'] == 'stats'
			),
			5 => array(
				'title'		=> $lang_admin['prefs'],
				'relIcon'	=> 'ico_prefs_common.png',
				'link'		=> $this->_adminLink() . '&action=prefs&',
				'active'	=> $_REQUEST['action'] == 'prefs'
			)
		);
		$tpl->assign('tabs', 	$tabs);
		$tpl->assign('pageURL',	$this->_adminLink());

		// actions
		if($_REQUEST['action'] == 'overview')
			$this->_overviewAdminPage();
		else if($_REQUEST['action'] == 'prefixes')
			$this->_prefixesAdminPage();
		else if($_REQUEST['action'] == 'gateways')
		{
			if(isset($_REQUEST['simple']))
				$this->_simpleGatewaysAdminPage();
			else
				$this->_gatewaysAdminPage();
		}
		else if($_REQUEST['action'] == 'signatures')
			$this->_signaturesAdminPage();
		else if($_REQUEST['action'] == 'stats')
			$this->_statsAdminPage();
		else if($_REQUEST['action'] == 'prefs')
			$this->_prefsAdminPage();
	}

	/**
	 * get stat data
	 *
	 * @param int $type Stat type
	 * @param int $time Stat time
	 * @return array
	 */
	function _getStatData($type, $time)
	{
		global $db;

		// load class, if needed
		if(!class_exists('BMCalendar'))
			include(B1GMAIL_DIR . 'serverlib/calendar.class.php');

		// pepare result array
		$result = array();
		for($i=1; $i<=BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); $i++)
			$result[(int)$i] = array($type => 0);

		// collect data
		for($i=1; $i<=BMCalendar::GetDaysInMonth(date('m', $time), date('Y', $time)); $i++)
		{
			$res = $db->Query('SELECT SUM(`count`) FROM {pre}modfax_stats WHERE `d`=? AND `m`=? AND `y`=? AND `type`=?',
				$i,
				date('m', $time),
				date('Y', $time),
				$type);
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			$result[(int)$i] = array($type => (int)$count);
		}

		return($result);
	}

	/**
	 * show stats admin page
	 *
	 */
	function _statsAdminPage()
	{
		global $db, $tpl, $lang_admin;

		if(!class_exists('BMChart'))
			include(B1GMAIL_DIR . 'serverlib/chart.class.php');

		// reset?
		if(isset($_REQUEST['do']) && $_REQUEST['do']=='reset')
		{
			$db->Query('TRUNCATE TABLE {pre}modfax_stats');
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

			$chart = new BMChart(sprintf('%s (%d/%d)', $lang_admin['modfax_stat_'.$statTypeItem], date('m', $time), date('Y', $time)),
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
		$statTypes = array('faxes', 'credits');

		// stat type
		$statType = isset($_REQUEST['statType'])
					? $_REQUEST['statType']
					: $statTypes[0];

		// special types
		$statsSpecial = array(
			'faxes'		=> array(MODFAX_EVENT_SENDFAX, MODFAX_EVENT_FAX_OK, MODFAX_EVENT_FAX_ERROR),
			'credits'	=> array(MODFAX_EVENT_CREDITS, MODFAX_EVENT_REFUNDS)
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
			$maxVal = $sum = 0;

			foreach($statData as $val)
			{
				if($val[$statTypeItem] > $maxVal)
					$maxVal = $val[$statTypeItem];
				$sum += $val[$statTypeItem];
			}

			if($maxVal%10 != 0)
				$maxVal += (10-$maxVal%10);

			$heights = array();
			foreach($statData as $day=>$val)
			{
				$theVal = $val[$statTypeItem];

				if($maxVal <= 0)
					$heights[$day] = 0;
				else
					$heights[$day] = round(($theVal/$maxVal)*240, 0);
			}

			$yScale = array();
			for($i=10; $i>0; $i--)
			{
				$scale = $maxVal == 0 ? '' : round($maxVal*$i/10, 1);
				$yScale[$i] = $scale;
			}

			$stats[] = array(
				'title'		=> sprintf('%s (%d/%d)', $lang_admin['modfax_stat_'.$statTypeItem], date('m', $time), date('Y', $time)),
				'key'		=> $statTypeItem,
				'data'		=> $statData,
				'maxVal'	=> $maxVal,
				'yScale'	=> $yScale,
				'heights'	=> $heights,
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
		$tpl->assign('page', 		$this->_templatePath('modfax.admin.stats.tpl'));
	}

	/**
	 * show prefs admin page
	 *
	 */
	function _prefsAdminPage()
	{
		global $db, $tpl;

		// save?
		if(isset($_REQUEST['save']))
		{
			$defaultCountryPrefix = preg_replace('/^(\+|00|0)/', '', $_REQUEST['default_country_prefix']);

			if(isset($_REQUEST['tpl_blocks']) && is_array($_REQUEST['tpl_blocks']) && count($_REQUEST['tpl_blocks']) > 0)
			{
				$defaultTemplate = array();
				$i = 0;

				foreach($_REQUEST['tpl_blocks'] as $val)
					if($val >= 0)
						$defaultTemplate[$i++] = $val;
			}

			if(!isset($defaultTemplate) || !is_array($defaultTemplate) || count($defaultTemplate) < 1)
				$defaultTemplate = array(0 => MODFAX_BLOCK_TEXT);

			$db->Query('UPDATE {pre}modfax_prefs SET `allow_ownname`=?,`allow_ownno`=?,`allow_pdf`=?,`default_name`=?,`default_no`=?,`send_safecode`=?,`default_country_prefix`=?,`default_faxgateid`=?,`refund_on_error`=?,`default_template`=?',
				isset($_REQUEST['allow_ownname']) ? 1 : 0,
				isset($_REQUEST['allow_ownno']) ? 1 : 0,
				isset($_REQUEST['allow_pdf']) ? 1 : 0,
				$_REQUEST['default_name'],
				$_REQUEST['default_no'],
				isset($_REQUEST['send_safecode']) ? 1 : 0,
				$defaultCountryPrefix,
				(int)$_REQUEST['default_faxgateid'],
				isset($_REQUEST['refund_on_error']) ? 1 : 0,
				serialize($defaultTemplate));

			$this->prefs = $this->_getPrefs();
		}

		// template
		if(!is_array($defaultTemplate = @unserialize($this->prefs['default_template'])))
			$defaultTemplate = array(0 => MODFAX_BLOCK_TEXT);
		$defaultTemplate = array_pad($defaultTemplate, 5, -1);

		// fetch gateways
		$gateways = array();
		$res = $db->Query('SELECT `faxgateid`,`title` FROM {pre}modfax_gateways ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$gateways[$row['faxgateid']] = $row['title'];
		$res->Free();

		// assign
		$tpl->assign('tplBlocks',		$defaultTemplate);
		$tpl->assign('faxPrefs',		$this->prefs);
		$tpl->assign('gateways',		$gateways);
		$tpl->assign('page',			$this->_templatePath('modfax.admin.prefs.tpl'));
	}

	/**
	 * get stats for event for overview page
	 *
	 * @param int $event Event ID
	 * @return array
	 */
	function _getOverviewStats($event)
	{
		global $db;

		$res = $db->Query('SELECT SUM(`count`) FROM {pre}modfax_stats WHERE `d`=? AND `m`=? AND `y`=? AND `type`=?',
			(int)date('d'), (int)date('m'), (int)date('Y'),
			$event);
		list($today) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$res = $db->Query('SELECT SUM(`count`) FROM {pre}modfax_stats WHERE `m`=? AND `y`=? AND `type`=?',
			(int)date('m'), (int)date('Y'),
			$event);
		list($month) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		$res = $db->Query('SELECT SUM(`count`) FROM {pre}modfax_stats WHERE `type`=?',
			$event);
		list($all) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		return(array((int)$today, (int)$month, (int)$all));
	}

	/**
	 * show overview admin page
	 *
	 */
	function _overviewAdminPage()
	{
		global $tpl, $currentLanguage;

		// stats: fax
		list($faxToday, $faxMonth, $faxAll) = $this->_getOverviewStats(MODFAX_EVENT_SENDFAX);

		// stats: fax errors
		list($errToday, $errMonth, $errAll) = $this->_getOverviewStats(MODFAX_EVENT_FAX_ERROR);

		// stats: credits
		list($creditsToday, $creditsMonth, $creditsAll) = $this->_getOverviewStats(MODFAX_EVENT_CREDITS);

		// stats: refunded credits
		list($refundsToday, $refundsMonth, $refundsAll) = $this->_getOverviewStats(MODFAX_EVENT_REFUNDS);

		// assign stats
		$tpl->assign('faxToday',			$faxToday);
		$tpl->assign('faxMonth',			$faxMonth);
		$tpl->assign('faxAll',				$faxAll);
		$tpl->assign('errToday',			$errToday);
		$tpl->assign('errMonth',			$errMonth);
		$tpl->assign('errAll',				$errAll);
		$tpl->assign('creditsToday',		$creditsToday);
		$tpl->assign('creditsMonth',		$creditsMonth);
		$tpl->assign('creditsAll',			$creditsAll);
		$tpl->assign('refundsToday',		$refundsToday);
		$tpl->assign('refundsMonth',		$refundsMonth);
		$tpl->assign('refundsAll',			$refundsAll);

		// assign
		$tpl->assign('notices',				$this->getNotices());
		$tpl->assign('version',				$this->version);
		$tpl->assign('lang',				$currentLanguage);
		$tpl->assign('page', $this->_templatePath('modfax.admin.overview.tpl'));
	}

	/**
	 * show signatures admin page
	 *
	 */
	function _signaturesAdminPage()
	{
		global $tpl, $db;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'overview';

		// assign groups
		$tpl->assign('groups',		BMGroup::GetSimpleGroupList());

		if($_REQUEST['do'] == 'overview')
		{
			// add?
			if(isset($_REQUEST['add']))
			{
				$groups	= is_array($_REQUEST['groups']) ? implode(',', $_REQUEST['groups']) : '*';
				$style 	= $showon = 0;

				if(isset($_REQUEST['style']) && is_array($_REQUEST['style']))
					foreach($_REQUEST['style'] as $val)
						$style |= $val;

				if(isset($_REQUEST['showon']) && is_array($_REQUEST['showon']))
					foreach($_REQUEST['showon'] as $val)
						$showon |= $val;

				$db->Query('INSERT INTO {pre}modfax_signatures(`groups`,`paused`,`weight`,`fontname`,`fontsize`,`align`,`style`,`text`,`showon`,`margin`) '
					. 'VALUES(?,?,?,?,?,?,?,?,?,?)',
					$groups,
					isset($_REQUEST['paused']) ? 1 : 0,
					(int)$_REQUEST['weight'],
					$_REQUEST['fontname'],
					$_REQUEST['fontsize'],
					$_REQUEST['align'],
					$style,
					$_REQUEST['text'],
					$showon,
					(int)$_REQUEST['margin']);
			}

			// pause?
			if(isset($_REQUEST['deactivate']))
			{
				$db->Query('UPDATE {pre}modfax_signatures SET `paused`=1 WHERE `signatureid`=?',
					(int)$_REQUEST['deactivate']);
			}

			// continue?
			if(isset($_REQUEST['activate']))
			{
				$db->Query('UPDATE {pre}modfax_signatures SET `paused`=0 WHERE `signatureid`=?',
					(int)$_REQUEST['activate']);
			}

			// delete?
			if(isset($_REQUEST['delete']))
			{
				$db->Query('DELETE FROM {pre}modfax_signatures WHERE `signatureid`=?',
					(int)$_REQUEST['delete']);
			}

			// mass action?
			if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] != '-'
				&& isset($_REQUEST['sigs']) && is_array($_REQUEST['sigs']))
			{
				$sigs = $_REQUEST['sigs'];

				if($_REQUEST['massAction'] == 'pause')
				{
					$db->Query('UPDATE {pre}modfax_signatures SET `paused`=1 WHERE `signatureid` IN ?',
						$sigs);
				}
				else if($_REQUEST['massAction'] == 'continue')
				{
					$db->Query('UPDATE {pre}modfax_signatures SET `paused`=0 WHERE `signatureid` IN ?',
						$sigs);
				}
				else if($_REQUEST['massAction'] == 'delete')
				{
					$db->Query('DELETE FROM {pre}modfax_signatures WHERE `signatureid` IN ?',
						$sigs);
				}
			}

			// fetch
			$signatures = array();
			$res = $db->Query('SELECT * FROM {pre}modfax_signatures ORDER BY `text` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$row['displayText'] = nl2br(HTMLFormat($row['text']));
				$signatures[$row['signatureid']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('signatures', 	$signatures);
			$tpl->assign('page', 		$this->_templatePath('modfax.admin.signatures.tpl'));
		}

		else if($_REQUEST['do'] == 'edit' && isset($_REQUEST['id']))
		{
			// save?
			if(isset($_REQUEST['save']))
			{
				$groups	= is_array($_REQUEST['groups']) ? implode(',', $_REQUEST['groups']) : '*';
				$style 	= $showon = 0;

				if(isset($_REQUEST['style']) && is_array($_REQUEST['style']))
					foreach($_REQUEST['style'] as $val)
						$style |= $val;

				if(isset($_REQUEST['showon']) && is_array($_REQUEST['showon']))
					foreach($_REQUEST['showon'] as $val)
						$showon |= $val;

				$db->Query('UPDATE {pre}modfax_signatures SET `groups`=?,`paused`=?,`weight`=?,`fontname`=?,`fontsize`=?,`align`=?,`style`=?,`text`=?,`showon`=?,`margin`=? WHERE `signatureid`=?',
					$groups,
					isset($_REQUEST['paused']) ? 1 : 0,
					(int)$_REQUEST['weight'],
					$_REQUEST['fontname'],
					$_REQUEST['fontsize'],
					$_REQUEST['align'],
					$style,
					$_REQUEST['text'],
					$showon,
					(int)$_REQUEST['margin'],
					$_REQUEST['id']);

				header('Location: ' . $this->_adminLink() . '&action=signatures&sid=' . session_id());
				exit();
			}

			// fetch
			$res = $db->Query('SELECT * FROM {pre}modfax_signatures WHERE `signatureid`=?',
				(int)$_REQUEST['id']);
			if($res->RowCount() != 1)
				die('Invalid ID');
			$sig = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			if($sig['align'] == 'L')
				$sig['alignText'] = 'left';
			else if($sig['align'] == 'C')
				$sig['alignText'] = 'center';
			else if($sig['align'] == 'R')
				$sig['alignText'] = 'right';
			else if($sig['align'] == 'J')
				$sig['alignText'] = 'justify';

			$sigGroups = explode(',', $sig['groups']);
			$groupList = BMGroup::GetSimpleGroupList();
			foreach($groupList as $key=>$val)
				$groupList[$key]['checked'] = in_array($key, $sigGroups);

			// assign
			$tpl->assign('sig', 		$sig);
			$tpl->assign('groups',		$groupList);
			$tpl->assign('page', 		$this->_templatePath('modfax.admin.signatures.edit.tpl'));
		}
	}

	/**
	 * show prefixes admin page
	 *
	 */
	function _prefixesAdminPage()
	{
		global $tpl, $db;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'overview';

		// fetch gateways
		$gateways = array();
		$res = $db->Query('SELECT `faxgateid`,`title` FROM {pre}modfax_gateways ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
			$gateways[$row['faxgateid']] = $row['title'];
		$res->Free();

		// assign prefs + gateways
		$tpl->assign('faxPrefs',	$this->prefs);
		$tpl->assign('gateways',	$gateways);

		if($_REQUEST['do'] == 'overview')
		{
			// add?
			if(isset($_REQUEST['add']))
			{
				$countryPrefix 	= preg_replace('/^(\+|00|0)/', '', $_REQUEST['country_prefix']);
				$prefix 		= preg_replace('/^(\+|00|0)/', '', $_REQUEST['prefix']);

				$db->Query('INSERT INTO {pre}modfax_prefixes(`country_prefix`,`prefix`,`faxgateid`,`price_firstpage`,`price_nextpages`) VALUES(?,?,?,?,?)',
					$countryPrefix,
					$prefix,
					(int)$_REQUEST['faxgateid'],
					(int)$_REQUEST['price_firstpage'],
					(int)$_REQUEST['price_nextpages']);
			}

			// delete?
			if(isset($_REQUEST['delete']))
			{
				$db->Query('DELETE FROM {pre}modfax_prefixes WHERE `prefixid`=?',
					(int)$_REQUEST['delete']);
			}

			// mass delete
			if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] == 'delete'
				&& isset($_REQUEST['prefixes']) && is_array($_REQUEST['prefixes']))
			{
				$db->Query('DELETE FROM {pre}modfax_prefixes WHERE `prefixid` IN ?',
					$_REQUEST['prefixes']);
			}

			// fetch prefixes
			$prefixes = array();
			$res = $db->Query('SELECT * FROM {pre}modfax_prefixes ORDER BY `country_prefix` ASC,`prefix` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$prefixes[$row['prefixid']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('prefixes', 	$prefixes);
			$tpl->assign('page', 		$this->_templatePath('modfax.admin.prefixes.tpl'));
		}

		else if($_REQUEST['do'] == 'edit' && isset($_REQUEST['id']))
		{
			// save?
			if(isset($_REQUEST['save']))
			{
				$countryPrefix 	= preg_replace('/^(\+|00|0)/', '', $_REQUEST['country_prefix']);
				$prefix 		= preg_replace('/^(\+|00|0)/', '', $_REQUEST['prefix']);

				$db->Query('UPDATE {pre}modfax_prefixes SET `country_prefix`=?,`prefix`=?,`faxgateid`=?,`price_firstpage`=?,`price_nextpages`=? WHERE `prefixid`=?',
					$countryPrefix,
					$prefix,
					(int)$_REQUEST['faxgateid'],
					(int)$_REQUEST['price_firstpage'],
					(int)$_REQUEST['price_nextpages'],
					(int)$_REQUEST['id']);

				header('Location: ' . $this->_adminLink() . '&action=prefixes&sid=' . session_id());
				exit();
			}

			// fetch
			$res = $db->Query('SELECT * FROM {pre}modfax_prefixes WHERE `prefixid`=?',
				(int)$_REQUEST['id']);
			if($res->RowCount() != 1)
				die('Invalid ID');
			$prefix = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			// assign
			$tpl->assign('prefix', 		$prefix);
			$tpl->assign('page', 		$this->_templatePath('modfax.admin.prefixes.edit.tpl'));
		}
	}

	/**
	 * show simple gateways admin page
	 *
	 */
	function _simpleGatewaysAdminPage()
	{
		global $tpl, $db;

		// save?
		if(isset($_REQUEST['save']) && isset($_REQUEST['gateways']) && is_array($_REQUEST['gateways']))
		{
			foreach($_REQUEST['gateways'] as $gatewayID=>$gatewayPrefs)
				$db->Query('UPDATE {pre}modfax_gateways SET `user`=?,`pass`=? WHERE `faxgateid`=?',
					$gatewayPrefs['user'],
					$gatewayPrefs['pass'],
					$gatewayID);
		}

		// fetch
		$gateways = array();
		$res = $db->Query('SELECT `faxgateid`,`title`,`protocol`,`user`,`pass` FROM {pre}modfax_gateways ORDER BY `title` ASC');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			$row['default'] = $this->prefs['default_faxgateid'] == $row['faxgateid'];
			$gateways[$row['faxgateid']] = $row;
		}
		$res->Free();

		// assign
		$tpl->assign('gateways', $gateways);
		$tpl->assign('page', $this->_templatePath('modfax.admin.gateways.simple.tpl'));
	}

	/**
	 * show gateways admin page
	 *
	 */
	function _gatewaysAdminPage()
	{
		global $tpl, $db, $currentLanguage;

		if(!isset($_REQUEST['do']))
			$_REQUEST['do'] = 'overview';

		if($_REQUEST['do'] == 'overview')
		{
			// add
			if(isset($_REQUEST['add']))
			{
				$prefs = $statusPrefs = array();
				$protocol = $statusMode = 0;

				if((int)$_REQUEST['protocol'] == MODFAX_PROTOCOL_EMAIL)
				{
					$protocol						= MODFAX_PROTOCOL_EMAIL;
					$prefs['from']					= $_REQUEST['email_from'];
					$prefs['to']					= $_REQUEST['email_to'];
					$prefs['subject']				= $_REQUEST['email_subject'];
					$prefs['text']					= $_REQUEST['email_text'];
					$prefs['pdffile']				= $_REQUEST['email_pdffile'];
				}
				else if((int)$_REQUEST['protocol'] == MODFAX_PROTOCOL_HTTP)
				{
					$protocol						= MODFAX_PROTOCOL_HTTP;
					$prefs['url']					= $_REQUEST['http_url'];
					$prefs['request']				= $_REQUEST['http_request'];
					$prefs['returnvalue']			= $_REQUEST['http_returnvalue'];
				}

				if((int)$_REQUEST['status_mode'] == MODFAX_STATUSMODE_EMAIL)
				{
					$statusMode						= MODFAX_STATUSMODE_EMAIL;
					$statusPrefs['emailfrom']		= $_REQUEST['status_emailfrom'];
					$statusPrefs['emailto']			= $_REQUEST['status_emailto'];
					$statusPrefs['emailsubject']	= $_REQUEST['status_emailsubject'];
					$statusPrefs['code_field']		= $_REQUEST['status_code_field'];
					$statusPrefs['success_field']	= $_REQUEST['status_success_field'];
					$statusPrefs['code_regex']		= $_REQUEST['status_code_regex'];
					$statusPrefs['success_regex']	= $_REQUEST['status_success_regex'];
				}
				else if((int)$_REQUEST['status_mode'] == MODFAX_STATUSMODE_HTTP)
				{
					$statusMode						= MODFAX_STATUSMODE_HTTP;
					$statusPrefs['code_param']		= $_REQUEST['status_code_param'];
					$statusPrefs['result_param']	= $_REQUEST['status_result_param'];
					$statusPrefs['result_regex']	= $_REQUEST['status_result_regex'];
				}

				$db->Query('INSERT INTO {pre}modfax_gateways(`title`,`number_format`,`status_mode`,`status_prefs`,`protocol`,`prefs`,`user`,`pass`) VALUES(?,?,?,?,?,?,?,?)',
					$_REQUEST['title'],
					(int)$_REQUEST['number_format'],
					$statusMode,
					serialize($statusPrefs),
					$protocol,
					serialize($prefs),
					$_REQUEST['user'],
					$_REQUEST['pass']);
			}

			// delete
			if(isset($_REQUEST['delete']))
			{
				$db->Query('UPDATE {pre}modfax_prefixes SET `faxgateid`=0 WHERE `faxgateid`=?',
					$_REQUEST['delete']);
				$db->Query('DELETE FROM {pre}modfax_gateways WHERE `faxgateid`=?',
					$_REQUEST['delete']);
			}

			// mass delete
			if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] == 'delete'
				&& isset($_REQUEST['gateways']) && is_array($_REQUEST['gateways']))
			{
				$db->Query('UPDATE {pre}modfax_prefixes SET `faxgateid`=0 WHERE `faxgateid` IN ?',
					$_REQUEST['gateways']);
				$db->Query('DELETE FROM {pre}modfax_gateways WHERE `faxgateid` IN ?',
					$_REQUEST['gateways']);
			}

			// set default
			if(isset($_REQUEST['massAction']) && $_REQUEST['massAction'] == 'setdefault'
				&& isset($_REQUEST['gateways']) && is_array($_REQUEST['gateways']))
			{
				$gatewayID = array_shift($_REQUEST['gateways']);
				$db->Query('UPDATE {pre}modfax_prefs SET `default_faxgateid`=?',
					$gatewayID);
				$this->prefs['default_faxgateid'] = $gatewayID;
			}

			// fetch
			$gateways = array();
			$res = $db->Query('SELECT `faxgateid`,`title`,`protocol` FROM {pre}modfax_gateways ORDER BY `title` ASC');
			while($row = $res->FetchArray(MYSQLI_ASSOC))
			{
				$row['default'] = $this->prefs['default_faxgateid'] == $row['faxgateid'];
				$gateways[$row['faxgateid']] = $row;
			}
			$res->Free();

			// assign
			$tpl->assign('gateways', $gateways);
			$tpl->assign('lang', $currentLanguage);
			$tpl->assign('page', $this->_templatePath('modfax.admin.gateways.tpl'));
		}

		else if($_REQUEST['do'] == 'edit'
			&& isset($_REQUEST['id']))
		{
			if(isset($_REQUEST['save'])
				&& isset($_REQUEST['id']))
			{
				$prefs = $statusPrefs = array();
				$protocol = $statusMode = 0;

				if((int)$_REQUEST['protocol'] == MODFAX_PROTOCOL_EMAIL)
				{
					$protocol						= MODFAX_PROTOCOL_EMAIL;
					$prefs['from']					= $_REQUEST['email_from'];
					$prefs['to']					= $_REQUEST['email_to'];
					$prefs['subject']				= $_REQUEST['email_subject'];
					$prefs['text']					= $_REQUEST['email_text'];
					$prefs['pdffile']				= $_REQUEST['email_pdffile'];
				}
				else if((int)$_REQUEST['protocol'] == MODFAX_PROTOCOL_HTTP)
				{
					$protocol						= MODFAX_PROTOCOL_HTTP;
					$prefs['url']					= $_REQUEST['http_url'];
					$prefs['request']				= $_REQUEST['http_request'];
					$prefs['returnvalue']			= $_REQUEST['http_returnvalue'];
				}

				if((int)$_REQUEST['status_mode'] == MODFAX_STATUSMODE_EMAIL)
				{
					$statusMode						= MODFAX_STATUSMODE_EMAIL;
					$statusPrefs['emailfrom']		= $_REQUEST['status_emailfrom'];
					$statusPrefs['emailto']			= $_REQUEST['status_emailto'];
					$statusPrefs['emailsubject']	= $_REQUEST['status_emailsubject'];
					$statusPrefs['code_field']		= $_REQUEST['status_code_field'];
					$statusPrefs['success_field']	= $_REQUEST['status_success_field'];
					$statusPrefs['code_regex']		= $_REQUEST['status_code_regex'];
					$statusPrefs['success_regex']	= $_REQUEST['status_success_regex'];
				}
				else if((int)$_REQUEST['status_mode'] == MODFAX_STATUSMODE_HTTP)
				{
					$statusMode						= MODFAX_STATUSMODE_HTTP;
					$statusPrefs['code_param']		= $_REQUEST['status_code_param'];
					$statusPrefs['result_param']	= $_REQUEST['status_result_param'];
					$statusPrefs['result_regex']	= $_REQUEST['status_result_regex'];
				}

				$db->Query('UPDATE {pre}modfax_gateways SET `title`=?,`number_format`=?,`status_mode`=?,`status_prefs`=?,`protocol`=?,`prefs`=?,`user`=?,`pass`=? WHERE `faxgateid`=?',
					$_REQUEST['title'],
					(int)$_REQUEST['number_format'],
					$statusMode,
					serialize($statusPrefs),
					$protocol,
					serialize($prefs),
					$_REQUEST['user'],
					$_REQUEST['pass'],
					$_REQUEST['id']);

				header('Location: ' . $this->_adminLink() . '&action=gateways&sid=' . session_id());
				exit();
			}

			// fetch
			$res = $db->Query('SELECT * FROM {pre}modfax_gateways WHERE `faxgateid`=?',
				$_REQUEST['id']);
			if($res->RowCount() == 0) die();
			$gateway = $res->FetchArray(MYSQLI_ASSOC);
			$res->Free();

			// assign
			$tpl->assign('gateway', $gateway);
			$tpl->assign('prefs', unserialize($gateway['prefs']));
			$tpl->assign('status_prefs', unserialize($gateway['status_prefs']));
			$tpl->assign('page', $this->_templatePath('modfax.admin.gateways.edit.tpl'));
		}
	}



	//
	// helper functions
	//

	/**
	 * fetch DB prefs
	 *
	 * @return array
	 */
	function _getPrefs()
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}modfax_prefs LIMIT 1');
		$prefs = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($prefs);
	}

	/**
	 * add statistic entry
	 *
	 * @param int $type Event type
	 * @param int $count Count (default: 1)
	 */
	function _add2Stat($type, $count = 1)
	{
		global $db;

		$d = date('d');
		$m = date('m');
		$y = date('Y');
		$id = 0;

		// is there already a row for this?
		$res = $db->Query('SELECT `statid` FROM {pre}modfax_stats WHERE `d`=? AND `m`=? AND `y`=? AND `type`=? LIMIT 1',
			$d,
			$m,
			$y,
			$type);
		if($res->RowCount() == 1)
		{
			list($id) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();
		}

		// update existing row...
		if($id != 0)
		{
			$db->Query('UPDATE {pre}modfax_stats SET `count`=`count`+'.(int)$count.' WHERE `statid`=?',
				$id);
		}

		// ...or insert new row
		else
		{
			$db->Query('INSERT INTO {pre}modfax_stats(`d`,`m`,`y`,`type`,`count`) VALUES(?,?,?,?,?)',
				$d,
				$m,
				$y,
				$type,
				$count);
		}
	}

	/**
	 * get signature for group
	 *
	 * @param int $groupID Group ID
	 * @return mixed false (i.e. no signature) or array with signature details
	 */
	function _getSignature($groupID)
	{
		global $db;

		$res = $db->Query('SELECT * FROM {pre}modfax_signatures '
				. 'WHERE `paused`=0 AND (`groups`=? OR (`groups`=? OR `groups` LIKE ? OR `groups` LIKE ? OR `groups` LIKE ?)) ORDER BY (`counter`/`weight`) ASC LIMIT 1',
				'*',
				$groupID,
				$groupID . ',%',
				'%,' . $groupID . ',%',
				'%,' . $groupID);
		if($res->RowCount() != 1) return(false);
		$result = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		return($result);
	}

	/**
	 * increment signature counter
	 *
	 * @param int $signatureID Signature ID
	 */
	function _incSignatureCounter($signatureID)
	{
		global $db;

		$db->Query('UPDATE {pre}modfax_signatures SET `counter`=`counter`+1 WHERE `signatureid`=?',
			$signatureID);
	}

	/**
	 * get gateway row with unserialized prefs array
	 *
	 * @param int $faxgateID Fax gateway ID
	 * @return mixed false on error or array on success
	 */
	function _getGateway($faxgateID)
	{
		global $db;

		if($faxgateID == 0)
			$faxgateID = $this->prefs['default_faxgateid'];

		$res = $db->Query('SELECT * FROM {pre}modfax_gateways WHERE `faxgateid`=?',
			$faxgateID);
		if($res->RowCount() != 1)
			return(false);
		$row = $res->FetchArray(MYSQLI_ASSOC);
		$res->Free();

		$row['prefs'] = unserialize($row['prefs']);

		return($row);
	}

	/**
	 * process fax status report
	 *
	 * @param string $statusCode Fax status code
	 * @param bool $success Success?
	 * @return bool
	 */
	function _processStatusReport($statusCode, $success)
	{
		global $db, $lang_user;

		$faxStatus = $success ? MODFAX_STATUS_SENT : MODFAX_STATUS_ERROR;

		$res = $db->Query('SELECT `pages`,`userid`,`price`,`faxid`,`statuscode`,`refunded` FROM {pre}modfax_outbox WHERE `statuscode`=? AND `status`=?',
			$statusCode,
			MODFAX_STATUS_SENDING);
		if($res->RowCount() == 1)
		{
			list($ourPageCount, $userID, $faxPrice, $faxID, $statusCode, $refunded) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			// error?
			if($faxStatus == MODFAX_STATUS_ERROR)
			{
				// refund?
				if($this->prefs['refund_on_error'] && !$refunded)
				{
					$userObject = _new('BMUser', array($userID));
					$userObject->Debit($faxPrice, $lang_user['modfax_txrefundtext']);
					$refunded 	= true;

					$this->_add2Stat(MODFAX_EVENT_REFUNDS, $faxPrice);

					PutLog(sprintf('Refunded <%d> credits to user <%s> (#%d) because the gateway failed to send fax <%d> (statusCode: %s)',
						$faxPrice,
						$userObject->_row['email'],
						$userObject->_id,
						$faxID,
						$statusCode),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
				}
			}

			// update outbox entry
			$db->Query('UPDATE {pre}modfax_outbox SET `status`=?,`refunded`=? WHERE `statuscode`=?',
				$faxStatus,
				$refunded ? 1 : 0,
				$statusCode);

			// update stats
			$this->_add2Stat($success ? MODFAX_EVENT_FAX_OK : MODFAX_EVENT_FAX_ERROR);

			// log
			PutLog(sprintf('Received status report for fax <%d> of user <%d> (statusCode: %s)',
				$faxID,
				$userID,
				$statusCode),
				PRIO_NOTE,
				__FILE__,
				__LINE__);
			return(true);
		}

		return(false);
	}

	/**
	 * convert array from _parseNo() to number in a specific format
	 *
	 * @param array $no Number as parsed by _parseNo()
	 * @param int $how Format (MODFAX_NUMBER_* constant)
	 * @return string
	 */
	function _formatNo($no, $how = 1)
	{
		if(!is_array($no))
			return('0');

		if($how == MODFAX_NUMBER_INTERNAT_00)
			return(sprintf('00%02d%s', $no['countryPrefix'], $no['no']));
		else if($how == MODFAX_NUMBER_INTERNAT_PLUS)
			return(sprintf('+%02d%s', $no['countryPrefix'], $no['no']));
		else if($how == MODFAX_NUMBER_INTERNAT_NONE)
			return(sprintf('%02d%s', $no['countryPrefix'], $no['no']));
		else if($how == MODFAX_NUMBER_NAT)
			return(sprintf('0%s', $no['no']));

		return('0');
	}

	/**
	 * return e-mail reply address for gateway
	 *
	 * @param int $gatewayID Gateway ID
	 * @return string
	 */
	function _answerEMailAddr($gatewayID)
	{
		return(GetPostmasterMail());
	}

	/**
	 * send fax through HTTP gateway
	 *
	 * @param array $gateway HTTP gateway array as returned by _getGateway()
	 * @param string $userMail User mail
	 * @param string $fileName PDF file name
	 * @param string $fromName From name
	 * @param string $fromNo From number
	 * @param array $to To number as parsed by _parseNo()
	 * @param string $statusCode Status code returned by gateway (if any)
	 * @return bool
	 */
	function _sendFaxThroughHTTPGateway($gateway, $userMail, $fileName, $fromName, $fromNo, $to, &$statusCode)
	{
		if($gateway['protocol'] != MODFAX_PROTOCOL_HTTP)
			return(false);

		$toNo 			= $this->_formatNo($to, $gateway['number_format']);
		$request 		= $gateway['prefs']['request'];
		$statusCode		= 0;

		// get data
		$fp 			= fopen($fileName, 'rb');
		if(!$fp)
			return(false);
		$pdfData		= fread($fp, filesize($fileName));
		fclose($fp);

		// replace variables
		$request		= str_replace('%%user%%',		urlencode($gateway['user']),	$request);
		$request		= str_replace('%%pass%%',		urlencode($gateway['pass']),	$request);
		$request		= str_replace('%%to%%',			urlencode($toNo),				$request);
		$request		= str_replace('%%from_no%%',	urlencode($fromNo),				$request);
		$request		= str_replace('%%from_name%%',	urlencode($fromName),			$request);
		$request		= str_replace('%%usermail%%',	urlencode($userMail),			$request);
		$request		= str_replace('%%answer_email%%',
							urlencode($this->_answerEMailAddr($gateway['faxgateid'])),
							$request);

		// replace data variables
		if(strpos($request, '%%pdf_data_urlsafe_base64%%') !== false)
			$request	= str_replace('%%pdf_data_urlsafe_base64%%',
							str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($pdfData)),
							$request);
		if(strpos($request, '%%pdf_data_base64%%') !== false)
			$request	= str_replace('%%pdf_data_base64%%',
							urlencode(base64_encode($pdfData)),
							$request);
		if(strpos($request, '%%pdf_data_raw%%') !== false)
			$request	= str_replace('%%pdf_data_raw%%',
							urlencode($pdfData),
							$request);

		// perform request
		$http 			= new BMHTTP_POST($gateway['prefs']['url']);
		$response		= $http->DownloadToString_POST($request);

		// ok?
		if(strpos($response, $gateway['prefs']['returnvalue']) !== false)
		{
			// status code?
			if(preg_match('/Status\:([0-9]*)/', $response, $reg) && is_array($reg) && isset($reg[1]))
				$statusCode = $reg[1];

			return(true);
		}

		return(false);
	}

	/**
	 * send fax through e-mail gateway
	 *
	 * @param array $gateway E-Mail gateway array as returned by _getGateway()
	 * @param string $userMail User mail
	 * @param string $fileName PDF file name
	 * @param string $fromName From name
	 * @param string $fromNo From number
	 * @param array $to To number as parsed by _parseNo()
	 * @param string $statusCode Status code returned by gateway (if any)
	 * @return bool
	 */
	function _sendFaxThroughEMailGateway($gateway, $userMail, $fileName, $fromName, $fromNo, $to, &$statusCode)
	{
		global $currentCharset;

		if($gateway['protocol'] != MODFAX_PROTOCOL_EMAIL)
			return(false);

		$toNo 			= $this->_formatNo($to, $gateway['number_format']);
		$statusCode		= mt_rand(0, 2147483647);
		$mailFrom		= $gateway['prefs']['from'];
		$mailTo			= $gateway['prefs']['to'];
		$mailSubject	= $gateway['prefs']['subject'];
		$mailText		= $gateway['prefs']['text'];
		$mailFileName	= $gateway['prefs']['pdffile'];

		// get data
		$fp 			= fopen($fileName, 'rb');
		if(!$fp)
			return(false);
		$pdfData		= fread($fp, filesize($fileName));
		fclose($fp);

		// load class, if needed
		if(!class_exists('BMMailBuilder'))
			include(B1GMAIL_DIR . 'serverlib/mailbuilder.class.php');

		// replace variables
		$vars = array('mailFileName', 'mailFrom', 'mailTo', 'mailSubject', 'mailText');
		foreach($vars as $var)
		{
			$$var		= str_replace('%%user%%',			$gateway['user'],	$$var);
			$$var		= str_replace('%%pass%%',			$gateway['pass'],	$$var);
			$$var		= str_replace('%%to%%',				$toNo,				$$var);
			$$var		= str_replace('%%from_no%%',		$fromNo,			$$var);
			$$var		= str_replace('%%from_name%%',		$fromName,			$$var);
			$$var		= str_replace('%%usermail%%',		$userMail,			$$var);
			$$var		= str_replace('%%status_code%%',	$statusCode,		$$var);
			$$var		= str_replace('%%answer_email%%',
							$this->_answerEMailAddr($gateway['faxgateid']),
							$$var);

			if($var != 'mailFileName')
				$$var	= str_replace('%%pdf_filename%%',	$mailFileName,		$$var);
		}

		// create mail
		$mail = _new('BMMailBuilder');
		$mail->AddHeaderField('From',		$mailFrom);
		$mail->AddHeaderField('To',			$mailTo);
		$mail->AddHeaderField('Subject',	$mailSubject);
		$mail->AddText($mailText, 			'plain', 			$currentCharset);
		$mail->AddAttachment($pdfData, 		'application/pdf', 	$mailFileName,	'attachment');
		$result = $mail->Send() !== false;
		$mail->CleanUp();

		// return
		return($result);
	}

	/**
	 * send fax
	 *
	 * @param int $pdfFileID PDF file ID
	 * @param string $fromName From name
	 * @param string $fromNo From number
	 * @param array $to To number as parsed by _parseNo()
	 * @param BMUser $userObject User object
	 * @return bool
	 */
	function _sendFax($pdfFileID, $fromName, $fromNo, $to, &$userObject)
	{
		global $db, $lang_user, $userRow;

		$result			= false;
		$tempFileName 	= TempFileName($pdfFileID);

		// load FPDF/FPDI
		$this->_ensureFPDFisLoaded();

		// get PDF page count
		$fpdi 			= new FPDI();
		$pageCount		= @$fpdi->setSourceFile($tempFileName);
		unset($fpdi);

		// page count OK?
		if($pageCount > 0)
		{
			// calculate price
			$faxgateID		= 0;
			$faxPrice		= $this->_calculatePrice($to, $pageCount, $faxgateID);
			if($faxPrice !== false)
			{
				// check account balance
				$balance 	= $userObject->GetBalance();
				if($balance < $faxPrice)
				{
					PutLog(sprintf('Failed to send fax from "%s" <%s> (user %d) to <%s>: Not enough credits',
						$fromName,
						$fromNo,
						$userRow['id'],
						$this->_formatNo($to)),
						PRIO_NOTE,
						__FILE__,
						__LINE__);
				}
				else
				{
					// get gateway type
					$gateway 	= $this->_getGateway($faxgateID);
					if($gateway)
					{
						$statusCode	= 0;
						if($gateway['protocol'] == MODFAX_PROTOCOL_EMAIL)
							$result = $this->_sendFaxThroughEMailGateway($gateway, $userObject->_row['email'], $tempFileName, $fromName, $fromNo, $to, $statusCode);
						else if($gateway['protocol'] == MODFAX_PROTOCOL_HTTP)
							$result = $this->_sendFaxThroughHTTPGateway($gateway, $userObject->_row['email'], $tempFileName, $fromName, $fromNo, $to, $statusCode);

						if($result)
						{
							// debit
							$outboxID = max(0, $userObject->Debit($faxPrice*-1, $lang_user['modfax_txtext']));
							$this->_add2Stat(MODFAX_EVENT_CREDITS, $faxPrice);

							// put to webdisk
							$diskFileID = 0;
							if(!class_exists('BMWebdisk'))
								include(B1GMAIL_DIR . 'serverlib/webdisk.class.php');
							$webdisk = _new('BMWebdisk', array($userObject->_id));
							if($webdisk->GetSpaceLimit() == -1 || ($webdisk->GetUsedSpace()+filesize($tempFileName) <= $webdisk->GetSpaceLimit()))
							{
								// folder?
								$folderID 		= $webdisk->FolderExists(0, $lang_user['modfax_sentfolder']);
								if(!$folderID)
									$folderID	= $webdisk->CreateFolder(0, $lang_user['modfax_sentfolder']);

								// store file
								$diskFileID 	= $webdisk->CreateFile($folderID, sprintf('fax-%d.pdf', time()), 'application/pdf', filesize($tempFileName));
								if($diskFileID > 0)
								{
									if(class_exists('BMBlobStorage'))
									{
										$sourceFP = fopen($tempFileName, 'rb');
										BMBlobStorage::createDefaultWebdiskProvider($userObject->_id)
											->storeBlob(BMBLOB_TYPE_WEBDISK, $diskFileID, $sourceFP);
										fclose($sourceFP);
									}
									else
									{
										@copy($tempFileName, DataFilename($diskFileID, 'dsk'));
									}
								}
							}

							// add to outbox
							$db->Query('INSERT INTO {pre}modfax_outbox(`userid`,`faxgateid`,`fromname`,`fromno`,`tono`,`pages`,`date`,`diskfileid`,`outboxid`,`price`,`statuscode`,`status`) '
								. 'VALUES(?,?,?,?,?,?,?,?,?,?,?,?)',
								$userObject->_id,
								$gateway['faxgateid'],
								$fromName,
								$fromNo,
								$this->_formatNo($to),
								$pageCount,
								time(),
								$diskFileID,
								$outboxID,
								$faxPrice,
								$statusCode,
								$gateway['status_mode'] > 0 ? MODFAX_STATUS_SENDING : MODFAX_STATUS_SENT);

							// stat
							$this->_add2Stat(MODFAX_EVENT_SENDFAX);

							// log
							PutLog(sprintf('Sent fax from "%s" <%s> (user %d) to <%s> (user: <%s> (#%d); gateway: %d; statusCode: %s; price: %d)',
								$fromName,
								$fromNo,
								$userObject->_id,
								$this->_formatNo($to),
								$userObject->_row['email'],
								$userObject->_id,
								$gateway['faxgateid'],
								$statusCode,
								$faxPrice),
								PRIO_NOTE,
								__FILE__,
								__LINE__);
						}
					}
					else
					{
						PutLog(sprintf('Failed to send fax from "%s" <%s> (user %d) to <%s>: Fax gateway <%d> not found',
							$fromName,
							$fromNo,
							$userObject->_id,
							$this->_formatNo($to),
							$faxgateID),
							PRIO_NOTE,
							__FILE__,
							__LINE__);
					}
				}
			}
		}

		// return
		return($result);
	}

	/**
	 * calculate fax price
	 *
	 * @param array $to Recipient number as parsed by _parseNo()
	 * @param int $pages Number of pages
	 * @param int $faxgateID Gateway ID output
	 * @return mixed false on error (i.e. number not supported) or int (credit price) on success
	 */
	function _calculatePrice($to, $pages, &$faxgateID)
	{
		global $db;

		if(!is_array($to) || $pages < 1)
			return(false);

		$result = false;

		$res = $db->Query('SELECT `prefix`,`faxgateid`,`price_firstpage`,`price_nextpages` FROM {pre}modfax_prefixes WHERE (`country_prefix`=? OR `country_prefix`=?) ORDER BY `country_prefix` DESC, `prefix` DESC',
			$to['countryPrefix'], '*');
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($row['prefix'] == '*'
				|| substr($to['no'], 0, strlen($row['prefix'])) == $row['prefix'])
			{
				// disallow number
				if($row['faxgateid'] == -1)
				{
					$result = false;
					break;
				}

				// allow number
				else
				{
					$faxgateID 	= $row['faxgateid'];
					$result 	= ($row['price_firstpage'] * 1)
									+ ($row['price_nextpages'] * ($pages-1));
					break;
				}
			}
		}
		$res->Free();

		return($result);
	}

	/**
	 * parse number to array containing country prefix and number
	 *
	 * @param string $no Number
	 * @return mixed false on error or array on success
	 */
	function _parseNo($no)
	{
		$result = false;

		$no = preg_replace('/[^\+0-9]/', '', $no);

		// country prefix with 00
		if(strlen($no) > 5 && preg_match('/^00/', $no))
		{
			$result = array(
				'countryPrefix'		=> substr($no, 2, 2),
				'no'				=> $no[4] == '0' ? substr($no, 5) : substr($no, 4)
			);
		}

		// country prefix with +
		else if(strlen($no) > 4 && preg_match('/^\+/', $no))
		{
			$result = array(
				'countryPrefix'		=> substr($no, 1, 2),
				'no'				=> $no[3] == '0' ? substr($no, 4) : substr($no, 3)
			);
		}

		// country prefix without 00 or +
		else if(strlen($no) > 3 && $no[0] != '0')
		{
			$result = array(
				'countryPrefix'		=> substr($no, 0, 2),
				'no'				=> $no[2] == '0' ? substr($no, 3) : substr($no, 2)
			);
		}

		// no country prefix
		else if(strlen($no) > 1 && $no[0] == '0')
		{
			$result = array(
				'countryPrefix'		=> $this->prefs['default_country_prefix'],
				'no'				=> substr($no, 1)
			);
		}

		return($result);
	}

	/**
	 * the magic PDF fax generator
	 *
	 * @param string $fromName From name
	 * @param string $fromNo From number
	 * @param string $toNo To number
	 * @param array $blocks Blocks
	 * @param bool $countOnly Do only page counting?
	 * @param int $pageCount Page count input/output
	 */
	function _generatePDF($fromName, $fromNo, $toNo, $blocks, &$pageCount, $countOnly = false, $signature = false)
	{
		global $userRow;

		// fetch signature?
		if($signature === false)
			$signature = $this->_getSignature($userRow['gruppe']);

		// determine page count
		if($pageCount == 0 && !$countOnly)
			$this->_generatePDF($fromName, $fromNo, $toNo, $blocks, $pageCount, true, $signature);

		// load FPDF/FPDI
		$this->_ensureFPDFisLoaded();

		// set locale to english for FPDF comma stuff...
		$locales = array('en_US', 'en_US.UTF-8', 'en_EN', 'en_EN.UTF-8', 'en_GB', 'en', 'english');
		foreach($locales as $locale)
			if(setlocale(LC_NUMERIC, $locale))
				break;

		// generate FPDI object
		$pdf = new FPDI_FaxPlugin('P', 'mm', 'A4');

		// set up
		$pdf->SetMargins(25, 25, 25);
		$pdf->SetAutoPageBreak(true, 20);
		$pdf->SetTitle('Fax');
		$pdf->SetCreator('b1gMail-FaxPlugin/' . $this->version);
		$pdf->SetCompression(true);
		$pdf->SetSignature($signature);
		$this->_haveNewPage = false;
		$this->_needNewPage = true;

		// process blocks
		if(is_array($blocks))
		{
			foreach($blocks as $block)
			{
				if(!is_array($block))
					continue;

				if($block['type'] != MODFAX_BLOCK_PDFFILE
					&& $this->_needNewPage)
				{
					$pdf->AddPage();
					$this->_needNewPage = false;
					$this->_haveNewPage = true;
				}

				switch($block['type'])
				{
				case MODFAX_BLOCK_TEXT:
					$this->_generatePDF_Block_Text($pdf, $block);
					$this->_haveNewPage = false;
					$this->_needNewPage = false;
					break;

				case MODFAX_BLOCK_PAGEBREAK:
					$pdf->AddPage();
					$this->_haveNewPage = true;
					$this->_needNewPage = false;
					break;

				case MODFAX_BLOCK_COVER:
					$this->_generatePDF_Block_Cover($pdf, $block, $fromName, $fromNo, $toNo, $pageCount);
					$this->_haveNewPage = false;
					$this->_needNewPage = false;
					break;

				case MODFAX_BLOCK_PDFFILE:
					if($this->prefs['allow_pdf'])
					{
						$this->_generatePDF_Block_PDFFile($pdf, $block);
						$this->_haveNewPage = false;
						$this->_needNewPage = true;
					}
					break;
				}
			}
		}

		// set page count
		$pageCount = $pdf->PageNo();

		// save to temp file
		if(!$countOnly)
		{
			$tempFileID = RequestTempFile($userRow['id'], time()+4*TIME_ONE_HOUR);
			$pdf->Output(TempFileName($tempFileID), 'F');

			// increment signature counter
			if($signature !== false && is_array($signature))
				$this->_incSignatureCounter($signature['signatureid']);

			// return temp ID
			return($tempFileID);
		}
		else
			return(true);
	}

	/**
	 * cover block generator
	 *
	 * @param FPDF $pdf FPDF object
	 * @param array $block Block
	 * @param string $fromName Sender name
	 * @param string $fromNo Sender no
	 * @param string $toNo Recipient no
	 * @param int $pageCount Page count
	 */
	function _generatePDF_Block_Cover(&$pdf, $block, $fromName, $fromNo, $toNo, $pageCount = 0)
	{
		global $lang_user, $userRow;

		if(!isset($block['cover']))
			return;

		$data 		= $block['cover'];
		$fontName 	= 'Arial';
		$fontSize 	= 10;
		$lineHeight = $fontSize*0.5;

		// 'Fax' heading
		$pdf->SetFont($fontName, 'B', $fontSize*2);
		$pdf->Cell(0, $lineHeight*2, $this->_pdfStr($lang_user['modfax_fax']), 0, 1, 'R', false);
		$pdf->Ln();

		// to
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['to'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(60, $lineHeight, $this->_pdfStr($data['toname']), 0, 0, 'L', false);
		// from
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['from'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(60, $lineHeight, $this->_pdfStr($fromName), 0, 1, 'L', false);
		// table line
		$pdf->Line(25, $pdf->GetY()+1, 210-25, $pdf->GetY()+1);
		$pdf->Ln();

		// fax
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['fax'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(60, $lineHeight, $this->_pdfStr($fromNo), 0, 0, 'L', false);
		// phone
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['phone'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(60, $lineHeight, $this->_pdfStr($data['phone']), 0, 1, 'L', false);
		// table line
		$pdf->Line(25, $pdf->GetY()+1, 210-25, $pdf->GetY()+1);
		$pdf->Ln();

		// date
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['date'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(60, $lineHeight, date('d.m.Y'), 0, $pageCount > 0 ? 0 : 1, 'L', false);
		// page count
		if($pageCount > 0)
		{
			$pdf->SetFont($fontName, 'B', $fontSize);
			$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['modfax_pages'] . ':'), 0, 0, 'L', false);
			$pdf->SetFont($fontName, '', $fontSize);
			$pdf->Cell(60, $lineHeight, $pageCount, 0, 1, 'L', false);
		}

		// table line
		$pdf->Line(25, $pdf->GetY()+1, 210-25, $pdf->GetY()+1);
		$pdf->Ln();

		// subject
		$pdf->SetFont($fontName, 'B', $fontSize);
		$pdf->Cell(20, $lineHeight, $this->_pdfStr($lang_user['subject'] . ':'), 0, 0, 'L', false);
		$pdf->SetFont($fontName, '', $fontSize);
		$pdf->Cell(140, $lineHeight, $this->_pdfStr($data['subject']), 0, 1, 'L', false);
		// table line
		$pdf->Line(25, $pdf->GetY()+1, 210-25, $pdf->GetY()+1);
		$pdf->Ln();

		// remarks
		$rectSize = $lineHeight*0.75;
		for($i=MODFAX_REMARK_URGENT; $i<=MODFAX_REMARK_FORINFORMATION; $i++)
		{
			$pdf->SetX($pdf->GetX() + 2);
			$rectX = $pdf->GetX();
			$rectY = $pdf->GetY()+0.5;

			// draw remark rect + text
			$pdf->Rect($rectX, $rectY, $rectSize, $rectSize, 'D');
			$pdf->SetX($pdf->GetX() + $lineHeight);
			$pdf->Cell(48, $lineHeight, $this->_pdfStr($lang_user['modfax_remark'.$i]), 0, 0, 'L', false);

			// cross?
			if(isset($data['remark']) && is_array($data['remark']) && in_array($i, $data['remark']))
			{
				$pdf->Line($rectX, $rectY, $rectX+$rectSize, $rectY+$rectSize);
				$pdf->Line($rectX+$rectSize, $rectY, $rectX, $rectY+$rectSize);
			}

			// new line?
			if($i == 2)
			{
				$pdf->Ln();
				$pdf->SetY($pdf->GetY() + $lineHeight*0.75);
			}
		}

		// table line
		$pdf->Ln();
		$pdf->Line(25, $pdf->GetY()+3, 210-25, $pdf->GetY()+3);

		// bottom space
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
	}

	/**
	 * pdffile block generator
	 *
	 * @param FPDF $pdf FPDF object
	 * @param array $block Block
	 */
	function _generatePDF_Block_PDFFile(&$pdf, $block)
	{
		global $userRow;

		if(!isset($block['pdf']))
			return;

		$data = $block['pdf'];

		// check temp file ownership
		$tempFileID = $data['fileid'];
		if(!ValidTempFile($userRow['id'], $tempFileID))
			return;

		// open PDF file
		$pageCount = $pdf->setSourceFile(TempFileName($tempFileID));
		if(!$pageCount)
			return;

		// add page?
		if(!$this->_haveNewPage)
			$pdf->AddPage();

		// import pages
		for($i=1; $i<=$pageCount; $i++)
		{
			if($i > 1)
				$pdf->AddPage();
			$pageTpl = $pdf->importPage($i, '/MediaBox');
			$pdf->useTemplate($pageTpl);
		}
	}

	/**
	 * text block generator
	 *
	 * @param FPDF $pdf FPDF object
	 * @param array $block Block
	 */
	function _generatePDF_Block_Text(&$pdf, $block)
	{
		if(!isset($block['text']))
			return;

		$data = $block['text'];

		$fontName = in_array($data['fontname'], array('arial', 'courier', 'times')) ? $data['fontname'] : 'courier';
		$fontSize = max(6, min(24, (int)$data['fontsize']));
		$fontStyle = '';

		if(isset($data['bold']))
			$fontStyle .= 'B';
		if(isset($data['italic']))
			$fontStyle .= 'I';
		if(isset($data['underlined']))
			$fontStyle .= 'U';

		switch($data['align'])
		{
		case 'right':
			$align = 'R';
			break;

		case 'center':
			$align = 'C';
			break;

		case 'justify':
			$align = 'J';
			break;

		case 'left':
		default:
			$align = 'L';
			break;
		}

		$pdf->SetFont($fontName,
			$fontStyle,
			$fontSize);

		$pdf->MultiCell(0,
			$fontSize*0.5,
			$this->_pdfStr($data['text']),
			0,
			$align,
			0);
		$pdf->Ln();
	}

	/**
	 * ensure FPDF and FPDI are loaded correctly
	 * aborts script execution on error
	 *
	 */
	function _ensureFPDFisLoaded()
	{
		if(!defined('FPDF_FONTPATH'))
			define('FPDF_FONTPATH', $this->_fpdfDir . 'font/');

		if(!class_exists('FPDF'))
		{
			if(!@include($this->_fpdfDir . 'fpdf.php'))
			{
				PutLog(sprintf('Fax plugin: Cannot include FPDF file <%s> - ensure FPDF is installed properly',
					$this->_fpdfDir . 'fpdf.php'),
					PRIO_ERROR,
					__FILE__,
					__LINE__);
				DisplayError(0x255+1,
					'Cannot load FPDF library',
					'The b1gMail fax plugin could not load the FPDF library.',
					'File: ' . $this->_fpdfDir . 'fpdf.php',
					__FILE__,
					__LINE__);
				die(1);
			}
		}

		if(!class_exists('Fpdi'))
        {
            if(!@include($this->_fpdfDir . 'fpdi/src/autoload.php'))
            {
                PutLog(sprintf('Fax plugin: Cannot include FPDI file <%s> - ensure FPDI is installed properly',
                    $this->_fpdfDir . 'fpdi/src/autoload.php'),
                    PRIO_ERROR,
                    __FILE__,
                    __LINE__);
                DisplayError(0x255+2,
                    'Cannot load FPDI library',
                    'The b1gMail fax plugin could not load the FPDI library.',
                    'File: ' . $this->_fpdfDir . 'fpdi/src/autoload.php',
                    __FILE__,
                    __LINE__);
                die(1);
            }
        }

        if(!class_exists('FPDI_FaxPlugin'))
            _FaxPluginCreateFPDISubclass();
    }

	/**
	 * Convert string encoding for use with FPDF
	 *
	 * @param string $in Input
	 * @return string Output
										 */
	function _pdfStr($in)
	{
		global $currentCharset;

		if(function_exists('CharsetDecode'))
			return(CharsetDecode($in, false, 'ISO-8859-15'));

		return($in);
	}
}

/**
 * create FPDI subclass with signature support
 *
 */
function _FaxPluginCreateFPDISubclass()
{
	/**
	 * FPDI subclass with signature support
	 *
	 */
	class FPDI_FaxPlugin extends FPDI
	{
		var $signatureArray = false;

		/**
		 * constructor
		 *
		 * @param string $orientation
		 * @param string $unit
		 * @param string $pageformat
		 * @return FPDI_FaxPlugin
		 */
		function __construct($orientation, $unit, $pageformat)
		{
			parent::__construct($orientation, $unit, $pageformat);
		}

		/**
		 * set signature
		 *
		 * @param array $sig Signature array
		 */
		function SetSignature($sig)
		{
			$this->signatureArray = $sig;

			if($sig !== false
				&& is_array($sig))
			{
				if(($sig['showon'] & MODFAX_SHOWON_BOTTOM) != 0)
					$this->SetAutoPageBreak(true, $sig['margin']);
				if(($sig['showon'] & MODFAX_SHOWON_TOP) != 0)
					$this->SetTopMargin($sig['margin']);
			}
		}

		/**
		 * draw header
		 *
		 */
		function Header()
		{
			if(!$this->_signatureIsForThisPage())
				return;

			if($this->signatureArray !== false
				&& is_array($this->signatureArray)
				&& ($this->signatureArray['showon'] & MODFAX_SHOWON_TOP) != 0)
			{
				$this->SetY(10);
				$this->_placeSignature();
				$this->SetY($this->signatureArray['margin']);
			}
		}

		/**
		 * draw x
		 *
		 */
		function Footer()
		{
			if(!$this->_signatureIsForThisPage())
				return;

			if($this->signatureArray !== false
				&& is_array($this->signatureArray)
				&& ($this->signatureArray['showon'] & MODFAX_SHOWON_BOTTOM) != 0)
			{
				$this->SetY($this->signatureArray['margin']*-1);
				$this->_placeSignature();
			}
		}

		/**
		 * check if signature should be displayed on this page
		 *
		 * @return bool
		 */
		function _signatureIsForThisPage()
		{
			if($this->signatureArray !== false
				&& is_array($this->signatureArray))
			{
				if($this->PageNo() == 1 && ($this->signatureArray['showon'] & MODFAX_SHOWON_FIRSTPAGE))
					return(true);
				else if($this->PageNo() > 1 && ($this->signatureArray['showon'] & MODFAX_SHOWON_OTHERPAGES))
					return(true);
			}

			return(false);
		}

		/**
		 * place the signature
		 *
		 */
		function _placeSignature()
		{
			$this->Ln();

			// border
			$border = '';
			if(($this->signatureArray['style'] & MODFAX_STYLE_TOPLINE) != 0)
				$border .= 'T';
			if(($this->signatureArray['style'] & MODFAX_STYLE_BOTTOMLINE) != 0)
				$border .= 'B';
			if($border == '')
				$border = 0;

			// font style
			$style = '';
			if(($this->signatureArray['style'] & MODFAX_STYLE_BOLD) != 0)
				$style .= 'B';
			if(($this->signatureArray['style'] & MODFAX_STYLE_ITALIC) != 0)
				$style .= 'I';
			if(($this->signatureArray['style'] & MODFAX_STYLE_UNDERLINED) != 0)
				$style .= 'U';

			// set font
			$this->SetFont($this->signatureArray['fontname'],
				$style,
				$this->signatureArray['fontsize']);

			// set fill color
			$this->SetFillColor(255, 255, 255);

			// draw
			if(function_exists('CharsetDecode')) $this->signatureArray['text'] = CharsetDecode($this->signatureArray['text'], false, 'ISO-8859-15');
			$this->MultiCell(0,
				($this->signatureArray['fontsize']/$this->k)*1.5,
				$this->signatureArray['text'],
				$border,
				$this->signatureArray['align'],
				true);

			$this->Ln();
		}
	}
}

/**
 * register plugin
 */
$plugins->registerPlugin('FaxPlugin');
