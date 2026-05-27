<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/widget-board.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b78b836_36726113',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b7be51bc38665500501d29368d4515369bb27a6b' => 
    array (
      0 => 'li/widget-board.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14822b78b836_36726113 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-widget-board">
	<div id="startBoxes"></div>
	<div id="startBoxes_elems" style="display:none">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('widgets'), 'widget', false, 'key');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('widget')->value) {
$foreach6DoElse = false;
?>
		<div title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('widget')['title']), $_smarty_tpl);?>
" rel="<?php if ($_smarty_tpl->getValue('widget')['hasPrefs']) {?>1<?php } else { ?>0<?php }?>,<?php echo $_smarty_tpl->getValue('widget')['prefsW'];?>
,<?php echo $_smarty_tpl->getValue('widget')['prefsH'];?>
,<?php if ($_smarty_tpl->getValue('widget')['icon']) {
echo $_smarty_tpl->getValue('widget')['icon'];
} else { ?>0<?php }?>" id="<?php echo $_smarty_tpl->getValue('key');?>
"><?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('widget')['template'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?></div>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</div>
</div>

<?php echo '<script'; ?>
 src="./clientlib/dragcontainer.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/dragcontainer.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/dashboard.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/dashboard.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
<!--
	currentSID = '<?php echo $_smarty_tpl->getValue('sid');?>
';
	var dc = bmInitWidgetBoard('startBoxes', <?php echo (($tmp = $_smarty_tpl->getValue('boardCols') ?? null)===null||$tmp==='' ? 3 ?? null : $tmp);?>
, 'dc', '<?php echo strtr((string)$_smarty_tpl->getValue('widgetOrder'), array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
						"\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
						"`" => "\\`", "\${" => "\\\$\{"));?>
', <?php echo $_smarty_tpl->getValue('boardSaveCallback');?>
);
	<?php if ((true && ($_smarty_tpl->hasVariable('autoSetPreviewPos') && null !== ($_smarty_tpl->getValue('autoSetPreviewPos') ?? null)))) {?>autoSetPreviewPos();<?php }?>
//-->
<?php echo '</script'; ?>
>
<?php }
}
