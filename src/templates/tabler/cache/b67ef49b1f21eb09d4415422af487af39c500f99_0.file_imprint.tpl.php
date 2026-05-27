<?php
/* Smarty version 5.8.0, created on 2026-05-25 15:46:40
  from 'file:nli/imprint.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a146ee05308d2_42237504',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b67ef49b1f21eb09d4415422af487af39c500f99' => 
    array (
      0 => 'nli/imprint.tpl',
      1 => 1779712350,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:nli/page.open.tpl' => 1,
    'file:nli/page.close.tpl' => 1,
  ),
))) {
function content_6a146ee05308d2_42237504 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
$_smarty_tpl->renderSubTemplate("file:nli/page.open.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
<h1 class="mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contact"), $_smarty_tpl);?>
</h1>

<?php if ($_smarty_tpl->getValue('contactform')) {?>
	<div class="row">
		<div class="col-md-6">
			<p>
				<?php echo $_smarty_tpl->getValue('imprint');?>

			</p>
		</div>
		<div class="col-md-6">
			<form action="index.php?action=imprint" method="post">
				<input type="hidden" name="do" value="submitContactForm" />

				<div class="panel panel-default">
					<div class="panel-heading panel-title">
						<span class="glyphicon glyphicon-comment"></span>
						<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"contactform"), $_smarty_tpl);?>

					</div>
					<div class="panel-body">
						<?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
						<div class="alert alert-success" role="alert"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"thankyou"), $_smarty_tpl);?>
.</strong> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cform_sent"), $_smarty_tpl);?>
</div>
						<?php } else { ?>
					
						<?php if ((true && ($_smarty_tpl->hasVariable('errorMsg') && null !== ($_smarty_tpl->getValue('errorMsg') ?? null)))) {?><div class="alert alert-danger" role="alert"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"error"), $_smarty_tpl);?>
:</strong> <?php echo $_smarty_tpl->getValue('errorMsg');?>
</div><?php }?>

						<?php if ($_smarty_tpl->getValue('contactform_name')) {?><div class="form-group">
							<label class="control-label" for="name">
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"name"), $_smarty_tpl);?>

								<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
								<input type="text" class="form-control" required="true" name="name" id="name" value="<?php if ((true && (true && null !== ($_POST['name'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['name'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
						</div><?php }?>
						<div class="form-group">
							<label class="control-label" for="email">
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>

								<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
								<input type="text" class="form-control" required="true" name="email" id="email" value="<?php if ((true && (true && null !== ($_POST['email'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['email'],'allowEmpty'=>true), $_smarty_tpl);
}?>" />
							</div>
						</div>
						<?php if ($_smarty_tpl->getValue('contactform_subject')) {?><div class="form-group">
							<label class="control-label" for="subject">
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subject"), $_smarty_tpl);?>

								<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
							</label>
							<select class="form-control" id="subject" name="subject">
								<option value="">--- <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"pleasechose"), $_smarty_tpl);?>
 ---</option>
								<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('contactform_subjects'), 'subject');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('subject')->value) {
$foreach0DoElse = false;
?>
								<option value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('subject')), $_smarty_tpl);?>
"<?php if ((true && (true && null !== ($_POST['subject'] ?? null))) && $_POST['subject'] == $_smarty_tpl->getValue('subject')) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('subject')), $_smarty_tpl);?>
</option>
								<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
							</select>
						</div><?php }?>
						<div class="form-group">
							<label class="control-label" for="text">
								<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"message"), $_smarty_tpl);?>

								<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
							</label>
							<textarea class="form-control" name="text" id="text" rows="6" required="true"><?php if ((true && (true && null !== ($_POST['text'] ?? null)))) {
echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['text'],'allowEmpty'=>true), $_smarty_tpl);
}?></textarea>
						</div>
						<div class="row">
							<?php if ($_smarty_tpl->getValue('captchaInfo')['hasOwnInput']) {?>
							<div class="col-md-12">
								<div class="form-group" id="captchaContainer">
									<label class="control-label">
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>

										<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
									</label>
									<?php echo $_smarty_tpl->getValue('captchaHTML');?>

								</div>
							</div>
							<?php } else { ?>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="safecode">
										<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>

										<span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
									</label>
									<input type="text" class="form-control" required="true" name="safecode" id="safecode" />
								</div>
							</div>
							<div class="col-md-6" id="captchaContainer">
								<?php echo $_smarty_tpl->getValue('captchaHTML');?>

							</div>
							<?php }?>
						</div>

						<button type="submit" class="btn btn-success"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"submit"), $_smarty_tpl);?>
</button>
						<?php }?>
					</div>
				</div>

				<?php if ((true && ($_smarty_tpl->hasVariable('invalidFields') && null !== ($_smarty_tpl->getValue('invalidFields') ?? null)))) {
echo '<script'; ?>
>
				<!--
					$(document).ready(function() {
					<?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('invalidFields'), 'field');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('field')->value) {
$foreach1DoElse = false;
?>
					markFieldAsInvalid('<?php echo $_smarty_tpl->getValue('field');?>
');
					<?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
					});
				//-->
				<?php echo '</script'; ?>
><?php }?>
			</form>
		</div>
	</div>
<?php } else { ?>
<div class="text-secondary">
	<?php echo $_smarty_tpl->getValue('imprint');?>

</div>
<?php }
$_smarty_tpl->renderSubTemplate("file:nli/page.close.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
