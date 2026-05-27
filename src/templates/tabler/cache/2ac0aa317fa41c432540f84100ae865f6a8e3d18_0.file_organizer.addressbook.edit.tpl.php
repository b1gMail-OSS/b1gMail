<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:06:17
  from 'file:li/organizer.addressbook.edit.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134c29dfe2f3_41570763',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2ac0aa317fa41c432540f84100ae865f6a8e3d18' => 
    array (
      0 => 'li/organizer.addressbook.edit.tpl',
      1 => 1779633433,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134c29dfe2f3_41570763 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/li';
?><div class="bm-organizer-page bm-organizer-form-page bm-organizer-contact-form">
	<div id="contentHeader" class="contentHeader bm-organizer-header">
		<div class="left">
			<i class="ti ti-address-book icon icon-sm" aria-hidden="true"></i>
			<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"editcontact"), $_smarty_tpl);
} else {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"addcontact"), $_smarty_tpl);
}?>
		</div>
		<div class="right bm-organizer-header-actions">
			<a href="organizer.addressbook.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-sm btn-ghost-secondary">
				<i class="ti ti-arrow-left icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"back"), $_smarty_tpl);?>

			</a>
		</div>
	</div>

	<form name="f1" method="post" class="bm-organizer-form" action="organizer.addressbook.php?action=<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')) {?>saveContact&id=<?php echo $_smarty_tpl->getValue('contact')['id'];
} else { ?>createContact<?php }?>&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" onsubmit="return(checkContactForm(this));">
		<input type="hidden" id="submitAction" name="submitAction" value="" />

		<div class="bm-organizer-form-body">
			<div class="row g-4">
				<div class="col-lg-8">
					<div class="bm-organizer-form-section mb-4">
						<h4 class="bm-organizer-form-section-title">
							<i class="ti ti-id icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"common"), $_smarty_tpl);?>

						</h4>
						<div class="row g-3">
							<div class="col-md-4">
								<label class="form-label" for="anrede"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"salutation"), $_smarty_tpl);?>
</label>
								<select class="form-select" name="anrede" id="anrede">
									<option value=""<?php if (( !true || empty($_smarty_tpl->getValue('contact')['anrede']))) {?> selected="selected"<?php }?>>&nbsp;</option>
									<option value="frau"<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')['anrede'] == 'frau') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mrs"), $_smarty_tpl);?>
</option>
									<option value="herr"<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')['anrede'] == 'herr') {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mr"), $_smarty_tpl);?>
</option>
								</select>
							</div>
							<div class="col-md-4">
								<label class="form-label required" for="vorname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"firstname"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="vorname" id="vorname" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['vorname'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['vorname'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label required" for="nachname"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"surname"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="nachname" id="nachname" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['nachname'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['nachname'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<div class="bm-organizer-form-section-header">
							<h4 class="bm-organizer-form-section-title mb-0">
								<i class="ti ti-user icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"priv"), $_smarty_tpl);?>

							</h4>
							<label class="form-check mb-0" for="default_priv">
								<input type="radio" class="form-check-input" name="default" id="default_priv" value="priv"<?php if (!(true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) || $_smarty_tpl->getValue('contact')['default_address'] != 2) {?> checked="checked"<?php }?> />
								<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"default"), $_smarty_tpl);?>
</span>
							</label>
						</div>
						<div class="row g-3">
							<div class="col-12">
								<label class="form-label" for="strassenr"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"streetnr"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="strassenr" id="strassenr" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['strassenr'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['strassenr'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="plz"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"zip"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="plz" id="plz" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['plz'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['plz'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-8">
								<label class="form-label" for="ort"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"city"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="ort" id="ort" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['ort'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['ort'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="land"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"country"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="land" id="land" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['land'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['land'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="email"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
								<input type="email" class="form-control" name="email" id="email" value="<?php if (!( !true || empty($_REQUEST['email']))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_REQUEST['email']), $_smarty_tpl);
} elseif ((true && (true && null !== ($_smarty_tpl->getValue('contact')['email'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['email'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="tel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"phone"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="tel" id="tel" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['tel'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['tel'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="fax"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fax"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="fax" id="fax" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['fax'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['fax'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="handy"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobile"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="handy" id="handy" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['handy'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['handy'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<div class="bm-organizer-form-section-header">
							<h4 class="bm-organizer-form-section-title mb-0">
								<i class="ti ti-building icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"work"), $_smarty_tpl);?>

							</h4>
							<label class="form-check mb-0" for="default_work">
								<input type="radio" class="form-check-input" name="default" id="default_work" value="work"<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['default_address'] ?? null))) && $_smarty_tpl->getValue('contact')['default_address'] == 2) {?> checked="checked"<?php }?> />
								<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"default"), $_smarty_tpl);?>
</span>
							</label>
						</div>
						<div class="row g-3">
							<div class="col-12">
								<label class="form-label" for="work_strassenr"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"streetnr"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="work_strassenr" id="work_strassenr" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_strassenr'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_strassenr'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_plz"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"zip"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="work_plz" id="work_plz" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_plz'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_plz'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-8">
								<label class="form-label" for="work_ort"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"city"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="work_ort" id="work_ort" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_ort'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_ort'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="work_land"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"country"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="work_land" id="work_land" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_land'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_land'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="work_email"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>
</label>
								<input type="email" class="form-control" name="work_email" id="work_email" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_email'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_email'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_tel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"phone"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="work_tel" id="work_tel" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_tel'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_tel'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_fax"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"fax"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="work_fax" id="work_fax" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_fax'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_fax'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-4">
								<label class="form-label" for="work_handy"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"mobile"), $_smarty_tpl);?>
</label>
								<input type="tel" class="form-control" name="work_handy" id="work_handy" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['work_handy'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['work_handy'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
						</div>
					</div>

					<div class="bm-organizer-form-section mb-4">
						<h4 class="bm-organizer-form-section-title">
							<i class="ti ti-dots icon icon-sm me-1" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"misc"), $_smarty_tpl);?>

						</h4>
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label" for="firma"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"company"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="firma" id="firma" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['firma'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['firma'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="position"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"position"), $_smarty_tpl);?>
</label>
								<input type="text" class="form-control" name="position" id="position" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['position'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['position'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label" for="web"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"web"), $_smarty_tpl);?>
</label>
								<input type="url" class="form-control" name="web" id="web" value="<?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['web'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['web'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
							<div class="col-md-6">
								<label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"birthday"), $_smarty_tpl);?>
</label>
								<div class="bm-organizer-datetime">
									<?php if (!( !true || empty($_smarty_tpl->getValue('contact')['geburtsdatum']))) {?>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('time'=>$_smarty_tpl->getValue('contact')['geburtsdatum'],'year_empty'=>"---",'day_empty'=>"---",'month_empty'=>"---",'start_year'=>"-120",'end_year'=>"+0",'prefix'=>"geburtsdatum_",'field_order'=>"DMY"), $_smarty_tpl);?>

									<?php } else { ?>
									<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('html_select_date')->handle(array('time'=>"---",'year_empty'=>"---",'day_empty'=>"---",'month_empty'=>"---",'start_year'=>"-120",'end_year'=>"+0",'prefix'=>"geburtsdatum_",'field_order'=>"DMY"), $_smarty_tpl);?>

									<?php }?>
								</div>
							</div>
							<div class="col-12">
								<label class="form-label" for="kommentar"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"comment"), $_smarty_tpl);?>
</label>
								<textarea class="form-control" name="kommentar" id="kommentar" rows="4"><?php if ((true && (true && null !== ($_smarty_tpl->getValue('contact')['kommentar'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('contact')['kommentar'],'allowEmpty'=>true), $_smarty_tpl);
}?></textarea>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"userpicture"), $_smarty_tpl);?>
</h3>
						</div>
						<div class="card-body text-center">
							<input type="hidden" name="pictureFile" id="pictureFile" value="" />
							<input type="hidden" name="pictureMime" id="pictureMime" value="" />
							<a href="javascript:addrUserPicture(<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')) {
echo $_smarty_tpl->getValue('contact')['id'];
} else { ?>-1<?php }?>);" class="d-inline-block mb-2">
								<span class="avatar avatar-xl bm-organizer-contact-avatar" id="pictureDiv" style="background-image: url(<?php if (!(true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) || !$_smarty_tpl->getValue('contact') || $_smarty_tpl->getValue('contact')['picture'] == '') {
echo $_smarty_tpl->getValue('tpldir');?>
images/li/no_picture.png<?php } else { ?>organizer.addressbook.php?action=addressbookPicture&id=<?php echo $_smarty_tpl->getValue('contact')['id'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');
}?>);"></span>
							</a>
							<div class="text-secondary small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"changepicbyclick"), $_smarty_tpl);?>
</div>
						</div>
					</div>

					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"groupmember"), $_smarty_tpl);?>
</h3>
						</div>
						<div class="card-body">
							<?php if (!$_smarty_tpl->getValue('groups')) {?>
							<div class="text-secondary small"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"nogroups"), $_smarty_tpl);?>
</div>
							<?php } else { ?>
							<div class="d-flex flex-column gap-2 mb-3">
								<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groups'), 'group', false, 'groupID');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('groupID')->value => $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
								<label class="form-check mb-0" for="group_<?php echo $_smarty_tpl->getValue('groupID');?>
">
									<input type="checkbox" class="form-check-input" id="group_<?php echo $_smarty_tpl->getValue('groupID');?>
" name="group_<?php echo $_smarty_tpl->getValue('groupID');?>
"<?php if (!( !true || empty($_smarty_tpl->getValue('group')['member']))) {?> checked="checked"<?php }?> />
									<span class="form-check-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('group')['title'],'cut'=>32), $_smarty_tpl);?>
</span>
								</label>
								<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</div>
							<?php }?>
							<div class="input-group input-group-sm">
								<span class="input-group-text">
									<input type="checkbox" class="form-check-input m-0" id="group_new" name="group_new" aria-label="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"newgroup"), $_smarty_tpl);?>
" />
								</span>
								<input type="text" class="form-control" name="group_new_name" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"newgroup"), $_smarty_tpl);?>
" value="" onchange="this.onkeypress();" onkeypress="EBID('group_new').checked = this.value.length > 0;" />
							</div>
						</div>
					</div>

					<div class="card bm-organizer-form-card mb-4">
						<div class="card-header">
							<h3 class="card-title"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"features"), $_smarty_tpl);?>
</h3>
						</div>
						<div class="card-body">
							<div class="list-group list-group-flush bm-organizer-contact-features">
								<?php if ((true && ($_smarty_tpl->hasVariable('contact') && null !== ($_smarty_tpl->getValue('contact') ?? null))) && $_smarty_tpl->getValue('contact')) {?>
								<a href="javascript:addrFunction('exportVCF');" class="list-group-item list-group-item-action">
									<i class="ti ti-address-book icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"exportvcf"), $_smarty_tpl);?>

								</a>
								<a href="javascript:addrFunction('selfComplete');" class="list-group-item list-group-item-action">
									<i class="ti ti-checkbox icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"complete"), $_smarty_tpl);?>

								</a>
								<a href="javascript:addrFunction('intelliFolder');" class="list-group-item list-group-item-action">
									<i class="ti ti-folder icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"convfolder"), $_smarty_tpl);?>

								</a>
								<a href="javascript:addrFunction('sendMail');" class="list-group-item list-group-item-action">
									<i class="ti ti-mail icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"sendmail"), $_smarty_tpl);?>

								</a>
								<?php } else { ?>
								<a href="javascript:addrImportVCF();" class="list-group-item list-group-item-action">
									<i class="ti ti-upload icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"importvcf"), $_smarty_tpl);?>

								</a>
								<a href="javascript:addrFunction('selfComplete');" class="list-group-item list-group-item-action">
									<i class="ti ti-checkbox icon icon-sm me-2" aria-hidden="true"></i><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"complete"), $_smarty_tpl);?>

								</a>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bm-organizer-form-footer">
				<div class="btn-list">
					<button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"ok"), $_smarty_tpl);?>
</button>
					<button type="reset" class="btn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"reset"), $_smarty_tpl);?>
</button>
					<a href="organizer.addressbook.php?sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="btn btn-ghost-secondary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cancel"), $_smarty_tpl);?>
</a>
				</div>
			</div>
		</div>
	</form>
</div>

<?php if (!( !$_smarty_tpl->hasVariable('jsCode') || empty($_smarty_tpl->getValue('jsCode')))) {
echo $_smarty_tpl->getValue('jsCode');
}
}
}
