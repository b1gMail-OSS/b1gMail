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
 * Strip 4-byte UTF-8 characters from a string, but only if current charset
 * is UTF-8.
 *
 * @param string $in String
 * @return string
 */
function Strip4ByteChars($in)
{
	global $currentCharset;
	if(DB_CHARSET=='utf8mb4') {

	}
	else if(in_array(strtolower($currentCharset), array('utf8', 'utf-8')))
        // It is possible that the provided string is actually not an UTF8 string. In this case, preg_replace will return null.
        $result = preg_replace('/[\x{10000}-\x{10FFFF}]/u', ' ', $in);
        if (isset($result) && !is_null($result)) {
            return $result;
        }

	return $in;
}

/**
 * Locale-formatted strftime using \IntlDateFormatter (PHP 8.1 compatible)
 * This provides a cross-platform alternative to strftime() for when it will be removed from PHP.
 * Note that output can be slightly different between libc sprintf and this function as it is using ICU.
 *
 *
 * @param  string $format Date format
 * @param  integer|string|DateTime $timestamp Timestamp
 * @return string
 * @author BohwaZ <https://bohwaz.net/>
 */

// From https://gist.github.com/bohwaz/42fc223031e2b2dd2585aab159a20f30

function _strftime(string $format, $timestamp = null, ?string $locale = null): string
{
	if (null === $timestamp) {
		$timestamp = new \DateTime;
	}
	elseif (is_numeric($timestamp)) {
		$timestamp = date_create('@' . $timestamp);

		if ($timestamp) {
			$timestamp->setTimezone(new \DateTimezone(date_default_timezone_get()));
		}
	}
	elseif (is_string($timestamp)) {
		$timestamp = date_create($timestamp);
	}

	if (!($timestamp instanceof \DateTimeInterface)) {
		throw new \InvalidArgumentException('$timestamp argument is neither a valid UNIX timestamp, a valid date-time string or a DateTime object.');
	}

	$locale = substr((string) $locale, 0, 5);

	$intl_formats = [
		'%a' => 'EEE',	// An abbreviated textual representation of the day	Sun through Sat
		'%A' => 'EEEE',	// A full textual representation of the day	Sunday through Saturday
		'%b' => 'MMM',	// Abbreviated month name, based on the locale	Jan through Dec
		'%B' => 'MMMM',	// Full month name, based on the locale	January through December
		'%h' => 'MMM',	// Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
	];

	$intl_formatter = function (\DateTimeInterface $timestamp, string $format) use ($intl_formats, $locale) {
		$tz = $timestamp->getTimezone();
		$date_type = \IntlDateFormatter::FULL;
		$time_type = \IntlDateFormatter::FULL;
		$pattern = '';

		// %c = Preferred date and time stamp based on locale
		// Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
		if ($format == '%c') {
			$date_type = \IntlDateFormatter::LONG;
			$time_type = \IntlDateFormatter::SHORT;
		}
		// %x = Preferred date representation based on locale, without the time
		// Example: 02/05/09 for February 5, 2009
		elseif ($format == '%x') {
			$date_type = \IntlDateFormatter::SHORT;
			$time_type = \IntlDateFormatter::NONE;
		}
		// Localized time format
		elseif ($format == '%X') {
			$date_type = \IntlDateFormatter::NONE;
			$time_type = \IntlDateFormatter::MEDIUM;
		}
		else {
			$pattern = $intl_formats[$format];
		}

		return (new \IntlDateFormatter($locale, $date_type, $time_type, $tz, null, $pattern))->format($timestamp);
	};

	// Same order as https://www.php.net/manual/en/function.strftime.php
	$translation_table = [
		// Day
		'%a' => $intl_formatter,
		'%A' => $intl_formatter,
		'%d' => 'd',
		'%e' => function ($timestamp) {
			return sprintf('% 2u', $timestamp->format('j'));
		},
		'%j' => function ($timestamp) {
			// Day number in year, 001 to 366
			return sprintf('%03d', $timestamp->format('z')+1);
		},
		'%u' => 'N',
		'%w' => 'w',

		// Week
		'%U' => function ($timestamp) {
			// Number of weeks between date and first Sunday of year
			$day = new \DateTime(sprintf('%d-01 Sunday', $timestamp->format('Y')));
			return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
		},
		'%V' => 'W',
		'%W' => function ($timestamp) {
			// Number of weeks between date and first Monday of year
			$day = new \DateTime(sprintf('%d-01 Monday', $timestamp->format('Y')));
			return sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
		},

		// Month
		'%b' => $intl_formatter,
		'%B' => $intl_formatter,
		'%h' => $intl_formatter,
		'%m' => 'm',

		// Year
		'%C' => function ($timestamp) {
			// Century (-1): 19 for 20th century
			return floor($timestamp->format('Y') / 100);
		},
		'%g' => function ($timestamp) {
			return substr($timestamp->format('o'), -2);
		},
		'%G' => 'o',
		'%y' => 'y',
		'%Y' => 'Y',

		// Time
		'%H' => 'H',
		'%k' => function ($timestamp) {
			return sprintf('% 2u', $timestamp->format('G'));
		},
		'%I' => 'h',
		'%l' => function ($timestamp) {
			return sprintf('% 2u', $timestamp->format('g'));
		},
		'%M' => 'i',
		'%p' => 'A', // AM PM (this is reversed on purpose!)
		'%P' => 'a', // am pm
		'%r' => 'h:i:s A', // %I:%M:%S %p
		'%R' => 'H:i', // %H:%M
		'%S' => 's',
		'%T' => 'H:i:s', // %H:%M:%S
		'%X' => $intl_formatter, // Preferred time representation based on locale, without the date

		// Timezone
		'%z' => 'O',
		'%Z' => 'T',

		// Time and Date Stamps
		'%c' => $intl_formatter,
		'%D' => 'm/d/Y',
		'%F' => 'Y-m-d',
		'%s' => 'U',
		'%x' => $intl_formatter,
	];

	$out = preg_replace_callback('/(?<!%)(%[a-zA-Z])/', function ($match) use ($translation_table, $timestamp) {
		if ($match[1] == '%n') {
			return "\n";
		}
		elseif ($match[1] == '%t') {
			return "\t";
		}

		if (!isset($translation_table[$match[1]])) {
			throw new \InvalidArgumentException(sprintf('Format "%s" is unknown in time format', $match[1]));
		}

		$replace = $translation_table[$match[1]];

		if (is_string($replace)) {
			return $timestamp->format($replace);
		}
		else {
			return $replace($timestamp, $match[1]);
		}
	}, $format);

	$out = str_replace('%%', '%', $out);
	return $out;
}

/**
 * multi byte compliant strlen() implementation
 * (return number of characters)
 *
 * @param string $str Input
 * @return int
 */
function _strlen($str)
{
	global $currentCharset;

	if(function_exists('mb_strlen'))
		return(mb_strlen($str, $currentCharset));
	if(function_exists('iconv_strlen'))
		return(iconv_strlen($str, $currentCharset));

	return(strlen($str));
}

/**
 * multi byte compliant substr() implementation
 *
 * @param string $str Input
 * @param int $start Start position
 * @param int $length Length
 * @return string
 */
function _substr($str, $start, $length = false)
{
	global $currentCharset;

	if($length === false)
		$length = _strlen($str) - $start;

	if(function_exists('mb_substr'))
		return(mb_substr($str, $start, $length, $currentCharset));
	if(function_exists('iconv_substr'))
		return(iconv_substr($str, $start, $length, $currentCharset));

	return(substr($str, $start, $length));
}

/**
 * check if $char is a whitespace char
 *
 * @param string $char Char
 * @return bool
 */
function _is_whitespace($char)
{
	return(in_array($char, array(' ', "\n", "\r", "\t")));
}

/**
 * multi byte compliant wordwrap() implementation
 *
 * @param string $str Input
 * @param int $width Line width
 * @param string $break Break string
 * @param bool $cut Cut words?
 * @return string
 */
function _wordwrap($str, $width = 75, $break = null, $cut = null)
{
	global $currentCharset;

	$currentLineLength = 0;
	$result = '';
	$shouldBreak = false;
	$strLen = _strlen($str);

	// it is _way_ faster to use preg_split to split the string
	// into characters than to use _substr / mb_substr
	if(in_array(strtolower($currentCharset), array('utf8', 'utf-8')) && function_exists('preg_split'))
	{
		$processed = 0;
		while($processed < $strLen)
		{
			// prevent splitting a too long string at once by processing it
			// in chunks
			$chunk = _substr($str, $processed, 2048);
			$processed += _strlen($chunk);
			$chars = preg_split('//u', $chunk, -1, PREG_SPLIT_NO_EMPTY);

			foreach($chars as $c)
			{
				$result .= $c;

				if($c == "\n")
					$currentLineLength = 0;
				else
					$currentLineLength++;

				if($currentLineLength >= $width)
				{
					if($cut || ($shouldBreak && _is_whitespace($c)))
					{
						$result = rtrim($result) . $break;
						$currentLineLength = 0;
						$shouldBreak = false;
					}
					else
						$shouldBreak = true;
				}
			}
		}
	}

	// fall back to slower method if PCRE does not support UTF-8
	else
	{
		for($i=0; $i<$strLen; $i++)
		{
			$c = _substr($str, $i, 1);
			$result .= $c;

			if($c == "\n")
				$currentLineLength = 0;
			else
				$currentLineLength++;

			if($currentLineLength >= $width)
			{
				if($cut || ($shouldBreak && _is_whitespace($c)))
				{
					$result = rtrim($result) . $break;
					$currentLineLength = 0;
					$shouldBreak = false;
				}
				else
					$shouldBreak = true;
			}
		}
	}

	return($result);
}

/**
 * decode hex-encoded char
 *
 * @param string $hex Hex string
 * @return string
 */
function _hex2utf($matches)
{
	$num = hexdec($matches[1]);
	if($num < 128)
		return(chr($num));
	else if($num < 1924)
		return(chr(($num >> 6) + 192) . chr(($num & 63) + 128));
	else if($num < 32768)
		return(chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128));
	else if($num < 2097152)
		return(chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128));
	return('');
}

/**
 * decode javascript escaped string
 *
 * @param string $str Input
 * @return string
 */
function _unescape($str)
{
	global $currentCharset;

	return(preg_replace_callback('/%u([0-9ABCDEF]{3,4})/i', '_hex2utf', CharsetDecode($str, 'ISO-8859-1')));
}

function _urlencode($str)
{
	global $currentCharset;

	if(strtolower($currentCharset) == 'utf-8' || strtolower($currentCharset) == 'utf8')
		return(urlencode(CharsetDecode($str, false, 'iso-8859-1')));
	return(urlencode($str));
}

function _urldecode($str)
{
	global $currentCharset;

	if(strtolower($currentCharset) == 'utf-8' || strtolower($currentCharset) == 'utf8')
		return(CharsetDecode(urldecode($str), 'iso-8859-1'));
	return(urldecode($str));
}

/**
 * Decode from UTF-8 to current charset provided that data has been passed over XMLHttpRequest
 * or with charset=utf-8 content type.
 *
 * @param string $in
 * @return string
 */
function AjaxCharsetDecode($in)
{
	global $currentCharset;

	// AJAX data is always utf-8 encoded, so there's nothing to do when we're in utf-8 mode
	if(in_array(strtolower($currentCharset), array('utf8', 'utf-8')))
		return $in;

	// Otherwise, check if this is AJAX-submitted
	if(strpos(strtolower($_SERVER['CONTENT_TYPE']), 'charset=utf-8') !== false
		|| (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'charset') === false
			&& isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
	{
		return CharsetDecode($in, 'utf8');
	}

	return $in;
}

/**
 * convert string charset
 *
 * @param string $text Input
 * @param string $charset Input charset (false => $currentCharset)
 * @param string $destCharset Output charset (false => $currentCharset)
 * @return string
 */
function CharsetDecode($text, $charset = false, $destCharset = false)
{
	global $currentCharset;

	if($charset !== false && $charset != '')
		$charset = trim(strtolower($charset));
	else
		$charset = trim(strtolower($currentCharset));

	$_charset = $charset;

	if($destCharset !== false)
		$myCharset = trim(strtolower($destCharset));
	else
		$myCharset = trim(strtolower($currentCharset));

	$_myCharset = $myCharset;

	if(substr($charset, 0, 10) == 'iso-8859-1')
		$charset = 'iso-8859-1';
	if(substr($myCharset, 0, 10) == 'iso-8859-1')
		$myCharset = 'iso-8859-1';

	if($charset == $myCharset)
		return($text);

	if(function_exists('mb_convert_encoding'))
	{
		if($newText = @mb_convert_encoding($text, $_myCharset, $_charset))
			$text = $newText;
		else if($newText = @mb_convert_encoding($text, $myCharset, $charset))
			$text = $newText;
	}
	else if(function_exists('iconv'))
	{
		if($newText = @iconv($_charset, $_myCharset, $text))
			$text = $newText;
		else if($newText = @iconv($charset, $myCharset, $text))
			$text = $newText;
	}
	else if(function_exists('utf8_encode'))
	{
		switch($charset)
		{
		case 'iso-8859-1':
			if($myCharset == 'utf8' || $myCharset == 'utf-8')
				$text = utf8_encode($text);
			break;

		case 'utf8':
		case 'utf-8':
			if($myCharset == 'iso-8859-1')
				$text = utf8_decode($text);
			break;
		}
	}

	return($text);
}
