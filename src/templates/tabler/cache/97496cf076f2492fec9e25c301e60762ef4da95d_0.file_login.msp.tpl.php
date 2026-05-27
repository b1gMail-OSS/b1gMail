<?php
/* Smarty version 5.8.0, created on 2026-05-25 13:16:23
  from 'file:nli/login.msp.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a144ba77c6be0_66066095',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97496cf076f2492fec9e25c301e60762ef4da95d' => 
    array (
      0 => 'nli/login.msp.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/login.brand.tpl' => 1,
    'file:nli/msp.tabs.tpl' => 2,
    'file:nli/login.welcomeback.tpl' => 1,
    'file:nli/login.form.tpl' => 1,
    'file:nli/msp.footer.tpl' => 1,
  ),
))) {
function content_6a144ba77c6be0_66066095 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><div class="page-body nli-msp-layout">
	<div class="container-xl py-4">
		<div class="text-center mb-4">
			<?php $_smarty_tpl->renderSubTemplate("file:nli/login.brand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		</div>

		<?php if ($_smarty_tpl->getValue('welcomeBack')) {?>
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card card-md">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.tabs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					<div class="card-body">
						<?php $_smarty_tpl->renderSubTemplate("file:nli/login.welcomeback.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<div class="row g-4 align-items-stretch">
			<div class="col-lg-7">
				<div class="card h-100">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.tabs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					<div class="card-body">
						<h1 class="h2 mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"welcome"), $_smarty_tpl);?>
</h1>
						<p class="text-secondary mb-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signuptxt"), $_smarty_tpl);?>
</p>

						<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
						<div class="card bg-primary-lt border-0 mb-4">
							<div class="card-body">
								<h3 class="h4 mb-2">
									<i class="ti ti-user-plus me-1 text-primary" aria-hidden="true"></i>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

								</h3>
								<div class="text-secondary mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notmembertxt"), $_smarty_tpl);?>
</div>
								<a href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup" class="btn btn-primary">
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>

								</a>
							</div>
						</div>
						<?php }?>

						<div class="mt-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('banner')->handle(array(), $_smarty_tpl);?>
</div>
					</div>
				</div>
			</div>

			<div class="col-lg-5">
				<div class="card card-md h-100">
					<div class="card-body">
						<h2 class="h2 text-center mb-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"login"), $_smarty_tpl);?>
</h2>
						<?php $_smarty_tpl->renderSubTemplate("file:nli/login.form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
					<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
					<div class="card-footer text-center text-secondary">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notmember"), $_smarty_tpl);?>
?
						<a href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>
</a>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
		<?php }?>

		<?php $_smarty_tpl->renderSubTemplate("file:nli/msp.footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	</div>
</div>
<?php }
}
