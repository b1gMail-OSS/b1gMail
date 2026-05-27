<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:05:00
  from 'file:li/organizer.addressbook.show.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134bdcd08f91_66805342',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd8f9ff62dd820c5b8384bfa9b1fd6390de18f4a3' => 
    array (
      0 => 'li/organizer.addressbook.show.tpl',
      1 => 1779633343,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134bdcd08f91_66805342 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-address-detail">
	<div class="bm-organizer-address-detail-body">
		<div class="d-flex align-items-start gap-3 mb-4">
			<span class="avatar avatar-lg bm-organizer-address-avatar" style="background-image: url(<?php if (!$_smarty_tpl->getValue('contact') || $_smarty_tpl->getValue('contact')['picture'] == '') {
echo $_smarty_tpl->getValue('tpldir');?>
images/li/no_picture.png<?php } else { ?>organizer.addressbook.php?action=addressbookPicture&id=<?php echo $_smarty_tpl->getValue('contact')['id'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');
}?>);"></span>
			<div class="min-w-0">
				<h3 class="mb-1">
					<?php if (!$_smarty_tpl->getValue('contact')['vorname'] && !$_smarty_tpl->getValue('contact')['nachname'] && $_smarty_tpl->getValue('contact')['firma']) {?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['firma']), $_smarty_tpl);?>

					<?php } else { ?>
					<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['vorname']), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['nachname']), $_smarty_tpl);?>

					<?php }?>
				</h3>
				<?php if (($_smarty_tpl->getValue('contact')['vorname'] || $_smarty_tpl->getValue('contact')['nachname']) && $_smarty_tpl->getValue('contact')['firma']) {?>
				<div class="text-secondary">
					<?php if ($_smarty_tpl->getValue('contact')['position']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['position']), $_smarty_tpl);?>
, <?php }
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['firma']), $_smarty_tpl);?>

				</div>
				<?php }?>
			</div>
		</div>

		<table class="table table-sm table-borderless bm-organizer-address-detail-table">
		<tbody>
		<?php if ($_smarty_tpl->getValue('contact')['email']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</th>
			<td><a href="email.compose.php?to=<?php echo $_smarty_tpl->getValue('privEmailTo');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['email']), $_smarty_tpl);?>
</a></td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['tel']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"phone"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['tel']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['fax']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fax"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['fax']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['handy']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobile"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['handy']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['strassenr'] || $_smarty_tpl->getValue('contact')['ort'] || $_smarty_tpl->getValue('contact')['plz'] || $_smarty_tpl->getValue('contact')['land']) {?>
		<tr class="bm-organizer-address-detail-section">
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priv"), $_smarty_tpl);?>
</th>
			<td>
				<?php if ($_smarty_tpl->getValue('contact')['strassenr']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['strassenr']), $_smarty_tpl);?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('contact')['ort'] || $_smarty_tpl->getValue('contact')['plz']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['plz']), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['ort']), $_smarty_tpl);?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('contact')['land']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['land']), $_smarty_tpl);
}?>
			</td>
		</tr>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('contact')['work_email']) {?>
		<tr class="bm-organizer-address-detail-section">
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</th>
			<td><a href="email.compose.php?to=<?php echo $_smarty_tpl->getValue('workEmailTo');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_email']), $_smarty_tpl);?>
</a></td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['work_tel']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"phone"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_tel']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['work_fax']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fax"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_fax']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['work_handy']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobile"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_handy']), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['work_strassenr'] || $_smarty_tpl->getValue('contact')['work_ort'] || $_smarty_tpl->getValue('contact')['work_plz'] || $_smarty_tpl->getValue('contact')['work_land']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"work"), $_smarty_tpl);?>
</th>
			<td>
				<?php if ($_smarty_tpl->getValue('contact')['work_strassenr']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_strassenr']), $_smarty_tpl);?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('contact')['work_ort'] || $_smarty_tpl->getValue('contact')['work_plz']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_plz']), $_smarty_tpl);?>
 <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_ort']), $_smarty_tpl);?>
<br /><?php }?>
				<?php if ($_smarty_tpl->getValue('contact')['work_land']) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_land']), $_smarty_tpl);
}?>
			</td>
		</tr>
		<?php }?>

		<?php if ($_smarty_tpl->getValue('contact')['geburtsdatum']) {?>
		<tr class="bm-organizer-address-detail-section">
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"birthday"), $_smarty_tpl);?>
</th>
			<td><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('contact')['geburtsdatum'],'format'=>"%d. %B %Y"), $_smarty_tpl);?>
</td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['web']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"web"), $_smarty_tpl);?>
</th>
			<td><a target="_blank" rel="noopener noreferrer" href="deref.php?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['web']), $_smarty_tpl);?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['web']), $_smarty_tpl);?>
</a></td>
		</tr>
		<?php }?>
		<?php if ($_smarty_tpl->getValue('contact')['kommentar']) {?>
		<tr>
			<th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"notes"), $_smarty_tpl);?>
</th>
			<td class="bm-organizer-address-notes"><?php echo $_smarty_tpl->getValue('contact')['kommentar'];?>
</td>
		</tr>
		<?php }?>
		</tbody>
		</table>
	</div>

	<div class="contentFooter bm-organizer-footer">
		<div class="right bm-organizer-footer-tools">
			<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='organizer.addressbook.php?action=editContact&id=<?php echo $_smarty_tpl->getValue('contact')['id'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
';">
				<i class="ti ti-pencil icon icon-sm me-1" aria-hidden="true"></i>
				<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"edit"), $_smarty_tpl);?>

			</button>
		</div>
	</div>
</div>
<?php }
}
