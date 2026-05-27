<?php
/* Smarty version 5.8.0, created on 2026-05-25 15:49:44
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/news.notloggedin.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a146f98abf5a1_87455262',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'efbe844fe94d3053d1904e040882d8bcc3c88875' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/news.notloggedin.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a146f98abf5a1_87455262 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><h1 class="mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"news_news"), $_smarty_tpl);?>
</h1>

<p class="text-secondary mb-4">
	<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"news_text"), $_smarty_tpl);?>

</p>

<div class="accordion" id="news">
	<?php if (!$_smarty_tpl->getValue('news')) {?>
	<p class="text-secondary mb-0"><em><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"news_nonews"), $_smarty_tpl);?>
</em></p>
	<?php } else { ?>
	<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('news'), 'item', false, 'id');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('id')->value => $_smarty_tpl->getVariable('item')->value) {
$foreach0DoElse = false;
?>
	<div class="accordion-item">
		<h2 class="accordion-header" id="news-heading-<?php echo $_smarty_tpl->getValue('id');?>
">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#news-<?php echo $_smarty_tpl->getValue('id');?>
" aria-expanded="false" aria-controls="news-<?php echo $_smarty_tpl->getValue('id');?>
">
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['title']), $_smarty_tpl);?>

			</button>
		</h2>
		<div id="news-<?php echo $_smarty_tpl->getValue('id');?>
" class="accordion-collapse collapse" aria-labelledby="news-heading-<?php echo $_smarty_tpl->getValue('id');?>
" data-bs-parent="#news">
			<div class="accordion-body">
				<?php echo $_smarty_tpl->getValue('item')['text'];?>

			</div>
		</div>
	</div>
	<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
	<?php }?>
</div>
<?php }
}
