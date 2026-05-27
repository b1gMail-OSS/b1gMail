<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:20:54
  from 'file:li/email.mailnotes.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1484f6267818_71784378',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b31a65b39c70764d223e4f2a6bf9f024fe731330' => 
    array (
      0 => 'li/email.mailnotes.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1484f6267818_71784378 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if ($_smarty_tpl->getValue('folderID') == -3) {?>
<div class="alert alert-info bm-mail-alert<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-file-pencil alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"thisisadraft"), $_smarty_tpl);?>

			<a class="alert-link" href="email.compose.php?redirect=<?php echo $_smarty_tpl->getValue('mailID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editsend"), $_smarty_tpl);?>
</a>
		</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('flags')&128) {?>
<div class="alert alert-danger bm-mail-alert<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-virus alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"infectedtext"), $_smarty_tpl);?>
: <?php echo $_smarty_tpl->getValue('infection');?>
</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('flags')&256) {?>
<div class="alert alert-warning bm-mail-alert bm-mail-alert-spam<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" id="spamQuestionDiv" style="display:;">
	<div class="d-flex">
		<div><i class="ti ti-ban alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"spamtext"), $_smarty_tpl);?>

			<?php if (!$_smarty_tpl->getValue('trained')) {?><a class="alert-link" href="javascript:setMailSpamStatus(<?php echo $_smarty_tpl->getValue('mailID');?>
, false<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>, true<?php }?>);"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"isnotspam"), $_smarty_tpl);?>
</a><?php }?>
		</div>
	</div>
</div>
<?php } elseif (!$_smarty_tpl->getValue('trained')) {?>
<div class="alert alert-warning bm-mail-alert bm-mail-alert-spam<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" id="spamQuestionDiv" style="display:;">
	<div class="d-flex">
		<div><i class="ti ti-help-circle alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<p class="bm-mail-alert-text mb-2 mb-md-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"spamquestion"), $_smarty_tpl);?>
</p>
			<div class="bm-mail-alert-actions">
				<button type="button" class="btn btn-sm btn-warning" onclick="setMailSpamStatus(<?php echo $_smarty_tpl->getValue('mailID');?>
, true<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>, true<?php }?>);"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"yes"), $_smarty_tpl);?>
</button>
				<button type="button" class="btn btn-sm btn-ghost-secondary" onclick="setMailSpamStatus(<?php echo $_smarty_tpl->getValue('mailID');?>
, false<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>, true<?php }?>);"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"no"), $_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('flags')&512) {?>
<div class="alert alert-info bm-mail-alert<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-certificate alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"certmailinfo"), $_smarty_tpl);?>
</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('htmlAvailable')) {?>
<div class="alert alert-info bm-mail-alert<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-code alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"htmlavailable"), $_smarty_tpl);?>

			<a class="alert-link" href="email.read.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&id=<?php echo $_smarty_tpl->getValue('mailID');?>
&htmlView=true"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"view"), $_smarty_tpl);?>
 &raquo;</a>
		</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('noExternal')) {?>
<div class="alert alert-info bm-mail-alert<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" id="noExternalDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-photo-off alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"noexternal"), $_smarty_tpl);?>

			<a class="alert-link" href="email.read.php?action=inlineHTML&mode=<?php echo $_smarty_tpl->getValue('textMode');?>
&id=<?php echo $_smarty_tpl->getValue('mailID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
&enableExternal=true" target="<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>textArea<?php } else { ?>mailFrame<?php }?>" onclick="document.getElementById('noExternalDiv').style.display='none';"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"showexternal"), $_smarty_tpl);?>
 &raquo;</a>
		</div>
	</div>
</div>
<?php }
if ($_smarty_tpl->getValue('confirmationTo')) {?>
<div class="alert alert-yellow bm-mail-alert bm-mail-alert-confirm<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" id="confirmationDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-mail-check alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"senderconfirmto"), $_smarty_tpl);?>

			<strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('confirmationTo')), $_smarty_tpl);?>
</strong>.
			<a class="alert-link" href="javascript:sendMailConfirmation(<?php echo $_smarty_tpl->getValue('mailID');?>
);"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendconfirmation"), $_smarty_tpl);?>
 &raquo;</a>
		</div>
	</div>
</div>
<?php } elseif ($_smarty_tpl->getValue('flags')&16384) {?>
<div class="alert alert-success bm-mail-alert bm-mail-alert-confirm<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?> preview<?php }?>" id="confirmationDiv" style="display:;" role="alert">
	<div class="d-flex">
		<div><i class="ti ti-check alert-icon icon" aria-hidden="true"></i></div>
		<div class="bm-mail-alert-body"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"confirmationsent"), $_smarty_tpl);?>
</div>
	</div>
</div>
<?php }
}
}
