<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:26:47
  from 'file:li/dialog.head.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1350f7411953_75609788',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b80a58971c0c38ea43d8ec374c4233b5f6f5f4da' => 
    array (
      0 => 'li/dialog.head.tpl',
      1 => 1779650505,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1350f7411953_75609788 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><!doctype html>
<html lang="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"langCode"), $_smarty_tpl);?>
">
<?php echo '<script'; ?>
>
(function(){
	try {
		var t = null;
		if(window.parent && window.parent !== window && window.parent.document)
			t = window.parent.document.documentElement.getAttribute('data-bs-theme');
		if(!t)
			t = localStorage.getItem('bm-tabler-theme');
		if(t === 'dark' || t === 'light')
			document.documentElement.setAttribute('data-bs-theme', t);
	} catch(e) {}
})();
<?php echo '</script'; ?>
>

<head>
	<title><?php if ((true && ($_smarty_tpl->hasVariable('title') && null !== ($_smarty_tpl->getValue('title') ?? null)))) {
echo $_smarty_tpl->getValue('title');
} elseif ((true && ($_smarty_tpl->hasVariable('dialogTitle') && null !== ($_smarty_tpl->getValue('dialogTitle') ?? null)))) {
echo $_smarty_tpl->getValue('dialogTitle');
} else {
echo $_smarty_tpl->getValue('service_title');
}?></title>

	<meta charset="<?php echo $_smarty_tpl->getValue('charset');?>
" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />

	<link rel="shortcut icon" href="<?php echo $_smarty_tpl->getValue('selfurl');?>
favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/tabler.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/tabler.min.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/tabler-icons.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/tabler-icons.min.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/inter.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/inter.css"), $_smarty_tpl);?>
" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/dialog-tabler.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/dialog-tabler.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/tabler-custom.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/tabler-custom.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_cssFiles')['li'], '_file');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_file')->value) {
$foreach1DoElse = false;
?><link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getValue('_file');?>
" /><?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

	<?php echo '<script'; ?>
 type="text/javascript">
	<!--
		var tplDir = '<?php echo $_smarty_tpl->getValue('tpldir');?>
';
	//-->
	<?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('selfurl');?>
clientlang.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('selfurl');?>
clientlib/overlay.js" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/common.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/common.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/loggedin.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/loggedin.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/dialog.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/dialog.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_jsFiles')['li'], '_file');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_file')->value) {
$foreach2DoElse = false;
echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->getValue('_file');?>
"><?php echo '</script'; ?>
><?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</head>

<body class="bm-dialog-body<?php if ((true && ($_smarty_tpl->hasVariable('dialogBodyClass') && null !== ($_smarty_tpl->getValue('dialogBodyClass') ?? null)))) {?> <?php echo $_smarty_tpl->getValue('dialogBodyClass');
}?>"<?php if ((true && ($_smarty_tpl->hasVariable('dialogOnLoad') && null !== ($_smarty_tpl->getValue('dialogOnLoad') ?? null)))) {?> onload="<?php echo $_smarty_tpl->getValue('dialogOnLoad');?>
"<?php }?>>
<?php }
}
