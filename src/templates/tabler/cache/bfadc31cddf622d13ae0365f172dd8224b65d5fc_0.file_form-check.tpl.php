<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:36:26
  from 'file:li/form-check.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bdfa4664f4_33328048',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bfadc31cddf622d13ae0365f172dd8224b65d5fc' => 
    array (
      0 => 'li/form-check.tpl',
      1 => 1779809782,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bdfa4664f4_33328048 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><label class="form-check<?php if (!( !$_smarty_tpl->hasVariable('inline') || empty($_smarty_tpl->getValue('inline')))) {?> form-check-inline<?php }
if ((true && ($_smarty_tpl->hasVariable('wrapClass') && null !== ($_smarty_tpl->getValue('wrapClass') ?? null)))) {?> <?php echo $_smarty_tpl->getValue('wrapClass');
} else { ?> mb-0<?php }?>">
	<input type="checkbox" class="form-check-input<?php if (!( !$_smarty_tpl->hasVariable('compact') || empty($_smarty_tpl->getValue('compact')))) {?> m-0<?php }?>"<?php if ((true && ($_smarty_tpl->hasVariable('id') && null !== ($_smarty_tpl->getValue('id') ?? null)))) {?> id="<?php echo $_smarty_tpl->getValue('id');?>
"<?php }
if ((true && ($_smarty_tpl->hasVariable('name') && null !== ($_smarty_tpl->getValue('name') ?? null)))) {?> name="<?php echo $_smarty_tpl->getValue('name');?>
"<?php }
if ((true && ($_smarty_tpl->hasVariable('value') && null !== ($_smarty_tpl->getValue('value') ?? null)))) {?> value="<?php echo $_smarty_tpl->getValue('value');?>
"<?php }
if (!( !$_smarty_tpl->hasVariable('checked') || empty($_smarty_tpl->getValue('checked')))) {?> checked="checked"<?php }
if (!( !$_smarty_tpl->hasVariable('disabled') || empty($_smarty_tpl->getValue('disabled')))) {?> disabled="disabled"<?php }
if (!( !$_smarty_tpl->hasVariable('readonly') || empty($_smarty_tpl->getValue('readonly')))) {?> readonly="readonly"<?php }
if ((true && ($_smarty_tpl->hasVariable('onclick') && null !== ($_smarty_tpl->getValue('onclick') ?? null)))) {?> onclick="<?php echo $_smarty_tpl->getValue('onclick');?>
"<?php }
if ((true && ($_smarty_tpl->hasVariable('onchange') && null !== ($_smarty_tpl->getValue('onchange') ?? null)))) {?> onchange="<?php echo $_smarty_tpl->getValue('onchange');?>
"<?php }
if ((true && ($_smarty_tpl->hasVariable('onkeypress') && null !== ($_smarty_tpl->getValue('onkeypress') ?? null)))) {?> onkeypress="<?php echo $_smarty_tpl->getValue('onkeypress');?>
"<?php }
if ((true && ($_smarty_tpl->hasVariable('ariaLabel') && null !== ($_smarty_tpl->getValue('ariaLabel') ?? null)))) {?> aria-label="<?php echo $_smarty_tpl->getValue('ariaLabel');?>
"<?php }
if (!( !$_smarty_tpl->hasVariable('ariaHidden') || empty($_smarty_tpl->getValue('ariaHidden')))) {?> aria-hidden="true"<?php }?> />
	<?php if ((true && ($_smarty_tpl->hasVariable('labelKey') && null !== ($_smarty_tpl->getValue('labelKey') ?? null)))) {?><span class="form-check-label"><?php if (!( !$_smarty_tpl->hasVariable('labelBold') || empty($_smarty_tpl->getValue('labelBold')))) {?><b><?php }
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>$_smarty_tpl->getValue('labelKey')), $_smarty_tpl);
if (!( !$_smarty_tpl->hasVariable('labelBold') || empty($_smarty_tpl->getValue('labelBold')))) {?></b><?php }?></span><?php } elseif ((true && ($_smarty_tpl->hasVariable('label') && null !== ($_smarty_tpl->getValue('label') ?? null)))) {?><span class="form-check-label"><?php echo $_smarty_tpl->getValue('label');?>
</span><?php }?>
</label>
<?php }
}
