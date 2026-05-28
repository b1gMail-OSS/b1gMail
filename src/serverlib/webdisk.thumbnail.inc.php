<?php
/*
 * b1gMail – Webdisk thumbnail helpers
 */

/**
 * Ensure gruppen.wd_thumbnails exists (DB update may not have been synced yet).
 */
function WebdiskThumbnailsEnsureSchema()
{
	global $db, $mysql;

	static $done = false;

	if($done || !isset($db) || !is_object($db) || !isset($mysql['prefix']))
		return;

	$done = true;
	$table = $mysql['prefix'] . 'gruppen';

	$res = $db->Query('SHOW COLUMNS FROM `' . $table . '` LIKE ?', 'wd_thumbnails');
	if($res->RowCount() == 0)
	{
		$db->Query('ALTER TABLE `' . $table . '` ADD `wd_thumbnails` enum(\'yes\',\'no\') NOT NULL DEFAULT \'no\'');
	}
	$res->Free();
}

/**
 * @return string|false
 */
function WebdiskThumbnailCachePath($userID, $fileID, $modified)
{
	return(B1GMAIL_DIR . 'temp/wdthumbs/' . (int)$userID . '/' . (int)$fileID . '_' . (int)$modified . '.jpg');
}

/**
 * @param array $fileInfo Row from BMWebdisk::GetFileInfo
 * @return bool
 */
function WebdiskThumbnailIsSupportedType($fileInfo)
{
	$ctype = strtolower($fileInfo['contenttype'] ?? '');

	return(strpos($ctype, 'image/') === 0 && $ctype !== 'image/svg+xml');
}

/**
 * @param resource $src
 * @return resource|false
 */
function WebdiskThumbnailLoadImageResource($src, $ctype)
{
	switch(strtolower($ctype))
	{
	case 'image/jpeg':
	case 'image/jpg':
	case 'image/pjpeg':
		return(@imagecreatefromjpeg($src));

	case 'image/png':
		return(@imagecreatefrompng($src));

	case 'image/gif':
		return(@imagecreatefromgif($src));

	case 'image/webp':
		if(function_exists('imagecreatefromwebp'))
			return(@imagecreatefromwebp($src));

		return(false);

	default:
		return(false);
	}
}

/**
 * @param resource $im
 * @return resource
 */
function WebdiskThumbnailResizeImage($im, $maxSize = 160)
{
	$width = imagesx($im);
	$height = imagesy($im);

	if($width < 1 || $height < 1)
		return($im);

	$scale = min($maxSize / $width, $maxSize / $height, 1);
	$newW = max(1, (int)round($width * $scale));
	$newH = max(1, (int)round($height * $scale));

	$dst = imagecreatetruecolor($newW, $newH);
	imagealphablending($dst, false);
	imagesavealpha($dst, true);
	$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
	imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
	imagecopyresampled($dst, $im, 0, 0, 0, 0, $newW, $newH, $width, $height);
	imagedestroy($im);

	return($dst);
}

/**
 * @param array $fileInfo
 * @param int $userID
 * @return string|false absolute path to JPEG cache file
 */
function WebdiskEnsureThumbnail($fileInfo, $userID)
{
	if(!function_exists('imagecreatetruecolor') || !WebdiskThumbnailIsSupportedType($fileInfo))
		return(false);

	$cachePath = WebdiskThumbnailCachePath($userID, $fileInfo['id'], $fileInfo['modified']);
	if(is_file($cachePath) && is_readable($cachePath))
		return($cachePath);

	$cacheDir = dirname($cachePath);
	if(!is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true) && !is_dir($cacheDir))
		return(false);

	$fp = BMBlobStorage::CreateProvider($fileInfo['blobstorage'], $userID)
		->loadBlob(BMBLOB_TYPE_WEBDISK, $fileInfo['id']);
	if(!$fp)
		return(false);

	$tmpFile = tempnam($cacheDir, 'wdsrc_');
	if(!$tmpFile)
	{
		fclose($fp);
		return(false);
	}

	$out = fopen($tmpFile, 'wb');
	if(!$out)
	{
		fclose($fp);
		@unlink($tmpFile);
		return(false);
	}

	while(!feof($fp))
		fwrite($out, fread($fp, 8192));
	fclose($fp);
	fclose($out);

	$im = WebdiskThumbnailLoadImageResource($tmpFile, $fileInfo['contenttype']);
	@unlink($tmpFile);

	if(!$im)
		return(false);

	$im = WebdiskThumbnailResizeImage($im);
	$ok = @imagejpeg($im, $cachePath, 82);
	imagedestroy($im);

	return($ok ? $cachePath : false);
}

/**
 * Tabler icon class for a webdisk folder or file item.
 *
 * @param array $item keys: type, ext, ctype, title
 * @return string
 */
function WebdiskGetItemIcon($item)
{
	if(!isset($item['type']) || $item['type'] == WEBDISK_ITEM_FOLDER)
	{
		if(isset($item['ext']) && $item['ext'] == '.SHAREDFOLDER')
			return('ti-folder-share');

		return('ti-folder');
	}

	$ext = isset($item['ext']) ? strtolower((string)$item['ext']) : '';
	if($ext === '?')
		$ext = '';

	if($ext === '' && !empty($item['title']))
	{
		$dotPos = strrchr((string)$item['title'], '.');
		if($dotPos !== false)
			$ext = strtolower(substr($dotPos, 1));
	}

	$fileTypeIcons = array(
		'bmp'	=> 'ti-file-type-bmp',
		'css'	=> 'ti-file-type-css',
		'csv'	=> 'ti-file-type-csv',
		'doc'	=> 'ti-file-type-doc',
		'docx'	=> 'ti-file-type-docx',
		'htm'	=> 'ti-file-type-html',
		'html'	=> 'ti-file-type-html',
		'jpeg'	=> 'ti-file-type-jpg',
		'jpg'	=> 'ti-file-type-jpg',
		'js'	=> 'ti-file-type-js',
		'jsx'	=> 'ti-file-type-jsx',
		'pdf'	=> 'ti-file-type-pdf',
		'php'	=> 'ti-file-type-php',
		'png'	=> 'ti-file-type-png',
		'ppt'	=> 'ti-file-type-ppt',
		'pptx'	=> 'ti-file-type-ppt',
		'rs'	=> 'ti-file-type-rs',
		'sql'	=> 'ti-file-type-sql',
		'svg'	=> 'ti-file-type-svg',
		'ts'	=> 'ti-file-type-ts',
		'tsx'	=> 'ti-file-type-tsx',
		'txt'	=> 'ti-file-type-txt',
		'vue'	=> 'ti-file-type-vue',
		'xls'	=> 'ti-file-type-xls',
		'xlsx'	=> 'ti-file-type-xls',
		'xml'	=> 'ti-file-type-xml',
		'zip'	=> 'ti-file-type-zip',
	);

	if($ext !== '' && isset($fileTypeIcons[$ext]))
		return($fileTypeIcons[$ext]);

	$groups = array(
		'ti-file-type-jpg'		=> array('jpe', 'jfif', 'gif', 'webp', 'ico', 'tif', 'tiff', 'heic', 'heif', 'avif', 'raw', 'cr2', 'nef'),
		'ti-file-type-zip'		=> array('rar', '7z', 'gz', 'bz2', 'bz', 'tar', 'tgz', 'xz', 'lz', 'lzma', 'ace', 'pak', 'pk3', 'gcf', 'jar', 'apk', 'deb', 'rpm'),
		'ti-movie'				=> array('mpg', 'mpeg', 'mp4', 'mkv', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'm2ts', 'divx', 'qt', 'vob', 'ogv', '3gp'),
		'ti-file-music'			=> array('mp3', 'flac', 'aac', 'ac3', 'wav', 'ogg', 'oga', 'opus', 'm4a', 'wma', 'aiff', 'aif', 'mid', 'midi'),
		'ti-file-type-doc'		=> array('odt', 'rtf', 'wri', 'sdw', 'docm'),
		'ti-file-type-ppt'		=> array('odp', 'pptm'),
		'ti-file-spreadsheet'	=> array('ods', 'xlsm', 'numbers'),
		'ti-file-text'			=> array('md', 'markdown', 'ini', 'inf', 'conf', 'cfg', 'log', 'nfo', 'eml', 'msg', 'rtfd', 'rst', 'tex'),
		'ti-file-code'			=> array('c', 'h', 'cpp', 'hpp', 'cc', 'cxx', 'cs', 'java', 'kt', 'kts', 'go', 'py', 'rb', 'pl', 'sh', 'bash', 'zsh', 'ps1', 'bat', 'cmd', 'yaml', 'yml', 'toml', 'json', 'jsonld', 'ndjson', 'properties', 'env', 'gitignore', 'dockerfile', 'cmake', 'swift', 'm', 'mm', 'scala', 'lua', 'r', 'sass', 'scss', 'less', 'gradle', 'mjs', 'cjs'),
		'ti-file-database'		=> array('db', 'sqlite', 'sqlite3', 'mdb', 'accdb'),
		'ti-file-certificate'	=> array('pem', 'crt', 'cer', 'key', 'p12', 'pfx', 'der', 'csr'),
		'ti-file-description'	=> array('epub', 'mobi', 'azw', 'fb2'),
	);

	foreach($groups as $icon => $extList)
	{
		if($ext !== '' && in_array($ext, $extList, true))
			return($icon);
	}

	$ctype = isset($item['ctype']) ? strtolower((string)$item['ctype']) : '';

	if($ctype !== '')
	{
		if(strpos($ctype, 'image/') === 0)
			return('ti-photo');
		if($ctype === 'application/pdf')
			return('ti-file-type-pdf');
		if(strpos($ctype, 'video/') === 0)
			return('ti-movie');
		if(strpos($ctype, 'audio/') === 0)
			return('ti-file-music');
		if(strpos($ctype, 'text/') === 0)
			return('ti-file-text');
		if(in_array($ctype, array('application/json', 'application/xml', 'application/javascript', 'application/x-javascript', 'application/sql'), true))
			return('ti-file-code');
		if(strpos($ctype, 'zip') !== false || strpos($ctype, 'compressed') !== false || strpos($ctype, 'archive') !== false)
			return('ti-file-type-zip');
		if(strpos($ctype, 'msword') !== false || strpos($ctype, 'wordprocessing') !== false)
			return('ti-file-type-doc');
		if(strpos($ctype, 'spreadsheet') !== false || strpos($ctype, 'excel') !== false)
			return('ti-file-type-xls');
		if(strpos($ctype, 'presentation') !== false || strpos($ctype, 'powerpoint') !== false)
			return('ti-file-type-ppt');
	}

	return('ti-file');
}

/**
 * @return int
 */
function WebdiskGetTextEditMaxBytes()
{
	return(1024 * 1024);
}

/**
 * @param string $fileName
 * @param string $contentType
 * @return bool
 */
function WebdiskIsTextPreviewFile($fileName, $contentType)
{
	static $extensions = array(
		'txt', 'md', 'markdown', 'json', 'csv', 'tsv', 'xml', 'yml', 'yaml',
		'ini', 'conf', 'cfg', 'log', 'sql', 'sh', 'bash', 'zsh', 'py', 'js',
		'mjs', 'cjs', 'ts', 'tsx', 'jsx', 'css', 'scss', 'less', 'html', 'htm',
		'php', 'env', 'properties', 'gitignore', 'htaccess', 'svg', 'rst',
		'tex', 'bib', 'toml', 'lock', 'map', 'vue', 'svelte', 'reg', 'cnf'
	);

	$fileName = strtolower(trim((string)$fileName));
	$contentType = strtolower(trim((string)$contentType));

	if($contentType !== '')
	{
		if(strpos($contentType, 'text/') === 0)
			return(true);

		if(in_array($contentType, array(
			'application/json',
			'application/xml',
			'application/javascript',
			'application/x-javascript',
			'application/x-yaml',
			'application/yaml',
			'application/csv',
			'application/sql',
			'application/xhtml+xml',
			'application/ld+json'
		), true))
			return(true);
	}

	$dotPos = strrpos($fileName, '.');
	if($dotPos === false)
		return(false);

	$ext = substr($fileName, $dotPos + 1);

	return(in_array($ext, $extensions, true));
}

/**
 * @param string $content
 * @return bool
 */
function WebdiskIsLikelyTextContent($content)
{
	if($content === '')
		return(true);

	if(strpos($content, "\0") !== false)
		return(false);

	if(!mb_check_encoding($content, 'UTF-8'))
	{
		if(function_exists('mb_convert_encoding'))
			$content = @mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
		if(!mb_check_encoding($content, 'UTF-8'))
			return(false);
	}

	return(true);
}

/**
 * @param array $folderContent
 * @return array gallery (images only), items (all viewable by id)
 */
function WebdiskBuildPreviewData($folderContent)
{
	$gallery = array();
	$items = array();

	foreach($folderContent as $item)
	{
		if(!isset($item['type']) || $item['type'] != WEBDISK_ITEM_FILE || empty($item['viewable']))
			continue;

		$ctype = strtolower($item['ctype'] ?? '');

		$isEditableText = function_exists('WebdiskIsTextPreviewFile')
			&& WebdiskIsTextPreviewFile($item['title'], $ctype);
		$isImage = (strpos($ctype, 'image/') === 0 && $ctype !== 'image/svg+xml');
		$isMedia = function_exists('WebdiskIsMediaPreviewFile')
			&& WebdiskIsMediaPreviewFile($item['title'], $ctype);
		$isAudio = (strpos($ctype, 'audio/') === 0)
			|| ($isMedia && preg_match('/\.(mp3|ogg|oga|opus|wav|aac|m4a|flac)$/i', $item['title']));
		$isVideo = (strpos($ctype, 'video/') === 0)
			|| ($isMedia && preg_match('/\.(avi|mov|mp4|ogv|wmv|webm|mkv|m4v|3gp|mpg|mpeg)$/i', $item['title']));
		$isMarkdown = ($isEditableText && preg_match('/\.(md|markdown)$/i', $item['title']));

		$entry = array(
			'id'				=> (int)$item['id'],
			'title'				=> $item['title'],
			'ctype'				=> $ctype,
			'isPdf'				=> ($ctype === 'application/pdf'),
			'isImage'			=> $isImage,
			'isAudio'			=> $isAudio,
			'isVideo'			=> $isVideo,
			'isText'			=> $isEditableText,
			'isEditableText'	=> $isEditableText,
			'isMarkdown'		=> (bool)$isMarkdown,
		);

		$items[(string)$item['id']] = $entry;

		if($isImage)
			$gallery[] = $entry;
	}

	return(array(
		'gallery'	=> $gallery,
		'items'		=> $items
	));
}

/**
 * @param array $folderContent
 * @return array image gallery for prev/next navigation
 */
function WebdiskBuildPreviewManifest($folderContent)
{
	$data = WebdiskBuildPreviewData($folderContent);

	return($data['gallery']);
}

/**
 * @param int $bytes
 * @return string
 */
function WebdiskFormatBytes($bytes)
{
	global $lang_user;

	$bytes = (int)$bytes;

	if($bytes < 0)
		$bytes = 0;

	if($bytes < 1024)
		return($bytes . ' B');
	if($bytes < 1024 * 1024)
		return(sprintf('%.2f KB', round($bytes / 1024, 2)));
	if($bytes < 1024 * 1024 * 1024)
		return(sprintf('%.2f MB', round($bytes / 1024 / 1024, 2)));

	return(sprintf('%.2f GB', round($bytes / 1024 / 1024 / 1024, 2)));
}

/**
 * @param int $usedSpace
 * @param int $spaceLimit
 * @param array $groupRow
 * @param array $userRow
 * @return int
 */
function WebdiskGetMaxUploadFileSize($usedSpace, $spaceLimit, $groupRow, $userRow)
{
	$max = 0;

	$uploadMax = ParsePHPSize(ini_get('upload_max_filesize'));
	$postMax = ParsePHPSize(ini_get('post_max_size'));

	if($uploadMax > 0)
		$max = $uploadMax;

	if($postMax > 0)
	{
		$postMax -= 65536;
		if($postMax < 0)
			$postMax = 0;
		if($max <= 0 || $postMax < $max)
			$max = $postMax;
	}

	if(isset($groupRow['maxsize']) && (int)$groupRow['maxsize'] > 0)
	{
		if($max <= 0 || (int)$groupRow['maxsize'] < $max)
			$max = (int)$groupRow['maxsize'];
	}

	if($spaceLimit > 0)
	{
		$left = $spaceLimit - (int)$usedSpace;
		if($left < 0)
			$left = 0;
		if($max <= 0 || $left < $max)
			$max = $left;
	}

	if(isset($groupRow['traffic']) && (int)$groupRow['traffic'] > 0)
	{
		$trafficLeft = (int)$groupRow['traffic'] + (int)$userRow['traffic_add']
			- (int)$userRow['traffic_down'] - (int)$userRow['traffic_up'];
		if($trafficLeft < 0)
			$trafficLeft = 0;
		if($max <= 0 || $trafficLeft < $max)
			$max = $trafficLeft;
	}

	return(max(0, (int)$max));
}

/**
 * @return bool
 */
function WebdiskIsUploadPostTooLarge()
{
	$contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
	$postMax = ParsePHPSize(ini_get('post_max_size'));

	return($postMax > 0 && $contentLength > $postMax);
}

/**
 * @param int $errorCode
 * @return string
 */
function WebdiskUploadIniErrorMessage($errorCode)
{
	global $lang_user;

	switch((int)$errorCode)
	{
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		return($lang_user['wd_upload_php_limit']);

	case UPLOAD_ERR_PARTIAL:
		return($lang_user['wd_upload_partial']);

	case UPLOAD_ERR_NO_FILE:
		return($lang_user['wd_upload_nofile']);

	default:
		return($lang_user['internalerror']);
	}
}

/**
 * Store upload feedback for redirect back to folder view.
 *
 * @param int $folderID
 * @param array $error
 * @param array $success
 */
function WebdiskStoreUploadFeedback($folderID, $error, $success)
{
	$_SESSION['webdiskUploadFeedback'] = array(
		'folderID' => (int)$folderID,
		'error'    => is_array($error) ? $error : array(),
		'success'  => is_array($success) ? $success : array(),
	);
}

/**
 * Apply stored upload feedback to template (one-time).
 *
 * @param int $folderID
 * @param object $tpl
 */
function WebdiskApplyUploadFeedback($folderID, $tpl)
{
	if(empty($_SESSION['webdiskUploadFeedback']))
		return;

	$fb = $_SESSION['webdiskUploadFeedback'];
	unset($_SESSION['webdiskUploadFeedback']);

	if((int)$fb['folderID'] !== (int)$folderID)
		return;

	if(!empty($fb['error']))
		$tpl->assign('uploadErrors', $fb['error']);
	if(!empty($fb['success']))
		$tpl->assign('uploadSuccess', $fb['success']);
}

/**
 * Forbidden upload rules for client-side checks (mirrors BMWebdisk::Forbidden).
 *
 * @return array{extensions: string[], mimetypes: string[]}
 */
function WebdiskGetForbiddenUploadRules()
{
	global $bm_prefs;

	$extensions = array();
	$mimetypes = array();

	if(!empty($bm_prefs['forbidden_extensions']))
	{
		foreach(explode(':', $bm_prefs['forbidden_extensions']) as $val)
		{
			$val = trim($val);
			if($val != '')
				$extensions[] = $val;
		}
	}

	if(!empty($bm_prefs['forbidden_mimetypes']))
	{
		foreach(explode(':', $bm_prefs['forbidden_mimetypes']) as $val)
		{
			$val = trim($val);
			if($val != '')
				$mimetypes[] = $val;
		}
	}

	return(array(
		'extensions' => $extensions,
		'mimetypes'  => $mimetypes,
	));
}

/**
 * Collect normalized upload file entries from $_FILES.
 *
 * @return array<int, array{name: string, size: int, type: string, tmp_name: string, error: int}>
 */
function WebdiskCollectUploadFiles()
{
	$files = array();

	$add = function($name, $size, $type, $tmpName, $error) use (&$files)
	{
		if(!is_string($name) || trim($name) == '')
			return;

		$files[] = array(
			'name'     => $name,
			'size'     => (int)$size,
			'type'     => (string)$type,
			'tmp_name' => (string)$tmpName,
			'error'    => (int)$error,
		);
	};

	$addMulti = function($fileField) use ($add)
	{
		if(!isset($_FILES[$fileField]) || !is_array($_FILES[$fileField]['name']))
			return;

		$f = $_FILES[$fileField];
		if(is_array($f['name']))
		{
			foreach($f['name'] as $i => $name)
				$add($name, $f['size'][$i], $f['type'][$i], $f['tmp_name'][$i], $f['error'][$i]);
		}
		else
			$add($f['name'], $f['size'], $f['type'], $f['tmp_name'], $f['error']);
	};

	$addMulti('files');
	$addMulti('localFile_wdUpload');

	foreach($_FILES as $key => $value)
	{
		if(!is_array($value) || substr($key, 0, 4) != 'file')
			continue;
		if(is_array($value['name']))
			continue;
		if(isset($value['name']) && trim($value['name']) != '')
			$add($value['name'], $value['size'], $value['type'], $value['tmp_name'], $value['error']);
	}

	return($files);
}

/**
 * Process one uploaded file (form POST).
 *
 * @return string|null Error message or null on success
 */
function WebdiskProcessUploadedFileEntry($webdisk, $folderID, $entry, $maxUpload, &$usedSpace, $spaceLimit, $groupRow, $userRow)
{
	global $lang_user, $db;

	$fileName = $entry['name'];
	$fileSize = $entry['size'];
	$mimeType = $entry['type'];

	if($entry['error'] != UPLOAD_ERR_OK)
		return(WebdiskUploadIniErrorMessage($entry['error']));

	if($mimeType == '' || $mimeType == 'application/octet-stream')
		$mimeType = GuessMIMEType($fileName);

	if($webdisk->Forbidden($fileName, $mimeType))
		return($lang_user['wd_fileforbidden']);

	if($maxUpload <= 0)
		return($lang_user['nospace']);

	if($fileSize > $maxUpload)
		return(sprintf($lang_user['wd_filetoolarge'], $fileName, WebdiskFormatBytes($maxUpload)));

	if($groupRow['traffic'] > 0 && ($userRow['traffic_down'] + $userRow['traffic_up'] + $fileSize) > $groupRow['traffic'] + $userRow['traffic_add'])
		return($lang_user['notraffic']);

	if($spaceLimit != -1 && $usedSpace + $fileSize > $spaceLimit)
		return($lang_user['nospace']);

	if(($fileID = $webdisk->CreateFile($folderID, $fileName, $mimeType, $fileSize)) === false)
		return($lang_user['fileexists']);

	$tempFileID = RequestTempFile($userRow['id'], time() + TIME_ONE_HOUR);
	$tempFileName = TempFileName($tempFileID);

	if(!@move_uploaded_file($entry['tmp_name'], $tempFileName))
	{
		$webdisk->DeleteFile($fileID);
		PutLog(sprintf('Failed to move uploaded file <%s> to <%s>, deleting webdisk file',
			$entry['tmp_name'],
			$tempFileName),
			PRIO_ERROR,
			__FILE__,
			__LINE__);

		return($lang_user['internalerror']);
	}

	$sourceFP = fopen($tempFileName, 'rb');
	if(!$sourceFP
		|| !BMBlobStorage::createDefaultWebdiskProvider($userRow['id'])->storeBlob(BMBLOB_TYPE_WEBDISK, $fileID, $sourceFP))
	{
		if($sourceFP)
			fclose($sourceFP);
		$webdisk->DeleteFile($fileID);
		ReleaseTempFile($userRow['id'], $tempFileID);

		return($lang_user['internalerror']);
	}

	fclose($sourceFP);
	ReleaseTempFile($userRow['id'], $tempFileID);

	$usedSpace += $fileSize;
	$db->Query('UPDATE {pre}users SET traffic_up=traffic_up+? WHERE id=?',
		$fileSize,
		$userRow['id']);
	Add2Stat('wd_up', ceil($fileSize / 1024));

	return(null);
}
