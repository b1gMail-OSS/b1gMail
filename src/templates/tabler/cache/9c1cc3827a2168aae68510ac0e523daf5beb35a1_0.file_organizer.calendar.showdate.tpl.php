<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:27:05
  from 'file:li/organizer.calendar.showdate.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a135109a70439_61194473',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9c1cc3827a2168aae68510ac0e523daf5beb35a1' => 
    array (
      0 => 'li/organizer.calendar.showdate.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:li/dialog.head.tpl' => 1,
    'file:li/dialog.foot.tpl' => 1,
  ),
))) {
function content_6a135109a70439_61194473 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
$_smarty_tpl->renderSubTemplate("file:li/dialog.head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('dialogTitle'=>$_smarty_tpl->getValue('date')['title'],'dialogBodyClass'=>"bm-dialog-calendar-date bm-dialog-modal-sections",'dialogOnLoad'=>"documentLoader()"), (int) 0, $_smarty_current_dir);
?>

<div class="bm-calendar-showdate">
	<div class="modal-body">
		<h3 class="modal-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"date2"), $_smarty_tpl);?>
</h3>
		<dl class="row mb-0">
			<dt class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"begin"), $_smarty_tpl);?>
</dt>
			<dd class="col-sm-9">
				<?php if (($_smarty_tpl->getValue('date')['flags']&1)) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['startdate'],'dayonly'=>true), $_smarty_tpl);?>
 (<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"wholeday"), $_smarty_tpl);?>
)<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['startdate'],'nice'=>true,'elapsed'=>true), $_smarty_tpl);
}?>
				<?php if (!( !true || empty($_smarty_tpl->getValue('date')['orig_startdate']))) {?><span class="text-secondary"> (<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"thisevent"), $_smarty_tpl);?>
 <?php if (($_smarty_tpl->getValue('date')['flags']&1)) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['orig_startdate'],'dayonly'=>true), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['orig_startdate'],'nice'=>true), $_smarty_tpl);
}?>)</span><?php }?>
			</dd>
			<dt class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"end"), $_smarty_tpl);?>
</dt>
			<dd class="col-sm-9">
				<?php if (($_smarty_tpl->getValue('date')['flags']&1)) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['enddate'],'dayonly'=>true), $_smarty_tpl);?>
 (<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"wholeday"), $_smarty_tpl);?>
)<?php } else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['enddate'],'nice'=>true,'elapsed'=>true), $_smarty_tpl);
}?>
				<?php if (!( !true || empty($_smarty_tpl->getValue('date')['orig_enddate']))) {?><span class="text-secondary"> (<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"thisevent"), $_smarty_tpl);?>
 <?php if (($_smarty_tpl->getValue('date')['flags']&1)) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['orig_enddate'],'dayonly'=>true), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('date')['orig_enddate'],'nice'=>true), $_smarty_tpl);
}?>)</span><?php }?>
			</dd>
			<dt class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"location"), $_smarty_tpl);?>
</dt>
			<dd class="col-sm-9"><?php if ($_smarty_tpl->getValue('date')['location']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('date')['location']), $_smarty_tpl);
} else { ?>—<?php }?></dd>
			<dt class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reminder"), $_smarty_tpl);?>
</dt>
			<dd class="col-sm-9">
				<label class="form-check form-switch mb-0">
					<input type="checkbox" class="form-check-input" <?php if (((true && (true && null !== ($_smarty_tpl->getValue('date')['flags'] ?? null))) && (($_smarty_tpl->getValue('date')['flags']&2) || ($_smarty_tpl->getValue('date')['flags']&4) || $_smarty_tpl->getValue('date')['flags']&8))) {?> checked="checked"<?php }?> disabled="disabled" />
				</label>
			</dd>
			<dt class="col-sm-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"repeating"), $_smarty_tpl);?>
</dt>
			<dd class="col-sm-9">
				<label class="form-check form-switch mb-0">
					<input type="checkbox" class="form-check-input" <?php if ($_smarty_tpl->getValue('date')['repeat_flags'] != 0) {?> checked="checked"<?php }?> disabled="disabled" />
				</label>
			</dd>
		</dl>
	</div>

	<div class="modal-body">
		<h3 class="modal-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attendees"), $_smarty_tpl);?>
</h3>
		<div class="addressDiv bm-calendar-attendee-list">
			<?php if (!$_smarty_tpl->getValue('attendees')) {?>
				<p class="text-secondary mb-0"><i>(<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"none"), $_smarty_tpl);?>
)</i></p>
			<?php } else { ?>
				<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('attendees'), 'person');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('person')->value) {
$foreach0DoElse = false;
?>
				<div class="addressItem" onclick="parent.document.location.href='organizer.addressbook.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
&action=editContact&id=<?php echo $_smarty_tpl->getValue('person')['id'];?>
';">
					<i class="ti ti-user icon icon-sm me-1" aria-hidden="true"></i>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('person')['nachname']), $_smarty_tpl);?>
, <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('person')['vorname']), $_smarty_tpl);?>

				</div>
				<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
			<?php }?>
		</div>
	</div>

	<div class="modal-body">
		<h3 class="modal-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</h3>
		<textarea class="form-control" rows="4" readonly="readonly"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('date')['text']), $_smarty_tpl);?>
</textarea>
	</div>

	<div class="modal-footer bm-calendar-showdate-footer">
		<div class="bm-calendar-showdate-footer-start">
			<?php if ($_smarty_tpl->getValue('attendees')) {?>
			<button type="button" class="btn btn-ghost-primary" onclick="parent.document.location.href='email.compose.php?to=<?php echo $_smarty_tpl->getValue('mailTo');?>
&subject=<?php echo $_smarty_tpl->getValue('mailSubject');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-mail icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mailattendees"), $_smarty_tpl);?>

			</button>
			<?php }?>
		</div>
		<div class="bm-calendar-showdate-footer-actions">
			<button type="button" class="btn btn-ghost-danger" onclick="if(confirm('<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"realdel"), $_smarty_tpl);?>
')) parent.document.location.href='organizer.calendar.php?action=deleteDate&id=<?php echo $_smarty_tpl->getValue('date')['id'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-trash icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"delete"), $_smarty_tpl);?>

			</button>
			<button type="button" class="btn btn-primary" onclick="parent.document.location.href='organizer.calendar.php?action=editDate&id=<?php echo $_smarty_tpl->getValue('date')['id'];
if ($_smarty_tpl->getValue('date')['repeat_flags'] != 0) {?>&jumpbackDate=<?php echo $_smarty_tpl->getValue('date')['startdate'];
}?>&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-pencil icon" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>

			</button>
		</div>
	</div>
</div>

<?php echo '<script'; ?>
>
<!--
	registerLoadAction(initCalendarShowDate);

	function initCalendarShowDate()
	{
		if(typeof parent.setOverlayTitle != 'function')
			return;

		parent.setOverlayTitle(
			"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('date')['title']), $_smarty_tpl);?>
",
			"<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"group"), $_smarty_tpl);?>
: <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('noentities'=>true,'escape'=>true,'value'=>$_smarty_tpl->getValue('groups')[$_smarty_tpl->getValue('date')['group']]['title']), $_smarty_tpl);?>
"
		);
	}
//-->
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:li/dialog.foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
