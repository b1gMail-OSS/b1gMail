<?php
/* Smarty version 5.8.0, created on 2026-05-25 13:16:23
  from 'file:nli/login.form.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a144ba77db4d8_85413435',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e81cf3286707cdd28b515527db79f43235122332' => 
    array (
      0 => 'nli/login.form.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a144ba77db4d8_85413435 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><form action="<?php if ($_smarty_tpl->getValue('ssl_login_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=login" method="post" id="loginFormMain" autocomplete="on">
	<input type="hidden" name="do" value="login" />
	<input type="hidden" name="timezone" value="<?php echo $_smarty_tpl->getValue('timezone');?>
" />

	<div class="alert alert-danger" style="display:none;" role="alert"></div>

	<?php if ($_smarty_tpl->getValue('domain_combobox')) {?>
	<div class="mb-3">
		<label class="form-label" for="email_local"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
		<div class="input-group nli-domain-group">
			<input type="text" name="email_local" id="email_local" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" autocomplete="username" />
			<input type="hidden" name="email_domain" data-bind="email-domain" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
" />
			<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown" aria-expanded="false">
				<span data-bind="label">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
</span>
			</button>
			<ul class="dropdown-menu dropdown-menu-end domainMenu">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('domainList'), 'domain', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('domain')->value) {
$foreach0DoElse = false;
?>
				<li<?php if ($_smarty_tpl->getValue('key') == 0) {?> class="active"<?php }?>><a class="dropdown-item" href="#">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domain')), $_smarty_tpl);?>
</a></li>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</ul>
		</div>
	</div>
	<?php } else { ?>
	<div class="mb-3">
		<label class="form-label" for="email_full"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
		<input type="email" name="email_full" id="email_full" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" autocomplete="username" />
	</div>
	<?php }?>

	<div class="mb-2">
		<label class="form-label" for="password">
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>

			<span class="form-label-description">
				<a href="#" data-bs-toggle="modal" data-bs-target="#lostPW"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"lostpw"), $_smarty_tpl);?>
?</a>
			</span>
		</label>
		<div class="input-group input-group-flat">
			<input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>
" required="true" autocomplete="current-password" />
			<span class="input-group-text">
				<a href="#" class="link-secondary" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>
" data-nli-toggle-password="password" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>
">
					<i class="ti ti-eye" aria-hidden="true"></i>
				</a>
			</span>
		</div>
	</div>

	<div class="mb-2">
		<label class="form-check">
			<input type="checkbox" class="form-check-input" name="savelogin" id="savelogin" />
			<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"savelogin"), $_smarty_tpl);?>
</span>
		</label>
	</div>

	<?php if ($_smarty_tpl->getValue('ssl_login_option')) {?>
	<div class="mb-3">
		<label class="form-check">
			<input type="checkbox" class="form-check-input" id="ssl"<?php if ($_smarty_tpl->getValue('ssl_login_enable')) {?> checked="checked"<?php }?> onchange="updateFormSSL(this)" onclick="updateFormSSL(this)" />
			<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ssl"), $_smarty_tpl);?>
</span>
		</label>
	</div>
	<?php }?>

	<div class="form-footer">
		<button type="submit" class="btn btn-primary w-100"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"login"), $_smarty_tpl);?>
</button>
	</div>
</form>
<?php }
}
