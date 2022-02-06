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
 * chart class
 */
class BMChart
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
	 * colors
	 *
	 * @var int
	 */
	var $_white, $_black, $_grey;

	/**
	 * plot rect
	 *
	 * @var uint
	 */
	var $_plotX, $_plotY, $_plotW, $_plotH;

	/**
	 * y/x axis start/end values
	 *
	 * @var int
	 */
	var $_yStart, $_yEnd, $_xStart, $_xEnd;

	/**
	 * y/x values per pixel (scaling factor)
	 *
	 * @var double
	 */
	var $_yPerPixel, $_xPerPixel;

	/**
	 * data to plot
	 *
	 * @var  array
	 */
	var $data;

	/**
	 * constructor
	 *
	 * @return BMChart
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
		$this->data = $data;

		// determiny yFrom and yTo value
		if($yFrom == -1
			|| $yTo == -1
			|| $xFrom == -1
			|| $xTo == -1)
		{
			$my_yFrom = $my_yTo = $my_xFrom = $my_xTo = -1;
			foreach($this->data as $x=>$y)
			{
				if($my_yFrom == -1)
				{
					$my_yFrom = $my_yTo = $y;
					$my_xFrom = $my_xTo = $x;
				}
				else
				{
					if($y < $my_yFrom)
						$my_yFrom = $y;
					if($y > $my_yTo)
						$my_yTo = $y;
					if($x < $my_xFrom)
						$my_xFrom = $x;
					if($x > $my_xTo)
						$my_xTo = $x;
				}
			}

			if($yFrom == -1)
				$yFrom = $my_yFrom;
			if($yTo == -1)
				$yTo = $my_yTo;
			if($xFrom == -1)
				$xFrom = $my_xFrom;
			if($xTo == -1)
				$xTo = $my_xTo;
		}
		$this->_yStart = $yFrom;
		$this->_yEnd = $yTo;
		$this->_xStart = $xFrom;
		$this->_xEnd = $xTo;

		// y spacing?
		$ySpacing = max($this->_plotH/10, 20);
		$this->_yPerPixel = ($this->_yEnd-$this->_yStart) / $this->_plotH;

		// draw Y lines / texts
		$yLines = floor($this->_plotH/$ySpacing);
		for($y=$this->_plotY+$this->_plotH; $y>=$this->_plotY; $y-=$ySpacing)
		{
			// line
			imageline($this->img,
				$this->_plotX-4,
				$y,
				$this->_plotX+$this->_plotW,
				$y,
				$this->_black);

			// text
			$relY = $this->_plotY+$this->_plotH-$y;
			$yValue = round($this->_yStart + $relY*$this->_yPerPixel, 1);
			$this->_rightText($yValue, 1, $y-6, $this->_plotX-8, $this->_black);
		}

		// x spacing?
		$xLines = count($data)-1;
		if($this->_plotW/$xLines < 12)
			$xLines = min(ceil($this->_xEnd-$this->_xStart), $this->_plotW/35);
		$xSpacing = $this->_plotW/$xLines;
		$this->_xPerPixel = ($this->_xEnd-$this->_xStart) / $this->_plotW;

		// draw X lines / texts
		$yLines = floor($this->_plotH/$ySpacing);
		for($x=$this->_plotX; $x<=$this->_plotX+$this->_plotW; $x+=$xSpacing)
		{
			// line
			imageline($this->img,
				$x,
				$this->_plotY+$this->_plotH+4,
				$x,
				$this->_plotY+$this->_plotH,
				$this->_black);

			// text
			$relX = $x-$this->_plotX;
			$xValue = round($this->_xStart + $relX*$this->_xPerPixel, 1);
			imagestring($this->img,
				2,
				$x-strlen($xValue)*3,
				$this->_plotY+$this->_plotH+8,
				$xValue,
				$this->_black);
		}

		// calc points
		$points = array();
		foreach($data as $x=>$y)
		{
			$x = $this->_plotX + ($x-$this->_xStart)/$this->_xPerPixel;
			if($this->_yPerPixel == 0)
				$y = $this->_plotY+$this->_plotH;
			else
				$y = $this->_plotY+$this->_plotH - ($y-$this->_yStart)/$this->_yPerPixel;
			$points[] = array($x, $y);
		}

		// draw points
		foreach($points as $key=>$pixel)
		{
			list($x, $y) = $pixel;
			imagefilledrectangle($this->img, $x-1, $y-1, $x+1, $y+1, $this->_black);

			if($key > 0)
				imageline($this->img, $points[$key-1][0], $points[$key-1][1],
					$x,
					$y,
					$this->_black);
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
		$this->_black = imagecolorallocate($this->img, 0, 0, 0);
		$this->_grey = imagecolorallocate($this->img, 0xC0, 0xC0, 0xC0);

		// draw border
		imagerectangle($this->img, 0, 0, $this->w-1, $this->h-1, $this->_black);

		// draw title
		$this->_centerText($this->title, 0, 10, $this->w, $this->_black);

		// plot rect
		$this->_plotX = ceil(1+0.1*$this->w);
		$this->_plotY = 40;
		$this->_plotW = $this->w - $this->_plotX - ceil(0.02*$this->w);
		$this->_plotH = $this->h - $this->_plotY - ceil(0.125*$this->h);

		// draw plot background
		imagefilledrectangle($this->img,
			$this->_plotX,
			$this->_plotY,
			$this->_plotX+$this->_plotW,
			$this->_plotY+$this->_plotH,
			$this->_grey);

		// draw axis
		imageline($this->img,
			$this->_plotX,
			$this->_plotY,
			$this->_plotX,
			$this->_plotY+$this->_plotH+4,
			$this->_black);
		imageline($this->img,
			$this->_plotX-4,
			$this->_plotY+$this->_plotH,
			$this->_plotX+$this->_plotW,
			$this->_plotY+$this->_plotH,
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
	 * draw text vertically and centered
	 *
	 * @param string $text
	 * @param int $x
	 * @param int $y
	 * @param int $h
	 * @param int $color
	 */
	function _centerTextV($text, $x, $y, $h, $color)
	{
		$y = $h/2 - strlen($text)*3 + $y;
		imagestringup($this->img, 2, $x, $y, $text, $color);
	}

	/**
	 * draw text horizontally and right aligned
	 *
	 * @param string $text
	 * @param int $x
	 * @param int $y
	 * @param int $w
	 * @param int $color
	 */
	function _rightText($text, $x, $y, $w, $color)
	{
		$x = $x+$w - strlen($text)*6;
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
