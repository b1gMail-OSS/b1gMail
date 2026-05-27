<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/lang.dropdown.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430fafbbe8_41051972',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4b921559e074a8ffcb802bdb4d36987f92585f9b' => 
    array (
      0 => 'nli/lang.dropdown.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a14430fafbbe8_41051972 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="dropdown<?php if ((($tmp = $_smarty_tpl->getValue('langDropdownDropup') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?> dropup<?php }?>">
	<a href="#" class="<?php echo (($tmp = $_smarty_tpl->getValue('langDropdownClass') ?? null)===null||$tmp==='' ? 'btn btn-sm btn-link text-secondary' ?? null : $tmp);?>
 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
		<i class="ti ti-world me-1" aria-hidden="true"></i>
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('languageList'), 'langInfo', false, 'langKey');
$foreach17DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('langKey')->value => $_smarty_tpl->getVariable('langInfo')->value) {
$foreach17DoElse = false;
if ($_smarty_tpl->getValue('langInfo')['active']) {
echo $_smarty_tpl->getValue('langInfo')['title'];
}
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</a>
	<ul class="dropdown-menu<?php if ((($tmp = $_smarty_tpl->getValue('langDropdownMenuEnd') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?> dropdown-menu-end<?php }?>">
		<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('languageList'), 'langInfo', false, 'langKey');
$foreach18DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('langKey')->value => $_smarty_tpl->getVariable('langInfo')->value) {
$foreach18DoElse = false;
?>
		<li<?php if ($_smarty_tpl->getValue('langInfo')['active']) {?> class="active"<?php }?>>
			<a class="dropdown-item" href="index.php?action=switchLanguage&amp;lang=<?php echo $_smarty_tpl->getValue('langKey');
if (!( !true || empty($_GET['action']))) {?>&amp;target=<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_GET['action']), $_smarty_tpl);
}?>"><?php echo $_smarty_tpl->getValue('langInfo')['title'];?>
</a>
		</li>
		<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	</ul>
</div>
<?php }
}
