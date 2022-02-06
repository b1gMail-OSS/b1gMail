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

include('./serverlib/init.inc.php');
RequestPrivileges(PRIVILEGES_USER);

/**
 * search sort callback
 */
function _searchSort($a, $b)
{
	global $sortColumn, $sortOrder;

	$result = 0;

	foreach(explode(',', $sortColumn) as $col)
	{
		if(!isset($b[$col]) || !isset($a[$col]))
			continue;

		switch($col)
		{
		case 'score':
		case 'size':
		case 'date':
			$result = $a[$col] - $b[$col];
			break;

		case 'title':
			$result = strcasecmp($a['title'], $b['title']);
			break;
		}

		if($result != 0)
			break;
	}

	if($sortOrder == 'desc')
		$result *= -1;

	return($result);
}

/**
 * file handler for modules
 */
ModuleFunction('FileHandler',
	array(substr(__FILE__, strlen(__DIR__)+1),
	isset($_REQUEST['action']) ? $_REQUEST['action'] : ''));

/**
 * default action = search
 */
if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'search';
$tpl->assign('activeTab', '_search');

/**
 * search query
 */
if(isset($_POST['q']))
	$q = trim($_POST['q']);
else if(isset($_GET['q']))
	$q = trim(_unescape($_GET['q']));
else
	$q = '';

/**
 * page menu
 */
$tpl->assign('pageMenuFile', 'li/search.sidebar.tpl');

/**
 * search
 */
if($_REQUEST['action'] == 'search')
{
	if(isset($_REQUEST['do'])
	   && $_REQUEST['do'] == 'massAction'
	   && isset($_REQUEST['items'])
	   && is_array($_REQUEST['items'])
	   && IsPOSTRequest())
	{
		$category =  '';
		foreach($_POST as $key=>$val)
		{
			if(substr($key, 0, 17) == 'submitMassAction_')
			{
				$category = substr($key, 17);
				break;
			}
		}

		if(isset($_REQUEST['massAction_' . $category]) && isset($_REQUEST['items'][$category])
		   && is_array($_REQUEST['items'][$category]))
		{
			$action = $_REQUEST['massAction_' . $category];

			$plugins->callFunction('HandleSearchMassAction', false, false, array($category, $action, $_REQUEST['items'][$category]));
		}
	}

	// categories
	$categoryPieces = $plugins->callFunction('GetSearchCategories', false, true, array());

	// merge categories
	$searchIn = array();
	$categories = array();
	foreach($categoryPieces as $categoryPiece)
	{
		foreach($categoryPiece as $catName=>$catInfo)
			$searchIn[$catName] = !isset($_REQUEST['searchIn']) || !is_array($_REQUEST['searchIn']) || in_array($catName, $_REQUEST['searchIn']);
		$categories = array_merge($categories, $categoryPiece);
	}

	// date
	$dateFrom = isset($_REQUEST['dateFromDay']) ? SmartyDateTime('dateFrom') 	: 0;
	$dateTo = isset($_REQUEST['dateToDay']) 	? SmartyDateTime('dateTo') 		: 0;
	if($dateFrom != 0)
		$dateFrom = mktime(0, 0, 0, date('m', $dateFrom), date('d', $dateFrom), date('Y', $dateFrom));
	if($dateTo != 0)
		$dateTo = mktime(0, 0, 0, date('m', $dateTo), date('d', $dateTo), date('Y', $dateTo));

	// sort info
	$sortColumns = array('score', 'date', 'size', 'title');
	$sortColumn = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], $sortColumns))
					? $_REQUEST['sort']
					: 'score,date,title,size';
	$sortOrder = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))
					? $_REQUEST['order']
					: 'desc';

	// perform search
	if(strlen($q) >= 3)
	{
		// search
		$resultPieces = $plugins->callFunction('OnSearch', false, true, array($q, $dateFrom, $dateTo));

		// merge results
		$resultCount = 0;
		$results = array();
		foreach($resultPieces as $resultPiece)
		{
			foreach($resultPiece as $subPieceKey=>$subPiece)
			{
				if(isset($subPiece['name']) && (!isset($searchIn[$subPiece['name']]) || !$searchIn[$subPiece['name']]))
					unset($resultPiece[$subPieceKey]);
				else
					foreach($subPiece['results'] as $result)
						$resultCount++;
			}
			$results = array_merge($results, $resultPiece);
		}

		// sort
		foreach($results as $key=>$val)
		{
			uasort($results[$key]['results'], '_searchSort');
		}

		// get page info
		$resultsPerPage = (int)$bm_prefs['ordner_proseite'];
		$pageNo = (isset($_REQUEST['page']))
						? (int)$_REQUEST['page']
						: 1;
		$pageCount = max(1, ceil($resultCount / max(1, $resultsPerPage)));
		$pageNo = min($pageCount, max(1, $pageNo));

		// limit pages
		$startNo = ($pageNo-1)*$resultsPerPage;
		$endNo = $startNo + $resultsPerPage;
		$currentNo = 0;
		foreach($results as $resultItemKey=>$resultItem)
		{
			foreach($resultItem['results'] as $resultKey=>$resultInfo)
			{
				if($currentNo < $startNo || $currentNo >= $endNo)
					unset($results[$resultItemKey]['results'][$resultKey]);

				$currentNo++;
			}

			if(count($results[$resultItemKey]['results']) == 0)
				unset($results[$resultItemKey]);
		}

		// show results
		$tpl->assign('sortColumn', $sortColumn);
		$tpl->assign('sortOrder', $sortOrder);
		$tpl->assign('sortOrderInv', $sortOrder == 'asc' ? 'desc' : 'asc');
		$tpl->assign('pageNo', $pageNo);
		$tpl->assign('pageCount', $pageCount);
		$tpl->assign('results', $results);
	}

	// page output
	$tpl->assign('flexSpans',	$bm_prefs['flexspans'] == 'yes');
	$tpl->assign('dateFrom',	$dateFrom == 0 	?  '--' : $dateFrom);
	$tpl->assign('dateTo',		$dateTo == 0 	?  '--' : $dateTo);
	$tpl->assign('searchIn',	$searchIn);
	$tpl->assign('categories', 	$categories);
	$tpl->assign('q', 			$q);
	$tpl->assign('encodedQ', 	_urlencode($q));
	$tpl->assign('pageTitle', 	$lang_user['search']);
	$tpl->assign('pageContent', 'li/search.details.tpl');
	$tpl->display('li/index.tpl');
}

/**
 * quick search
 */
else if($_REQUEST['action'] == 'quickSearch'
		&& isset($_REQUEST['q'])
		&& strlen(trim($_REQUEST['q'])) > 2)
{
	// search
	$resultPieces = $plugins->callFunction('OnSearch', false, true, array($q));

	// merge results
	$resultCount = 0;
	$results = array();
	foreach($resultPieces as $resultPiece)
	{
		foreach($resultPiece as $subPiece)
			$resultCount += count($subPiece['results']);
		$results = array_merge($results, $resultPiece);
	}

	// sort
	$sortColumn = 'score,date,title,size';
	$sortOrder = 'desc';
	foreach($results as $key=>$val)
	{
		uasort($results[$key]['results'], '_searchSort');
	}

	// limit results
	if($resultCount > MAX_SEARCH_RESULTS)
	{
		$factor = MAX_SEARCH_RESULTS / $resultCount;
		foreach($results as $key=>$val)
			$results[$key]['results'] = array_slice($val['results'], 0, ceil($factor*count($val['results'])));
	}

	// show results
	$tpl->assign('q', _urlencode($q));
	$tpl->assign('results', $results);
	$tpl->display('li/search.results.tpl');
}

/**
 * fts background indexing
 */
else if($_REQUEST['action'] == 'ftsBGIndexing'
		&& $bm_prefs['fts_bg_indexing'] == 'yes'
		&& $groupRow['ftsearch'] == 'yes'
		&& FTS_SUPPORT)
{
	@session_write_close();

	if(!class_exists('BMSearchIndex'))
		include(B1GMAIL_DIR . 'serverlib/searchindex.class.php');
	if(!class_exists('BMMailbox'))
		include(B1GMAIL_DIR . 'serverlib/mailbox.class.php');

	$mailbox = _new('BMMailbox', array($userRow['id'], $userRow['email'], $thisUser));
	$mails = $mailbox->GetMailsWithFlags(FLAG_INDEXED, false, FTS_BGINDEX_COUNT+1);

	if(count($mails) == 0)
	{
		echo '1';
		exit();
	}

	$idx = _new('BMSearchIndex', array($userRow['id']));
	$processedIDs = array();
	$haveMore = false;

	if(count($mails) > FTS_BGINDEX_COUNT)
	{
		$haveMore = true;
		array_pop($mails);
	}

	if(!$idx->beginTx(true))
	{
		echo '2';
		exit();
	}
	foreach($mails as $mailID)
	{
		$mail = $mailbox->GetMail($mailID);
		if(is_object($mail))
		{
			$mail->AddToIndex($idx);
			unset($mail);
		}

		$processedIDs[] = $mailID;
	}
	if(!$idx->endTx())
	{
		echo '2';
		exit();
	}

	foreach($processedIDs as $mailID)
		$mailbox->FlagMail(FLAG_INDEXED, true, $mailID);

	echo $haveMore ? '0' : '1';
	exit();
}
