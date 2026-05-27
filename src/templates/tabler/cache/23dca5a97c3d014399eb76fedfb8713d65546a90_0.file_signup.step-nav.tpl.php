<?php
/* Smarty version 5.8.0, created on 2026-05-25 17:03:43
  from 'file:nli/signup.step-nav.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1480eff22944_15508418',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '23dca5a97c3d014399eb76fedfb8713d65546a90' => 
    array (
      0 => 'nli/signup.step-nav.tpl',
      1 => 1779728605,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1480eff22944_15508418 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="bm-signup-nav d-flex justify-content-between align-items-center">
	<button type="button" class="btn btn-outline-secondary" data-role="prev-block" style="display:none">
		<i class="ti ti-chevron-left me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>

	</button>
	<button type="button" class="btn btn-primary ms-auto" data-role="next-block">
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"next"), $_smarty_tpl);?>
<i class="ti ti-chevron-right ms-1" aria-hidden="true"></i>
	</button>
</div>
<?php }
}
