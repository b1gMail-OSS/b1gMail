<?php
/* Smarty version 5.8.0, created on 2026-05-26 14:19:36
  from 'file:li/organizer.start.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a158fd8de49c1_58739028',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9cdb04f453441b1276c7342a2631be315c380108' => 
    array (
      0 => 'li/organizer.start.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/widget-board.tpl' => 1,
  ),
))) {
function content_6a158fd8de49c1_58739028 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-dashboard icon icon-sm" aria-hidden="true"></i>
			<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"overview"), $_smarty_tpl);?>

		</div>
		<div class="right bm-dashboard-header-actions">
			<a href="organizer.php?action=customize&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-outline-primary">
				<i class="ti ti-layout-grid-add icon"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"customize"), $_smarty_tpl);?>

			</a>
		</div>
	</div>

	<div class="bm-dashboard-body">
		<?php $_smarty_tpl->renderSubTemplate("file:li/widget-board.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('boardSaveCallback'=>"organizerBoardOrderChanged"), (int) 0, $_smarty_current_dir);
?>
	</div>
</div>
<?php }
}
