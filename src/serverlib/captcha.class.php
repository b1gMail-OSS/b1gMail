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

/**
 * captcha provider interface
 */
interface BMCaptchaProviderInterface
{
	/**
	 * generate captcha HTML code
	 *
	 * @return string
	 */
	public function getHTML();

	/**
	 * check captcha
	 *
	 * @param bool $release Whether it is safe to release data associated with the current captcha
	 * @return bool
	 */
	public function check($release = true);

	/**
	 * get captcha provider info array
	 *
	 * @return array
	 */
	public function getInfo();

	/**
	 * generate and output captcha image (if applicable)
	 *
	 * @return void
	 */
	public function generate();

	/**
	 * check if this captcha provider is available (i.e. all system requirements are met)
	 *
	 * @return bool
	 */
	public function isAvailable();

	/**
	 * sets the internal name of the captcha provider (called by factory)
	 *
	 * @return void
	 */
	public function setInternalName($name);
}

/**
 * abstract captcha provider with commonly used functions
 */
abstract class BMAbstractCaptchaProvider implements BMCaptchaProviderInterface
{
	/**
	 * internal name of the captcha provider
	 */
	private $internalName;

	/**
	 * sets the internal name of the captcha provider (called by factory)
	 *
	 * @return void
	 */
	public function setInternalName($name)
	{
		$this->internalName = $name;
	}

	/**
	 * get the captcha provider's current config
	 *
	 * @return array
	 */
	protected function getConfig()
	{
		global $bm_prefs;

		$result = array();
		$info = $this->getInfo();

		foreach($info['configFields'] as $key=>$val)
			$result[$key] = $val['default'];

		$config = @unserialize($bm_prefs['captcha_config']);
		if(is_array($config) && isset($config[$this->internalName]))
			$result = array_merge($result, $config[$this->internalName]);

		return($result);
	}
}

/**
 * captcha provider management clas
 */
class BMCaptcha
{
	/**
	 * array of captcha provider names
	 */
	private static $providers = array();

	/**
	 * returns the name of the default provider
	 *
	 * @return string
	 */
	public static function getDefaultProvider()
	{
		global $bm_prefs;
		return($bm_prefs['captcha_provider']);
	}

	/**
	 * returns an instance of the default provider
	 *
	 * @return BMCaptchaProviderInterface
	 */
	public static function createDefaultProvider()
	{
		return(BMCaptcha::createProvider(BMCaptcha::getDefaultProvider()));
	}

	/**
	 * get captcha provider directory
	 *
	 * @return string
	 */
	private static function getCaptchaDir()
	{
		return(B1GMAIL_DIR . 'serverlib/captchaprovider/');
	}

	/**
	 * get list of all available captcha providers
	 *
	 * @return array provider name => provider info array
	 */
	public static function getAvailableProviders()
	{
		$result = array();
		BMCaptcha::loadAllProviders();

		foreach(BMCaptcha::getProviders() as $provider)
		{
			$p = BMCaptcha::createProvider($provider);
			if(!$p->isAvailable())
				continue;
			$result[$provider] = $p->getInfo();
		}

		return($result);
	}

	/**
	 * create an instance of a captcha provider
	 *
	 * @param string $provider Name of the provider to create
	 * @return BMCaptchaProviderInterface
	 */
	private static function createProvider($provider)
	{
		list($file, $class) = explode('/', $provider);

		if(!class_exists($class))
			include(BMCaptcha::getCaptchaDir() . $file);

		$p = new $class;
		if(!($p instanceof BMCaptchaProviderInterface))
		{
			DisplayError(0x19, 'Invalid captcha provider', 'Captcha providers must implement the BMCaptchaProviderInterface interface.',
				sprintf("File: %\nProvider:\n%s", $file, $class),
				__FILE__,
				__LINE__);
			exit();
		}
		$p->setInternalName($provider);

		return($p);
	}

	/**
	 * load all available captcha providers
	 *
	 * @return void
	 */
	private static function loadAllProviders()
	{
		$captchaDir = BMCaptcha::getCaptchaDir();

		$d = dir($captchaDir);
		while($entry = $d->read())
		{
			if($entry[0] == '.' || strpos($entry, '.php') === false)
				continue;

			include_once($captchaDir . $entry);
		}
	}

	/**
	 * get array of available providers
	 *
	 * @return array
	 */
	private static function getProviders()
	{
		return(array_unique(BMCaptcha::$providers));
	}

	/**
	 * register a captcha provider with the captcha system
	 * (called by captcha provider file after inclusion)
	 *
	 * @return void
	 */
	public static function registerProvider($file, $provider)
	{
		BMCaptcha::$providers[] = $file . '/' . $provider;
	}
}
