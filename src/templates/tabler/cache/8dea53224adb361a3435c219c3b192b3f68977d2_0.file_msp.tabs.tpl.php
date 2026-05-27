<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/msp.tabs.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430faf15f9_17500850',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8dea53224adb361a3435c219c3b192b3f68977d2' => 
    array (
      0 => 'nli/msp.tabs.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14430faf15f9_17500850 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->assign('nliAction', (($tmp = $_REQUEST['action'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp), false, NULL);
if ($_smarty_tpl->getValue('page') == 'nli/login.tpl' || $_smarty_tpl->getValue('page') == 'nli/login.smsvalidation.tpl' || $_smarty_tpl->getValue('page') == 'nli/loginresult.tpl') {?>
	<?php $_smarty_tpl->assign('nliAction', 'login', false, NULL);
} elseif ($_smarty_tpl->getValue('page') == 'nli/signup.tpl' || $_smarty_tpl->getValue('page') == 'nli/regdone.tpl') {?>
	<?php $_smarty_tpl->assign('nliAction', 'signup', false, NULL);
} elseif ($_smarty_tpl->getValue('page') == 'nli/faq.tpl') {?>
	<?php $_smarty_tpl->assign('nliAction', 'faq', false, NULL);
} elseif ($_smarty_tpl->getValue('page') == 'nli/tos.tpl') {?>
	<?php $_smarty_tpl->assign('nliAction', 'tos', false, NULL);
} elseif ($_smarty_tpl->getValue('page') == 'nli/imprint.tpl' || $_smarty_tpl->getValue('page') == 'nli/contact.complete.tpl') {?>
	<?php $_smarty_tpl->assign('nliAction', 'imprint', false, NULL);
}?>

<div class="card-header">
	<ul class="nav nav-pills card-header-pills">
		<li class="nav-item">
			<a class="nav-link<?php if ($_smarty_tpl->getValue('nliAction') == 'login') {?> active<?php }?>" href="index.php">
				<i class="icon nav-link-icon icon-2 ti ti-home" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"home"), $_smarty_tpl);?>

			</a>
		</li>
		<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
		<li class="nav-item">
			<a class="nav-link<?php if ($_smarty_tpl->getValue('nliAction') == 'signup') {?> active<?php }?>" href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup">
				<i class="icon nav-link-icon icon-2 ti ti-user-plus" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

			</a>
		</li>
		<?php }?>
		<li class="nav-item">
			<a class="nav-link<?php if ($_smarty_tpl->getValue('nliAction') == 'faq') {?> active<?php }?>" href="index.php?action=faq">
				<i class="icon nav-link-icon icon-2 ti ti-help" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>

			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?php if ($_smarty_tpl->getValue('nliAction') == 'tos') {?> active<?php }?>" href="index.php?action=tos">
				<i class="icon nav-link-icon icon-2 ti ti-file-text" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>

			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?php if ($_smarty_tpl->getValue('nliAction') == 'imprint') {?> active<?php }?>" href="index.php?action=imprint">
				<i class="icon nav-link-icon icon-2 ti ti-address-book" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>

			</a>
		</li>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach15DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach15DoElse = false;
if (!(($tmp = $_smarty_tpl->getValue('item')['top'] ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
		<li class="nav-item">
			<?php $_smarty_tpl->assign('_pluginLink', (($tmp = $_smarty_tpl->getValue('item')['link'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp), false, NULL);?>
			<a class="nav-link<?php if ((($tmp = $_smarty_tpl->getValue('item')['active'] ?? null)===null||$tmp==='' ? false ?? null : $tmp) || ($_smarty_tpl->getValue('nliAction') != '' && ($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('_pluginLink'),$_smarty_tpl->getValue('nliAction'),'') != $_smarty_tpl->getValue('_pluginLink')))) {?> active<?php }?>" href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['text']), $_smarty_tpl);?>
">
				<i class="icon nav-link-icon icon-2 ti ti-link" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getValue('item')['text'];?>

			</a>
		</li>
	<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</ul>
</div>
<?php }
}
