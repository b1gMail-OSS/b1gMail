<?php
/* Smarty version 5.8.0, created on 2026-05-26 15:55:01
  from 'file:nli/index.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15c2557fdf90_32906511',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0628450aaffa5d4e41462e04e5e27c98baee7b5f' => 
    array (
      0 => 'nli/index.tpl',
      1 => 1779809686,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/layout.vars.tpl' => 1,
    'file:nli/page.open.tpl' => 1,
    'file:nli/page.close.tpl' => 1,
  ),
))) {
function content_6a15c2557fdf90_32906511 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><!doctype html>
<html lang="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"langCode"), $_smarty_tpl);?>
">

<head>
	<meta charset="<?php echo $_smarty_tpl->getValue('charset');?>
" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<?php if ((true && ($_smarty_tpl->hasVariable('robotsNoIndex') && null !== ($_smarty_tpl->getValue('robotsNoIndex') ?? null)))) {?><meta name="robots" content="noindex" /><?php }?>

	<title><?php echo $_smarty_tpl->getValue('service_title');
if ((true && ($_smarty_tpl->hasVariable('pageTitle') && null !== ($_smarty_tpl->getValue('pageTitle') ?? null)))) {?> - <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('pageTitle')), $_smarty_tpl);
}?></title>

	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />

	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/tabler.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/tabler.min.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/tabler-icons.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/tabler-icons.min.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
css/inter.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"css/inter.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/bs3-compat.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/bs3-compat.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/tabler-custom.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/tabler-custom.css"), $_smarty_tpl);?>
" />
	<link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/notloggedin.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/notloggedin.css"), $_smarty_tpl);?>
" />

	<?php echo '<script'; ?>
 type="text/javascript">
	<!--
		var tplDir = '<?php echo $_smarty_tpl->getValue('tpldir');?>
', sslURL = '<?php echo $_smarty_tpl->getValue('ssl_url');?>
', serverTZ = <?php echo $_smarty_tpl->getValue('serverTZ');?>
;
	//-->
	<?php echo '</script'; ?>
>

	<?php echo '<script'; ?>
 src="clientlang.php" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="clientlib/jquery/jquery-3.7.1.min.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/tabler.min.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/tabler.min.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript">
	<!--
		if(typeof bootstrap === 'undefined' && typeof tabler !== 'undefined' && tabler.bootstrap)
			window.bootstrap = tabler.bootstrap;
	//-->
	<?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/nli.main.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/nli.main.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"nli:index.tpl:head"), $_smarty_tpl);?>

</head>

<?php $_smarty_tpl->renderSubTemplate("file:nli/layout.vars.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 2, $_smarty_current_dir);
?>
<body class="nli-body<?php if ($_smarty_tpl->getValue('nliCompactLayout') && $_smarty_tpl->getValue('page') != 'nli/login.tpl') {?> nli-msp-layout<?php }
if ($_smarty_tpl->getValue('page') == 'nli/login.tpl' && $_smarty_tpl->getValue('nliStyle') != 'msp') {?> nli-login-layout nli-login-<?php echo (($tmp = $_smarty_tpl->getValue('nliStyle') ?? null)===null||$tmp==='' ? 'cover' ?? null : $tmp);
if ($_smarty_tpl->getValue('nliStyle') == 'cover' || $_smarty_tpl->getValue('nliStyle') == 'minimal') {?> d-flex flex-column bg-white<?php }
}?>">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"nli:index.tpl:beforeContent"), $_smarty_tpl);?>


	<?php if (!$_smarty_tpl->getValue('nliCompactLayout') && $_smarty_tpl->getValue('page') != 'nli/login.tpl') {?>
	<header class="navbar navbar-expand-md navbar-dark bg-dark d-print-none sticky-top">
		<div class="container-xl">
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nli-navbar-menu" aria-controls="nli-navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a class="navbar-brand" href="index.php">
				<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/logo.png" border="0" alt="" class="navbar-brand-image me-2" style="height:24px;" />
				<?php echo $_smarty_tpl->getValue('service_title');?>

			</a>
			<div class="collapse navbar-collapse" id="nli-navbar-menu">
				<ul class="navbar-nav">
					<li class="nav-item<?php if ((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] == 'login') {?> active<?php }?>">
						<a class="nav-link" href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"home"), $_smarty_tpl);?>
</a>
					</li>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach0DoElse = false;
if ((true && (true && null !== ($_smarty_tpl->getValue('item')['top'] ?? null))) && $_smarty_tpl->getValue('item')['after'] == 'login') {?>
					<li class="nav-item<?php if ($_smarty_tpl->getValue('item')['active']) {?> active<?php }?>"><a class="nav-link" href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a></li>
				<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					<?php if ($_smarty_tpl->getValue('_regEnabled') || (!$_smarty_tpl->getValue('templatePrefs')['hideSignup'])) {?>
					<li class="nav-item<?php if ((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] == 'signup') {?> active<?php }?>">
						<a class="nav-link" href="<?php if ($_smarty_tpl->getValue('ssl_signup_enable')) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=signup"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"signup"), $_smarty_tpl);?>
</a>
					</li>
					<?php }?>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach1DoElse = false;
if ((true && (true && null !== ($_smarty_tpl->getValue('item')['top'] ?? null))) && $_smarty_tpl->getValue('item')['after'] == 'signup') {?>
					<li class="nav-item<?php if ($_smarty_tpl->getValue('item')['active']) {?> active<?php }?>"><a class="nav-link" href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a></li>
				<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					<li class="nav-item<?php if ((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] == 'faq') {?> active<?php }?>">
						<a class="nav-link" href="index.php?action=faq"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>
</a>
					</li>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach2DoElse = false;
if ((true && (true && null !== ($_smarty_tpl->getValue('item')['top'] ?? null))) && $_smarty_tpl->getValue('item')['after'] == 'faq') {?>
					<li class="nav-item<?php if ($_smarty_tpl->getValue('item')['active']) {?> active<?php }?>"><a class="nav-link" href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a></li>
				<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					<li class="nav-item<?php if ((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] == 'tos') {?> active<?php }?>">
						<a class="nav-link" href="index.php?action=tos"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"tos"), $_smarty_tpl);?>
</a>
					</li>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach3DoElse = false;
if ((true && (true && null !== ($_smarty_tpl->getValue('item')['top'] ?? null))) && (!$_smarty_tpl->getValue('item')['after'] || $_smarty_tpl->getValue('item')['after'] == 'tos')) {?>
					<li class="nav-item<?php if ($_smarty_tpl->getValue('item')['active']) {?> active<?php }?>"><a class="nav-link" href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a></li>
				<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					<li class="nav-item<?php if ((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] == 'imprint') {?> active<?php }?>">
						<a class="nav-link" href="index.php?action=imprint"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>
</a>
					</li>
				</ul>
				<form action="<?php if ($_smarty_tpl->getValue('ssl_login_enable') || ($_smarty_tpl->getValue('welcomeBack') && $_COOKIE['bm_savedSSL'])) {
echo $_smarty_tpl->getValue('ssl_url');
}?>index.php?action=login" method="post" id="loginFormPopover" class="ms-md-auto d-flex align-items-center">
					<input type="hidden" name="do" value="login" />
					<input type="hidden" name="timezone" value="<?php echo $_smarty_tpl->getValue('timezone');?>
" />

					<ul class="navbar-nav flex-row gap-2">
						<?php if (((true && (true && null !== ($_REQUEST['action'] ?? null))) && $_REQUEST['action'] != 'login') || $_smarty_tpl->getValue('welcomeBack')) {?>
						<li class="nav-item login-li<?php if (!$_smarty_tpl->getValue('welcomeBack')) {?> d-none d-md-block<?php }?>">
							<?php if ($_smarty_tpl->getValue('welcomeBack')) {?>
							<input type="hidden" name="email_full" value="<?php echo $_COOKIE['bm_savedUser'];?>
" />
							<input type="hidden" name="password" value="" />
							<input type="hidden" name="savelogin" value="true" />
							<?php if ($_COOKIE['bm_savedSSL']) {?><input type="hidden" name="ssl" value="true" /><?php }?>

							<div class="btn-group">
								<button type="submit" class="btn btn-primary">
									<i class="ti ti-user me-1"></i>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_COOKIE['bm_savedUser'],'cut'=>18), $_smarty_tpl);?>

								</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
									<span class="visually-hidden">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-end">
									<li><a class="dropdown-item" href="index.php?action=forgetCookie"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"logout"), $_smarty_tpl);?>
</a></li>
								</ul>
							</div>
							<?php } else { ?>
							<div class="dropdown">
								<button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"login"), $_smarty_tpl);?>

								</button>
								<div class="dropdown-menu dropdown-menu-end p-3 login-li" id="loginPopoverDropdown">
									<div id="loginPopover">
										<div class="alert alert-danger" style="display:none;"></div>

										<div class="mb-3">
											<div class="input-group">
												<span class="input-group-text"><i class="ti ti-user"></i></span>
											<?php if ($_smarty_tpl->getValue('domain_combobox')) {?>
												<label class="visually-hidden" for="email_local_p"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
												<input type="text" name="email_local" id="email_local_p" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" />
												<input type="hidden" name="email_domain" data-bind="email-domain" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
" />
												<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"><span data-bind="label">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
</span></button>
												<ul class="dropdown-menu dropdown-menu-end domainMenu">
													<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('domainList'), 'domain', false, '_key');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_key')->value => $_smarty_tpl->getVariable('domain')->value) {
$foreach4DoElse = false;
?><li<?php if ($_smarty_tpl->getValue('_key') == 0) {?> class="active"<?php }?>><a class="dropdown-item" href="#">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domain')), $_smarty_tpl);?>
</a></li><?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
												</ul>
											<?php } else { ?>
												<label class="visually-hidden" for="email_full_p"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
												<input type="email" name="email_full" id="email_full_p" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" />
											<?php }?>
											</div>
										</div>
										<div class="mb-3">
											<div class="input-group">
												<span class="input-group-text"><i class="ti ti-lock"></i></span>
												<label class="visually-hidden" for="password_p"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>
</label>
												<input type="password" name="password" id="password_p" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"password"), $_smarty_tpl);?>
" required="true" />
											</div>
										</div>
										<label class="form-check mb-2">
											<input type="checkbox" class="form-check-input" name="savelogin" id="savelogin_p" />
											<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"savelogin"), $_smarty_tpl);?>
</span>
										</label>
										<?php if ($_smarty_tpl->getValue('ssl_login_option')) {?>
										<label class="form-check mb-3">
											<input type="checkbox" class="form-check-input" id="ssl_p"<?php if ($_smarty_tpl->getValue('ssl_login_enable')) {?> checked="checked"<?php }?> onchange="updateFormSSL(this)" onclick="updateFormSSL(this)" />
											<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ssl"), $_smarty_tpl);?>
</span>
										</label>
										<?php }?>
										<button type="submit" class="btn btn-success w-100"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"login"), $_smarty_tpl);?>
</button>
										<div class="login-lostpw mt-2">
											<a href="#" data-bs-toggle="modal" data-bs-target="#lostPW"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"lostpw"), $_smarty_tpl);?>
?</a>
										</div>
									</div>
								</div>
							</div>
							<?php }?>
						</li>
						<?php }?>

						<li class="nav-item dropdown">
							<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('languageList'), 'langInfo', false, 'langKey');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('langKey')->value => $_smarty_tpl->getVariable('langInfo')->value) {
$foreach5DoElse = false;
if ($_smarty_tpl->getValue('langInfo')['active']) {
echo $_smarty_tpl->getValue('langInfo')['title'];
}
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?></a>
							<ul class="dropdown-menu dropdown-menu-end">
								<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('languageList'), 'langInfo', false, 'langKey');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('langKey')->value => $_smarty_tpl->getVariable('langInfo')->value) {
$foreach6DoElse = false;
?>
								<li<?php if ($_smarty_tpl->getValue('langInfo')['active']) {?> class="active"<?php }?>><a class="dropdown-item" href="index.php?action=switchLanguage&amp;lang=<?php echo $_smarty_tpl->getValue('langKey');
if (!( !true || empty($_GET['action']))) {?>&amp;target=<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_GET['action']), $_smarty_tpl);
}?>"><?php echo $_smarty_tpl->getValue('langInfo')['title'];?>
</a></li>
								<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</ul>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</header>
	<?php }?>

	<div class="modal modal-blur fade" id="lostPW" tabindex="-1" aria-labelledby="lostPWLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<form action="index.php?action=lostPassword" method="post">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lostPWLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"lostpw"), $_smarty_tpl);?>
</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cancel"), $_smarty_tpl);?>
"></button>
				</div>
				<div class="modal-body">
					<p class="text-muted mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"lostpw"), $_smarty_tpl);?>
</p>
					<?php if ($_smarty_tpl->getValue('domain_combobox')) {?>
					<div class="mb-3">
						<label class="form-label" for="email_local_lpw"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
						<div class="input-group nli-domain-group">
							<span class="input-group-text text-muted"><i class="ti ti-mail"></i></span>
							<input type="text" name="email_local" id="email_local_lpw" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" />
							<input type="hidden" name="email_domain" data-bind="email-domain" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
" />
							<button type="button" class="btn btn-outline-secondary dropdown-toggle nli-domain-btn" data-bs-toggle="dropdown"><span data-bind="label">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domainList')[0]), $_smarty_tpl);?>
</span></button>
							<ul class="dropdown-menu dropdown-menu-end domainMenu">
								<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('domainList'), 'domain', false, '_key');
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_key')->value => $_smarty_tpl->getVariable('domain')->value) {
$foreach7DoElse = false;
?><li<?php if ($_smarty_tpl->getValue('_key') == 0) {?> class="active"<?php }?>><a class="dropdown-item" href="#">@<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('domain')->handle(array('value'=>$_smarty_tpl->getValue('domain')), $_smarty_tpl);?>
</a></li><?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</ul>
						</div>
					</div>
					<?php } else { ?>
					<div class="mb-3">
						<label class="form-label" for="email_full_lpw"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
						<div class="input-group">
							<span class="input-group-text text-muted"><i class="ti ti-mail"></i></span>
							<input type="email" name="email_full" id="email_full_lpw" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
" required="true" />
						</div>
					</div>
					<?php }?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cancel"), $_smarty_tpl);?>
</button>
					<button type="submit" class="btn btn-primary ms-auto"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"requestpw"), $_smarty_tpl);?>
</button>
				</div>
			</div>
			</form>
		</div>
	</div>

	<?php $_smarty_tpl->assign('_nliPagePath', (($tmp = $_smarty_tpl->getValue('page') ?? null)===null||$tmp==='' ? '' ?? null : $tmp), false, NULL);?>
	<?php $_smarty_tpl->assign('_nliPageIsPlugin', ($_smarty_tpl->getSmarty()->getModifierCallback('replace')($_smarty_tpl->getValue('_nliPagePath'),'plugins/templates','') != $_smarty_tpl->getValue('_nliPagePath')), false, NULL);?>
	<?php if ($_smarty_tpl->getValue('nliCompactLayout') && ((($tmp = $_smarty_tpl->getValue('nliWrapPlugin') ?? null)===null||$tmp==='' ? true ?? null : $tmp)) && $_smarty_tpl->getValue('_nliPageIsPlugin')) {?>
				<?php $_smarty_tpl->renderSubTemplate("file:nli/page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		<?php $_smarty_tpl->renderSubTemplate(((string)$_smarty_tpl->getValue('page')), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
		<?php $_smarty_tpl->renderSubTemplate("file:nli/page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	<?php } else { ?>
		<?php $_smarty_tpl->renderSubTemplate(((string)$_smarty_tpl->getValue('page')), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
	<?php }?>

	<?php if (!$_smarty_tpl->getValue('nliCompactLayout') && $_smarty_tpl->getValue('page') != 'nli/login.tpl') {?>
	<footer class="footer footer-transparent d-print-none border-top">
		<div class="container-xl">
			<div class="row text-secondary small py-3">
				<div class="col-md-4">&copy; <?php echo $_smarty_tpl->getValue('year');?>
 <?php echo $_smarty_tpl->getValue('service_title');?>
</div>
				<div class="col-md-4 text-center">
					<a href="<?php echo $_smarty_tpl->getValue('mobileURL');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobilepda"), $_smarty_tpl);?>
</a>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pluginUserPages'), 'item');
$foreach8DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach8DoElse = false;
if (!$_smarty_tpl->getValue('item')['top']) {?>
					| <a href="<?php echo $_smarty_tpl->getValue('item')['link'];?>
"><?php echo $_smarty_tpl->getValue('item')['text'];?>
</a>
					<?php }
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</div>
				<div class="col-md-4 text-md-end">
					powered by <a href="https://www.b1gmail.eu/" target="_blank" rel="noreferrer">b1gMail.eu</a>
				</div>
			</div>
		</div>
	</footer>
	<?php }?>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"nli:index.tpl:afterContent"), $_smarty_tpl);?>

</body>

</html>
<?php }
}
