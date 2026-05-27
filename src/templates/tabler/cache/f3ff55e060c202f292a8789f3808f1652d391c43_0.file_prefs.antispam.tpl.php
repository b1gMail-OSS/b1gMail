<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:43:47
  from 'file:li/prefs.antispam.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15bfb333fd51_11338793',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f3ff55e060c202f292a8789f3808f1652d391c43' => 
    array (
      0 => 'li/prefs.antispam.tpl',
      1 => 1779809933,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a15bfb333fd51_11338793 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-antispam">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-ban icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"antispam"), $_smarty_tpl);?>

	</div>
</div>

<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">

<form name="f1" method="post" action="prefs.php?action=antispam&do=save&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"antispam"), $_smarty_tpl);?>
</th>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="spamfilter"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"spamfilter"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="spamfilter" id="spamfilter"<?php if ($_smarty_tpl->getValue('spamFilter')) {?> checked="checked"<?php }?> /><span class="form-check-label"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"enable"), $_smarty_tpl);?>
</b></span></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="unspamme"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"unspamme"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="unspamme" id="unspamme"<?php if ($_smarty_tpl->getValue('unspamMe')) {?> checked="checked"<?php }?> /><span class="form-check-label"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"marknonspam"), $_smarty_tpl);?>
</b></span></label>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="addressbook_nospam"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mailsfromab"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="addressbook_nospam" id="addressbook_nospam"<?php if ($_smarty_tpl->getValue('addressbookNoSpam')) {?> checked="checked"<?php }?> /><span class="form-check-label"><b><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"marknonspam"), $_smarty_tpl);?>
</b></span></label>
			</td>
		</tr>
		<?php if ($_smarty_tpl->getValue('localMode')) {?>
		<tr>
			<td class="listTableLeft"><label for="bayes_border"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"bayesborder"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<table class="bayesBorderTable">
					<tr>
						<td colspan="2">
							<div class="bayesBorderSlider">
								<table class="bayesBorderTable2">
									<td><input type="radio" name="bayes_border" value="98"<?php if ($_smarty_tpl->getValue('bayes_border') == 98) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="96"<?php if ($_smarty_tpl->getValue('bayes_border') == 96) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="94"<?php if ($_smarty_tpl->getValue('bayes_border') == 94) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="92"<?php if ($_smarty_tpl->getValue('bayes_border') == 92) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="90"<?php if ($_smarty_tpl->getValue('bayes_border') == 90) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="88"<?php if ($_smarty_tpl->getValue('bayes_border') == 88) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="84"<?php if ($_smarty_tpl->getValue('bayes_border') == 84) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="80"<?php if ($_smarty_tpl->getValue('bayes_border') == 80) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="75"<?php if ($_smarty_tpl->getValue('bayes_border') == 75) {?> checked="checked"<?php }?> /></td>
									<td><input type="radio" name="bayes_border" value="70"<?php if ($_smarty_tpl->getValue('bayes_border') == 70) {?> checked="checked"<?php }?> /></td>					
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td class="bayesBorderLeftTD"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"defensive"), $_smarty_tpl);?>
</td>
						<td class="bayesBorderRightTD"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"aggressive"), $_smarty_tpl);?>
</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td class="listTableLeft"><label for="spamaction"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"spamaction"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<select name="spamaction" id="spamaction">
					<option value="-1"<?php if ($_smarty_tpl->getValue('spamAction') == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"block"), $_smarty_tpl);?>
</option>
					
					<optgroup label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"move"), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"moveto"), $_smarty_tpl);?>
">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dropdownFolderList'), 'dFolderTitle', false, 'dFolderID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dFolderID')->value => $_smarty_tpl->getVariable('dFolderTitle')->value) {
$foreach0DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('dFolderID');?>
" style="font-family:courier;"<?php if ($_smarty_tpl->getValue('spamAction') == $_smarty_tpl->getValue('dFolderID')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('dFolderTitle');?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</optgroup>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="btn btn-primary" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
" />
				<input type="reset" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
" />
			</td>
		</tr>
	</table>
</form>

<?php if ($_smarty_tpl->getValue('localMode')) {?>
<br />
<form name="f1" method="post" action="prefs.php?action=antispam&do=resetDB&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"spamindex"), $_smarty_tpl);?>
</th>
		</tr>
		<tr>
			<td class="listTableLeft"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"entries"), $_smarty_tpl);?>
:</td>
			<td class="listTableRight">
				<?php echo $_smarty_tpl->getValue('dbEntries');?>

			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"resetindex"), $_smarty_tpl);?>
"<?php if ($_smarty_tpl->getValue('dbEntries') == 0) {?> disabled="disabled"<?php }?> /><br />
				<small><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"resetindextext"), $_smarty_tpl);?>
</small>
			</td>
		</tr>
	</table>
</form>
<?php }?>

</div></div>
</div>
<?php }
}
