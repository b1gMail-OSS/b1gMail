<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:49:20
  from 'file:li/notifications.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a20c7a959_23961723',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eee3b28ffd2e18a8c3d7010074f281c7f9fb5ece' => 
    array (
      0 => 'li/notifications.tpl',
      1 => 1779525291,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a20c7a959_23961723 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if ($_smarty_tpl->getValue('bmNotifications')) {?>
<ul>
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('bmNotifications'), '_item');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_item')->value) {
$foreach0DoElse = false;
?>
	<li><a href="#"<?php if ($_smarty_tpl->getValue('_item')['link']) {?> onmousedown="<?php if ($_smarty_tpl->getValue('_item')['flags']&2) {
echo $_smarty_tpl->getValue('_item')['link'];
} else { ?>document.location.href='<?php echo $_smarty_tpl->getValue('_item')['link'];?>
sid=<?php echo $_smarty_tpl->getValue('sid');?>
';<?php }?>"<?php }
if (!$_smarty_tpl->getValue('_item')['read']) {?> class="unread"<?php }
if ($_smarty_tpl->getValue('_item')['old']) {?> style="opacity:0.5;"<?php }?>>
		<?php if ($_smarty_tpl->getValue('_item')['icon']) {?><table><tr><td style="width:40px;"><i class="fa <?php echo $_smarty_tpl->getValue('_item')['faIcon'];?>
 fa-3x" aria-hidden="true"></i></td><td><?php }?>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'value'=>$_smarty_tpl->getValue('_item')['text'],'cut'=>150), $_smarty_tpl);?>

		<div class="date"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('nice'=>true,'timestamp'=>$_smarty_tpl->getValue('_item')['date']), $_smarty_tpl);?>
</div>
		<?php if ($_smarty_tpl->getValue('_item')['icon']) {?></td></tr></table><?php }?>
	</a></li>
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</ul>
<?php } else { ?>
	<center style="margin-top:1em;color:#999;"><em>(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nonotifications"), $_smarty_tpl);?>
)</em></center>
<?php }
}
}
