<?php
/*
 * b1gMail ALTCHA provider (self-hosted PoW + ALTCHA Cloud)
 * Copyright (c) 2026 OneSystems GmbH
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
 * Widget: ALTCHA 3.2.2 (MIT), src/clientlib/altcha/altcha.js
 * https://altcha.org/
 */

if(!defined('B1GMAIL_INIT'))
	die('Directly calling this file is not supported');

class BMCaptchaProvider_ALTCHA extends BMAbstractCaptchaProvider
{
	const CHALLENGE_TTL = 600;
	const WIDGET_JS = 'clientlib/altcha/altcha.js';

	public function isAvailable()
	{
		return(function_exists('hash_hmac') && function_exists('hash_equals'));
	}

	public function generate()
	{
		$config = $this->getConfig();
		if($this->isCloud($config))
		{
			header('HTTP/1.1 404 Not Found');
			return;
		}

		$maxNumber = $this->maxNumber($config);
		$expires = time() + self::CHALLENGE_TTL;
		$salt = bin2hex(random_bytes(12)) . '?expires=' . $expires;
		$secretNumber = random_int(0, $maxNumber);
		$challenge = hash('sha256', $salt . $secretNumber);
		$signature = hash_hmac('sha256', $challenge, $this->hmacKey($config));

		while(ob_get_level() > 0)
			ob_end_clean();
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-store');
		echo json_encode(array(
			'algorithm'	=> 'SHA-256',
			'challenge'	=> $challenge,
			'salt'		=> $salt,
			'signature'	=> $signature,
			'maxnumber'	=> $maxNumber,
			'maxNumber'	=> $maxNumber,
		));
	}

	public function getHTML()
	{
		global $bm_prefs, $currentLanguage;

		$config = $this->getConfig();
		$base = rtrim(function_exists('PublicFqdnSelfUrl') ? PublicFqdnSelfUrl() : $bm_prefs['selfurl'], '/');
		$jsFile = B1GMAIL_DIR . self::WIDGET_JS;
		$jsVer = is_file($jsFile) ? filemtime($jsFile) : time();
		$jsUrl = $base . '/' . self::WIDGET_JS . '?' . $jsVer;

		if($this->isCloud($config))
		{
			$apiKey = trim((string)$config['apiKey']);
			if($apiKey === '')
			{
				PutLog('ALTCHA Cloud: no API key set', PRIO_WARNING, __FILE__, __LINE__);
			}
			$challenge = $this->cloudBase($config) . '/v1/challenge?apiKey=' . rawurlencode($apiKey);
		}
		else
		{
			if(function_exists('PublicRoutingActive') && PublicRoutingActive())
				$challenge = NliUrl('index.php', array('action' => 'codegen'));
			else
				$challenge = SessionUrl('index.php?action=codegen');
			if(strpos($challenge, '://') === false && strpos($challenge, '/') !== 0)
				$challenge = $base . '/' . ltrim($challenge, '/');
		}

		$langMap = array(
			'deutsch'	=> 'de',
			'english'	=> 'en',
			'francais'	=> 'fr',
		);
		$lang = 'en';
		if(isset($currentLanguage) && isset($langMap[$currentLanguage]))
			$lang = $langMap[$currentLanguage];

		return '<script type="module" src="' . HTMLFormat($jsUrl) . '"></script>'
			. '<altcha-widget'
			. ' challenge="' . HTMLFormat($challenge) . '"'
			. ' language="' . HTMLFormat($lang) . '"'
			. ' name="altcha"'
			. ' auto="off"'
			. '></altcha-widget>';
	}

	public function check($release = true)
	{
		$payload = '';
		if(isset($_REQUEST['altcha']) && is_string($_REQUEST['altcha']))
			$payload = trim($_REQUEST['altcha']);
		if($payload === '')
			return(false);

		$payloadKey = md5($payload);
		if(isset($_SESSION['altchaVerify'][$payloadKey]))
		{
			if($release)
				unset($_SESSION['altchaVerify'][$payloadKey]);
			return(true);
		}

		$config = $this->getConfig();
		$ok = false;
		if($this->isCloud($config))
			$ok = $this->verifyCloud($payload, $config);
		else
			$ok = $this->verifyLocal($payload, $config);

		if($ok)
		{
			if(!isset($_SESSION['altchaVerify']) || !is_array($_SESSION['altchaVerify']))
				$_SESSION['altchaVerify'] = array();
			$_SESSION['altchaVerify'][$payloadKey] = true;
			return(true);
		}

		return(false);
	}

	public function getInfo()
	{
		global $lang_admin;

		return(array(
			'title'				=> 'ALTCHA',
			'author'			=> 'OneSystems GmbH',
			'website'			=> 'https://altcha.org/',
			'showNotReadable'	=> false,
			'hasOwnInput'		=> true,
			'hasOwnAJAXCheck'	=> false,
			'failAction'		=> 'var w=document.querySelector("altcha-widget");if(w&&w.reset)w.reset();',
			'heightHint'		=> '80px',
			'configFields'		=> array(
				'mode'				=> array(
					'title'			=> $lang_admin['altcha_mode'].':',
					'type'			=> FIELD_DROPDOWN,
					'options'		=> array(
						'local'		=> $lang_admin['altcha_mode_local'],
						'cloud'		=> $lang_admin['altcha_mode_cloud'],
					),
					'default'		=> 'local'
				),
				'hmacKey'			=> array(
					'title'			=> $lang_admin['altcha_hmac'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
				'maxnumber'			=> array(
					'title'			=> $lang_admin['altcha_complexity'].':',
					'type'			=> FIELD_DROPDOWN,
					'options'		=> array(
						'20000'		=> $lang_admin['altcha_complexity_easy'],
						'100000'	=> $lang_admin['altcha_complexity_medium'],
						'500000'	=> $lang_admin['altcha_complexity_hard'],
					),
					'default'		=> '100000'
				),
				'cloudRegion'		=> array(
					'title'			=> $lang_admin['altcha_cloud_region'].':',
					'type'			=> FIELD_DROPDOWN,
					'options'		=> array(
						'eu'		=> 'EU (eu.altcha.org)',
						'us'		=> 'US (us.altcha.org)',
					),
					'default'		=> 'eu'
				),
				'apiKey'			=> array(
					'title'			=> $lang_admin['altcha_apikey'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
				'apiSecret'			=> array(
					'title'			=> $lang_admin['altcha_apisecret'].':',
					'type'			=> FIELD_TEXT,
					'default'		=> ''
				),
			)
		));
	}

	private function isCloud($config)
	{
		return(isset($config['mode']) && $config['mode'] === 'cloud');
	}

	private function hmacKey($config)
	{
		$key = isset($config['hmacKey']) ? trim((string)$config['hmacKey']) : '';
		if($key !== '')
			return($key);
		return(hash_hmac('sha256', 'altcha-local', B1GMAIL_SIGNKEY));
	}

	private function maxNumber($config)
	{
		$n = isset($config['maxnumber']) ? (int)$config['maxnumber'] : 100000;
		if($n < 1000)
			$n = 100000;
		if($n > 2000000)
			$n = 2000000;
		return($n);
	}

	private function cloudBase($config)
	{
		$region = isset($config['cloudRegion']) ? $config['cloudRegion'] : 'eu';
		return($region === 'us') ? 'https://us.altcha.org' : 'https://eu.altcha.org';
	}

	private function decodePayload($payload)
	{
		$raw = $payload;
		if(!str_starts_with($payload, '{'))
		{
			$decoded = base64_decode($payload, true);
			if(is_string($decoded) && $decoded !== '')
				$raw = $decoded;
		}
		$data = json_decode($raw, true);
		return is_array($data) ? $data : false;
	}

	private function verifyLocal($payload, $config)
	{
		$data = $this->decodePayload($payload);
		if($data === false)
			return(false);

		$challenge = isset($data['challenge']) ? (string)$data['challenge'] : '';
		$salt = isset($data['salt']) ? (string)$data['salt'] : '';
		$signature = isset($data['signature']) ? (string)$data['signature'] : '';
		$number = isset($data['number']) ? (string)$data['number'] : '';
		if($challenge === '' || $salt === '' || $signature === '' || $number === '')
			return(false);

		$qpos = strpos($salt, '?');
		if($qpos !== false)
		{
			parse_str(substr($salt, $qpos + 1), $params);
			if(isset($params['expires']) && (int)$params['expires'] < time())
				return(false);
		}

		$expectedSig = hash_hmac('sha256', $challenge, $this->hmacKey($config));
		if(!hash_equals($expectedSig, $signature))
			return(false);

		$expectedChallenge = hash('sha256', $salt . $number);
		return(hash_equals($expectedChallenge, $challenge));
	}

	private function verifyCloud($payload, $config)
	{
		$secret = isset($config['apiSecret']) ? trim((string)$config['apiSecret']) : '';
		if($secret === '')
		{
			PutLog('ALTCHA Cloud: no API secret set', PRIO_WARNING, __FILE__, __LINE__);
			return(false);
		}

		$url = $this->cloudBase($config) . '/v1/verify/signature';
		$post = json_encode(array(
			'payload'	=> $payload,
			'secret'	=> $secret,
		));

		if(!class_exists('BMHTTP'))
			include(B1GMAIL_DIR . 'serverlib/http.class.php');

		$http = _new('BMHTTP', array($url));
		$resp = $http->DownloadToString_POST($post, 'application/json');

		PutLog(sprintf('ALTCHA Cloud verify: "%s"', $resp),
			PRIO_DEBUG,
			__FILE__,
			__LINE__);

		$json = json_decode($resp);
		return(is_object($json) && !empty($json->verified));
	}
}

BMCaptcha::registerProvider(basename(__FILE__), 'BMCaptchaProvider_ALTCHA');
