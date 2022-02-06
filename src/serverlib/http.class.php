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
 * http request classs
 *
 */
class BMHTTP
{
	var $_fp;		// HTTP socket
	var $_url;		// full URL
	var $_host;		// HTTP host
	var $_protocol;	// protocol (HTTP/HTTPS)
	var $_uri;		// request URI
	var $_port;		// port

	/**
	 * constructor
	 *
	 * @param string $url URL
	 * @return BMHTTP
	 */
	function __construct($url)
	{
		$this->_url = $url;
		$this->_scan_url();
	}

	/**
	 * parse URL
	 *
	 */
	function _scan_url()
	{
		$req = $this->_url;

		$pos = strpos($req, '://');
		$this->_protocol = strtolower(substr($req, 0, $pos));

		$req = substr($req, $pos+3);
		$pos = strpos($req, '/');
		if($pos === false)
			$pos = strlen($req);
		$host = substr($req, 0, $pos);

		if(strpos($host, ':') !== false)
		{
			list($this->_host, $this->_port) = explode(':', $host);
		}
		else
		{
			$this->_host = $host;
			$this->_port = ($this->_protocol == 'https') ? 443 : 80;
		}

		$this->_uri = substr($req, $pos);
		if($this->_uri == '')
			$this->_uri = '/';
	}

	/**
	 * create the socket in self::$_fp (for SSL with enabled peer verification)
	 *
	 */
	private function createSocket()
	{
		if($this->_protocol === 'https')
		{
			$streamContext = stream_context_create([
				'ssl' => [
					'verify_peer' => true,
					'verify_peer_name' => true,
					'allow_self_signed' => false,
					'cafile' => B1GMAIL_DIR . 'res/ca.pem'
				]
			]);
			$errNo = $errStr = null;
			$this->_fp = stream_socket_client('ssl://' . $this->_host . ':' . $this->_port,
				$errNo,
				$errStr,
				SOCKET_TIMEOUT,
				STREAM_CLIENT_CONNECT,
				$streamContext);
		}
		else
		{
			$this->_fp = fsockopen($this->_host, $this->_port,
				$errNo, $errStr, SOCKET_TIMEOUT);
		}
	}

	/**
	 * download URL and return contents
	 *
	 * @return string
	 */
	function DownloadToString()
	{
		$crlf = "\r\n";

		// generate request
		$req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf
			.	'Host: ' . $this->_host . $crlf
			.	$crlf;

		// fetch
		$this->createSocket();
		fwrite($this->_fp, $req);
		$response = '';
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
			$response .= fread($this->_fp, 1024);
		fclose($this->_fp);

		// split header and body
		$pos = strpos($response, $crlf . $crlf);
		if($pos === false)
			return($response);
		$header = substr($response, 0, $pos);
		$body = substr($response, $pos + 2 * strlen($crlf));

		// parse headers
		$headers = array();
		$lines = explode($crlf, $header);
		foreach($lines as $line)
			if(($pos = strpos($line, ':')) !== false)
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));

		// redirection?
		if(isset($headers['location']))
		{
			$http = new BMHTTP($headers['location']);
			return($http->DownloadToString($http));
		}
		else
		{
			return($body);
		}
	}

	/**
	 * make post request and return response
	 *
	 * @param string $postData Data to post to the URL
	 * @param string $contentType Data content type
	 * @return string
	 */
	function DownloadToString_POST($postData, $contentType = 'application/x-www-form-urlencoded', &$headers = null)
	{
		$crlf = "\r\n";

		// generate request
		$req = 'POST ' . $this->_uri . ' HTTP/1.0' . $crlf
			.	'Host: ' . $this->_host . $crlf
			.	'Content-Type: ' . $contentType . $crlf
			.	'Content-Length: ' . strlen($postData) . $crlf
			.	$crlf;

		// fetch
		$this->createSocket();
		fwrite($this->_fp, $req);
		fwrite($this->_fp, $postData);
		$response = '';
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
			$response .= fread($this->_fp, 1024);
		fclose($this->_fp);

		// split header and body
		$pos = strpos($response, $crlf . $crlf);
		if($pos === false)
			return($response);
		$header = substr($response, 0, $pos);
		$body = substr($response, $pos + 2 * strlen($crlf));

		// parse headers
		$headers = array();
		$lines = explode($crlf, $header);
		foreach($lines as $line)
			if(($pos = strpos($line, ':')) !== false)
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));

		// redirection?
		if(isset($headers['location']))
		{
			$http = new BMHTTP_POST($headers['location']);
			return($http->DownloadToString_POST($postData, $contentType, $headers));
		}
		else
		{
			return($body);
		}
	}

	/**
	 * make post request and write response to FP
	 *
	 * @param string $postData Data to post to the URL
	 * @param string $contentType Data content type
	 * @return bool
	 */
	function DownloadToFP_POST($fp, $postData, $contentType = 'application/x-www-form-urlencoded', &$headers = null)
	{
		$crlf = "\r\n";

		// generate request
		$req = 'POST ' . $this->_uri . ' HTTP/1.0' . $crlf
			.	'Host: ' . $this->_host . $crlf
			.	'Content-Type: ' . $contentType . $crlf
			.	'Content-Length: ' . strlen($postData) . $crlf
			.	$crlf;

		// fetch
		$this->createSocket();
		if(!$this->_fp)
			return(false);
		fwrite($this->_fp, $req);
		fwrite($this->_fp, $postData);
		$header = '';
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
		{
			$line = fgets($this->_fp, 1024);
			if(trim($line) == '')
				break;
			$header .= rtrim($line) . "\r\n";
		}
		while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
		{
			$buffer = fread($this->_fp, 1024);
			fwrite($fp, $buffer);
		}
		fclose($this->_fp);

		// parse headers
		$headers = array();
		$lines = explode($crlf, $header);
		foreach($lines as $line)
			if(($pos = strpos($line, ':')) !== false)
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));

		// redirection?
		if(isset($headers['location']))
		{
			$http = new BMHTTP($headers['location']);
			return($http->DownloadToFP_POST($fp, $postData, $contentType, $headers));
		}
		else
		{
			return(true);
		}
	}
}
