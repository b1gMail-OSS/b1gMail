<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:20:54
  from 'file:li/email.preview.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1484f6258701_52123542',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '30c805d6c2c0d74f9a3c6eca0ae9a91a9f4f3cf9' => 
    array (
      0 => 'li/email.preview.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/email.mailnotes.tpl' => 1,
    'file:li/email.attachments.chips.tpl' => 1,
  ),
))) {
function content_6a1484f6258701_52123542 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="previewMailHeader bm-mail-read-header">
	<div class="bm-mail-read-header-top">
		<div class="bm-mail-read-header-main">
			<button type="button" class="btn btn-sm btn-ghost-secondary bm-mail-meta-toggle" onclick="advancedOptions('mailHeaders', 'right', 'bottom', '<?php echo $_smarty_tpl->getValue('tpldir');?>
');" aria-expanded="<?php if ($_smarty_tpl->getValue('narrow')) {?>true<?php } else { ?>false<?php }?>" aria-controls="advanced_mailHeaders_body">
				<i class="ti icon ti-chevron-<?php if ($_smarty_tpl->getValue('narrow')) {?>down<?php } else { ?>right<?php }?>" id="advanced_mailHeaders_arrow" aria-hidden="true"></i>
			</button>
			<h1 class="bm-mail-subject"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('subject')), $_smarty_tpl);?>
</h1>
		</div>
		<div class="bm-mail-read-header-actions">
			<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="currentID=<?php echo $_smarty_tpl->getValue('mailID');?>
;showMailMenu(event,this);">
				<i class="ti ti-dots-vertical icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"actions"), $_smarty_tpl);?>

			</button>
		</div>
	</div>

	<div class="bm-mail-meta-compact" id="advanced_mailHeaders_body2" style="display:<?php if ($_smarty_tpl->getValue('narrow')) {?>none<?php }?>;">
		<span class="bm-mail-meta-item">
			<span class="bm-mail-meta-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"from2"), $_smarty_tpl);?>
</span>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('fromAddresses'),'short'=>true), $_smarty_tpl);?>

		</span>
		<span class="bm-mail-meta-item">
			<span class="bm-mail-meta-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to2"), $_smarty_tpl);?>
</span>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('toAddresses'),'short'=>true), $_smarty_tpl);?>

		</span>
		<span class="bm-mail-meta-item bm-mail-meta-date">
			<i class="ti ti-clock icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date'),'nice'=>true), $_smarty_tpl);?>

		</span>
		<?php if ($_smarty_tpl->getValue('attachments')) {?>
		<button type="button" class="btn btn-sm btn-ghost-secondary bm-mail-meta-attach" onclick="advancedOptions('mailHeaders', 'right', 'bottom', '<?php echo $_smarty_tpl->getValue('tpldir');?>
');" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>
">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('attachments'));?>

		</button>
		<?php }?>
	</div>

	<div class="bm-mail-meta-detail" id="advanced_mailHeaders_body" style="display:<?php if (!$_smarty_tpl->getValue('narrow')) {?>none<?php }?>;">
		<dl class="bm-mail-meta-list">
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"from"), $_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('fromAddresses')), $_smarty_tpl);?>
</dd>
			</div>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to"), $_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('toAddresses')), $_smarty_tpl);?>
</dd>
			</div>
			<?php if ($_smarty_tpl->getValue('ccAddresses')) {?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cc"), $_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('ccAddresses')), $_smarty_tpl);?>
</dd>
			</div>
			<?php }?>
			<?php if ($_smarty_tpl->getValue('replyToAddresses')) {?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"replyto"), $_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('addressList')->handle(array('list'=>$_smarty_tpl->getValue('replyToAddresses')), $_smarty_tpl);?>
</dd>
			</div>
			<?php }?>
			<?php if ($_smarty_tpl->getValue('priority') != 0) {?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priority"), $_smarty_tpl);?>
</dt>
				<dd>
					<?php if ($_smarty_tpl->getValue('priority') == 1) {?><i class="ti ti-alert-triangle icon text-warning" aria-hidden="true"></i><?php }?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_".((string)$_smarty_tpl->getValue('priority'))), $_smarty_tpl);?>

				</dd>
			</div>
			<?php }?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"date"), $_smarty_tpl);?>
</dt>
				<dd><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date'),'elapsed'=>true), $_smarty_tpl);?>
</dd>
			</div>

			<?php if ($_smarty_tpl->getValue('smimeStatus') != 0 && !($_smarty_tpl->getValue('smimeStatus')&1)) {?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"security"), $_smarty_tpl);?>
</dt>
				<dd class="bm-mail-meta-security">
					<?php if ($_smarty_tpl->getValue('smimeStatus')&2) {?>
					<span class="text-danger">
						<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/mailico_signed_bad.png" width="16" height="16" border="0" alt="" align="absmiddle" />
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"badsigned"), $_smarty_tpl);?>

					</span>
					<?php }?>
					<?php if ($_smarty_tpl->getValue('smimeStatus')&4) {?>
					<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/mailico_signed_ok.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('<?php echo $_smarty_tpl->getValue('smimeCertificateHash');?>
');"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signed"), $_smarty_tpl);?>
</a>
					<?php }?>
					<?php if ($_smarty_tpl->getValue('smimeStatus')&8) {?>
					<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/mailico_signed_noverify.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<a href="javascript:void(0);" onclick="showCertificate('<?php echo $_smarty_tpl->getValue('smimeCertificateHash');?>
');" class="text-warning"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"noverifysigned"), $_smarty_tpl);?>
</a>
					<?php }?>
					<?php if ($_smarty_tpl->getValue('smimeStatus')&64) {?>
					<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/mailico_encrypted_error.png" width="16" height="16" border="0" alt="" align="absmiddle" />
					<span class="text-danger"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"decryptionfailed"), $_smarty_tpl);?>
</span>
					<?php }?>
					<?php if ($_smarty_tpl->getValue('smimeStatus')&128) {?>
					<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/mailico_encrypted.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"encrypted"), $_smarty_tpl);?>

					<?php }?>
				</dd>
			</div>
			<?php }?>

			<?php if ($_smarty_tpl->getValue('deliveryStatus')) {?>
			<div class="bm-mail-meta-row">
				<dt><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"deliverystatus"), $_smarty_tpl);?>
</dt>
				<dd>
					<?php if ($_smarty_tpl->getValue('deliveryStatus')['exception']) {?><i class="ti ti-alert-triangle icon text-warning" aria-hidden="true"></i>
					<?php } elseif ($_smarty_tpl->getValue('deliveryStatus')['allDelivered']) {?><i class="ti ti-check icon text-success" aria-hidden="true"></i>
					<?php } else { ?><i class="ti ti-refresh icon" aria-hidden="true"></i><?php }?>
					<a href="javascript:showDeliveryStatus(<?php echo $_smarty_tpl->getValue('mailID');?>
);"><?php echo $_smarty_tpl->getValue('deliveryStatus')['statusText'];?>
</a>
				</dd>
			</div>
			<?php }?>
		</dl>

		<?php if ($_smarty_tpl->getValue('notes')) {?>
		<div class="bm-mail-notes-section">
			<div class="bm-mail-attachments-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</div>
			<div class="bm-mail-notes-box"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('notes'),'allowEmpty'=>true), $_smarty_tpl);?>
</div>
		</div>
		<?php }?>
	</div>
</div>

<div id="bigFormToolbar" class="bm-mail-toolbar">
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply(<?php echo $_smarty_tpl->getValue('mailID');?>
,false);">
		<i class="ti ti-arrow-back-up icon" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reply"), $_smarty_tpl);?>

	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="mailReply(<?php echo $_smarty_tpl->getValue('mailID');?>
,true);">
		<i class="ti ti-arrows-double-ne-sw icon" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"replyall"), $_smarty_tpl);?>

	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&forward=<?php echo $_smarty_tpl->getValue('mailID');?>
';">
		<i class="ti ti-arrow-forward-up icon" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forward"), $_smarty_tpl);?>

	</button>
	<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="printMail(<?php echo $_smarty_tpl->getValue('mailID');?>
,'<?php echo $_smarty_tpl->getValue('sid');?>
');">
		<i class="ti ti-printer icon" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"print"), $_smarty_tpl);?>

	</button>
	<?php if (!(true && (true && null !== ($_smarty_tpl->getValue('folderInfo')['readonly'] ?? null)))) {?><button type="button" class="btn btn-sm btn-ghost-danger" onclick="<?php if ($_smarty_tpl->getValue('folderID') == -5) {?>if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) <?php }?> deleteMail(<?php echo $_smarty_tpl->getValue('mailID');?>
);">
		<i class="ti ti-trash icon" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>

	</button><?php }?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.preview.tpl:afterButtons"), $_smarty_tpl);?>

</div>

<div class="bm-mail-alerts">
<?php $_smarty_tpl->renderSubTemplate("file:li/email.mailnotes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('preview'=>true), (int) 0, $_smarty_current_dir);
?>
</div>

<iframe width="100%" style="height:200px;" id="textArea" name="textArea" src="about:blank" class="mailHTMLText" frameborder="no"></iframe>
<textarea id="textArea_raw" style="display:none;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('text'),'allowDoubleEnc'=>true), $_smarty_tpl);?>
</textarea>

<?php if ($_smarty_tpl->getValue('attachments')) {?>
<div class="bm-mail-attachments-footer">
	<div class="bm-mail-attachments-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>
</div>
	<?php $_smarty_tpl->renderSubTemplate("file:li/email.attachments.chips.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
</div>
<?php }?>

<form id="quoteForm" action="email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&reply=<?php echo $_smarty_tpl->getValue('mailID');?>
" method="post">
	<input type="hidden" name="text" id="quoteText" value="" />
</form>
<?php }
}
