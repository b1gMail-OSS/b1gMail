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

class BMCaptchaProvider_KeyCAPTCHA extends BMAbstractCaptchaProvider
{
	public function isAvailable()
	{
		return(true);
	}

	public function generate()
	{
		return;
	}

	public function getHTML()
	{
		$config = $this->getConfig();

		if(empty($config['privateKey']))
		{
			PutLog('KeyCAPTCHA: No private key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		if(empty($config['userID']))
		{
			PutLog('KeyCAPTCHA: No user ID set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		$sessionID 	= uniqid();
		$wSign 		= md5($sessionID . $_SERVER['REMOTE_ADDR'] . $config['privateKey']);
		$wSign2 	= md5($sessionID . $config['privateKey']);

		return "<input type=\"hidden\" name=\"capcode\" id=\"capcode\" />\n"
				. "<!-- KeyCAPTCHA code (www.keycaptcha.com) -->\n"
				. "<script type=\"text/javascript\">\n"
				. "	var s_s_c_user_id = '" . $config['userID'] . "';\n"
				. "	var s_s_c_session_id = '" . $sessionID . "';\n"
				. "	var s_s_c_captcha_field_id = 'capcode';\n"
				. "	var s_s_c_submit_button_id = 'sendButton';\n"
				. "	var s_s_c_web_server_sign = '" . $wSign . "';\n"
				. "	var s_s_c_web_server_sign2 = '" . $wSign2 . "';\n"
				. "</script>\n"
				. "<script language=\"javascript\" src=\"http://backs.keycaptcha.com/swfs/cap.js\"></script>\n"
				. "<!-- end of KeyCAPTCHA code -->\n";
	}

	public function check($release = true)
	{
		if(!isset($_REQUEST['capcode']))
			return(false);

		$vars = explode('|', $_REQUEST['capcode']);
		if(count($vars) < 4)
			return(false);

		$config = $this->getConfig();
		if(empty($config['privateKey']))
		{
			PutLog('KeyCAPTCHA: No private key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		if($vars[0] === md5('accept' . $vars[1] . $config['privateKey'] . $vars[2]))
		{
			if(strpos($vars[2], 'http://') !== 0)
			{
				PutLog('KeyCAPTCHA: Please enable the "Allow outgoing requests" setting at www.keycaptcha.com!',
					PRIO_WARNING,
					__FILE__,
					__LINE__);
				return(false);
			}
			else
			{
				if(!class_exists('BMHTTP'))
					include(B1GMAIL_DIR . 'serverlib/http.class.php');

				$http 	= _new('BMHTTP', array($vars[2]));
				$resp 	= $http->DownloadToString();

				PutLog(sprintf('KeyCAPTCHA response data: "%s"', $resp),
					PRIO_DEBUG,
					__FILE__,
					__LINE__);

				return(trim($resp) == '1');
			}
		}
		else
		{
			PutLog('KeyCAPTCHA: MD5 mismatch',
				PRIO_DEBUG,
				__FILE__,
				__LINE__);
		}

		return(false);
	}

	public function getInfo()
	{
		global $lang_admin;

		return(array(
			'title'				=> 'KeyCAPTCHA',
			'author'			=> 'b1gMail Project',
			'website'			=> 'https://www.b1gmail.org/',
			'showNotReadable'	=> false,
			'hasOwnInput'		=> true,
			'hasOwnAJAXCheck'	=> true,
			'failAction'		=> '',
			'heightHint'		=> '190px',
			'configFields'		=> array(
				'userID'			=> array(
					'title'			=> 'User ID:',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
				'privateKey'		=> array(
					'title'			=> $lang_admin['privatekey'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				)
			)
		));
	}
}

BMCaptcha::registerProvider(basename(__FILE__), 'BMCaptchaProvider_KeyCAPTCHA');
