<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.search.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee3c934_25620945',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '626d9f2f83f5741a6f94cc7d96a46348e798e625' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.search.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee3c934_25620945 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget" style="text-align:center;">
	<form action="start.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=search" method="post" target="_blank">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td><input type="text" name="q" value="" style="width:100%;" /></td>
				<td width="105" align="right"><input type="submit" value=" <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"websearch"), $_smarty_tpl);?>
 " /></td>
			</tr>
		</table>
	</form>
</div><?php }
}
