<?php
/* Smarty version 5.8.0, created on 2026-05-25 08:26:09
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.nli.support.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1407a1025605_17880393',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64d880dd14432d21158c7ec9081e64032a89e5fa' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.nli.support.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1407a1025605_17880393 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="container">
    <div class="page-header"><h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFrontSupportTitle"), $_smarty_tpl);?>
</h1></div>

    <?php if ($_smarty_tpl->getValue('ShowDepList')) {?>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFrontSelectDep"), $_smarty_tpl);?>

        <?php if ($_smarty_tpl->getValue('Departments')) {?>
            <div class="row">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart', false, NULL, 'foo', array (
));
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach0DoElse = false;
?>
                <div class="<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('Departments')) > 1) {?>col-md-6<?php } else { ?>col-md-12<?php }?>"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
ticket/<?php echo $_smarty_tpl->getValue('depart')['Short'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=support&depshort=<?php echo $_smarty_tpl->getValue('depart')['Short'];
}?>" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('depart')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('depart')['Description'];?>
</div></a></div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            </div>
        <?php }?>
    <?php } else { ?>
        <form action="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
ticket/<?php echo $_smarty_tpl->getValue('DepShort');
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=support&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
}?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="do" value="submitContactForm" />

            <?php if ($_smarty_tpl->getValue('success')) {?>
                <div class="alert alert-success" role="alert"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"thankyou"), $_smarty_tpl);?>
.</strong> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"cform_sent"), $_smarty_tpl);?>
</div>
            <?php } else { ?>

                <?php if ($_smarty_tpl->getValue('errorMsg')) {?>
                    <div class="alert alert-danger" role="alert"><strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"error"), $_smarty_tpl);?>
:</strong> <?php echo $_smarty_tpl->getValue('errorMsg');?>
</div>
                <?php } else { ?>
                    <div class="alert alert-warning" role="alert"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemNewTicketInfo"), $_smarty_tpl);?>
</div>
                <?php }?>

                <div class="form-group">
                    <label class="control-label" for="name">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketDepartment"), $_smarty_tpl);?>

                    </label>
                    <input type="text" class="form-control" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('Department')), $_smarty_tpl);?>
" disabled />
                </div>
                <div class="form-group">
                    <label class="control-label" for="name">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"name"), $_smarty_tpl);?>

                        <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                    </label>
                    <input type="text" class="form-control" required="true" name="name" id="name" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['name'],'allowEmpty'=>true), $_smarty_tpl);?>
" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="email">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"email"), $_smarty_tpl);?>

                        <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                    </label>
                    <input type="text" class="form-control" required="true" name="email" id="email" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['email'],'allowEmpty'=>true), $_smarty_tpl);?>
" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="subject">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subject"), $_smarty_tpl);?>

                        <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                    </label>
                    <input type="text" class="form-control" required="true" name="subject" id="subject" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['subject'],'allowEmpty'=>true), $_smarty_tpl);?>
" />
                </div>

                <?php if (is_array($_smarty_tpl->getValue('DepartmentFields'))) {?>
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('DepartmentFields'), 'field');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('field')->value) {
$foreach1DoElse = false;
?>
                    <div class="form-group">
                        <label class="control-label" for="subject">
                            <?php echo $_smarty_tpl->getValue('field')['Name'];?>

                            <?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>
                                <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                            <?php }?>
                        </label>
                        <?php if ($_smarty_tpl->getValue('field')['Type'] == 'text') {?>
                            <input type="text" class="form-control" <?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>required="true"<?php }?> name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" value="" />
                        <?php } elseif ($_smarty_tpl->getValue('field')['Type'] == 'email') {?>
                            <input type="email" class="form-control" <?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>required="true"<?php }?> name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" value="" />
                        <?php } elseif ($_smarty_tpl->getValue('field')['Type'] == 'checkbox') {?>
                            <br /><label class="control-label"><input type="checkbox" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" <?php if ("addfield_".((string)$_smarty_tpl->getValue('field')['ID'])) {?> == 'On'}checked="checked"<?php }?> />&nbsp; <strong><?php echo $_smarty_tpl->getValue('field')['Value'];?>
</strong></label>
                        <?php } elseif ($_smarty_tpl->getValue('field')['Type'] == 'radio') {?>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('field')['Value'], 'value');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('value')->value) {
$foreach2DoElse = false;
?>
                                <div class="radio"><label><input type="radio" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" value="<?php echo $_smarty_tpl->getValue('value');?>
">&nbsp; <strong><?php echo $_smarty_tpl->getValue('value');?>
</strong></label></div>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        <?php } elseif ($_smarty_tpl->getValue('field')['Type'] == 'select') {?>
                            <select class="form-control" name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
">
                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('field')['Value'], 'value');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('value')->value) {
$foreach3DoElse = false;
?>
                                    <option value="<?php echo $_smarty_tpl->getValue('value');?>
"><?php echo $_smarty_tpl->getValue('value');?>
</option>
                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                            </select>
                        <?php }?>
                    </div>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php }?>

                <?php if ($_smarty_tpl->getValue('FileUpload') == 'NLI') {?>
                    <div class="form-group">
                        <label class="control-label" for="text">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>

                        </label>
                        <input type="file" id="attachments" name="attachments[]" class="form-control" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>
" multiple />
                    </div>
                <?php }?>

                <div class="form-group">
                    <label class="control-label" for="text">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"message"), $_smarty_tpl);?>

                        <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                    </label>
                    <textarea class="form-control" name="text" id="text" rows="6" required="true"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_POST['text'],'allowEmpty'=>true), $_smarty_tpl);?>
</textarea>
                </div>

                <?php if ($_smarty_tpl->getValue('TicketCaptcha') == 'yes') {?>
                    <?php if ($_smarty_tpl->getValue('captchaInfo')['hasOwnInput']) {?>
                        <div class="form-group" id="captchaContainer">
                            <label class="control-label">
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>

                                <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                            </label>
                            <?php echo $_smarty_tpl->getValue('captchaHTML');?>

                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label class="control-label" for="safecode">
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"safecode"), $_smarty_tpl);?>

                                <span class="required"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"required"), $_smarty_tpl);?>
</span>
                            </label>
                            <div id="captchaContainer" style="margin-bottom: 10px;">
                                <?php echo $_smarty_tpl->getValue('captchaHTML');?>

                            </div>
                            <input type="text" class="form-control" required="true" name="safecode" id="safecode" />
                        </div>
                    <?php }?>
                <?php }?>

                <button type="submit" class="btn btn-success"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBTNNewTicket"), $_smarty_tpl);?>
</button>
            <?php }?>

            <?php if ($_smarty_tpl->getValue('invalidFields')) {
echo '<script'; ?>
 language="javascript">
                <!--
                $(document).ready(function() {
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('invalidFields'), 'field');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('field')->value) {
$foreach4DoElse = false;
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
    <?php }?>
</div><?php }
}
