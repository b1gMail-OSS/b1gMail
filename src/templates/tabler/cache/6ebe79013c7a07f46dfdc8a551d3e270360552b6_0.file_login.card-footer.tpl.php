<?php
/* Smarty version 5.8.0, created on 2026-05-26 15:55:32
  from 'file:nli/login.card-footer.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c2749f09b2_97378681',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6ebe79013c7a07f46dfdc8a551d3e270360552b6' => 
    array (
      0 => 'nli/login.card-footer.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/lang.dropdown.tpl' => 1,
  ),
))) {
function content_6a15c2749f09b2_97378681 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="card-footer">
	<div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
		<?php $_smarty_tpl->renderSubTemplate("file:nli/lang.dropdown.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
		<div class="text-end">
			<a href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup" class="btn btn-sm btn-primary">
				<i class="ti ti-user-plus me-1" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

			</a>
		</div>
		<?php }?>
	</div>
</div>
<?php }
}
