<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:21:20
  from 'file:li/index.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1590407d3c38_53865256',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '60a3b4022aef0c75bc941fbc181a6e4200009b79' => 
    array (
      0 => 'li/index.tpl',
      1 => 1779798074,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/navbar-tools.tpl' => 2,
    'file:li/navbar-user.tpl' => 2,
    'file:li/sidebar.footer.tpl' => 1,
    'file:li/tab-icon.tpl' => 3,
    'file:li/icon.tpl' => 1,
  ),
))) {
function content_6a1590407d3c38_53865256 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><!doctype html>
<html lang="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"langCode"), $_smarty_tpl);?>
">
<?php if ((($tmp = $_smarty_tpl->getValue('templatePrefs')['enableDarkMode'] ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {
echo '<script'; ?>
>
(function(){
	try {
		var t = localStorage.getItem('bm-tabler-theme');
		if(t === 'dark' || t === 'light')
			document.documentElement.setAttribute('data-bs-theme', t);
	} catch(e) {}
})();
<?php echo '</script'; ?>
>
<?php }?>

<head>
	<title><?php if ((true && ($_smarty_tpl->hasVariable('pageTitle') && null !== ($_smarty_tpl->getValue('pageTitle') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('pageTitle')), $_smarty_tpl);?>
 - <?php }
echo $_smarty_tpl->getValue('service_title');?>
</title>

	<meta charset="<?php echo $_smarty_tpl->getValue('charset');?>
" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />

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
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/loggedin.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/loggedin.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/dtree.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/dtree.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/tabler-custom.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/tabler-custom.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
style/legacy-addon.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"style/legacy-addon.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
libs/fontawesome/css/all.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"libs/fontawesome/css/all.min.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="<?php echo $_smarty_tpl->getValue('tpldir');?>
libs/fontawesome/css/v4-shims.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"libs/fontawesome/css/v4-shims.min.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
	<link href="clientlib/fontawesome/css/font-awesome-animation.min.css?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/fontawesome/css/font-awesome-animation.min.css"), $_smarty_tpl);?>
" rel="stylesheet" type="text/css" />
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_cssFiles')['li'], '_file');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_file')->value) {
$foreach0DoElse = false;
?>	<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->getValue('_file');?>
" />
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

	<?php echo '<script'; ?>
 type="text/javascript">
	<!--
		var currentSID = '<?php echo $_smarty_tpl->getValue('sid');?>
', tplDir = '<?php echo $_smarty_tpl->getValue('tpldir');?>
', serverTZ = <?php echo $_smarty_tpl->getValue('serverTZ');?>
, ftsBGIndexing = <?php if ($_smarty_tpl->getValue('ftsBGIndexing')) {?>true<?php } else { ?>false<?php }
if ($_smarty_tpl->getValue('bmNotifyInterval')) {?>,
			notifyInterval = <?php echo $_smarty_tpl->getValue('bmNotifyInterval');?>
, notifySound = <?php if ($_smarty_tpl->getValue('bmNotifySound')) {?>true<?php } else { ?>false<?php }
}?>,
			bmEnableDarkMode = <?php if ((($tmp = $_smarty_tpl->getValue('templatePrefs')['enableDarkMode'] ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {?>true<?php } else { ?>false<?php }?>;
	//-->
	<?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="clientlang.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/tabler.min.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/tabler.min.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/common.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/common.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/loggedin.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/loggedin.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
clientlib/dtree.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"clientlib/dtree.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
clientlib/overlay.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"clientlib/overlay.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('tpldir');?>
js/modal.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"js/modal.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="clientlib/autocomplete.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/autocomplete.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="clientlib/favico.min.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/favico.min.js"), $_smarty_tpl);?>
" type="text/javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript">
	
	var favicon=new Favico({
			animation:'fade'
		});
	
	<?php if ($_smarty_tpl->getValue('bmUnreadNotifications') != 0) {?>favicon.badge(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('number')->handle(array('value'=>$_smarty_tpl->getValue('bmUnreadNotifications'),'min'=>0,'max'=>99), $_smarty_tpl);?>
);<?php }?>
	<?php echo '</script'; ?>
>
<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('_jsFiles')['li'], '_file');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_file')->value) {
$foreach1DoElse = false;
?>	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->getValue('_file');?>
"><?php echo '</script'; ?>
>
<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"li:index.tpl:head"), $_smarty_tpl);?>

</head>

<body class="layout-fluid bm-loggedin bm-layout-combo<?php if ($_smarty_tpl->getValue('activeTab') == 'start') {?> bm-li-start<?php }
if ($_smarty_tpl->getValue('activeTab') == 'email') {?> bm-li-email bm-mail-preview-lines-<?php echo (($tmp = $_smarty_tpl->getValue('templatePrefs')['mailListPreviewLines'] ?? null)===null||$tmp==='' ? 2 ?? null : $tmp);
}
if ($_smarty_tpl->getValue('activeTab') == 'organizer') {?> bm-li-organizer<?php }
if ($_smarty_tpl->getValue('activeTab') == 'webdisk') {?> bm-li-webdisk<?php }
if ($_smarty_tpl->getValue('activeTab') == 'sms') {?> bm-li-sms<?php }
if ($_smarty_tpl->getValue('activeTab') == 'prefs') {?> bm-li-prefs<?php }
if ($_smarty_tpl->getValue('pageContent') == 'li/email.compose.tpl' || $_smarty_tpl->getValue('pageContent') == 'li/sms.compose.tpl') {?> bm-li-compose<?php }
if ($_smarty_tpl->getValue('pageContent') == 'li/email.folders.tpl' || $_smarty_tpl->getValue('pageContent') == 'li/email.folders.edit.tpl' || $_smarty_tpl->getValue('pageContent') == 'li/email.folders.editsys.tpl') {?> bm-li-folders<?php }
if (substr((string) $_smarty_tpl->getValue('pageContent'), (int) 0, (int) 22) == 'li/organizer.calendar.') {?> bm-li-organizer-calendar<?php }?>" onload="documentLoader()">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"li:index.tpl:beforeContent"), $_smarty_tpl);?>


	<div class="page" id="main">
		<aside class="navbar navbar-vertical navbar-expand-lg bm-li-vertical-nav" data-bs-theme="dark" id="mainMenu">
			<div class="container-fluid">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"navigation"), $_smarty_tpl);?>
">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="navbar-brand navbar-brand-autodark">
					<a href="start.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" aria-label="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('service_title'), ENT_QUOTES, 'UTF-8', true);?>
">
						<img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/logo.png" height="32" alt="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('service_title'), ENT_QUOTES, 'UTF-8', true);?>
" class="navbar-brand-image" />
					</a>
				</div>

				<div class="navbar-nav flex-row d-lg-none">
					<div class="d-flex">
						<?php $_smarty_tpl->renderSubTemplate("file:li/navbar-tools.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
					<?php $_smarty_tpl->renderSubTemplate("file:li/navbar-user.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>

				<div class="collapse navbar-collapse bm-li-vertical-nav-collapse" id="sidebar-menu">
					<div class="bm-li-vertical-nav-body">
						<div class="bm-li-sidebar-content pt-lg-3" id="mainMenuContainer">
							<?php if ($_smarty_tpl->getValue('pageMenuFile')) {?>
							<?php $_smarty_tpl->renderSubTemplate(((string)$_smarty_tpl->getValue('pageMenuFile')), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
							<?php }?>
							<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"li:sidebar.tpl:plugins"), $_smarty_tpl);?>

						</div>
						<?php $_smarty_tpl->renderSubTemplate("file:li/sidebar.footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
				</div>
			</div>
		</aside>

		<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
			<div class="container-xl">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"navigation"), $_smarty_tpl);?>
">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="navbar-nav flex-row order-md-last">
					<div class="d-none d-md-flex">
						<?php $_smarty_tpl->renderSubTemplate("file:li/navbar-tools.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
					<?php $_smarty_tpl->renderSubTemplate("file:li/navbar-user.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
				</div>

				<div class="collapse navbar-collapse" id="navbar-menu">
					<ul class="navbar-nav">
						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pageTabs'), 'tab', false, 'tabID');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tabID')->value => $_smarty_tpl->getVariable('tab')->value) {
$foreach2DoElse = false;
?>
						<?php if ($_smarty_tpl->getValue('tabID') != 'prefs') {?>
						<li class="nav-item<?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('tabID')) {?> active<?php }?>">
							<a class="nav-link<?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('tabID')) {?> active<?php }?>" href="<?php echo $_smarty_tpl->getValue('tab')['link'];
echo $_smarty_tpl->getValue('sid');?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('tab')['text'], ENT_QUOTES, 'UTF-8', true);?>
">
								<span class="nav-link-icon d-md-none d-lg-inline-block"><?php $_smarty_tpl->renderSubTemplate("file:li/tab-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('tab'=>$_smarty_tpl->getValue('tab')), (int) 0, $_smarty_current_dir);
?></span>
								<span class="nav-link-title"><?php echo $_smarty_tpl->getValue('tab')['text'];?>
</span>
							</a>
						</li>
						<?php }?>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</ul>
				</div>
			</div>
		</header>

		<header class="navbar navbar-expand-md d-lg-none d-print-none bm-li-mobile-tabs">
			<div class="container-xl">
				<div class="collapse navbar-collapse show" id="navbar-menu-mobile">
					<ul class="navbar-nav flex-row flex-wrap">
						<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pageTabs'), 'tab', false, 'tabID');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tabID')->value => $_smarty_tpl->getVariable('tab')->value) {
$foreach3DoElse = false;
?>
						<?php if ($_smarty_tpl->getValue('tabID') != 'prefs') {?>
						<li class="nav-item" data-tab="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('tabID'), ENT_QUOTES, 'UTF-8', true);?>
">
							<a class="nav-link<?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('tabID')) {?> active<?php }?>" href="<?php echo $_smarty_tpl->getValue('tab')['link'];
echo $_smarty_tpl->getValue('sid');?>
" title="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('tab')['text'], ENT_QUOTES, 'UTF-8', true);?>
" aria-label="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('tab')['text'], ENT_QUOTES, 'UTF-8', true);?>
">
								<span class="nav-link-icon"><?php $_smarty_tpl->renderSubTemplate("file:li/tab-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('tab'=>$_smarty_tpl->getValue('tab')), (int) 0, $_smarty_current_dir);
?></span>
							</a>
						</li>
						<?php }?>
						<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</ul>
				</div>
			</div>
		</header>

		<div class="page-wrapper">
			<?php if ((true && ($_smarty_tpl->hasVariable('pageToolbarFile') && null !== ($_smarty_tpl->getValue('pageToolbarFile') ?? null))) || (true && ($_smarty_tpl->hasVariable('pageToolbar') && null !== ($_smarty_tpl->getValue('pageToolbar') ?? null)))) {?>
			<div class="page-header d-print-none">
				<div class="container-xl">
					<div class="row g-2 align-items-center bm-li-page-toolbar">
						<?php if ((true && ($_smarty_tpl->hasVariable('pageToolbarFile') && null !== ($_smarty_tpl->getValue('pageToolbarFile') ?? null)))) {?>
						<?php $_smarty_tpl->renderSubTemplate(((string)$_smarty_tpl->getValue('pageToolbarFile')), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
						<?php } else { ?>
						<div class="col"><?php echo $_smarty_tpl->getValue('pageToolbar');?>
</div>
						<?php }?>
					</div>
				</div>
			</div>
			<?php }?>

			<div class="page-body">
				<div class="container-xl" id="mainContent">
					<div id="mainBanner" style="display:none;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('banner')->handle(array(), $_smarty_tpl);?>
</div>
					<div id="mainContentArea">
						<?php $_smarty_tpl->renderSubTemplate(((string)$_smarty_tpl->getValue('pageContent')), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
					</div>
				</div>
			</div>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('comment')->handle(array('text'=>"search popup"), $_smarty_tpl);?>

		<div class="headerBox card shadow bm-li-header-popup" id="searchPopup" style="display:none">
			<div class="card-body p-2">
				<div class="input-group input-group-flat">
					<span class="input-group-text">
						<i id="searchSpinner" class="fa fa-spinner fa-pulse fa-fw" style="display:none;"></i>
						<i class="icon ti ti-search icon-1 search-icon-default"></i>
					</span>
					<input type="search" class="form-control" id="searchField" name="searchField" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"search"), $_smarty_tpl);?>
" onkeypress="searchFieldKeyPress(event,<?php if ($_smarty_tpl->getValue('searchDetailsDefault')) {?>true<?php } else { ?>false<?php }?>)" />
				</div>
				<div id="searchResultBody" class="mt-2" style="display:none">
					<div id="searchResults" class="list-group list-group-flush"></div>
				</div>
			</div>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('comment')->handle(array('text'=>"new menu"), $_smarty_tpl);?>

		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="newMenu" style="display:none">
			<div class="card">
				<div class="card-body p-0">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('newMenu'), 'item');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach4DoElse = false;
?>
					<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('array_key_exists')('sep',$_smarty_tpl->getValue('item'))) {?>
					<div class="dropdown-divider m-0"></div>
					<?php } else { ?>
					<a class="dropdown-item" href="<?php echo $_smarty_tpl->getValue('item')['link'];
echo $_smarty_tpl->getValue('sid');?>
">
						<span class="me-2"><?php $_smarty_tpl->renderSubTemplate("file:li/icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('faIcon'=>$_smarty_tpl->getValue('item')['faIcon']), (int) 0, $_smarty_current_dir);
?></span>
						<?php echo $_smarty_tpl->getValue('item')['text'];?>
...
					</a>
					<?php }?>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</div>
			</div>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('comment')->handle(array('text'=>"notifications"), $_smarty_tpl);?>

		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="notifyBox" style="display:none">
			<div class="card">
				<div class="card-header d-flex">
					<h3 class="card-title mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notifications"), $_smarty_tpl);?>
</h3>
					<button type="button" class="btn-close ms-auto" onclick="hideNotifications(true); return false;" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"close"), $_smarty_tpl);?>
"></button>
				</div>
				<div class="card-body p-0" id="notifyInner" style="max-height: 320px; overflow-y: auto;"></div>
			</div>
		</div>

		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('comment')->handle(array('text'=>"user menu"), $_smarty_tpl);?>

		<div class="headerBox bm-li-header-popup dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" id="userMenu" style="display:none">
			<div class="card">
				<a href="prefs.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="dropdown-item">
					<i class="ti ti-settings icon dropdown-item-icon"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prefs"), $_smarty_tpl);?>

				</a>
				<a href="prefs.php?action=faq&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="dropdown-item">
					<i class="ti ti-help icon dropdown-item-icon"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"faq"), $_smarty_tpl);?>

				</a>
				<div class="dropdown-divider m-0"></div>
				<a href="start.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&amp;action=logout" class="dropdown-item" onclick="return confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"logoutquestion"), $_smarty_tpl);?>
');">
					<i class="ti ti-logout icon dropdown-item-icon"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"logout"), $_smarty_tpl);?>

				</a>
			</div>
		</div>

				<div class="menu fade d-md-none" id="dropdownNavMenu" style="display:none;">
			<div class="row g-2 p-2">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('pageTabs'), 'tab', false, 'tabID');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('tabID')->value => $_smarty_tpl->getVariable('tab')->value) {
$foreach5DoElse = false;
?>
				<?php if ($_smarty_tpl->getValue('tabID') != 'prefs') {?>
				<div class="col-4">
					<a href="<?php echo $_smarty_tpl->getValue('tab')['link'];
echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-ghost-secondary w-100 py-3<?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('tabID')) {?> active<?php }?>" title="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('tab')['text'], ENT_QUOTES, 'UTF-8', true);?>
">
						<?php $_smarty_tpl->renderSubTemplate("file:li/tab-icon.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('tab'=>$_smarty_tpl->getValue('tab')), (int) 0, $_smarty_current_dir);
?>
						<span class="d-block small mt-1 text-truncate"><?php echo $_smarty_tpl->getValue('tab')['text'];?>
</span>
					</a>
				</div>
				<?php }?>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			</div>
		</div>
	</div>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"li:index.tpl:afterContent"), $_smarty_tpl);?>

</body>

</html>
<?php }
}
