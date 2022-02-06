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

include('../serverlib/admin.inc.php');
RequestPrivileges(PRIVILEGES_ADMIN);
AdminRequirePrivilege('optimize');

if(!isset($_REQUEST['action']))
	$_REQUEST['action'] = 'db';

$tabs = array(
	0 => array(
		'title'		=> $lang_admin['db'],
		'relIcon'	=> 'db_optimize.png',
		'link'		=> 'optimize.php?',
		'active'	=> $_REQUEST['action'] == 'db'
	),
	1 => array(
		'title'		=> $lang_admin['filesystem'],
		'relIcon'	=> 'tempfiles.png',
		'link'		=> 'optimize.php?action=filesystem&',
		'active'	=> $_REQUEST['action'] == 'filesystem'
	),
	2 => array(
		'title'		=> $lang_admin['cache'],
		'relIcon'	=> 'cache.png',
		'link'		=> 'optimize.php?action=cache&',
		'active'	=> $_REQUEST['action'] == 'cache'
	)
);

/**
 * optimize DB
 */
if($_REQUEST['action'] == 'db')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'execute')
	{
		if($_REQUEST['operation'] == 'struct')
		{
			// read default structure
			include('../serverlib/database.struct.php');
			$databaseStructure = unserialize(base64_decode($databaseStructure));

			// get tables
			$defaultTables = array();
			$res = $db->Query('SHOW TABLES');
			while($row = $res->FetchArray(MYSQLI_NUM))
				$myTables[] = $row[0];
			$res->Free();

			// compare tables
			$result = array();
			$repair = false;
			foreach($databaseStructure as $tableName=>$tableInfo)
			{
				$tableFields = $tableInfo['fields'];
				$tableIndexes = $tableInfo['indexes'];

				$tableResult = array();
				$tableResult['table'] = $tableName;
				$tableResult['exists'] = false;
				$tableResult['missing'] = 0;
				$tableResult['invalid'] = 0;

				if(in_array($tableName, $myTables))
				{
					$tableResult['exists'] = true;

					// get my fields
					$myFields = array();
					$res = $db->Query('SHOW FIELDS FROM ' . $tableName);
					while($row = $res->FetchArray(MYSQLI_ASSOC))
					{
						if($row['Null'] == '') $row['Null'] = 'NO';
						$myFields[$row['Field']] = array($row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
					}
					$res->Free();

					// get my indexes
					$myIndexes = array();
					$res = $db->Query('SHOW INDEX FROM ' . $tableName);
					while($row = $res->FetchArray(MYSQLI_ASSOC))
						if(isset($myIndexes[$row['Key_name']]))
							$myIndexes[$row['Key_name']][] = $row['Column_name'];
						else
							$myIndexes[$row['Key_name']] = array($row['Column_name']);
					$res->Free();

					// compare fields
					foreach($tableFields as $field)
					{
						if(!isset($myFields[$field[0]]))
							$tableResult['missing']++;
						else
						{
							$myField = $myFields[$field[0]];
							if($myField[1] != $field[1]
								|| $myField[2] != $field[2]
								|| ($myField[4] != $field[4] && !(($myField[4]==0 && $field[4]=='') || ($myField[4]=='' && $field[4]==0)))
								|| $myField[5] != $field[5])
								$tableResult['invalid']++;
						}
					}

					// compare indexes
					foreach($tableIndexes as $indexName=>$indexFields)
					{
						if(!isset($myIndexes[$indexName]))
							$tableResult['missing']++;
						else if($myIndexes[$indexName] != $indexFields)
							$tableResult['invalid']++;
					}
				}

				if(!$tableResult['exists'] || $tableResult['missing'] > 0 || $tableResult['invalid'] > 0)
					$repair = true;

				$result[] = $tableResult;
			}

			$tpl->assign('repair', $repair);
			$tpl->assign('result', $result);
			$tpl->assign('executeStruct', true);
		}
		else
		{
			$op = $_REQUEST['operation'] == 'optimize'
					? 'OPTIMIZE TABLE '
					: 'REPAIR TABLE ';
			$result = array();

			foreach($_POST['tables'] as $table)
			{
				$res = $db->Query($op . $table);
				if($res)
				{
					$row = $res->FetchArray();
					$res->Free();

					$result[] = array(
						'table'		=> $table,
						'type'		=> $row['Msg_type'],
						'status'	=> $row['Msg_text'],
						'query'		=> $op  . $table
					);
				}
				else
				{
					$result[] = array(
						'table'		=> $table,
						'type'		=> 'error',
						'query'		=> $op  . $table
					);
				}
			}

			$tpl->assign('result', $result);
			$tpl->assign('execute', true);
		}

		// assign
		$tpl->assign('page', 'optimize.db.tpl');
	}
	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'repairStruct')
	{
		// read default structure
		include('../serverlib/database.struct.php');
		$databaseStructure = unserialize(base64_decode($databaseStructure));
		$executedQueries = SyncDBStruct($databaseStructure);

		// assign
		$tpl->assign('backLink', 'optimize.php?');
		$tpl->assign('msgIcon', 'info32');
		$tpl->assign('msgTitle', $lang_admin['repairstruct']);
		$tpl->assign('msgText', $lang_admin['repairdone']);
		$tpl->assign('page', 'msg.tpl');
	}
	else
	{
		$tables = array();
		$res = $db->Query('SHOW TABLES');
		while($row = $res->FetchArray(MYSQLI_NUM))
			if(substr($row[0], 0, strlen($mysql['prefix'])) == $mysql['prefix'])
				$tables[] = $row[0];

		// assign
		$tpl->assign('tables', $tables);
		$tpl->assign('page', 'optimize.db.tpl');
	}
}

/**
 * optimize filesystem
 */
else if($_REQUEST['action'] == 'filesystem')
{
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'cleanupTempFiles')
	{
		CleanupTempFiles();
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'vacuumBlobStor')
	{
		$perPage = max(1, $_REQUEST['perpage']);
		$pos = (int)$_REQUEST['pos'];

		$res = $db->Query('SELECT COUNT(*) FROM {pre}users');
		list($count) = $res->FetchArray(MYSQLI_NUM);
		$res->Free();

		if($pos >= $count)
		{
			die('DONE');
		}
		else
		{
			$res = $db->Query('SELECT `id` FROM {pre}users ORDER BY `id` ASC LIMIT '
								. (int)$pos . ',' . (int)$perPage);
			while($row = $res->FetchArray())
			{
				$dbFileName = DataFilename($row['id'], 'blobdb');
				if(file_exists($dbFileName))
				{
					try
					{
						$sdb = new SQLite3($dbFileName);
						$sdb->busyTimeout(500);
						$sdb->query('VACUUM');
						unset($sdb);
					}
					catch(Exception $ex) { }
				}

				$pos++;
			}
			$res->Free();

			if($pos >= $count)
				die('DONE');
			else
				die($pos . '/' . $count);
		}
	}

	else if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'rebuildBlobStor' && isset($_REQUEST['rebuild']))
	{
		$perPage = max(1, $_REQUEST['perpage']);

		if($_REQUEST['rebuild'] == 'email')
		{
			$destBlobStorage 	= $bm_prefs['blobstorage_provider'];
			$blobType 			= BMBLOB_TYPE_MAIL;
			$queryAll 			= 'SELECT COUNT(*) FROM {pre}mails '
									. 'LEFT JOIN {pre}blobstate ON {pre}blobstate.`blobstorage`={pre}mails.`blobstorage` AND {pre}blobstate.blobid={pre}mails.`id` AND {pre}blobstate.`blobtype`='.BMBLOB_TYPE_MAIL.' '
									. 'WHERE {pre}mails.`userid`!=-1 AND {pre}mails.`blobstorage`!=? AND ({pre}blobstate.`defect` IS NULL OR {pre}blobstate.`defect`=0)';
			$query 				= 'SELECT {pre}mails.`id`,{pre}mails.`userid`,{pre}mails.`blobstorage` FROM {pre}mails '
									. 'LEFT JOIN {pre}blobstate ON {pre}blobstate.`blobstorage`={pre}mails.`blobstorage` AND {pre}blobstate.blobid={pre}mails.`id` AND {pre}blobstate.`blobtype`='.BMBLOB_TYPE_MAIL.' '
									. 'WHERE {pre}mails.`userid`!=-1 AND {pre}mails.`blobstorage`!=? AND ({pre}blobstate.`defect` IS NULL OR {pre}blobstate.`defect`=0) ORDER BY {pre}mails.`userid` ASC, {pre}mails.`blobstorage` ASC LIMIT ' . (int)$perPage;
			$queryUpdate 		= 'UPDATE {pre}mails SET `blobstorage`=? WHERE `id`=?';
		}
		else if($_REQUEST['rebuild'] == 'webdisk')
		{
			$destBlobStorage 	= $bm_prefs['blobstorage_provider_webdisk'];
			$blobType 			= BMBLOB_TYPE_WEBDISK;
			$queryAll 			= 'SELECT COUNT(*) FROM {pre}diskfiles '
									. 'LEFT JOIN {pre}blobstate ON {pre}blobstate.`blobstorage`={pre}diskfiles.`blobstorage` AND {pre}blobstate.blobid={pre}diskfiles.`id` AND {pre}blobstate.`blobtype`='.BMBLOB_TYPE_WEBDISK.' '
									. 'WHERE {pre}diskfiles.`user`!=-1 AND {pre}diskfiles.`blobstorage`!=? AND ({pre}blobstate.`defect` IS NULL OR {pre}blobstate.`defect`=0)';
			$query 				= 'SELECT {pre}diskfiles.`id`,`user` AS `userid`,{pre}diskfiles.`blobstorage` FROM {pre}diskfiles '
									. 'LEFT JOIN {pre}blobstate ON {pre}blobstate.`blobstorage`={pre}diskfiles.`blobstorage` AND {pre}blobstate.blobid={pre}diskfiles.`id` AND {pre}blobstate.`blobtype`='.BMBLOB_TYPE_WEBDISK.' '
									. 'WHERE {pre}diskfiles.`user`!=-1 AND {pre}diskfiles.`blobstorage`!=? AND ({pre}blobstate.`defect` IS NULL OR {pre}blobstate.`defect`=0) ORDER BY {pre}diskfiles.`user` ASC, {pre}diskfiles.`blobstorage` ASC LIMIT ' . (int)$perPage;
			$queryUpdate 		= 'UPDATE {pre}diskfiles SET `blobstorage`=? WHERE `id`=?';
		}
		else
			die('Invalid rebuild type');

		if(!isset($_REQUEST['all']))
		{
			$db->Query('DELETE FROM {pre}blobstate WHERE `blobtype`=?',
				$blobType);

			$res = $db->Query($queryAll, $destBlobStorage);
			while($row = $res->FetchArray(MYSQLI_NUM))
			{
				$all = $row[0];
			}
			$res->Free();
		}
		else
			$all = max(0, (int)$_REQUEST['all']);

		if(!isset($all) || $all == 0)
			die('DONE');

		$processedCount = 0;
		$currentUserID = 0;
		$currentSourceProvider = $currentDestProvider = false;
		$currentToDelete = array();
		$currentToUpdate = array();

		$res = $db->Query($query, $destBlobStorage);
		while($row = $res->FetchArray(MYSQLI_ASSOC))
		{
			if($currentUserID != $row['userid'])
			{
				$currentUserID = $row['userid'];

				if(is_object($currentDestProvider))
				{
					$currentDestProvider->endTx();

					foreach($currentToUpdate as $rowID)
					{
						$db->Query($queryUpdate,
							$currentDestProvider->providerID,
							$rowID);
					}
					$currentToUpdate = array();
				}

				$currentDestProvider = BMBlobStorage::createProvider($destBlobStorage, $row['userid']);
				$currentDestProvider->beginTx();

				if(is_object($currentSourceProvider))
				{
					foreach($currentToDelete as $rowID)
						$currentSourceProvider->deleteBlob($blobType, $rowID);
					$currentSourceProvider->endTx();
					$currentToDelete = array();
				}

				$currentSourceProvider = false;
			}

			if(!is_object($currentSourceProvider) || $currentSourceProvider->providerID != $row['blobstorage'])
			{
				if(is_object($currentSourceProvider))
				{
					foreach($currentToDelete as $rowID)
						$currentSourceProvider->deleteBlob($blobType, $rowID);
					$currentSourceProvider->endTx();
					$currentToDelete = array();
				}

				$currentSourceProvider = BMBlobStorage::createProvider($row['blobstorage'], $row['userid']);
				$currentSourceProvider->beginTx();
			}

			$defect = false;

			$fpSource = $currentSourceProvider->loadBlob($blobType, $row['id']);
			if($fpSource)
			{
				if($currentDestProvider->storeBlob($blobType, $row['id'], $fpSource))
				{
					fclose($fpSource);

					$currentToDelete[] = $row['id'];
					$currentToUpdate[] = $row['id'];
				}
				else
					$defect = true;
			}
			else
				$defect = true;

			if($defect)
			{
				$db->Query('REPLACE INTO {pre}blobstate(`blobstorage`,`blobtype`,`blobid`,`defect`) VALUES(?,?,?,?)',
					$row['blobstorage'],
					$blobType,
					$row['id'],
					1);
			}

			++$processedCount;
		}
		$res->Free();

		if(is_object($currentSourceProvider))
		{
			foreach($currentToDelete as $rowID)
				$currentSourceProvider->deleteBlob($blobType, $rowID);
			$currentSourceProvider->endTx();

			unset($currentSourceProvider);
			$currentToDelete = array();
		}

		if(is_object($currentDestProvider))
		{
			$currentDestProvider->endTx();

			foreach($currentToUpdate as $rowID)
			{
				$db->Query($queryUpdate,
					$currentDestProvider->providerID,
					$rowID);
			}

			unset($currentDestProvider);
			$currentToUpdate = array();
		}

		if($processedCount == 0 || $processedCount >= $all)
			echo 'DONE';
		else
			printf('%d/%d', $processedCount, $all);
		exit;
	}

	//
	// temp files
	//
	$tempFileCount = 0;
	$tempFileSize = 0;
	$res = $db->Query('SELECT id FROM {pre}tempfiles');
	while($row = $res->FetchArray())
	{
		$tempFileCount++;
		$fileName = TempFileName($row['id']);
		$tempFileSize += @filesize($fileName);
	}
	$res->Free();

	$tpl->assign('haveSQLite3', class_exists('SQLite3'));
	$tpl->assign('tempFileCount', $tempFileCount);
	$tpl->assign('tempFileSize', $tempFileSize);
	$tpl->assign('msTitle', $bm_prefs['storein'] == 'db' ? $lang_admin['file2db'] : $lang_admin['db2file']);
	$tpl->assign('msDesc', $bm_prefs['storein'] == 'db' ? $lang_admin['file2db_desc'] : $lang_admin['db2file_desc']);
	$tpl->assign('page', 'optimize.filesystem.tpl');
}

/**
 * optimize caches
 */
else if($_REQUEST['action'] == 'cache')
{
	//
	// empty file cache
	//
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'cleanupFileCache'
		&& $bm_prefs['cache_type'] == CACHE_B1GMAIL)
	{
		$cacheManager->CleanUp(true);
	}

	//
	// rebuild caches
	//
	if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'rebuild'
		&& isset($_REQUEST['perpage']) && isset($_REQUEST['pos']))
	{
		$perpage = (int)$_REQUEST['perpage'];
		$pos = (int)$_REQUEST['pos'];

		//
		// rebuild mailsizes
		//
		if($_REQUEST['rebuild'] == 'mailsizes')
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}mails WHERE (`flags`&'.FLAG_DECEPTIVE.')=0');
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($pos >= $count)
			{
				die('DONE');
			}
			else
			{
				$res = $db->Query('SELECT id,size,blobstorage,userid FROM {pre}mails WHERE (`flags`&'.FLAG_DECEPTIVE.')=0 ORDER BY id DESC LIMIT '
									. (int)$pos . ',' . (int)$perpage);
				while($row = $res->FetchArray())
				{
					$cachedSize = $row['size'];
					$actualSize = BMBlobStorage::createProvider($row['blobstorage'], $row['userid'])->getBlobSize(BMBLOB_TYPE_MAIL, $row['id']);

					if($actualSize != $cachedSize)
						$db->Query('UPDATE {pre}mails SET size=? WHERE id=?',
							$actualSize,
							$row['id']);

					$pos++;
				}
				$res->Free();

				if($pos >= $count)
					die('DONE');
				else
					die($pos . '/' . $count);
			}
		}

		//
		// rebuild disk sizes
		//
		else if($_REQUEST['rebuild'] == 'disksizes')
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}diskfiles');
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($pos >= $count)
			{
				die('DONE');
			}
			else
			{
				$res = $db->Query('SELECT `id`,`size`,`blobstorage`,`user` FROM {pre}diskfiles ORDER BY id DESC LIMIT '
									. (int)$pos . ',' . (int)$perpage);
				while($row = $res->FetchArray())
				{
					$cachedSize = $row['size'];

					$actualSize = BMBlobStorage::createProvider($row['blobstorage'], $row['user'])->getBlobSize(BMBLOB_TYPE_WEBDISK, $row['id']);

					if($actualSize != $cachedSize)
						$db->Query('UPDATE {pre}diskfiles SET size=? WHERE id=?',
							$actualSize,
							$row['id']);

					$pos++;
				}
				$res->Free();

				if($pos >= $count)
					die('DONE');
				else
					die($pos . '/' . $count);
			}
		}

		//
		// rebuild user sizes
		//
		else if($_REQUEST['rebuild'] == 'usersizes')
		{
			$res = $db->Query('SELECT COUNT(*) FROM {pre}users');
			list($count) = $res->FetchArray(MYSQLI_NUM);
			$res->Free();

			if($pos >= $count)
			{
				die('DONE');
			}
			else
			{
				$res = $db->Query('SELECT id,email,mailspace_used,diskspace_used FROM {pre}users ORDER BY id DESC LIMIT '
									. (int)$pos . ',' . (int)$perpage);
				while($row = $res->FetchArray())
				{
					$cachedMailSize = $row['mailspace_used'];
					$cachedDiskSize = $row['diskspace_used'];

					$res2 = $db->Query('SELECT SUM(size) FROM {pre}mails WHERE userid=?',
						$row['id']);
					list($actualMailSize) = $res2->FetchArray(MYSQLI_NUM);
					$res2->Free();

					$res2 = $db->Query('SELECT SUM(size) FROM {pre}diskfiles WHERE user=?',
						$row['id']);
					list($actualDiskSize) = $res2->FetchArray(MYSQLI_NUM);
					$res2->Free();

					if($actualDiskSize != $cachedDiskSize
						|| $actualMailSize != $cachedMailSize)
						$db->Query('UPDATE {pre}users SET mailspace_used=?,diskspace_used=? WHERE id=?',
							$actualMailSize,
							$actualDiskSize,
							$row['id']);

					$pos++;
				}
				$res->Free();

				if($pos >= $count)
					die('DONE');
				else
					die($pos . '/' . $count);
			}
		}
	}

	// retrieve cache info
	$res = $db->Query('SELECT COUNT(*),SUM(size) FROM {pre}file_cache');
	list($cacheFileCount, $cacheFileSize) = $res->FetchArray(MYSQLI_NUM);
	$res->Free();

	// assign
	$tpl->assign('fileCache', $bm_prefs['cache_type'] == CACHE_B1GMAIL);
	$tpl->assign('cacheFileCount', $cacheFileCount);
	$tpl->assign('cacheFileSize', $cacheFileSize);
	$tpl->assign('page', 'optimize.cache.tpl');
}

$tpl->assign('tabs', $tabs);
$tpl->assign('title', $lang_admin['tools'] . ' &raquo; ' . $lang_admin['optimize']);
$tpl->display('page.tpl');
?>