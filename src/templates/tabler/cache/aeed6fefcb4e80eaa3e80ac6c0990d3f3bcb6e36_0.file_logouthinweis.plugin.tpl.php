<?php
/* Smarty version 5.8.0, created on 2026-05-25 19:20:46
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/logouthinweis.plugin.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1484ee918227_62060316',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aeed6fefcb4e80eaa3e80ac6c0990d3f3bcb6e36' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/logouthinweis.plugin.tpl',
      1 => 1779525290,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1484ee918227_62060316 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="jumbotron splash" style="background-image: url(<?php echo $_smarty_tpl->getValue('tpldir');?>
images/nli/<?php echo $_smarty_tpl->getValue('templatePrefs')['splashImage'];?>
);">
	<div class="container">
		<div class="panel panel-primary login">
			<div class="panel-heading">
				<i class="glyphicon glyphicon-exclamation-sign"></i> <?php echo $_smarty_tpl->getValue('title');?>

			</div>
			<div class="panel-body">
				<?php echo $_smarty_tpl->getValue('msg');?>

				<br /><br />
				<div class="form-group">
					<button type="button" class="btn btn-success" onclick="document.location.href='<?php echo $_smarty_tpl->getValue('backLink');?>
';">
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"go_to_mailbox"), $_smarty_tpl);?>

					</button>	
				</div>
			</div>
		</div>
	</div>
</div><?php }
}
