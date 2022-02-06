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

class BMCaptchaProvider_Default extends BMAbstractCaptchaProvider
{
	public $Safecode;
	public function __construct()
	{
		if(!class_exists('Safecode'))
			include(B1GMAIL_DIR . 'serverlib/safecode.class.php');
		$this->Safecode = new Safecode();
	}

	public function isAvailable()
	{
		return(function_exists('imagepng'));
	}

	public function generate()
	{
		if(!isset($_REQUEST['id']))
			return(false);
		$codeID = (int)$_REQUEST['id'];
		$config = $this->getConfig();

		$this->Safecode->DumpCode($codeID, max(1, min(10, $config['scsf'])));
	}

	public function getHTML()
	{
		global $lang_user;

		$codeID = $this->Safecode->RequestCode();
		return '<input type="hidden" name="codeID" value="' . $codeID . '" />'
			. '<img src="index.php?action=codegen&id=' . $codeID . '" style="cursor:pointer;" '
			. 'onclick="this.src=\'index.php?action=codegen&id=' . $codeID . '&rand=\'+parseInt(Math.random()*10000);" '
			. 'data-toggle="tooltip" data-placement="bottom" title="' . $lang_user['notreadable'] . '" />';
	}

	public function check($release = true)
	{
		if(!isset($_REQUEST['codeID']) || !isset($_REQUEST['safecode']))
			return(false);
		$codeID = (int)$_REQUEST['codeID'];

		$code = $this->Safecode->GetCode($codeID);
		if(strlen($code) < 4)
			return(false);

		if($release)
			$this->Safecode->ReleaseCode($codeID);

		$input = trim($_REQUEST['safecode']);
		if(strlen($input) < 4)
			return(false);

		return(strcasecmp($code, $input) == 0);
	}

	public function getInfo()
	{
		global $lang_admin;

		return(array(
			'title'				=> $lang_admin['default'],
			'author'			=> 'b1gMail.eu Project',
			'website'			=> 'https://www.b1gmail.eu/',
			'showNotReadable'	=> true,
			'hasOwnInput'		=> false,
			'hasOwnAJAXCheck'	=> false,
			'failAction'		=> '',
			'configFields'		=> array(
				'scsf'				=> array(
					'title'			=> $lang_admin['scsf'].' (1-10):',
					'type'			=> FIELD_TEXT,
					'default'		=> '6'
				)
			)
		));
	}
}

BMCaptcha::registerProvider(basename(__FILE__), 'BMCaptchaProvider_Default');
