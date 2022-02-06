{if $enablePreview}
	{if $narrow}
		<div id="hSep1">
			<div id="folderMailArea">
				{include file="li/email.folder.contents.narrow.tpl"}
			</div>
			<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		<div id="hSepSep"></div>
		<div id="hSep2">
			<div class="scrollContainer withoutContentHeader" style="display:none;" id="previewArea"></div>
	
			<div class="scrollContainer withoutContentHeader" id="multiSelPreview">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count">{lng p="nomailsselected"}</div>
					</div>
				</div>
			</div>
			
			<div id="previewLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		
		<script>
		<!--
			var folderNarrowView = true;
			registerLoadAction('initHSep()');
		//-->
		</script>
	{else}
		<div id="vSep1">
			<div id="folderMailArea">
				{include file="li/email.folder.contents.tpl"}
			</div>
			<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		<div id="vSepSep"></div>
		<div id="vSep2">
			<div class="scrollContainer withoutContentHeader" style="display:none;" id="previewArea"></div>
	
			<div class="scrollContainer withoutContentHeader" id="multiSelPreview">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count">{lng p="nomailsselected"}</div>
					</div>
				</div>
			</div>
			
			<div id="previewLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		
		<script>
		<!--
			var folderNarrowView = false;
			registerLoadAction('initVSep()');
		//-->
		</script>
	{/if}
{else}
<div id="folderMailArea">
	{include file="li/email.folder.contents.tpl"}
</div>
<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
{/if}

<img src="{$tpldir}images/li/drag_email.png" style="display:none;" /><img src="{$tpldir}images/li/drag_emails.png" style="display:none;" />

<!-- mail menu -->
<div id="mailMenu" class="mailMenu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a class="mailMenuItem" href="javascript:document.location.href='email.read.php?id='+currentID+'&sid='+currentSID;"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> {lng p="mail_read"}</a>
	<a class="mailMenuItem" href="javascript:printMail(currentID, currentSID);"><i class="fa fa-print" aria-hidden="true"></i> {lng p="print"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?reply='+currentID+'&sid='+currentSID;"><i class="fa fa-reply" aria-hidden="true"></i> {lng p="reply"}</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?forward='+currentID+'&sid='+currentSID;"><i class="fa fa-share" aria-hidden="true"></i> {lng p="forward"}</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?redirect='+currentID+'&sid='+currentSID;"><i class="fa fa-level-up" aria-hidden="true"></i> {lng p="redirect"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 1, false);"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> {lng p="markread"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 1, true);"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="markunread"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 16, true);"><i class="fa fa-flag" aria-hidden="true"></i> {lng p="mark"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 16, false);"><i class="fa fa-flag-o" aria-hidden="true"></i> {lng p="unmark"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 4096, true);"><i class="fa fa-check-square-o" aria-hidden="true"></i> {lng p="markdone"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 4096, false);"><i class="fa fa-square-o" aria-hidden="true"></i> {lng p="unmarkdone"}</a>
	<div class="mailMenuSep"></div>
	<div class="mailColorButtons">
		<span id="mailColorButton_0" onclick="javascript:folderColorMail(currentID, 0);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_1" onclick="javascript:folderColorMail(currentID, 1);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_2" onclick="javascript:folderColorMail(currentID, 2);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_3" onclick="javascript:folderColorMail(currentID, 3);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_4" onclick="javascript:folderColorMail(currentID, 4);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_5" onclick="javascript:folderColorMail(currentID, 5);"><img src="{$tpldir}images/pixel.gif" /></span>
		<span id="mailColorButton_6" onclick="javascript:folderColorMail(currentID, 6);"><img src="{$tpldir}images/pixel.gif" /></span>
	</div>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:showMailSource(currentID);"><i class="fa fa-code" aria-hidden="true"></i> {lng p="showsource"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:if(confirm('{lng p="realdel"}')) deleteMail(currentID);"><i class="fa fa-trash-o" aria-hidden="true"></i> {lng p="mail_del"}</a>
</div>

<!-- multi mail menu -->
<div id="multiMailMenu" class="mailMenu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 1, false);"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> {lng p="markread"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 1, true);"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="markunread"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 16, true);"><i class="fa fa-flag" aria-hidden="true"></i> {lng p="mark"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 16, false);"><i class="fa fa-flag-o" aria-hidden="true"></i> {lng p="unmark"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 4096, true);"><i class="fa fa-check-square-o" aria-hidden="true"></i> {lng p="markdone"}</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 4096, false);"><i class="fa fa-square-o" aria-hidden="true"></i> {lng p="unmarkdone"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:setMailSpamStatus(currentIDs, true, true);"><i class="fa fa-bug" aria-hidden="true"></i> {lng p="markspam"}</a>
	<a class="mailMenuItem" href="javascript:setMailSpamStatus(currentIDs, false, true);"><i class="fa fa-ban" aria-hidden="true"></i> {lng p="marknonspam"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:if(confirm('{lng p="realdel"}')) deleteMail(currentIDs);"><i class="fa fa-trash-o" aria-hidden="true"></i> {lng p="mails_del"}</a>
</div>

<!-- folder menu -->
<div id="folderMenu" class="mailMenu" style="display:none;position:absolute;left:0px;top:0px;">
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=markAllAsRead&folder='+currentFolderID+'&sid={$sid}';"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> {lng p="markallasread"}</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=markAllAsRead&unread=true&folder='+currentFolderID+'&sid={$sid}';"><i class="fa fa-envelope-o" aria-hidden="true"></i> {lng p="markallasunread"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=downloadAll&folder='+currentFolderID+'&sid={$sid}';"><i class="fa fa-download" aria-hidden="true"></i> {lng p="downloadall"}</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:void(0);" onclick="if(confirm('{lng p="realempty"}')) document.location.href='email.php?do=emptyFolder&folder='+currentFolderID+'&sid={$sid}';"><i class="fa fa-trash-o" aria-hidden="true"></i> {lng p="emptyfolder"}</a>
</div>

{hook id="email.folder.tpl:foot"}

<script>
<!--
	var currentFolderID = {$folderID};
{if $refreshEnabled&&$refreshInterval>0}
	initFolderRefresh({$folderID}, {$refreshInterval});
{/if}
{if $hotkeys}
	registerLoadAction('registerFolderHotkeyHandler()');
{/if}
//-->
</script>

{include file="li/email.addressmenu.tpl"}
