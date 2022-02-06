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
 * bar chart class
 */
class BMBarChart
{
	/**
	 * chart width
	 *
	 * @var int
	 */
	var $w;

	/**
	 * chart height
	 *
	 * @var int
	 */
	var $h;

	/**
	 * chart title
	 *
	 * @var string
	 */
	var $title;

	/**
	 * image
	 *
	 * @var resource
	 */
	var $img;

	/**
	 * data to plot
	 *
	 * @var  array
	 */
	var $data;

	/**
	 * bar width
	 *
	 * @var int
	 */
	var $_barWidth;

	/**
	 * constructor
	 *
	 * @return BMBarChart
	 */
	function __construct($title, $width, $height)
	{
		$this->w = $width;
		$this->h = $height;
		$this->img = imagecreate($this->w, $this->h);
		$this->title = $title;
		$this->_init();
	}

	/**
	 * set graph data
	 *
	 * @param array $data
	 * @param int $yFrom
	 * @param int $yTo
	 */
	function SetData($data, $yFrom = -1, $yTo = -1, $xFrom = -1, $xTo = -1)
	{
		global $lang_admin;

		$colors = array();
		$colors[] = imagecolorallocate($this->img, 0xFF, 0x80, 0x80);
		$colors[] = imagecolorallocate($this->img, 0x00, 0x80, 0xFF);
		$colors[] = imagecolorallocate($this->img, 0x00, 0x80, 0x40);
		$colors[] = imagecolorallocate($this->img, 0x00, 0x00, 0xFF);
		$colors[] = imagecolorallocate($this->img, 0xFF, 0x00, 0x00);
		$colors[] = imagecolorallocate($this->img, 0x80, 0xFF, 0x00);

		$dataSum = array_sum($data);
		$pos = $c = $captionPos = 0;

		arsort($data);

		if(count($data) > 4)
		{
			$i = 0;
			$miscSum = 0;
			foreach($data as $key=>$val)
			{
				if($i++ >= 4)
				{
					unset($data[$key]);
					$miscSum += $val;
				}
			}
			$data[$lang_admin['misc']] = $miscSum;
		}

		foreach($data as $key=>$val)
		{
			if(strlen($key) > 12)
				$key = substr($key, 0, 9) . '...';

			$color = $colors[$c++];

			// bar
			$width = round(($this->_barWidth/$dataSum) * $val, 0);

			if($width > 0)
			{
				imagefilledrectangle($this->img,
					$this->_barX+$pos+1,
					$this->_barY+1,
					$this->_barX+$pos+$width,
					$this->_barY+$this->_barHeight-1,
					$color);
			}

			if($width > 2)
			{
				imagerectangle($this->img,
					$this->_barX+$pos,
					$this->_barY,
					$this->_barX+$pos+$width,
					$this->_barY+$this->_barHeight,
					$this->_black);
			}

			// caption
			imagefilledrectangle($this->img,
				$captionPos+5,
				$this->_barY+$this->_barHeight+8,
				$captionPos+12,
				$this->_barY+$this->_barHeight+16,
				$color);
			imagerectangle($this->img,
				$captionPos+5,
				$this->_barY+$this->_barHeight+8,
				$captionPos+12,
				$this->_barY+$this->_barHeight+16,
				$this->_black);
			imagestring($this->img, 2, $captionPos+18, $this->_barY+$this->_barHeight+5, $key, $this->_black);
			$captionPos += strlen($key)*6 + 28;

			$pos += $width;
		}
	}

	/**
	 * init
	 *
	 */
	function _init()
	{
		// register colors
		$this->_white = imagecolorallocate($this->img, 255, 255, 255);
		$this->_grey = imagecolorallocate($this->img, 0xEE, 0xEE, 0xEE);
		$this->_black = imagecolorallocate($this->img, 0, 0, 0);

		// draw title
		$this->_centerText($this->title, 0, 10, $this->w, $this->_black);

		// bar size
		$this->_barX = 5;
		$this->_barY = 25 + $this->_barX;
		$this->_barWidth = $this->w - 2*$this->_barX;
		$this->_barHeight = $this->h - $this->_barY - 2*$this->_barX - 20;

		// draw bar
		imagerectangle($this->img,
			$this->_barX,
			$this->_barY,
			$this->_barX+$this->_barWidth,
			$this->_barY+$this->_barHeight,
			$this->_black);
	}

	/**
	 * draw text horizontally and centered
	 *
	 * @param string $text
	 * @param int $x
	 * @param int $y
	 * @param int $w
	 * @param int $color
	 */
	function _centerText($text, $x, $y, $w, $color)
	{
		$x = $w/2 - strlen($text)*3 + $x;
		imagestring($this->img, 2, $x, $y, $text, $color);
	}

	/**
	 * display chart (send to browser)
	 *
	 */
	function Display()
	{
		// output
		header('Content-Type: image/png');
		imagepng($this->img);
	}
}
