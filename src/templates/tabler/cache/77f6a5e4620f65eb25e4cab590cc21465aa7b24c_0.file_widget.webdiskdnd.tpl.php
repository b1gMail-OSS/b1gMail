<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.webdiskdnd.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee3e363_12316948',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '77f6a5e4620f65eb25e4cab590cc21465aa7b24c' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.webdiskdnd.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee3e363_12316948 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget" style="text-align:center;">
	<div id="wdDnDArea">
		<i class="fa fa-folder-open-o fa-5x" aria-hidden="true"></i>
	</div>
	
	<?php echo '<script'; ?>
 src="./clientlib/dndupload.js" type="text/javascript"><?php echo '</script'; ?>
>
	
	<?php echo '<script'; ?>
>
	<!--
		initDnDUpload(EBID('wdDnDArea'), 'webdisk.php?sid='+currentSID+'&folder=0&action=dndUpload', function() { document.location.href='webdisk.php?sid='+currentSID+'&folder=0'; });
	//-->
	<?php echo '</script'; ?>
>
</div>
<?php }
}
