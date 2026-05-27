<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:55:41
  from 'file:li/organizer.notes.edit.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133b9dcf0037_65368729',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a8c25ae3eb72ce7c3205970a854419d9a46a154c' => 
    array (
      0 => 'li/organizer.notes.edit.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133b9dcf0037_65368729 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-form-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-notes icon icon-sm" aria-hidden="true"></i>
			<?php if ((true && ($_smarty_tpl->hasVariable('note') && null !== ($_smarty_tpl->getValue('note') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editnote"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addnote"), $_smarty_tpl);
}?>
		</div>
	</div>

	<form name="f1" method="post" action="organizer.notes.php?action=<?php if ((true && ($_smarty_tpl->hasVariable('note') && null !== ($_smarty_tpl->getValue('note') ?? null)))) {?>saveNote&id=<?php echo $_smarty_tpl->getValue('note')['id'];
} else { ?>createNote<?php }?>&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="card bm-organizer-form-card" onsubmit="return(checkNoteForm(this));">
		<div class="card-body">
			<h3 class="card-title mb-4"><?php if ((true && ($_smarty_tpl->hasVariable('note') && null !== ($_smarty_tpl->getValue('note') ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editnote"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addnote"), $_smarty_tpl);
}?></h3>

			<div class="row g-3">
				<div class="col-md-4">
					<label class="form-label" for="priority"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priority"), $_smarty_tpl);?>
</label>
					<select class="form-select" name="priority" id="priority">
						<option value="1"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('note')['priority'] ?? null))) && $_smarty_tpl->getValue('note')['priority'] == 1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_1"), $_smarty_tpl);?>
</option>
						<option value="0"<?php if (( !true || empty($_smarty_tpl->getValue('note')['id'])) || ( !true || empty($_smarty_tpl->getValue('note')['priority']))) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_0"), $_smarty_tpl);?>
</option>
						<option value="-1"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('note')['priority'] ?? null))) && $_smarty_tpl->getValue('note')['priority'] == -1) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"prio_-1"), $_smarty_tpl);?>
</option>
					</select>
				</div>

				<div class="col-12">
					<label class="form-label required" for="text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"text"), $_smarty_tpl);?>
</label>
					<textarea class="form-control" name="text" id="text" rows="12"><?php if ((true && (true && null !== ($_smarty_tpl->getValue('note')['text'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('note')['text'],'allowEmpty'=>true), $_smarty_tpl);
}?></textarea>
				</div>
			</div>

			<div class="btn-list mt-4">
				<button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
				<button type="reset" class="btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
</button>
			</div>
		</div>
	</form>
</div>
<?php }
}
