<!DOCTYPE html> 
<html> 
<head> 
	<title>{$service_title}</title> 
	
	<meta http-equiv="content-type" content="text/html; charset={$charset}" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	
	<link rel="stylesheet" href="{$selfurl}clientlib/jquery/jquery.mobile-1.3.0.min.css" />
	<link rel="stylesheet" href="{$selfurl}{$_tpldir}style/m.css?{fileDateSig file="style/m.css"}" type="text/css" />
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="{$selfurl}clientlib/fontawesome/css/font-awesome-animation.min.css" rel="stylesheet" type="text/css" />
	<script src="{$selfurl}{$_tpldir}js/m.js?{fileDateSig file="js/m.js"}"></script>
	<script src="{$selfurl}clientlib/jquery/jquery-1.8.2.min.js"></script>
	<script src="{$selfurl}clientlib/jquery/jquery.mobile-1.3.0.min.js"></script>
	
	<script type="text/javascript">
	<!--
		var currentSID = '{$sid}';
	//-->
	</script>
</head> 
<body> 

<div data-role="{if $isDialog}dialog{else}page{/if}" id="page">

	{if !$isDialog&&$activeTab}
	<div id="menu" data-role="panel" data-position="left" data-display="reveal" data-dismissible="true" data-theme="a">
		<ul data-role="listview" data-theme="a">
			<li data-icon="delete"><a href="#" data-rel="close">{lng p="close"}</a></li>
			<li data-icon="email"{if $activeTab=='email'} data-theme="b"{/if}><a href="email.php?sid={$sid}" data-transition="none">{lng p="email"}</a></li>
			<li data-icon="contacts"{if $activeTab=='contacts'} data-theme="b"{/if}><a href="contacts.php?sid={$sid}" data-transition="none">{lng p="contacts"}</a></li>
			<li data-icon="calendar"{if $activeTab=='calendar'} data-theme="b"{/if}><a href="calendar.php?sid={$sid}" data-transition="none">{lng p="calendar"}</a></li>
			<li data-icon="tasks"{if $activeTab=='tasks'} data-theme="b"{/if}><a href="tasks.php?sid={$sid}" data-transition="none">{lng p="tasks"}</a></li>
			{if $pageTabs.webdisk}<li data-icon="webdisk"{if $activeTab=='webdisk'} data-theme="b"{/if}><a href="webdisk.php?sid={$sid}" data-transition="none">{lng p="webdisk"}</a></li>{/if}
			<li data-icon="delete"><a href="email.php?action=logout&sid={$sid}" data-transition="none">{lng p="logout"}</a></li>
		</ul>
	</div>
	{/if}

	{include file="$page"}	
	
	{if !$isDialog&&$activeTab}
	<div data-role="footer" data-position="fixed">
		<a href="#menu" data-icon="bars" class="ui-btn-left">{lng p="menu"}</a>
		<span class="ui-title"></span>
		<a href="email.php?action=logout&sid={$sid}" data-icon="delete" class="ui-btn-right" data-transition="none">{lng p="logout"}</a>
	</div>
	{/if}
	
</div>

</body>
</html>
