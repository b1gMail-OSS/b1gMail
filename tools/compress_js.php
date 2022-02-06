<?php
/**
 * compress_js.php
 * Simple tool to compress b1gMail JS files using yui-compressor.
 */

if(count($_SERVER['argv']) != 2)
{
	echo 'Usage: ' . $_SERVER['argv'][0] . ' dir' . "\n";
	exit(1);
}

$dir = $_SERVER['argv'][1];

if(empty($dir))
{
	echo 'No directory specified' . "\n";
	exit(1);
}

if(substr($dir, -1) != '/') $dir .= '/';

if(!file_exists($dir))
{
	echo 'Directory not found: ' . $dir . "\n";
	exit(1);
}

$files = array();
$d = dir($dir);
while($entry = $d->read())
{
	if(substr($entry, 0, 1) == '.')
		continue;
		
	if($entry == 'dtree.js'
		|| $entry == 'IE9.js'
		|| $entry == 'md5.js'
		|| $entry == 'pngfix.js')
		continue;
	
	if(substr($entry, -3) == '.js' 
		&& strpos($entry, '.uncompressed.') === false)
		$files[] = $dir.$entry;
}
$d->close();

foreach($files as $file)
{
	$backupFile = substr($file, 0, -3) . '.uncompressed.js';
	if(file_exists($backupFile))
	{
		echo '    File already compressed: ' . basename($file) . "\n";
		continue;
	}
	
	echo '    Storing uncompressed version of ' . basename($file) . ' in ' . basename($backupFile) . "\n";
	
	if(!copy($file, $backupFile))
	{
		echo '         FAILED' . "\n";
		continue;
	}

	$out = '';
	$ret = 1;
	exec("java -jar ".dirname(__FILE__)."/yui-compressor/yuicompressor-2.4.8.jar --nomunge --type js -o \"$file\" \"$backupFile\"",
		$out,
		$ret);

	if($ret != 0)
		echo '     YUI compressor failed for ' . $file . "\n";
}
