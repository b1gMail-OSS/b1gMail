<?php
/*
 * b1gMail
 * Copyright (c) 2021 Patrick Schlangen et al, 2022-2025 b1gMail.eu
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

require './serverlib/init.inc.php';

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	$_REQUEST['action'] ?? ''));

/**
 * clean url with known tracking links
 *
 * @param string $string
 * @return $string
 */
function cleanUrl($targetURL) {
    // Parse the URL and extract the query parameters
    $parsedUrl = parse_url($targetURL);
    parse_str($parsedUrl['query'], $queryParams);

    // Check if the URL contains 'safelinks.protection.outlook'
    if (strpos($targetURL, 'safelinks.protection.outlook')) {
        if (isset($queryParams['url'])) {
            // Decode the URL and parse it
            $decodedUrl = urldecode($queryParams['url']);
            $parsedUrl = parse_url($decodedUrl);
			if(!empty($parsedUrl['query'])) parse_str($parsedUrl['query'], $queryParams);
			else $queryParams = [];
        }
    }

    // Remove UTM parameters
    foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $utmParam) {
        unset($queryParams[$utmParam]);
    }

    // Build the cleaned URL without UTM parameters
    $cleanUrlParam = http_build_query($queryParams);
    $finalCleanUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ($parsedUrl['path'] ?? '') . 
                     (!empty($cleanUrlParam) ? '?' . $cleanUrlParam : '');

    return $finalCleanUrl;
}



/**
 * check referer
 */
if(!isset($_SERVER['HTTP_REFERER'])
	|| strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower($_SERVER['HTTP_HOST'])) === false)
{
	if($bm_prefs['cookie_lock'] == 'yes')
	{
		$ok = false;
		foreach($_COOKIE as $key=>$val)
			if(substr($key, 0, strlen('sessionSecret_')) == 'sessionSecret_')
				$ok = true;
		if(!$ok)
			die('Access denied');
	}
}

/**
 * deref code
 */
$url = $_SERVER['REQUEST_URI'];
$sepPos = strpos($url, '?');
if($sepPos !== false)
{
	$targetURL = cleanUrl(substr($url, $sepPos+1));
	$targetURL = str_replace('%23','#',$targetURL);
	$tpl->assign('pref_exturl_warning', $bm_prefs['exturl_warning']);
	if($bm_prefs['exturl_warning']=='no') {
		$tpl->assign('url', HTMLFormat($targetURL));
	}
	else {
		$tpl->assign('exturlwarningurl', sprintf($lang_custom['deref'], '<a href="'.HTMLFormat($targetURL).'" rel="noreferrer nofollow noopener">'.HTMLFormat($targetURL).'</a>'));
	}
	$tpl->display('nli/deref.tpl');
}