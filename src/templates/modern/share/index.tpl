<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>{$service_title} - {lng p="sharing"}{if $userMail} - {$userMail}{/if}</title>
	
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />

	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="{$selfurl}{$_tpldir}images/li/webdisk_folder.png" />
	<link rel="stylesheet" type="text/css" href="{$selfurl}{$_tpldir}style/share.css" />

	<!-- client scripts -->
	<script type="text/javascript" src="{$selfurl}clientlang.php"></script>
	<script type="text/javascript" src="{$selfurl}{$_tpldir}js/common.js"></script>
	<script type="text/javascript" src="{$selfurl}clientlib/overlay.js"></script>
	<script type="text/javascript" src="{$selfurl}clientlib/share.js"></script>

	<!-- font awesome -->
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome-animation.min.css" rel="stylesheet" type="text/css" />
</head>

<!-- body -->
<body{if !$error} onload="shareInit('{$user}','{$selfurl}{$_tpldir}')"{/if}>

	<br />
	<center>

		<p>
			{banner}
		</p>
		
	{if $error}
		<div class="errorMessage">
			<p><b>{$title}</b></p>
			<p>{$msg}</p>
		</div>
	{else}
		<div id="mainLayer">
			<div id="toolBar">
				<div id="titleBar">
					<i class="fa fa-cloud-download" aria-hidden="true"></i> <span id="titleIcon" />
					<span id="titleLayer">...</span>
				</div>
			</div>
			<table id="headingTable" cellspacing="0" cellpadding="0">
				<tr>
					<th id="thTitle">{lng p="title"}</th>
					<th id="thModified">{lng p="modified"}</th>
					<th id="thSize">{lng p="size"}</th>
					<th id="thActions">&nbsp;</th>
				</tr>
			</table>
			<div id="contentLayer">
				<table cellspacing="0" cellpadding="0" id="contentTable"></table>
			</div>
			<div id="locationBar"></div>
		</div>
	{/if}
	
	</center>

</body>

</html>
