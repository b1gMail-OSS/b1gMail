<?php
/* Smarty version 5.8.0, created on 2026-05-26 15:56:11
  from 'file:nli/login.cover.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c29bcf37e2_58148790',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c45459b20383f9ee9f2ff62f82f0308b51d899a8' => 
    array (
      0 => 'nli/login.cover.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/login.brand.tpl' => 1,
    'file:nli/login.welcomeback.tpl' => 1,
    'file:nli/login.cover-footer.tpl' => 2,
    'file:nli/login.form.tpl' => 1,
  ),
))) {
function content_6a15c29bcf37e2_58148790 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="row g-0 flex-fill nli-login-cover">
	<div class="col-12 col-lg-6 col-xl-4 border-top-wide border-primary d-flex flex-column justify-content-center">
		<div class="container container-tight my-5 px-lg-5">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.brand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

			<?php if ($_smarty_tpl->getValue('welcomeBack')) {?>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.welcomeback.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.cover-footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<?php } else { ?>
			<h2 class="h3 text-center mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"welcome"), $_smarty_tpl);?>
</h2>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.cover-footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
			<?php }?>

			<div class="text-center text-secondary mt-4 small">
				<div>&copy; <?php echo $_smarty_tpl->getValue('year');?>
 <?php echo $_smarty_tpl->getValue('service_title');?>
</div>
				<div class="mt-1">
					<a href="index.php?action=faq" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>
</a>
					<span class="mx-1">·</span>
					<a href="index.php?action=imprint" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>
</a>
					<span class="mx-1">·</span>
					<a href="<?php echo $_smarty_tpl->getValue('mobileURL');?>
" class="text-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobilepda"), $_smarty_tpl);?>
</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block p-0">
		<div class="bg-cover h-100 min-vh-100" style="background-image: url('<?php echo $_smarty_tpl->getValue('tpldir');?>
images/nli/<?php echo $_smarty_tpl->getValue('templatePrefs')['splashImage'];?>
');"></div>
	</div>
</div>
<?php }
}
