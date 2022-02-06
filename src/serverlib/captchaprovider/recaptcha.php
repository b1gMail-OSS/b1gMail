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

class BMCaptchaProvider_reCAPTCHA extends BMAbstractCaptchaProvider
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

		if(empty($config['publicKey']))
		{
			PutLog('reCAPTCHA: No public key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		$server = 'https://www.google.com/recaptcha/api.js';

		return '<script src="' . $server . '" async defer></script>'
					. '<div class="g-recaptcha" data-sitekey="' . $config['publicKey'] . '" data-theme="' . $config['theme'] . '"></div>';
	}

	public function check($release = true)
	{
		if(!isset($_REQUEST['g-recaptcha-response']))
			return(false);

		$response 	= $_REQUEST['g-recaptcha-response'];

		if(empty($response))
			return(false);

		if(isset($_SESSION['reCAPTCHAverify']) && is_array($_SESSION['reCAPTCHAverify'])
			&& isset($_SESSION['reCAPTCHAverify'][md5($response)]))
		{
			if($release)
				unset($_SESSION['reCAPTCHAverify'][md5($response)]);
			return(true);
		}

		$config 	= $this->getConfig();
		$url 		= 'https://www.google.com/recaptcha/api/siteverify';

		if(empty($config['privateKey']))
		{
			PutLog('reCAPTCHA: No private key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

		$data 		= 'secret=' . urlencode($config['privateKey'])
						. '&remoteip=' . urlencode($_SERVER['REMOTE_ADDR'])
						. '&response=' . urlencode($response);

		if(!class_exists('BMHTTP'))
			include(B1GMAIL_DIR . 'serverlib/http.class.php');

		$http 		= _new('BMHTTP', array($url));
		$resp 		= $http->DownloadToString_POST($data);

		PutLog(sprintf('reCAPTCHA response data: "%s"', $resp),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);

		$resp 		= json_decode($resp);

		if(is_object($resp) && $resp->success)
		{
			if(!isset($_SESSION['reCAPTCHAverify']))
				$_SESSION['reCAPTCHAverify'] = array();
			$_SESSION['reCAPTCHAverify'][md5($response)] = true;
			return(true);
		}

		return(false);
	}

	public function getInfo()
	{
		global $lang_admin;

		return(array(
			'title'				=> 'reCAPTCHA',
			'author'			=> 'b1gMail Project',
			'website'			=> 'https://www.b1gmail.org/',
			'showNotReadable'	=> false,
			'hasOwnInput'		=> true,
			'hasOwnAJAXCheck'	=> false,
			'failAction'		=> 'grecaptcha.reset();',
			'heightHint'		=> '90px',
			'configFields'		=> array(
				'publicKey'			=> array(
					'title'			=> $lang_admin['republickey'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
				'privateKey'		=> array(
					'title'			=> $lang_admin['reprivatekey'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
				'theme'				=> array(
					'title'			=> $lang_admin['theme'].':',
					'type'			=> FIELD_DROPDOWN,
					'options'		=> array('light' => 'light', 'dark' => 'dark'),
					'default'		=> 'light'
				)
			)
		));
	}
}

BMCaptcha::registerProvider(basename(__FILE__), 'BMCaptchaProvider_reCAPTCHA');
