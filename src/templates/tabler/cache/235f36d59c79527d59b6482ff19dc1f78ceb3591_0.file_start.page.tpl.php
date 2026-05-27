<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:08:59
  from 'file:li/start.page.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14822b7883c1_54616564',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '235f36d59c79527d59b6482ff19dc1f78ceb3591' => 
    array (
      0 => 'li/start.page.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/widget-board.tpl' => 1,
  ),
))) {
function content_6a14822b7883c1_54616564 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-home icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"welcome"), $_smarty_tpl);?>
!
		</div>
		<div class="right bm-dashboard-header-actions">
			<?php if ($_smarty_tpl->getValue('templatePrefs')['showUserEmail']) {?>
			<span class="bm-dashboard-header-email text-secondary"><?php echo $_smarty_tpl->getValue('_userEmail');?>
</span>
			<?php }?>
			<a href="start.php?action=customize&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-outline-primary">
				<i class="ti ti-layout-grid-add icon"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"customize"), $_smarty_tpl);?>

			</a>
		</div>
	</div>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"start.page.tpl:head"), $_smarty_tpl);?>


	<div class="bm-dashboard-body">
		<?php $_smarty_tpl->renderSubTemplate("file:li/widget-board.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('boardSaveCallback'=>"startBoardOrderChanged"), (int) 0, $_smarty_current_dir);
?>
	</div>

	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('id'=>"start.page.tpl:foot"), $_smarty_tpl);?>

</div>
<?php }
}
