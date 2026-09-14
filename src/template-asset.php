<?php
/*
 * b1gMail – custom template branding images (logo / cover)
 */

if(!defined('B1GMAIL_INIT'))
	require './serverlib/init.inc.php';

$template = isset($_REQUEST['template']) ? $_REQUEST['template'] : '';
$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
TemplateAssetOutput($template, $key);
