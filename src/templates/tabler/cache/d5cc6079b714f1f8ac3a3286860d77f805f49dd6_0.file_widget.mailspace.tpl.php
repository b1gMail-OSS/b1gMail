<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.mailspace.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee2c229_18439682',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd5cc6079b714f1f8ac3a3286860d77f805f49dd6' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.mailspace.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee2c229_18439682 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget" style="text-align:center;">
	<table cellspacing="0" cellpadding="2" width="100%">
		<tr>
			<td align="center"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('progressBar')->handle(array('value'=>$_smarty_tpl->getValue('bmwidget_mailspace_spaceUsed'),'max'=>$_smarty_tpl->getValue('bmwidget_mailspace_spaceLimit'),'width'=>250), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('bmwidget_mailspace_spaceUsed')), $_smarty_tpl);?>
 / <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('size')->handle(array('bytes'=>$_smarty_tpl->getValue('bmwidget_mailspace_spaceLimit')), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"used"), $_smarty_tpl);?>
</td>
		</tr>
	</table>
</div><?php }
}
