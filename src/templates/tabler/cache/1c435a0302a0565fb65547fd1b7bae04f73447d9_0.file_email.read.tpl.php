<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:47:22
  from 'file:li/email.read.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c08ae94836_94945962',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1c435a0302a0565fb65547fd1b7bae04f73447d9' => 
    array (
      0 => 'li/email.read.tpl',
      1 => 1779809684,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/email.mailnotes.tpl' => 1,
    'file:li/email.attachments.chips.tpl' => 1,
    'file:li/email.addressmenu.tpl' => 1,
  ),
))) {
function content_6a15c08ae94836_94945962 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div id="contentHeader" class="bm-mail-read-page-header">
	<div class="left">
		<a class="btn btn-sm btn-ghost-secondary" href="email.php?folder=<?php echo $_smarty_tpl->getValue('folderID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
			<i class="ti ti-<?php if ($_smarty_tpl->getValue('folderInfo')['type'] == 'inbox') {?>inbox<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'outbox') {?>send<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'drafts') {?>file-pencil<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'spam') {?>ban<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'trash') {?>trash<?php } elseif ($_smarty_tpl->getValue('folderInfo')['type'] == 'intellifolder') {?>folder<?php } else { ?>folder<?php }?> icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getValue('folderInfo')['title'];?>

		</a>
	</div>
	<div class="right">
		<?php if (( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?><button type="button" class="btn btn-sm btn-ghost-secondary" onclick="moveMail('<?php echo $_smarty_tpl->getValue('mailID');?>
');">
			<i class="ti ti-arrows-move icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"move"), $_smarty_tpl);?>

		</button><?php }?>
	</div>
</div>

<div class="scrollContainer withBottomBar<?php if (!( !true || empty($_GET['openConversationView']))) {?>AndLayer<?php }?> bm-mail-read-scroll" id="mailReadScrollContainer">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:head"), $_smarty_tpl);?>


	<div class="previewMailHeader bm-mail-read-header" id="mailHeader">
		<div class="bm-mail-read-header-top">
			<h1 class="bm-mail-subject"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('subject')), $_smarty_tpl);?>
</h1>
		</div>
		<dl class="bm-mail-meta-list bm-mail-meta-list-read">
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

			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:metaTable"), $_smarty_tpl);?>

		</dl>
	</div>

	<div id="bigFormToolbar" class="bm-mail-toolbar">

		<?php if ($_smarty_tpl->getValue('prevID')) {?><button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?id=<?php echo $_smarty_tpl->getValue('prevID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="ti ti-chevron-left icon" aria-hidden="true"></i>
		</button><?php }?>

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

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&redirect=<?php echo $_smarty_tpl->getValue('mailID');?>
';">
			<i class="ti ti-mail-forward icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"redirect"), $_smarty_tpl);?>

		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=download&id=<?php echo $_smarty_tpl->getValue('mailID');?>
';">
			<i class="ti ti-download icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>

		</button>

		<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="printMail(<?php echo $_smarty_tpl->getValue('mailID');?>
,'<?php echo $_smarty_tpl->getValue('sid');?>
');">
			<i class="ti ti-printer icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"print"), $_smarty_tpl);?>

		</button>

		<?php if (( !true || empty($_smarty_tpl->getValue('folderInfo')['readonly']))) {?><button type="button" class="btn btn-sm btn-ghost-danger" onclick="<?php if ($_smarty_tpl->getValue('folderID') == -5) {?>if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) <?php }?> document.location.href='email.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&do=deleteMail&id=<?php echo $_smarty_tpl->getValue('mailID');?>
&folder=<?php echo $_smarty_tpl->getValue('folderID');?>
';">
			<i class="ti ti-trash icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>

		</button><?php }?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:afterButtons"), $_smarty_tpl);?>


		<?php if ($_smarty_tpl->getValue('nextID')) {?><button type="button" class="btn btn-sm btn-ghost-secondary" onclick="document.location.href='email.read.php?id=<?php echo $_smarty_tpl->getValue('nextID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
			<i class="ti ti-chevron-right icon" aria-hidden="true"></i>
		</button><?php }?>

	</div>



<div class="pad bm-mail-read-body">

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:beforeText"), $_smarty_tpl);?>

<div class="bm-mail-alerts">
<?php $_smarty_tpl->renderSubTemplate("file:li/email.mailnotes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:mailNotes"), $_smarty_tpl);?>

	</div>

	<iframe name="mailFrame" width="100%" style="height:200px;" id="textArea" src="about:blank" class="mailHTMLText" frameborder="no"></iframe>
	<textarea id="textArea_raw" style="display:none;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('text'),'allowDoubleEnc'=>true), $_smarty_tpl);?>
</textarea>

	<?php echo '<script'; ?>
>
	<!--
		initEMailTextArea(EBID('textArea_raw').value);
	//-->
	<?php echo '</script'; ?>
>

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:afterText"), $_smarty_tpl);?>


<div id="afterText">
<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:foot"), $_smarty_tpl);?>


<form id="quoteForm" action="email.compose.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&reply=<?php echo $_smarty_tpl->getValue('mailID');?>
" method="post">
	<input type="hidden" name="text" id="quoteText" value="" />
</form>

</div>

</div></div>

<?php if ($_smarty_tpl->getValue('attachments') || (true && ($_smarty_tpl->hasVariable('vcards') && null !== ($_smarty_tpl->getValue('vcards') ?? null)))) {?>
<div class="contentBottomLayer bm-mail-bottom-layer" id="bottomLayer_attachments" style="display:none;">
	<div class="bm-mail-bottom-layer-header">
		<span class="bm-mail-bottom-layer-title">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);
if ($_smarty_tpl->getValue('attachments')) {?> (<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('attachments'));?>
)<?php }?>
		</span>
		<button type="button" class="btn btn-sm btn-ghost-secondary btn-icon" onclick="readMailHideBottomLayers()" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
">
			<i class="ti ti-x icon" aria-hidden="true"></i>
		</button>
	</div>

	<?php if ($_smarty_tpl->getValue('attachments')) {?>
	<form name="attachmentsForm" method="get" action="email.read.php" class="bm-mail-attachments-panel-form">
	<input type="hidden" name="id" value="<?php echo $_smarty_tpl->getValue('mailID');?>
" />
	<input type="hidden" name="sid" value="<?php echo $_smarty_tpl->getValue('sid');?>
" />
	<?php }?>

	<div class="bm-mail-attachments-panel-body">
		<?php if ($_smarty_tpl->getValue('attachments')) {?>
		<?php $_smarty_tpl->renderSubTemplate("file:li/email.attachments.chips.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('selectable'=>true), (int) 0, $_smarty_current_dir);
?>
		<?php }?>

		<?php if ((true && ($_smarty_tpl->hasVariable('vcards') && null !== ($_smarty_tpl->getValue('vcards') ?? null)))) {?>
		<div class="bm-mail-vcards">
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('vcards'), 'card', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('card')->value) {
$foreach0DoElse = false;
?>
			<div class="card bm-mail-vcard-card">
				<div class="card-body">
					<div class="row g-2 align-items-start">
						<div class="col-auto">
							<span class="avatar bg-primary-lt text-primary">
								<i class="ti ti-address-book icon" aria-hidden="true"></i>
							</span>
						</div>
						<div class="col">
							<dl class="row bm-mail-vcard-dl mb-0">
								<dt class="col-sm-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"firstname"), $_smarty_tpl);?>
</dt>
								<dd class="col-sm-8"><?php if ($_smarty_tpl->getValue('card')['vorname']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('card')['vorname']), $_smarty_tpl);
} else { ?>-<?php }?></dd>
								<dt class="col-sm-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"surname"), $_smarty_tpl);?>
</dt>
								<dd class="col-sm-8"><?php if ($_smarty_tpl->getValue('card')['nachname']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('card')['nachname']), $_smarty_tpl);
} else { ?>-<?php }?></dd>
								<dt class="col-sm-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"company"), $_smarty_tpl);?>
</dt>
								<dd class="col-sm-8"><?php if ($_smarty_tpl->getValue('card')['firma']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('card')['firma']), $_smarty_tpl);
} else { ?>-<?php }?></dd>
								<dt class="col-sm-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</dt>
								<dd class="col-sm-8"><?php if ($_smarty_tpl->getValue('card')['email']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('card')['email']), $_smarty_tpl);
} else { ?>-<?php }?></dd>
							</dl>
						</div>
						<div class="col-12 col-md-auto d-flex flex-wrap gap-1">
							<a class="btn btn-sm btn-primary" href="email.read.php?id=<?php echo $_smarty_tpl->getValue('mailID');?>
&action=importVCF&attachment=<?php echo $_smarty_tpl->getValue('key');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
								<i class="ti ti-upload icon" aria-hidden="true"></i>
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"importvcf"), $_smarty_tpl);?>

							</a>
							<a class="btn btn-sm btn-ghost-secondary" href="email.read.php?id=<?php echo $_smarty_tpl->getValue('mailID');?>
&action=downloadAttachment&attachment=<?php echo $_smarty_tpl->getValue('key');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
								<i class="ti ti-download icon" aria-hidden="true"></i>
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>

							</a>
						</div>
					</div>
				</div>
			</div>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
		</div>
		<?php }?>
	</div>

	<?php if ($_smarty_tpl->getValue('attachments')) {?>
	<div class="bm-mail-bottom-layer-footer bm-mail-attachments-bulk-footer">
		<label class="form-check mb-0">
			<input class="form-check-input" type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.attachmentsForm, 'att');" />
			<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"all"), $_smarty_tpl);?>
</span>
		</label>
		<div class="input-group input-group-sm bm-mail-attachments-bulk-actions">
			<select class="form-select" name="do">
				<option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"selaction"), $_smarty_tpl);?>
 ------</option>
				<option value="downloadAttachments"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"download"), $_smarty_tpl);?>
</option>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"email.read.tpl:attachSelect"), $_smarty_tpl);?>

			</select>
			<button class="btn btn-primary" type="submit"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
		</div>
	</div>
	</form>
	<?php }?>
</div>
<?php }?>

<?php if ($_smarty_tpl->getValue('conversationView')) {?>
<div class="contentBottomLayer" id="bottomLayer_conversation" style="display:<?php if (( !true || empty($_GET['openConversationView']))) {?>none<?php }?>;">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-comment"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"conversation"), $_smarty_tpl);?>

		</div>
		<div class="right">
			<button onclick="readMailHideBottomLayers()">
				<i class="fa fa-close"></i>
			</button>
		</div>
	</div>

	<div class="bigForm">
		<iframe id="conversationIFrame" style="width:100%;height:100%;" src="email.read.php?action=showThread&id=<?php echo $_smarty_tpl->getValue('mailID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" border="0" frameborder="0"></iframe>
	</div>
</div>
<?php }?>

<div class="contentBottomLayer bm-mail-bottom-layer" id="bottomLayer_props" style="display:none;">
	<div class="bm-mail-bottom-layer-header">
		<span class="bm-mail-bottom-layer-title">
			<i class="ti ti-tags icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"props"), $_smarty_tpl);?>

		</span>
		<button type="button" class="btn btn-sm btn-ghost-secondary btn-icon" onclick="readMailHideBottomLayers()" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
">
			<i class="ti ti-x icon" aria-hidden="true"></i>
		</button>
	</div>

	<form method="post" action="email.read.php?id=<?php echo $_smarty_tpl->getValue('mailID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="bm-mail-props-form">
	<input type="hidden" name="do" value="saveMeta" />

	<div class="bm-mail-props-body">
		<div class="row g-3">
			<div class="col-md-4">
				<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"color"), $_smarty_tpl);?>
</label>
				<div class="bm-mail-color-grid" role="radiogroup">
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 1) {?> checked="checked"<?php }?> name="color" value="1" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-1" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 2) {?> checked="checked"<?php }?> name="color" value="2" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-2" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 3) {?> checked="checked"<?php }?> name="color" value="3" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-3" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 4) {?> checked="checked"<?php }?> name="color" value="4" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-4" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 5) {?> checked="checked"<?php }?> name="color" value="5" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-5" title=""></span>
					</label>
					<label class="bm-mail-color-option">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 6) {?> checked="checked"<?php }?> name="color" value="6" />
						<span class="bm-mail-color-swatch bm-mail-color-swatch-6" title=""></span>
					</label>
					<label class="bm-mail-color-option bm-mail-color-option-none">
						<input type="radio" class="form-check-input"<?php if ($_smarty_tpl->getValue('color') == 0) {?> checked="checked"<?php }?> name="color" value="0" />
						<span class="bm-mail-color-none-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"none"), $_smarty_tpl);?>
</span>
					</label>
				</div>
			</div>
			<div class="col-md-4">
				<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"flags"), $_smarty_tpl);?>
</label>
				<div class="bm-mail-flags-list">
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[1]" id="flags1"<?php if ($_POST['do'] == 'saveMeta' && ($_smarty_tpl->getValue('flags')&1)) {?> checked="checked"<?php }?> />
						<span class="form-check-label"><i class="ti ti-mail icon" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unread"), $_smarty_tpl);?>
</span>
					</label>
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[16]" id="flags16"<?php if ($_smarty_tpl->getValue('flags')&16) {?> checked="checked"<?php }?> />
						<span class="form-check-label"><i class="ti ti-flag-filled icon" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"marked"), $_smarty_tpl);?>
</span>
					</label>
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="flags[4096]" id="flags4096"<?php if ($_smarty_tpl->getValue('flags')&4096) {?> checked="checked"<?php }?> />
						<span class="form-check-label"><i class="ti ti-circle-check icon" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"done"), $_smarty_tpl);?>
</span>
					</label>
				</div>
			</div>
			<div class="col-md-4">
				<label class="form-label" for="mailNotesField"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</label>
				<textarea class="form-control" id="mailNotesField" name="notes" rows="4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('notes'),'allowEmpty'=>true), $_smarty_tpl);?>
</textarea>
			</div>
		</div>
	</div>

	<div class="bm-mail-bottom-layer-footer">
		<button class="btn btn-primary" type="submit"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('folderInfo')['readonly'] ?? null)))) {?> disabled="disabled"<?php }?>>
			<i class="ti ti-check icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"save"), $_smarty_tpl);?>

		</button>
	</div>

	</form>
</div>

<div id="contentFooter" class="contentFooter bm-mail-read-footer">
	<div class="left bm-mail-read-footer-actions">
		<?php if ($_smarty_tpl->getValue('attachments') || (true && ($_smarty_tpl->hasVariable('vcards') && null !== ($_smarty_tpl->getValue('vcards') ?? null)))) {?>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="attachments" onclick="readMailShowBottomLayer('attachments');">
			<i class="ti ti-paperclip icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);
if ($_smarty_tpl->getValue('attachments')) {?> <span class="badge bg-primary-lt ms-1"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('attachments'));?>
</span><?php }?>
		</button>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('conversationView')) {?>
		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="conversation" onclick="readMailShowBottomLayer('conversation');">
			<i class="ti ti-messages icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"conversation"), $_smarty_tpl);?>

		</button>
		<?php }?>

		<button type="button" class="btn btn-sm btn-ghost-secondary" data-bottom-layer="props" onclick="readMailShowBottomLayer('props');">
			<i class="ti ti-tags icon" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"props"), $_smarty_tpl);?>

		</button>
	</div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:li/email.addressmenu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
