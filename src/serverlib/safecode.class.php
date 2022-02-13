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
 * captcha generator
 *
 */
class BMCaptchaGenerator
{
	var $img;
	var $code;
	var $white, $black;
	var $w, $h, $letterW, $letterH, $borderSpacing;
	var $fontPath, $bgPath, $fontList, $bgList;
	var $perturbation;

	/**
	 * constructor
	 *
	 * @param string $code
	 * @return BMCaptchaGenerator
	 */
	public function __construct($code)
	{
		$this->fontPath			= B1GMAIL_DIR . 'res/fonts/';
		$this->bgPath			= B1GMAIL_DIR . 'res/bg/';
		$this->code 			= $code;
		$this->w				= 220;
		$this->h				= 60;
		$this->borderSpacing	= 3;
		$this->letterW			= floor(($this->w-2*$this->borderSpacing) / strlen($this->code));
		$this->letterH			= $this->h-3*$this->borderSpacing;
		$this->perturbation		= 5;
		$this->_readFonts();
		$this->_readBGs();
	}


	/**
	 * read fonts
	 *
	 */
	private function _readFonts()
	{
		$this->fontList = array();
		$d = dir($this->fontPath);
		while($entry = $d->read())
			if(strtolower(substr($entry, -4)) == '.ttf')
				$this->fontList[] = $this->fontPath . $entry;
		$d->close();
	}

	/**
	 * read backgrounds
	 *
	 */
	private function _readBGs()
	{
		$this->bgList = array();
		$d = dir($this->bgPath);
		while($entry = $d->read())
			if(strtolower(substr($entry, -4)) == '.jpg')
				$this->bgList[] = $this->bgPath . $entry;
		$d->close();
	}

	/**
	 * generate captcha
	 *
	 */
	private function _generateCaptcha()
	{
		$this->img 		= imagecreatetruecolor($this->w, $this->h);
		$this->white 	= imagecolorallocate($this->img, 255, 255, 255);
		$this->black 	= imagecolorallocate($this->img, 0, 0, 0);

		$this->_drawBG();
		$this->_placeLetters();
		$this->_drawPerturbation();
		$this->_drawBorder();
	}

	/**
	 * draw background
	 *
	 */
	private function _drawBG()
	{
		// get random bg
		$bgFile = $this->_randomBG();

		// load bg img
		$bgImg = imagecreatefromjpeg($bgFile);
		$bgW = imagesx($bgImg);
		$bgH = imagesy($bgImg);

		// area rect
		$areaW = min($bgW, mt_rand($this->w/4, $this->w*4));
		$areaH = min($bgH, mt_rand($this->h/4, $this->h*4));
		$areaX = mt_rand(0, $bgW-$areaW);
		$areaY = mt_rand(0, $bgH-$areaH);

		// copy area to image
		imagecopyresampled($this->img, $bgImg, 0, 0, $areaX, $areaY, $this->w, $this->h, $areaW, $areaH);
	}

	/**
	 * place letters
	 *
	 */
	private function _placeLetters()
	{
		for($i=0; $i<strlen($this->code); $i++)
		{
			$letter = $this->code[$i];
			$letterImg = $this->_generateLetter($letter, imagecolorsforindex($this->img, imagecolorat($this->img, $this->borderSpacing + $i*$this->letterW, $this->borderSpacing)));

			$w = imagesx($letterImg);
			$h = imagesy($letterImg);

			$xArea = $this->letterW - $w;
			$yArea = $this->letterH - $h;

			if($xArea < 0) { // Workaround for PHP 8, detecting if xArea is negative
				$x_mtrand = mt_rand($xArea/2, ($xArea/2)*-1);
			}
			else {
				$x_mtrand = mt_rand(($xArea/2)*-1, $xArea/2);
			}

			$x = max($this->borderSpacing, $this->borderSpacing + $i*$this->letterW
					+ $x_mtrand);
			$y = ($this->h-2*$this->borderSpacing)/2 - $h/2
					+ @mt_rand(($yArea/2)*-1, $yArea/2);

			imagecopy($this->img, $letterImg, $x, $y, 0, 0, $w, $h);
			imagedestroy($letterImg);
		}
	}

	/**
	 * generate letter
	 *
	 * @param string $letter
	 * @return resource
	 */
	private function _generateLetter($letter, $baseColor = false)
	{
		$angleRange	= ceil(($this->perturbation/10) * 45);

		$fontSize 	= 30;
		$fontAngle 	= mt_rand($angleRange*-1, $angleRange);
		$fontFile 	= $this->_randomFont();
		$fontBox 	= imagettfbbox($fontSize, 0, $fontFile, $letter);

		$letterW	= $fontBox[4] - $fontBox[6];
		$letterH	= $fontBox[1] - $fontBox[7];

		// color
		$r = mt_rand(0, 255);
		$g = mt_rand(0, 255);
		$b = mt_rand(0, 255);

		if($baseColor !== false)
		{
			if(abs($r - $baseColor['red']) < 50)
					$r += ($r < 255-50) ? 50 : -50;
			if(abs($g - $baseColor['green']) < 50)
					$g += ($g < 255-50) ? 50 : -50;
			if(abs($b - $baseColor['blue']) < 50)
					$b += ($b < 255-50) ? 50 : -50;
		}

		// generate
		$img 		= imagecreatetruecolor($letterW+4, $letterH+4);
		imagesavealpha($img, true);
		$white		= imagecolorallocatealpha($img, 255, 255, 255, 127);
		$fg			= imagecolorallocate($img, $r, $g, $b);
		imagefill($img, 0, 0, $white);
		imagettftext($img, $fontSize, 0, 0, $letterH, $fg, $fontFile, $letter);

		if(function_exists('imagerotate'))
		{
			$newImg = imagerotate($img, $fontAngle, $white);
			imagedestroy($img);
			return($newImg);
		}
		else
			return($img);
	}

	/**
	 * select random font
	 *
	 * @return string
	 */
	private function _randomFont()
	{
		return($this->fontList[ mt_rand(0, count($this->fontList)-1) ]);
	}

	/**
	 * select random bg
	 *
	 * @return string
	 */
	private function _randomBG()
	{
		return($this->bgList[ mt_rand(0, count($this->bgList)-1) ]);
	}

	private function _drawPerturbation()
	{
		$points = mt_rand($this->perturbation*25, $this->perturbation*100);
		for($i=0; $i<$points; $i++)
			imagesetpixel($this->img, mt_rand(0, $this->w), mt_rand(0, $this->h),
				imagecolorallocate($this->img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
	}

	/**
	 * draw border
	 *
	 */
	private function _drawBorder()
	{
		imagerectangle($this->img, 0, 0, $this->w-1, $this->h-1, $this->black);
	}

	/**
	 * output
	 *
	 */
	public function Output()
	{
		$this->_generateCaptcha();
		header('Content-Type: image/png');
		imagepng($this->img);
	}
}

/**
 * safe code class
 */
class Safecode
{
	/**
	 * request new safecode
	 *
	 * @return int
	 */
	static function RequestCode()
	{
		global $db;

		$code = Safecode::CodeGen(6);

		$db->Query('INSERT INTO {pre}safecode(code,generation) VALUES(?,UNIX_TIMESTAMP())',
			$code);

		return($db->InsertId());
	}

	/**
	 * release safecode
	 *
	 * @param int $id
	 */
	function ReleaseCode($id)
	{
		global $db;

		$db->Query('DELETE FROM {pre}safecode WHERE id=?',
			$id);
	}

	/**
	 * generate code
	 *
	 * @param int $chars
	 * @return string
	 */
	static function CodeGen($chars)
	{
		$vocals = 'aeiou';
		$cons = 'bcdfghjklmnpqrstvwxz';
		$result = '';

		for($i = 0; $i < $chars; $i++)
			if($i % 2 == 0)
				$result .= $vocals[ mt_rand(0, strlen($vocals)-1) ];
			else
				$result .= $cons[ mt_rand(0, strlen($cons)-1) ];

		return(strtoupper($result));
	}

	/**
	 * get code by id
	 *
	 * @param int $id
	 * @param bool $new New code?
	 * @return string
	 */
	function GetCode($id, $new = false)
	{
		global $db;

		if($new)
		{
			$code = Safecode::CodeGen(6);
			$db->Query('UPDATE {pre}safecode SET code=? WHERE id=?',
				$code,
				$id);
			return($code);
		}
		else
		{
			$res = $db->Query('SELECT code FROM {pre}safecode WHERE id=?',
				$id);
			$row = $res->FetchArray();
			$res->Free();
			return($row['code']);
		}
	}

	/**
	 * dump code as image
	 *
	 * @param int $id
	 */
	function DumpCode($id, $perturbation = -1)
	{
		global $bm_prefs;

		if($perturbation == -1)
			$perturbation = $bm_prefs['scsf'];

		// get code from db
		$z = $this->GetCode($id, true);

		// generate
		$generator = _new('BMCaptchaGenerator', array($z));
		$generator->perturbation = $perturbation/2;
		$generator->Output();
	}
}
