<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:09:04
  from 'file:li/email.folder.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1482302fd6c3_00326312',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '36ad8ce277a3c71af7fc43edca5dee995beda165' => 
    array (
      0 => 'li/email.folder.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/email.folder.contents.narrow.tpl' => 2,
    'file:li/email.folder.contents.tpl' => 1,
    'file:li/email.addressmenu.tpl' => 1,
  ),
))) {
function content_6a1482302fd6c3_00326312 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if (!( !$_smarty_tpl->hasVariable('enablePreview') || empty($_smarty_tpl->getValue('enablePreview')))) {?>
	<?php if ($_smarty_tpl->getValue('narrow')) {?>
		<div id="hSep1">
			<div id="folderMailArea">
				<?php $_smarty_tpl->renderSubTemplate("file:li/email.folder.contents.narrow.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			</div>
			<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		<div id="hSepSep"></div>
		<div id="hSep2">
			<div class="scrollContainer withoutContentHeader" style="display:none;" id="previewArea"></div>
	
			<div class="scrollContainer withoutContentHeader" id="multiSelPreview">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nomailsselected"), $_smarty_tpl);?>
</div>
					</div>
				</div>
			</div>
			
			<div id="previewLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		
		<?php echo '<script'; ?>
>
		<!--
			var folderNarrowView = true;
			var previewCompactHeader = true;
			registerLoadAction('initHSep()');
		//-->
		<?php echo '</script'; ?>
>
	<?php } else { ?>
		<div id="vSep1">
			<div id="folderMailArea">
				<?php $_smarty_tpl->renderSubTemplate("file:li/email.folder.contents.narrow.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			</div>
			<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		<div id="vSepSep"></div>
		<div id="vSep2">
			<div class="scrollContainer withoutContentHeader" style="display:none;" id="previewArea"></div>
	
			<div class="scrollContainer withoutContentHeader" id="multiSelPreview">
				<div id="multiSelPreview_vCenter">
					<div id="multiSelPreview_inner">
						<div id="multiSelPreview_count"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nomailsselected"), $_smarty_tpl);?>
</div>
					</div>
				</div>
			</div>
			
			<div id="previewLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
		</div>
		
		<?php echo '<script'; ?>
>
		<!--
			var folderNarrowView = true;
			var previewCompactHeader = false;
			registerLoadAction('initVSep()');
		//-->
		<?php echo '</script'; ?>
>
	<?php }
} else { ?>
<div id="folderMailArea">
	<?php $_smarty_tpl->renderSubTemplate("file:li/email.folder.contents.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
</div>
<div id="folderLoading" style="display:none"><i class="fa fa-spinner fa-pulse fa-fw fa-3x"></i></div>
<?php }?>

<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/drag_email.png" style="display:none;" /><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/drag_emails.png" style="display:none;" />

<!-- mail menu -->
<div id="mailMenu" class="mailMenu bm-mail-menu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a class="mailMenuItem" href="javascript:document.location.href='email.read.php?id='+currentID+'&sid='+currentSID;"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mail_read"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:printMail(currentID, currentSID);"><i class="fa fa-print" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"print"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?reply='+currentID+'&sid='+currentSID;"><i class="fa fa-reply" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reply"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?forward='+currentID+'&sid='+currentSID;"><i class="fa fa-share" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.compose.php?redirect='+currentID+'&sid='+currentSID;"><i class="fa fa-level-up" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"redirect"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 1, false);"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markread"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 1, true);"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markunread"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 16, true);"><i class="fa fa-flag" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mark"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 16, false);"><i class="fa fa-flag-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmark"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 4096, true);"><i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markdone"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentID, 4096, false);"><i class="fa fa-square-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmarkdone"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<div class="mailColorButtons">
		<span id="mailColorButton_0" onclick="javascript:folderColorMail(currentID, 0);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_1" onclick="javascript:folderColorMail(currentID, 1);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_2" onclick="javascript:folderColorMail(currentID, 2);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_3" onclick="javascript:folderColorMail(currentID, 3);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_4" onclick="javascript:folderColorMail(currentID, 4);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_5" onclick="javascript:folderColorMail(currentID, 5);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
		<span id="mailColorButton_6" onclick="javascript:folderColorMail(currentID, 6);"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/pixel.gif" /></span>
	</div>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:showMailSource(currentID);"><i class="fa fa-code" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"showsource"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) deleteMail(currentID);"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mail_del"), $_smarty_tpl);?>
</a>
</div>

<!-- multi mail menu -->
<div id="multiMailMenu" class="mailMenu bm-mail-menu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 1, false);"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markread"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 1, true);"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markunread"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 16, true);"><i class="fa fa-flag" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mark"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 16, false);"><i class="fa fa-flag-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmark"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 4096, true);"><i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markdone"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:folderFlagMail(currentIDs, 4096, false);"><i class="fa fa-square-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unmarkdone"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:setMailSpamStatus(currentIDs, true, true);"><i class="fa fa-bug" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markspam"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:setMailSpamStatus(currentIDs, false, true);"><i class="fa fa-ban" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"marknonspam"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) deleteMail(currentIDs);"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mails_del"), $_smarty_tpl);?>
</a>
</div>

<!-- folder menu -->
<div id="folderMenu" class="mailMenu bm-mail-menu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=markAllAsRead&folder='+currentFolderID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markallasread"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=markAllAsRead&unread=true&folder='+currentFolderID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"markallasunread"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:document.location.href='email.php?do=downloadAll&folder='+currentFolderID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="fa fa-download" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"downloadall"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep"></div>
	<a class="mailMenuItem" href="javascript:void(0);" onclick="if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realempty"), $_smarty_tpl);?>
')) document.location.href='email.php?do=emptyFolder&folder='+currentFolderID+'&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"emptyfolder"), $_smarty_tpl);?>
</a>
</div>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.folder.tpl:foot"), $_smarty_tpl);?>


<?php echo '<script'; ?>
>
<!--
	var currentFolderID = <?php echo $_smarty_tpl->getValue('folderID');?>
;
<?php if ($_smarty_tpl->getValue('refreshEnabled') && $_smarty_tpl->getValue('refreshInterval') > 0) {?>
	initFolderRefresh(<?php echo $_smarty_tpl->getValue('folderID');?>
, <?php echo $_smarty_tpl->getValue('refreshInterval');?>
);
<?php }
if ($_smarty_tpl->getValue('hotkeys')) {?>
	registerLoadAction('registerFolderHotkeyHandler()');
<?php }?>
//-->
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:li/email.addressmenu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
