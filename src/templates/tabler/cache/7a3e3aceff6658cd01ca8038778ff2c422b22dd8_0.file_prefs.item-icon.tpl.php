<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:39:10
  from 'file:li/prefs.item-icon.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15be9ecb24a5_57076931',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a3e3aceff6658cd01ca8038778ff2c422b22dd8' => 
    array (
      0 => 'li/prefs.item-icon.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/icon.tpl' => 1,
  ),
))) {
function content_6a15be9ecb24a5_57076931 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
if ((true && (true && null !== ($_smarty_tpl->getValue('prefsIcons')[$_smarty_tpl->getValue('item')] ?? null)))) {?>
	<img src="<?php echo $_smarty_tpl->getValue('prefsIcons')[$_smarty_tpl->getValue('item')];?>
" width="20" height="20" class="bm-prefs-item-icon-img" alt="" />
<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] ?? null)))) {?>
	<?php if ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-user' || $_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-user-o') {?><i class="ti ti-at icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-cogs' || $_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-cog') {?><i class="ti ti-settings icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-reply') {?><i class="ti ti-mail-forward icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-ban') {?><i class="ti ti-ban icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-bug') {?><i class="ti ti-bug icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-filter') {?><i class="ti ti-filter icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-id-badge') {?><i class="ti ti-ticket icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-id-card' || $_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-id-card-o') {?><i class="ti ti-id icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-compress') {?><i class="ti ti-mail-down icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-key') {?><i class="ti ti-key icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-quote-right') {?><i class="ti ti-quote icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-download') {?><i class="ti ti-download icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-shopping-cart') {?><i class="ti ti-shopping-cart icon icon-1" aria-hidden="true"></i>
	<?php } elseif ($_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-question-circle' || $_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')] == 'fa-question-circle-o') {?><i class="ti ti-help icon icon-1" aria-hidden="true"></i>
	<?php } else {
$_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>$_smarty_tpl->getValue('prefsfaIcons')[$_smarty_tpl->getValue('item')]), (int) 0, $_smarty_current_dir);
?>
	<?php }
} elseif ($_smarty_tpl->getValue('item') == 'aliases') {?><i class="ti ti-at icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'common') {?><i class="ti ti-settings icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'autoresponder') {?><i class="ti ti-mail-forward icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'antispam') {?><i class="ti ti-ban icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'antivirus') {?><i class="ti ti-bug icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'filters') {?><i class="ti ti-filter icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'coupons') {?><i class="ti ti-ticket icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'membership') {?><i class="ti ti-id icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'extpop3') {?><i class="ti ti-mail-down icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'keyring') {?><i class="ti ti-key icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'signatures') {?><i class="ti ti-quote icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'software') {?><i class="ti ti-download icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'contact') {?><i class="ti ti-address-book icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'faq') {?><i class="ti ti-help icon icon-1" aria-hidden="true"></i>
<?php } elseif ($_smarty_tpl->getValue('item') == 'orders') {?><i class="ti ti-shopping-cart icon icon-1" aria-hidden="true"></i>
<?php } else { ?><i class="ti ti-point icon icon-1" aria-hidden="true"></i>
<?php }
}
}
