<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:50:22
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.notes.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a5ee2e730_59730422',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1889fccd9850d7f21201bc78b1761eef78645272' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/widget.notes.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a5ee2e730_59730422 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="innerWidget notePreview" id="notePreview">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"clicknote"), $_smarty_tpl);?>

</div>
<div class="innerWidget" style="max-height: 79px; overflow-y: auto; border-top: 1px solid #DDDDDD;">
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('bmwidget_notes_items'), 'note', false, 'noteID');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('noteID')->value => $_smarty_tpl->getVariable('note')->value) {
$foreach3DoElse = false;
?>
	<a href="javascript:previewNote('<?php echo $_smarty_tpl->getValue('sid');?>
', '<?php echo $_smarty_tpl->getValue('noteID');?>
');">
	<i class="fa fa-sticky-note-o" aria-hidden="true"></i>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('note')['text'],'cut'=>30), $_smarty_tpl);?>
</a><br />
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</div><?php }
}
