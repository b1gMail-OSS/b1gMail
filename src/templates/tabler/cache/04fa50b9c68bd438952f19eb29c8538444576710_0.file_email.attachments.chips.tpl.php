<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:47:22
  from 'file:li/email.attachments.chips.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c08ae9d288_74352101',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '04fa50b9c68bd438952f19eb29c8538444576710' => 
    array (
      0 => 'li/email.attachments.chips.tpl',
      1 => 1779809694,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15c08ae9d288_74352101 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-mail-attachments">
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('attachments'), 'attachment', false, 'attID');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('attID')->value => $_smarty_tpl->getVariable('attachment')->value) {
$foreach1DoElse = false;
?>
	<?php if ($_smarty_tpl->getValue('attachment')['mimetype'] == 'message/rfc822' || $_smarty_tpl->getValue('attachment')['filetype'] == '.eml') {?>
		<?php ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'cut'=>45,'escape'=>true), $_smarty_tpl);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->assign('attHref', "javascript:showAttachedMail(".((string)$_smarty_tpl->getValue('mailID')).", '".((string)$_smarty_tpl->getValue('attID'))."', '".$_prefixVariable1."');", false, NULL);?>
	<?php } elseif ($_smarty_tpl->getValue('attachment')['mimetype'] == 'application/zip' || $_smarty_tpl->getValue('attachment')['filetype'] == '.zip') {?>
		<?php ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'cut'=>45,'escape'=>true), $_smarty_tpl);
$_prefixVariable2=ob_get_clean();
$_smarty_tpl->assign('attHref', "javascript:showAttachedZIP(".((string)$_smarty_tpl->getValue('mailID')).", '".((string)$_smarty_tpl->getValue('attID'))."', '".$_prefixVariable2."');", false, NULL);?>
	<?php } else { ?>
		<?php ob_start();
if ($_smarty_tpl->getValue('attachment')['viewable']) {
echo "&view=true";
}
$_prefixVariable3=ob_get_clean();
$_smarty_tpl->assign('attHref', "email.read.php?id=".((string)$_smarty_tpl->getValue('mailID'))."&action=downloadAttachment&attachment=".((string)$_smarty_tpl->getValue('attID')).$_prefixVariable3."&sid=".((string)$_smarty_tpl->getValue('sid')), false, NULL);?>
	<?php }?>
	<?php if ((true && ($_smarty_tpl->hasVariable('selectable') && null !== ($_smarty_tpl->getValue('selectable') ?? null))) && $_smarty_tpl->getValue('selectable')) {?>
	<div class="bm-mail-attachment-chip bm-mail-attachment-chip-selectable">
		<label class="form-check mb-0">
			<input type="checkbox" class="form-check-input bm-mail-attachment-check" name="att[]" id="att_<?php echo $_smarty_tpl->getValue('attID');?>
" value="<?php echo $_smarty_tpl->getValue('attID');?>
" />
		</label>
		<a class="bm-mail-attachment-chip-link" href="<?php echo $_smarty_tpl->getValue('attHref');?>
"<?php if ($_smarty_tpl->getValue('attachment')['mimetype'] != 'message/rfc822' && $_smarty_tpl->getValue('attachment')['filetype'] != '.eml' && $_smarty_tpl->getValue('attachment')['mimetype'] != 'application/zip' && $_smarty_tpl->getValue('attachment')['filetype'] != '.zip') {?> target="_blank"<?php }?> title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'escape'=>true), $_smarty_tpl);?>
">
			<span class="bm-mail-attachment-icon"><i class="ti ti-paperclip icon" aria-hidden="true"></i></span>
			<span class="bm-mail-attachment-meta">
				<span class="bm-mail-attachment-name"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'cut'=>40), $_smarty_tpl);?>
</span>
				<span class="bm-mail-attachment-size"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('attachment')['size']), $_smarty_tpl);?>
</span>
			</span>
		</a>
	</div>
	<?php } else { ?>
	<a class="bm-mail-attachment-chip" href="<?php echo $_smarty_tpl->getValue('attHref');?>
"<?php if ($_smarty_tpl->getValue('attachment')['mimetype'] != 'message/rfc822' && $_smarty_tpl->getValue('attachment')['filetype'] != '.eml' && $_smarty_tpl->getValue('attachment')['mimetype'] != 'application/zip' && $_smarty_tpl->getValue('attachment')['filetype'] != '.zip') {?> target="_blank"<?php }?> title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'escape'=>true), $_smarty_tpl);?>
">
		<span class="bm-mail-attachment-icon"><i class="ti ti-paperclip icon" aria-hidden="true"></i></span>
		<span class="bm-mail-attachment-meta">
			<span class="bm-mail-attachment-name"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('attachment')['filename'],'cut'=>40), $_smarty_tpl);?>
</span>
			<span class="bm-mail-attachment-size"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('attachment')['size']), $_smarty_tpl);?>
</span>
		</span>
	</a>
	<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
