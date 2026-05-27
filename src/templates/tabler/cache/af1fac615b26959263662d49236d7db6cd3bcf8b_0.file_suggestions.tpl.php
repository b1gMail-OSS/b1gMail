<?php
/* Smarty version 5.8.0, created on 2026-05-25 16:43:05
  from 'file:nli/suggestions.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a147c19250162_37884849',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af1fac615b26959263662d49236d7db6cd3bcf8b' => 
    array (
      0 => 'nli/suggestions.tpl',
      1 => 1779724234,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a147c19250162_37884849 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><p class="text-secondary small mb-2">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"suggestions_desc"), $_smarty_tpl);?>

</p>

<div class="list-group list-group-flush bm-email-suggestion-list">
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('suggestions'), 'email');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('email')->value) {
$foreach0DoElse = false;
?>
	<div class="list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2 px-0">
		<span class="text-break">
			<i class="ti ti-circle-check text-success me-1" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('email')), $_smarty_tpl);?>

		</span>
		<button type="button" class="btn btn-sm btn-primary" onclick="chooseAddress('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('email')), $_smarty_tpl);?>
');return false;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"choose"), $_smarty_tpl);?>
</button>
	</div>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
