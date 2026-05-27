<?php
/* Smarty version 5.8.0, created on 2026-05-25 18:12:30
  from 'file:nli/signup.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14910e7964e9_22767661',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7ed59108290d24a0e825da09b33addc71e2c0e57' => 
    array (
      0 => 'nli/signup.tpl',
      1 => 1779728938,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/signup.page.open.tpl' => 1,
    'file:nli/signup.step-nav.tpl' => 6,
    'file:nli/signup.page.close.tpl' => 1,
  ),
))) {
function content_6a14910e7964e9_22767661 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->renderSubTemplate("file:nli/signup.page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
<h1 class="mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>
</h1>

<p class="text-secondary mb-4"><?php if ($_smarty_tpl->getValue('signupText')) {
echo $_smarty_tpl->getValue('signupText');
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signuptxt"), $_smarty_tpl);
}?> <?php if ($_smarty_tpl->getValue('code')) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signuptxt_code"), $_smarty_tpl);
}?></p>

<?php if (!(($tmp = $_smarty_tpl->getValue('nliCompactLayout') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?><form action="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup" method="post" id="signupForm"><?php }?>
		<input type="hidden" name="do" value="createAccount" />
		<input type="hidden" name="transPostVars" value="true" />
		<input type="hidden" name="codeID" value="<?php echo $_smarty_tpl->getValue('codeID');?>
" />
	
		<?php if ((true && ($_smarty_tpl->hasVariable('errorStep') && null !== ($_smarty_tpl->getValue('errorStep') ?? null)))) {?><div class="alert alert-danger" role="alert"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"error"), $_smarty_tpl);?>
:</strong> <?php echo $_smarty_tpl->getValue('errorInfo');?>
</div><?php }?>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"nli:signup.tpl:formStart"), $_smarty_tpl);?>


		<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['email_domain'] ?? null))) && $_smarty_tpl->getValue('_safePost')['email_domain'] != '') {
$_smarty_tpl->assign('signupEmailDomain', $_smarty_tpl->getValue('_safePost')['email_domain'], false, NULL);
} else {
$_smarty_tpl->assign('signupEmailDomain', $_smarty_tpl->getValue('domainListSignup')[0], false, NULL);
}?>

		<div class="accordion bm-signup-wizard" id="signup">

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"nli:signup.tpl:panelGroupStart"), $_smarty_tpl);?>


		<div class="accordion-item bm-signup-step bm-signup-step-active" data-signup-target="signup-account" data-signup-email="1">
			<div class="accordion-header bm-signup-step-head" id="signup-head-account">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">1</span>
				<i class="ti ti-user me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"wishaddressandpw"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-account" class="accordion-collapse collapse show" aria-labelledby="signup-head-account" data-bs-parent="#signup">
				<div class="accordion-body">
					<?php if ($_smarty_tpl->getValue('f_anrede') != "n") {?>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label<?php if ($_smarty_tpl->getValue('f_anrede') == "p") {?> required<?php }?>" for="salutation"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"salutation"), $_smarty_tpl);?>
</label>
								<select<?php if ($_smarty_tpl->getValue('f_anrede') == "p") {?> required="required"<?php }?> class="form-control" name="salutation" id="salutation">
									<option value="">&nbsp;</option>
									<option value="herr"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['salutation'] ?? null))) && $_smarty_tpl->getValue('_safePost')['salutation'] == 'herr') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mr"), $_smarty_tpl);?>
</option>
									<option value="frau"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['salutation'] ?? null))) && $_smarty_tpl->getValue('_safePost')['salutation'] == 'frau') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mrs"), $_smarty_tpl);?>
</option>
								</select>
							</div>
						</div>
					</div>
					<?php }?>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="firstname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"firstname"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" required="true" name="firstname" id="firstname" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['firstname'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['firstname'];
}?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="surname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"surname"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" required="true" name="surname" id="surname" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['surname'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['surname'];
}?>" />
							</div>
						</div>
					</div>

					<div class="row bm-signup-section-email">
						<div class="col-12">
							<div class="mb-3">
								<label class="form-label required" for="email_local"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"wishaddress"), $_smarty_tpl);?>
</label>
								<div class="input-group nli-domain-group">
									<span class="input-group-text text-muted"><i class="ti ti-mail" aria-hidden="true"></i></span>
									<input type="text" name="email_local" id="email_local" class="form-control" required="true" autocomplete="username" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['email_local'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['email_local'];
}?>" />
									<input type="hidden" name="email_domain" id="email_domain" data-bind="email-domain" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('signupEmailDomain')), $_smarty_tpl);?>
" />
									<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown" aria-expanded="false"><span data-bind="label">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('signupEmailDomain')), $_smarty_tpl);?>
</span></button>
									<ul class="dropdown-menu dropdown-menu-end domainMenu">
										<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('domainListSignup'), 'domain', false, 'key');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('domain')->value) {
$foreach0DoElse = false;
?><li<?php if ((( !true || empty($_smarty_tpl->getValue('_safePost')['email_domain'])) && $_smarty_tpl->getValue('key') == 0) || ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['email_domain'] ?? null))) && $_smarty_tpl->getValue('_safePost')['email_domain'] == $_smarty_tpl->getValue('domain'))) {?> class="active"<?php }?>><a class="dropdown-item" href="#">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domain')), $_smarty_tpl);?>
</a></li><?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
									</ul>
								</div>
							</div>
							<div class="alert alert-info d-none mb-3" role="alert" id="email_alert"></div>
							<?php if ($_smarty_tpl->getValue('signupSuggestions')) {?>
							<div id="email_suggestions_wrap" class="d-none mt-3">
								<div id="email_suggestions_content" class="bm-email-suggestions"></div>
							</div>
							<?php }?>
						</div>
					</div>

					<div class="row bm-signup-section-password g-3">
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="form-label required" for="pass1">
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>

									<span class="form-label-description"><?php echo $_smarty_tpl->getValue('minPassText');?>
</span>
								</label>
								<input type="password" data-min-length="<?php echo $_smarty_tpl->getValue('minPassLength');?>
" class="form-control" required="true" autocomplete="new-password" name="pass1" id="pass1"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['pass1'] ?? null)))) {?> value="<?php echo $_smarty_tpl->getValue('_safePost')['pass1'];?>
"<?php }?> />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-0">
								<label class="form-label required" for="pass2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"repeat"), $_smarty_tpl);?>
</label>
								<input type="password" class="form-control" required="true" autocomplete="new-password" name="pass2" id="pass2"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['pass2'] ?? null)))) {?> value="<?php echo $_smarty_tpl->getValue('_safePost')['pass2'];?>
"<?php }?> />
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>

		<?php if ($_smarty_tpl->getValue('f_strasse') != "n") {?>
		<div class="accordion-item bm-signup-step" data-signup-target="signup-address">
			<div class="accordion-header bm-signup-step-head" id="signup-head-address">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">2</span>
				<i class="ti ti-home me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"address"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-address" class="accordion-collapse collapse" aria-labelledby="signup-head-address" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required<?php }?>" for="street"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"street"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required="true"<?php }?> name="street" id="street" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['street'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['street'];
}?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required<?php }?>" for="no"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nr"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required="true"<?php }?> name="no" id="no" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['no'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['no'];
}?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required<?php }?>" for="zip"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"zip"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required="true"<?php }?> name="zip" id="zip" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['zip'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['zip'];
}?>" />
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required<?php }?>" for="city"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"city"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required="true"<?php }?> name="city" id="city" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['city'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['city'];
}?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_strasse') && null !== ($_smarty_tpl->getValue('f_strasse') ?? null))) && $_smarty_tpl->getValue('f_strasse') == "p") {?> required<?php }?>" for="country"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"country"), $_smarty_tpl);?>
</label>
								<select class="form-control" name="country" id="country">
									<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('countryList'), 'country', false, 'id');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('id')->value => $_smarty_tpl->getVariable('country')->value) {
$foreach1DoElse = false;
?>
									<option value="<?php echo $_smarty_tpl->getValue('id');?>
"<?php if ((!$_smarty_tpl->getValue('_safePost')['country'] && $_smarty_tpl->getValue('id') == $_smarty_tpl->getValue('defaultCountry')) || ($_smarty_tpl->getValue('_safePost')['country'] == $_smarty_tpl->getValue('id'))) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('country');?>
</option>
									<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
								</select>
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('f_telefon') != 'n' || $_smarty_tpl->getValue('f_fax') != 'n' || $_smarty_tpl->getValue('f_mail2sms_nummer') != 'n' || $_smarty_tpl->getValue('f_alternativ') != 'n') {?>
		<div class="accordion-item bm-signup-step" data-signup-target="signup-contact">
			<div class="accordion-header bm-signup-step-head" id="signup-head-contact">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">3</span>
				<i class="ti ti-phone me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contactinfo"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-contact" class="accordion-collapse collapse" aria-labelledby="signup-head-contact" data-bs-parent="#signup">
				<div class="accordion-body">
					<?php if ($_smarty_tpl->getValue('f_telefon') != 'n' || $_smarty_tpl->getValue('f_fax') != 'n' || $_smarty_tpl->getValue('f_mail2sms_nummer') != 'n') {?>
					<div class="row">
						<?php if ($_smarty_tpl->getValue('f_telefon') != 'n') {?>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label<?php if ((true && ($_smarty_tpl->hasVariable('f_telefon') && null !== ($_smarty_tpl->getValue('f_telefon') ?? null))) && $_smarty_tpl->getValue('f_telefon') == 'p') {?> required<?php }?>" for="phone"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"phone"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_telefon') && null !== ($_smarty_tpl->getValue('f_telefon') ?? null))) && $_smarty_tpl->getValue('f_telefon') == 'p') {?> required="true"<?php }?> name="phone" id="phone" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['phone'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['phone'];
}?>" />
							</div>
						</div>
						<?php }?>
						<?php if ((true && ($_smarty_tpl->hasVariable('f_fax') && null !== ($_smarty_tpl->getValue('f_fax') ?? null))) && $_smarty_tpl->getValue('f_fax') != 'n') {?>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label<?php if ($_smarty_tpl->getValue('f_fax') == 'p') {?> required<?php }?>" for="fax"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fax"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ((true && ($_smarty_tpl->hasVariable('f_fax') && null !== ($_smarty_tpl->getValue('f_fax') ?? null))) && $_smarty_tpl->getValue('f_fax') == 'p') {?> required="true"<?php }?> name="fax" id="fax" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['fax'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['fax'];
}?>" />
							</div>
						</div>
						<?php }?>
						<?php if ((true && ($_smarty_tpl->hasVariable('f_mail2sms_nummer') && null !== ($_smarty_tpl->getValue('f_mail2sms_nummer') ?? null))) && $_smarty_tpl->getValue('f_mail2sms_nummer') != 'n') {?>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label<?php if ($_smarty_tpl->getValue('f_mail2sms_nummer') == 'p') {?> required<?php }?>" for="mail2sms_nummer"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobile"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control"<?php if ($_smarty_tpl->getValue('f_mail2sms_nummer') == 'p') {?> required="true"<?php }?> name="mail2sms_nummer" id="mail2sms_nummer" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['mail2sms_nummer'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['mail2sms_nummer'];
}?>" />
							</div>
						</div>
						<?php }?>
					</div>
					<?php }?>

					<?php if ((true && ($_smarty_tpl->hasVariable('f_alternativ') && null !== ($_smarty_tpl->getValue('f_alternativ') ?? null))) && $_smarty_tpl->getValue('f_alternativ') != 'n') {?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label<?php if ($_smarty_tpl->getValue('f_alternativ') == 'p') {?> required<?php }?>" for="altmail"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"altmail2"), $_smarty_tpl);?>
</label>
								<input type="email" class="form-control"<?php if ($_smarty_tpl->getValue('f_alternativ') == 'p') {?> required="true"<?php }?> name="altmail" id="altmail" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['altmail'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['altmail'];
}?>" />
							</div>
						</div>
					</div>
					<?php }?>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('profilfelder')) {?>
		<div class="accordion-item bm-signup-step" data-signup-target="signup-misc">
			<div class="accordion-header bm-signup-step-head" id="signup-head-misc">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">4</span>
				<i class="ti ti-list-details me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"misc"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-misc" class="accordion-collapse collapse" aria-labelledby="signup-head-misc" data-bs-parent="#signup">
				<div class="accordion-body">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('profilfelder'), 'feld');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('feld')->value) {
$foreach2DoElse = false;
?>
					<?php $_smarty_tpl->assign('fieldID', $_smarty_tpl->getValue('feld')['id'], false, NULL);?>
					<?php $_smarty_tpl->assign('fieldName', "field_".((string)$_smarty_tpl->getValue('fieldID')), false, NULL);?>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<?php if ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] != 2) {?><label class="form-label<?php if ($_smarty_tpl->getValue('feld')['pflicht']) {?> required<?php }?>" for="<?php echo $_smarty_tpl->getValue('fieldName');?>
"><?php echo $_smarty_tpl->getValue('feld')['feld'];?>
</label><?php }?>

								<?php if ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] == 1) {?>
								<input<?php if ($_smarty_tpl->getValue('feld')['pflicht']) {?> required="true"<?php }?> class="form-control" name="<?php echo $_smarty_tpl->getValue('fieldName');?>
" id="<?php echo $_smarty_tpl->getValue('fieldName');?>
" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')];
}?>" type="text" />
								<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] == 2) {?>
								<label class="control-label">
									<input type="checkbox" name="<?php echo $_smarty_tpl->getValue('fieldName');?>
" id="<?php echo $_smarty_tpl->getValue('fieldName');?>
"<?php if ($_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')]) {?> checked="checked"<?php }?> />
									<?php echo $_smarty_tpl->getValue('feld')['feld'];?>

								</label>
								<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] == 4) {?>
								<select class="form-control" name="<?php echo $_smarty_tpl->getValue('fieldName');?>
" id="<?php echo $_smarty_tpl->getValue('fieldName');?>
">
									<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('feld')['extra'], 'item');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach3DoElse = false;
?>
									<option value="<?php echo $_smarty_tpl->getValue('item');?>
"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')] ?? null))) && $_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')] == $_smarty_tpl->getValue('item')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('item');?>
</option>
									<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
								</select>
								<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] == 8) {?>
									<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('feld')['extra'], 'item');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach4DoElse = false;
?>
									<div class="radio">
										<label>
											<input type="radio" id="<?php echo $_smarty_tpl->getValue('fieldName');?>
_<?php echo $_smarty_tpl->getValue('item');?>
" name="<?php echo $_smarty_tpl->getValue('fieldName');?>
" value="<?php echo $_smarty_tpl->getValue('item');?>
"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')] ?? null))) && $_smarty_tpl->getValue('_safePost')[$_smarty_tpl->getValue('fieldName')] == $_smarty_tpl->getValue('item')) {?> checked="checked"<?php }?> />
											<?php echo $_smarty_tpl->getValue('item');?>

										</label> 
									</div>
									<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
								<?php } elseif ((true && (true && null !== ($_smarty_tpl->getValue('feld')['typ'] ?? null))) && $_smarty_tpl->getValue('feld')['typ'] == 32) {?>
									<div><?php if ($_smarty_tpl->getValue('feld')['pflicht']) {
$_smarty_tpl->assign('all_extra', 'required="true"', false, NULL);
} else {
$_smarty_tpl->assign('all_extra', '', false, NULL);
}
if ($_smarty_tpl->getValue('dateFields')[$_smarty_tpl->getValue('fieldName')]) {?>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('time'=>$_smarty_tpl->getValue('dateFields')[$_smarty_tpl->getValue('fieldName')],'year_empty'=>"---",'day_empty'=>"---",'month_empty'=>"---",'start_year'=>"-120",'end_year'=>"+0",'prefix'=>((string)$_smarty_tpl->getValue('fieldName')),'field_order'=>"DMY",'class'=>"form-control",'style'=>"width:auto;display:inline-block;",'all_extra'=>((string)$_smarty_tpl->getValue('all_extra'))), $_smarty_tpl);?>

									<?php } else { ?>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('time'=>"---",'year_empty'=>"---",'day_empty'=>"---",'month_empty'=>"---",'start_year'=>"-120",'end_year'=>"+0",'prefix'=>((string)$_smarty_tpl->getValue('fieldName')),'field_order'=>"DMY",'class'=>"form-control",'style'=>"width:auto;display:inline-block;",'all_extra'=>((string)$_smarty_tpl->getValue('all_extra'))), $_smarty_tpl);?>

									<?php }?></div>
								<?php }?>
							</div>
						</div>
					</div>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('code')) {?>
		<div class="accordion-item bm-signup-step" data-signup-target="signup-code">
			<div class="accordion-header bm-signup-step-head" id="signup-head-code">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">5</span>
				<i class="ti ti-ticket me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"code"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-code" class="accordion-collapse collapse" aria-labelledby="signup-head-code" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label" for="code"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"code"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['code'] ?? null)))) {
echo $_smarty_tpl->getValue('_safePost')['code'];
}?>" name="code" id="code" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signuptxt_code"), $_smarty_tpl);?>
" />
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>
		<?php }?>

		<?php if ((true && ($_smarty_tpl->hasVariable('f_safecode') && null !== ($_smarty_tpl->getValue('f_safecode') ?? null))) && $_smarty_tpl->getValue('f_safecode') != 'n') {?>
		<div class="accordion-item bm-signup-step" data-signup-target="signup-finish">
			<div class="accordion-header bm-signup-step-head" id="signup-head-finish">
				<span class="badge bg-primary text-primary-fg bm-signup-step-badge">6</span>
				<i class="ti ti-shield-check me-2 text-primary" aria-hidden="true"></i>
				<span class="bm-signup-step-title fw-semibold"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"completesignup"), $_smarty_tpl);?>
</span>
				<i class="ti ti-check bm-signup-step-check ms-auto text-success" aria-hidden="true"></i>
			</div>
			<div id="signup-finish" class="accordion-collapse collapse" aria-labelledby="signup-head-finish" data-bs-parent="#signup">
				<div class="accordion-body">
					<div class="row">
						<?php if ($_smarty_tpl->getValue('captchaInfo')['hasOwnInput']) {?>
						<div class="col-md-12">
							<div class="form-group" id="captchaContainer">
								<label class="form-label required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>
</label>
								<?php echo $_smarty_tpl->getValue('captchaHTML');?>

							</div>
						</div>
						<?php } else { ?>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label required" for="safecode"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" required="true" name="safecode" id="safecode" />
							</div>
						</div>
						<div class="col-md-6" id="captchaContainer">
							<?php echo $_smarty_tpl->getValue('captchaHTML');?>

						</div>
						<?php }?>
					</div>
				</div>
				<div class="card-footer bm-signup-step-footer py-3">
					<?php $_smarty_tpl->renderSubTemplate("file:nli/signup.step-nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>
			</div>
		</div>
		<?php }?>

		</div>

<?php if (!(($tmp = $_smarty_tpl->getValue('nliCompactLayout') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>
		<div id="signupFinishArea" class="bm-signup-finish mt-4">
			<div class="text-secondary small mb-3">
				<i class="ti ti-info-circle me-1" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"iprecord"), $_smarty_tpl);?>

			</div>
			<p id="signupFinishHint" class="text-secondary small d-none mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"completesignup"), $_smarty_tpl);?>
</p>
			<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
				<label class="form-check mb-0">
					<input type="checkbox" class="form-check-input" name="tos" value="true"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('_safePost')['tos'] ?? null))) && $_smarty_tpl->getValue('_safePost')['tos'] == 'true') {?> checked="checked"<?php }?> />
					<span class="form-check-label">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"accepttos"), $_smarty_tpl);?>

						<a href="#" data-bs-toggle="modal" data-bs-target="#tosModal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</a>
					</span>
				</label>
				<button type="submit" id="signupSubmit" class="btn btn-success">
					<i class="ti ti-check me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"submit"), $_smarty_tpl);?>

				</button>
			</div>
		</div>
	</form>
<?php }
$_smarty_tpl->renderSubTemplate("file:nli/signup.page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<div class="modal modal-blur fade" id="tosModal" tabindex="-1" role="dialog" aria-labelledby="tosLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="visually-hidden"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
</span></button>
				<h4 class="modal-title" id="tosLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</h4>
			</div>
			<div class="modal-body" style="max-height:400px;overflow-y:auto;">
				<?php echo $_smarty_tpl->getValue('tos_html');?>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>
</div>

<?php if ($_smarty_tpl->getValue('signupSuggestions')) {?><div class="modal modal-blur fade" id="suggestionsModal" tabindex="-1" aria-labelledby="suggestionsLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="suggestionsLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"suggestions"), $_smarty_tpl);?>
</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
"></button>
			</div>
			<div class="modal-body" id="suggestionsBody"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nothanks"), $_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>
</div><?php }?>

<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/nli.signup.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/nli.signup.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
<!--
	$(document).ready(function() {
	<?php if ((true && ($_smarty_tpl->hasVariable('errorStep') && null !== ($_smarty_tpl->getValue('errorStep') ?? null)))) {?>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('invalidFields'), 'field');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('field')->value) {
$foreach5DoElse = false;
?>
	markFieldAsInvalid('<?php echo $_smarty_tpl->getValue('field');?>
');
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	checkEMailAvailability();
	var $errField = $('#signupForm .is-invalid').first();
	if($errField.length && $errField.closest('.bm-signup-step').length)
		bmSignupShowStep($errField.closest('.bm-signup-step'));
	<?php } else { ?>
	setTimeout(function() {
		if($('#salutation').length) $('#salutation').focus();
		else if($('#firstname').length) $('#firstname').focus();
	}, 150);
	<?php }?>
	});
//-->
<?php echo '</script'; ?>
>
<?php }
}
