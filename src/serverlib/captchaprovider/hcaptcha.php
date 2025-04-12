<?php
/*
 * b1gMail hCAPTCHA PRovider
 * Copyright (c) 2025 Michael Kleger (OneSystems GmbH)
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

class BMCaptchaProvider_hCAPTCHA extends BMAbstractCaptchaProvider {
	public function isAvailable() {
		return(true);
	}

	public function generate() {
		return;
	}

	public function getHTML() {
		$config = $this->getConfig();

		if(empty($config['publicKey'])) {
			PutLog('hCAPTCHA: No public key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
		}

		$server = 'https://js.hcaptcha.com/1/api.js';

		return '<script src="' . $server . '" async defer></script>'
					. '<div class="h-captcha" data-sitekey="'. $config['publicKey']  .'"></div>';
	}

	public function check($release = true) {
		if(!isset($_REQUEST['h-captcha-response']))
			return(false);

		$response 	= $_POST['h-captcha-response'];

		if(empty($response))
			return(false);

		$config 	= $this->getConfig();

		if(empty($config['privateKey'])) {
			PutLog('hCAPTCHA: No private key set',
				PRIO_WARNING,
				__FILE__,
				__LINE__);
			return(false);
		}

  	if(isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])) {
      $secret = $config['privateKey'];
      $verifyResponse = file_get_contents('https://hcaptcha.com/siteverify?secret='.$secret.'&response='.$_POST['h-captcha-response'].'&remoteip='.$_SERVER['REMOTE_ADDR']);
      $responseData = json_decode($verifyResponse);
      if($responseData->success) {
        return(true);
      } else {
			  PutLog(sprintf('hCAPTCHA response data: "%s"', $responseData), PRIO_DEBUG, __FILE__, __LINE__);
        return(false);
    	}
    }

		return(false);
	}

	public function getInfo() {
		global $lang_admin;

		return(array(
			'title'			=> 'hCAPTCHA',
			'author'		=> 'OneSystems GmbH',
			'website'		=> 'https://www.onesystems.ch/',
			'showNotReadable'	=> false,
			'hasOwnInput'		=> true,
			'hasOwnAJAXCheck'	=> false,
			'failAction'		=> 'hcaptcha.reset();',
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
				)
			)
		));
	}
}

BMCaptcha::registerProvider(basename(__FILE__), 'BMCaptchaProvider_hCAPTCHA');
