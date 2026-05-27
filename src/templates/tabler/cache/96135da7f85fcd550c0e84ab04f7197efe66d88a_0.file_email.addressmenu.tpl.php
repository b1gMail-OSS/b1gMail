<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:09:04
  from 'file:li/email.addressmenu.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a148230319dc1_43780482',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '96135da7f85fcd550c0e84ab04f7197efe66d88a' => 
    array (
      0 => 'li/email.addressmenu.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a148230319dc1_43780482 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><!-- address menu -->
<div id="addressMenu" class="mailMenu bm-mail-menu" style="display:none;position:absolute;left:0px;top:0px;z-index:1000;" oncontextmenu="return(false);" onmousedown="if(event.button==2) return(false);">
	<a id="addressMenuReadItem" class="mailMenuItem" style="display:none;" href="javascript:<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>parent.<?php }?>document.location.href='email.read.php?id='+encodeURIComponent(currentEMailID)+'&sid='+currentSID;"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mail_read"), $_smarty_tpl);?>
</a>
	<div class="mailMenuSep" id="addressMenuReadItemSep"></div>
	<a class="mailMenuItem" href="javascript:<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>parent.<?php }?>document.location.href='email.compose.php?to='+encodeURIComponent(currentEMail)+'&sid='+currentSID;"><i class="fa fa-reply" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>
</a>
	<a class="mailMenuItem" href="javascript:<?php if ((true && ($_smarty_tpl->hasVariable('preview') && null !== ($_smarty_tpl->getValue('preview') ?? null)))) {?>parent.<?php }?>document.location.href='organizer.addressbook.php?action=addContact&email='+encodeURIComponent(currentEMail)+'&sid='+currentSID;"><i class="fa fa-address-book-o" aria-hidden="true"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"toaddr"), $_smarty_tpl);?>
</a>
</div><?php }
}
