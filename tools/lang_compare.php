<?php
/**
 * lang_compare.php
 * Compares German/English language files and shows missing phrases in English file
 */

function conv($in)
{
	$in = addslashes(htmlentities($in));
	$in = preg_replace_callback("/([\n]+)/", function($match)
	{
		return "' . \"" . str_replace("\n", "\\n", $match[0]) . "\"\n\t\t\t\t. '";
	}, $in);
	$in = str_replace("\r", '\\r', $in);
	return $in;
}

$lang_user = $lang_admin = $lang_client = $lang_custom = array();
include('../src/languages/deutsch.lang.php');
$lang_user_de = $lang_user;
$lang_admin_de = $lang_admin;
$lang_client_de = $lang_client;
$lang_custom_de = $lang_custom;

$lang_user = $lang_admin = $lang_client = $lang_custom = array();
include('../src/languages/english.lang.php');
$lang_user_en = $lang_user;
$lang_admin_en = $lang_admin;
$lang_client_en = $lang_client;
$lang_custom_en = $lang_custom;

header('Content-Type: text/html; charset=UTF-8');

echo '<pre>';

foreach($lang_user_de as $key=>$val)
	if(!isset($lang_user_en[$key]))
		printf('$lang_user[\'%s\']	= \'%s\';'."\n", $key, conv($val));
echo "\n\n";

foreach($lang_admin_de as $key=>$val)
	if(!isset($lang_admin_en[$key]))
		printf('$lang_admin[\'%s\']	= \'%s\';'."\n", $key, conv($val));
echo "\n\n";

foreach($lang_client_de as $key=>$val)
	if(!isset($lang_client_en[$key]))
		printf('$lang_client[\'%s\']	= \'%s\';'."\n", $key, conv($val));
echo "\n\n";

foreach($lang_custom_de as $key=>$val)
	if(!isset($lang_custom_en[$key]))
		printf('$lang_custom[\'%s\']	= \'%s\';'."\n", $key, conv($val));
echo "\n\n";

echo '</pre>';
