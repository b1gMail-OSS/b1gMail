<?php
/* Smarty version 5.8.0, created on 2026-05-26 15:55:32
  from 'file:nli/login.center.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c2749ec484_22099140',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '10fa634ab27ca0843f8487346ad647f521963a44' => 
    array (
      0 => 'nli/login.center.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/login.brand.tpl' => 1,
    'file:nli/login.welcomeback.tpl' => 1,
    'file:nli/login.card-footer.tpl' => 2,
    'file:nli/login.form.tpl' => 1,
  ),
))) {
function content_6a15c2749ec484_22099140 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="page page-center">
	<div class="container container-tight py-4">
		<?php $_smarty_tpl->renderSubTemplate("file:nli/login.brand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

		<?php if ($_smarty_tpl->getValue('welcomeBack')) {?>
		<div class="card card-md">
			<div class="card-body">
				<?php $_smarty_tpl->renderSubTemplate("file:nli/login.welcomeback.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			</div>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.card-footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>
		<?php } else { ?>
		<div class="card card-md">
			<div class="card-body">
				<h2 class="h2 text-center mb-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"welcome"), $_smarty_tpl);?>
</h2>
				<?php $_smarty_tpl->renderSubTemplate("file:nli/login.form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			</div>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.card-footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>
		<?php }?>

		<div class="text-center text-secondary mt-3 small">
			<div>&copy; <?php echo $_smarty_tpl->getValue('year');?>
 <?php echo $_smarty_tpl->getValue('service_title');?>
</div>
			<div class="mt-1">
				<a href="index.php?action=faq" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>
</a>
				<span class="mx-1">·</span>
				<a href="index.php?action=tos" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</a>
				<span class="mx-1">·</span>
				<a href="index.php?action=imprint" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>
</a>
			</div>
			<div class="mt-1">
				powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer" class="text-secondary">b1gMail.eu</a>
			</div>
		</div>
	</div>
</div>
<?php }
}
