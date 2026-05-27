<?php
/* Smarty version 5.8.0, created on 2026-05-26 17:39:10
  from 'file:li/prefs.common.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a15be9ecc6a04_65250351',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'be81e03e2dfd2841a633305dec50967ebdfbad0b' => 
    array (
      0 => 'li/prefs.common.tpl',
      1 => 1779809933,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/form-check.tpl' => 25,
  ),
))) {
function content_6a15be9ecc6a04_65250351 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-prefs-page bm-prefs-page-common">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-settings icon icon-sm" aria-hidden="true"></i>
		<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"common"), $_smarty_tpl);?>

	</div>
</div>

<form name="f1" method="post" action="prefs.php?action=common&do=save&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
<?php if ($_smarty_tpl->getValue('allownewsoptout') != 'yes' && $_smarty_tpl->getValue('newsletter_optin') == 'yes') {?>
<input type="hidden" name="newsletter_optin" value="true" />
<?php }?>
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"common"), $_smarty_tpl);?>
</th>
		</tr>
	
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-cogs" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"common"), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="preferred_language"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"language"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<select name="preferred_language" id="preferred_language">
					<option value="">(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"auto"), $_smarty_tpl);?>
)</option>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'langInfo', false, 'lang');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value => $_smarty_tpl->getVariable('langInfo')->value) {
$foreach1DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('lang');?>
"<?php if ($_smarty_tpl->getValue('preferred_language') == $_smarty_tpl->getValue('lang')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('langInfo')['title'];?>
</option>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="c_firstday"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"weekstart"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<select name="c_firstday" id="c_firstday">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('fullWeekdays'), 'dayName', false, 'dayKey');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dayKey')->value => $_smarty_tpl->getVariable('dayName')->value) {
$foreach2DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('dayKey');?>
"<?php if ($_smarty_tpl->getValue('dayKey') == $_smarty_tpl->getValue('c_firstday')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('dayName');?>
</option>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="datumsformat"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"dateformat"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<input type="text" name="datumsformat" id="datumsformat" value="<?php if ((true && ($_smarty_tpl->hasVariable('datumsformat') && null !== ($_smarty_tpl->getValue('datumsformat') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('datumsformat')), $_smarty_tpl);
}?>" style="width:250px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="hotkeys"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"hotkeys"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"hotkeys",'name'=>"hotkeys",'checked'=>$_smarty_tpl->getValue('hotkeys'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="search_details_default"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"search"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"search_details_default",'name'=>"search_details_default",'checked'=>$_smarty_tpl->getValue('searchDetailsDefault'),'labelKey'=>"details_default"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>

		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-bell-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notifications"), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_sound"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notify_sound"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"notify_sound",'name'=>"notify_sound",'checked'=>$_smarty_tpl->getValue('notifySound'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="notify_types"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notify_types"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"notify_email",'name'=>"notify_email",'checked'=>$_smarty_tpl->getValue('notifyEMail'),'labelKey'=>"notify_email"), (int) 0, $_smarty_current_dir);
?>
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"notify_birthday",'name'=>"notify_birthday",'checked'=>$_smarty_tpl->getValue('notifyBirthday'),'labelKey'=>"notify_birthday"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>

		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-inbox" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</td>
		</tr>
		<?php if ($_smarty_tpl->getValue('allownewsoptout') == 'yes') {?>
		<tr>
			<td class="listTableLeft"><label for="newsletter_optin"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"newsletter"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"newsletter_optin",'name'=>"newsletter_optin",'checked'=>($_smarty_tpl->getValue('newsletter_optin') == 'yes'),'labelKey'=>"subscribe"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td class="listTableLeft"><label for="in_refresh"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"inboxrefresh"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('wrapClass'=>"d-inline-flex me-2 mb-0",'id'=>"in_refresh_active",'name'=>"in_refresh_active",'checked'=>($_smarty_tpl->getValue('in_refresh') > 0)), (int) 0, $_smarty_current_dir);
?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"every"), $_smarty_tpl);?>
 <input type="text" name="in_refresh" id="in_refresh" value="<?php echo $_smarty_tpl->getValue('in_refresh');?>
" size="4" onkeypress="EBID('in_refresh_active').checked=true;" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"seconds"), $_smarty_tpl);?>

			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="preview"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"preview"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"preview",'name'=>"preview",'checked'=>($_smarty_tpl->getValue('preview') == 'yes'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"plaintextcourier"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"plaintext_courier",'name'=>"plaintext_courier",'checked'=>($_smarty_tpl->getValue('plaintext_courier') == 'yes'),'labelKey'=>"usecourier"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="soforthtml"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"insthtmlview"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"soforthtml",'name'=>"soforthtml",'checked'=>($_smarty_tpl->getValue('soforthtml') == 'yes'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="conversation_view"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"conversationview"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"conversation_view",'name'=>"conversation_view",'checked'=>($_smarty_tpl->getValue('conversation_view') == 'yes'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="autosend_dn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mailconfirmation"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"autosend_dn",'name'=>"autosend_dn",'checked'=>$_smarty_tpl->getValue('autosend_dn'),'labelKey'=>"autosend"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-reply" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"composeprefs"), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="absendername"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendername"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<input type="text" name="absendername" id="absendername" value="<?php if ((true && ($_smarty_tpl->hasVariable('absendername') && null !== ($_smarty_tpl->getValue('absendername') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('absendername'),'allowEmpty'=>true), $_smarty_tpl);
}?>" style="width:350px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="defaultSender"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"defaultsender"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<select name="defaultSender" id="defaultSender">
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('possibleSenders'), 'senderName', false, 'senderKey');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('senderKey')->value => $_smarty_tpl->getVariable('senderName')->value) {
$foreach3DoElse = false;
?>
					<option value="<?php echo $_smarty_tpl->getValue('senderKey');?>
"<?php if ($_smarty_tpl->getValue('senderKey') == $_smarty_tpl->getValue('defaultSender')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('senderName')), $_smarty_tpl);?>
</option>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"defaults"), $_smarty_tpl);?>
 (1):</label></td>
			<td class="listTableRight">
				<i class="fa fa-id-card-o" aria-hidden="true"></i> <?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>"composeDefaults[attachVCard]",'id'=>"attachVCard",'checked'=>(true && (true && null !== ($_smarty_tpl->getValue('composeDefaults')['attachVCard'] ?? null))),'labelKey'=>"attachvc"), (int) 0, $_smarty_current_dir);
?>
				&nbsp;
				<i class="fa fa-certificate" aria-hidden="true"></i> <?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>"composeDefaults[certMail]",'id'=>"certMail",'checked'=>(true && (true && null !== ($_smarty_tpl->getValue('composeDefaults')['certMail'] ?? null))),'labelKey'=>"certmail"), (int) 0, $_smarty_current_dir);
?>
				&nbsp;
				<i class="fa fa-bullhorn" aria-hidden="true"></i> <?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>"composeDefaults[mailConfirmation]",'id'=>"mailConfirmation",'checked'=>(true && (true && null !== ($_smarty_tpl->getValue('composeDefaults')['mailConfirmation'] ?? null))),'labelKey'=>"mailconfirmation"), (int) 0, $_smarty_current_dir);
?>

			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"defaults"), $_smarty_tpl);?>
 (2):</label></td>
			<td class="listTableRight">
				<i class="fa fa-inbox" aria-hidden="true"></i> <label for="savecopy"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"savecopy"), $_smarty_tpl);?>
:</label>
					<select name="composeDefaults[savecopy]" id="savecopy">
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dropdownFolderList'), 'dFolderTitle', false, 'dFolderID');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('dFolderID')->value => $_smarty_tpl->getVariable('dFolderTitle')->value) {
$foreach4DoElse = false;
?>
						<option value="<?php echo $_smarty_tpl->getValue('dFolderID');?>
" style="font-family:courier;"<?php if ((!$_smarty_tpl->getValue('composeDefaults')['savecopy'] && $_smarty_tpl->getValue('composeDefaults')['savecopy'] !== '0' && $_smarty_tpl->getValue('dFolderID') == -2) || $_smarty_tpl->getValue('composeDefaults')['savecopy'] == $_smarty_tpl->getValue('dFolderID')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getValue('dFolderTitle');?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</select>
				&nbsp;
				<i class="fa fa-flag" aria-hidden="true"></i>
					<select name="composeDefaults[priority]" id="priority">
						<option value="1"<?php if ($_smarty_tpl->getValue('composeDefaults')['priority'] == 1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_1"), $_smarty_tpl);?>
</option>
						<option value="0"<?php if (!$_smarty_tpl->getValue('composeDefaults')['priority'] || $_smarty_tpl->getValue('composeDefaults')['priority'] == 0) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_0"), $_smarty_tpl);?>
</option>
						<option value="-1"<?php if ($_smarty_tpl->getValue('composeDefaults')['priority'] == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_-1"), $_smarty_tpl);?>
</option>
					</select>
				<?php if ($_smarty_tpl->getValue('signatures')) {?>
				&nbsp;
					<i class="fa fa-quote-right" aria-hidden="true"></i>
					<select name="composeDefaults[signature]" id="signature">
						<option value="0">-</option>
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('signatures'), 'signature');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('signature')->value) {
$foreach5DoElse = false;
?>
						<option value="<?php echo $_smarty_tpl->getValue('signature')['id'];?>
"<?php if ($_smarty_tpl->getValue('composeDefaults')['signature'] == $_smarty_tpl->getValue('signature')['id']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('signature')['titel'],'cut'=>15), $_smarty_tpl);?>
</option>
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					</select>
				<?php }?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="re"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"retext"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<input type="text" name="re" id="re" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('re')), $_smarty_tpl);?>
" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fwd"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fwdtext"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<input type="text" name="fwd" id="fwd" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('fwd')), $_smarty_tpl);?>
" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="fwd"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"atreply"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"reply_quote",'name'=>"reply_quote",'checked'=>($_smarty_tpl->getValue('reply_quote') == 'yes'),'labelKey'=>"insertquote"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="attcheck"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attcheck"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"attcheck",'name'=>"attcheck",'checked'=>($_smarty_tpl->getValue('attcheck') == 'yes'),'labelKey'=>"attcheck_desc"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="linesep"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"linesep"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"linesep",'name'=>"linesep",'checked'=>$_smarty_tpl->getValue('linesep'),'labelKey'=>"linesep_desc"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<?php if ($_smarty_tpl->getValue('draftAutoSaveAllowed')) {?>
		<tr>
			<td class="listTableLeft"><label for="auto_save_drafts"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"auto_save_drafts"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('wrapClass'=>"d-inline-flex me-2 mb-0",'id'=>"auto_save_drafts",'name'=>"auto_save_drafts",'checked'=>$_smarty_tpl->getValue('autoSaveDrafts')), (int) 0, $_smarty_current_dir);
?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"every"), $_smarty_tpl);?>
 <input type="text" name="auto_save_drafts_interval" id="auto_save_drafts_interval" value="<?php echo $_smarty_tpl->getValue('autoSaveDraftsInterval');?>
" size="4" onkeypress="EBID('auto_save_drafts').checked=true;" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"seconds"), $_smarty_tpl);?>

			</td>
		</tr>
		<?php }?>
		
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-folder-open-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"webdisk"), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="webdisk_hidehidden"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"hiddenelements"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"webdisk_hidehidden",'name'=>"webdisk_hidehidden",'checked'=>$_smarty_tpl->getValue('webdisk_hidehidden'),'labelKey'=>"hide"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>

		<?php if ($_smarty_tpl->getValue('smimeAllowed')) {?>
		<tr>
			<td class="listTableLeftDesc"><i class="fa fa-key" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"security"), $_smarty_tpl);?>
</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="smimeSign"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sign"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"smimeSign",'name'=>"smimeSign",'checked'=>$_smarty_tpl->getValue('smimeSign'),'labelKey'=>"enablebydefault"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="smimeEncrypt"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"encrypt"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"smimeEncrypt",'name'=>"smimeEncrypt",'checked'=>$_smarty_tpl->getValue('smimeEncrypt'),'labelKey'=>"enablebydefault"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr>
		<?php }?>
		
		<?php if ($_smarty_tpl->getValue('mail2smsAllowed') || $_smarty_tpl->getValue('forwardingAllowed')) {?><tr>
			<td class="listTableLeftDesc"><i class="fa fa-folder-o" aria-hidden="true"></i></td>
			<td class="listTableRightDesc"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"receiveprefs"), $_smarty_tpl);?>
</td>
		</tr><?php }?>
		<?php if ($_smarty_tpl->getValue('mail2smsAllowed')) {?><tr>
			<td class="listTableLeft"><label for="mail2sms"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mail2sms"), $_smarty_tpl);?>
:</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"mail2sms",'name'=>"mail2sms",'checked'=>($_smarty_tpl->getValue('mail2sms') == 'yes'),'labelKey'=>"enable"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr><?php }?>
		<?php if ($_smarty_tpl->getValue('forwardingAllowed')) {?><tr>
			<td class="listTableLeft"><label for="forward_to"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"forwarding"), $_smarty_tpl);?>
?</label></td>
			<td class="listTableRight">
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('wrapClass'=>"d-inline-flex me-2 mb-0",'id'=>"forward",'name'=>"forward",'checked'=>($_smarty_tpl->getValue('forward') == 'yes')), (int) 0, $_smarty_current_dir);
?>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"to2"), $_smarty_tpl);?>
 <input type="email" name="forward_to" id="forward_to" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('email')->handle(array('value'=>$_smarty_tpl->getValue('forward_to')), $_smarty_tpl);?>
" style="width:200px;" onkeypress="EBID('forward').checked=true;" /><br />
				<?php $_smarty_tpl->renderSubTemplate("file:li/form-check.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('id'=>"forward_delete",'name'=>"forward_delete",'checked'=>($_smarty_tpl->getValue('forward_delete') == 'yes'),'labelKey'=>"deleteforwarded"), (int) 0, $_smarty_current_dir);
?>
			</td>
		</tr><?php }?>
		
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
</div></div>
</form>
</div>
<?php }
}
