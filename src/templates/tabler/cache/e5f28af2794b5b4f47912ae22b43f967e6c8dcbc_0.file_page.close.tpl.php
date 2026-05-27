<?php
/* Smarty version 5.8.0, created on 2026-05-25 12:39:43
  from 'file:nli/page.close.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a14430faf5da5_03997895',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e5f28af2794b5b4f47912ae22b43f967e6c8dcbc' => 
    array (
      0 => 'nli/page.close.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/msp.page.close.tpl' => 1,
  ),
))) {
function content_6a14430faf5da5_03997895 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
if ((($tmp = $_smarty_tpl->getValue('nliCompactLayout') ?? null)===null||$tmp==='' ? false ?? null : $tmp)) {
$_smarty_tpl->renderSubTemplate("file:nli/msp.page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
} else { ?>
			</div>
		</div>
	</div>
</div>
<?php }
}
}
