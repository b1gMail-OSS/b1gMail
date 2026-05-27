<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:14:40
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.support.new.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134e205854a2_67743405',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '40648985aa7ea08555e406d42754f070a217a134' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.support.new.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134e205854a2_67743405 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><form name="f1" method="post" action="start.php?action=supportsystem&do=new&dep=<?php echo $_smarty_tpl->getValue('tdepart');?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
" autocomplete="off" onreset="if(!askReset()) return(false);editor.reset();">

    <div id="contentHeader">
        <div class="left">
            <img src="plugins/templates/images/supportsystem_tickets.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemNewTicket"), $_smarty_tpl);?>

        </div>
    </div>

    <div class="bigForm withBottomBar" style="overflow-y:auto">
        <div class="draftNote" id="draftNote">
            <div><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemNewTicketInfo"), $_smarty_tpl);?>
<br /><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemNewTicketInfoAttachment"), $_smarty_tpl);?>
</div>
            <br class="clear" />
        </div>
        <div class="previewMailHeader" id="composeHeader">
            <table class="lightTable">
                <?php if ($_smarty_tpl->getValue('MSG')) {?>
                <tr>
                    <td colspan="3"><?php echo $_smarty_tpl->getValue('MSG');?>
</td>
                </tr>
                <?php }?>
                <tr>
                    <th width="120">* <label for="tdepart"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketDepartment"), $_smarty_tpl);?>
:</label></th>
                    <td>
                        <select name="tdepart" id="tdepart" onchange="document.location.href=this.value" style="width:100%;">
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach0DoElse = false;
?>
                                <option value="start.php?action=supportsystem&do=new&dep=<?php echo $_smarty_tpl->getValue('depart')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
" <?php if ($_smarty_tpl->getValue('depart')['ID'] == $_smarty_tpl->getValue('tdepart')) {?>selected<?php }?>><?php echo $_smarty_tpl->getValue('depart')['Name'];?>
</option>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </select>
                    </td>
                    <td width="140">&nbsp;</td>
                </tr>

                <tr>
                    <th>* <label for="tpriority"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsPriority"), $_smarty_tpl);?>
:</label></th>
                    <td>
                        <select name="tpriority" id="tpriority" style="width:100%;">
                            <option value="High" <?php if ('High' == $_smarty_tpl->getValue('tpriority')) {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsPriorityHigh"), $_smarty_tpl);?>
</option>
                            <option value="Normal" <?php if ('Normal' == $_smarty_tpl->getValue('tpriority')) {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsPriorityNormal"), $_smarty_tpl);?>
</option>
                            <option value="Low" <?php if ('Low' == $_smarty_tpl->getValue('tpriority')) {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsPriorityLow"), $_smarty_tpl);?>
</option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>

                <tr>
                    <th>* <label for="tsubject"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"subject"), $_smarty_tpl);?>
:</label></th>
                    <td><input type="text" name="tsubject" id="tsubject" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('tsubject')), $_smarty_tpl);?>
" style="width:100%;" /></td>
                    <td>&nbsp;</td>
                </tr>

                <?php if (is_array($_smarty_tpl->getValue('DepartmentFields'))) {?>
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('DepartmentFields'), 'field');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('field')->value) {
$foreach1DoElse = false;
?>
                        <tr>
                            <th><?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>* <?php }?><label for="tsubject"><?php echo $_smarty_tpl->getValue('field')['Name'];?>
:</label></th>
                            <td>
                                <?php if ($_smarty_tpl->getValue('field')['Type'] == 'text') {?>
                                    <input type="text" <?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>required="true"<?php }?> name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" value="" style="width:100%;" />
                                <?php } elseif ($_smarty_tpl->getValue('field')['Type'] == 'email') {?>
                                    <input type="email" <?php if ($_smarty_tpl->getValue('field')['Mandatory'] == 'Yes') {?>required="true"<?php }?> name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" value="" style="width:100%;" />
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
                                    <select name="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" id="addfield_<?php echo $_smarty_tpl->getValue('field')['ID'];?>
" style="width:100%;">
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
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php }?>

                <?php if ($_smarty_tpl->getValue('FileUpload') == 'LI' || $_smarty_tpl->getValue('FileUpload') == 'NLI') {?>
                <tr>
                    <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"attachments"), $_smarty_tpl);?>
:</th>
                    <td>
                        <input type="hidden" name="attachments" value="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('mail')['attachments'],'allowEmpty'=>true), $_smarty_tpl);?>
" id="attachments" />
                        <div id="attachmentList"></div>
                    </td>
                    <td valign="top">
                        <button onclick="javascript:addAttachment('<?php echo $_smarty_tpl->getValue('sid');?>
')" type="button">
                            <i class="fa fa-plus-circle"></i>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"add"), $_smarty_tpl);?>

                        </button>
                    </td>
                </tr>
                <?php }?>
            </table>
        </div>

        <div id="composeText" style="width:100%;position:absolute;">
            <textarea class="composeTextarea<?php if ($_smarty_tpl->getValue('lineSep')) {?> lineSep<?php }?>" name="tmessage" id="tmessage" style="width:100%;height:100%;font-family:arial;"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('allowEmpty'=>true,'value'=>$_smarty_tpl->getValue('mail')['text']), $_smarty_tpl);?>
</textarea>
            <input type="hidden" name="textMode" value="html" />
            <?php echo '<script'; ?>
 language="javascript" src="./clientlib/wysiwyg.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/wysiwyg.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 type="text/javascript" src="./clientlib/ckeditor/ckeditor.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"../../clientlib/ckeditor/ckeditor.js"), $_smarty_tpl);?>
"><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 language="javascript">
                <!--
                var editor = new htmlEditor('tmessage', '<?php echo $_smarty_tpl->getValue('tpldir');?>
images/editor/');
                editor.modeField = 'textMode';
                editor.onReady = function()
                    {
                    editor.start();
                    }
                editor.init();
                //-->
            <?php echo '</script'; ?>
>
        </div>
    </div>

    <div id="contentFooter">
        <div class="right">
            <button class="primary" type="submit" name="tsend" value="new">
                <i class="fa fa-ticket"></i>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBTNNewTicket"), $_smarty_tpl);?>

            </button>
        </div>
    </div>

</form>

<?php echo '<script'; ?>
 src="./clientlib/dndupload.js?<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('fileDateSig')->handle(array('file'=>"./clientlib/dndupload.js"), $_smarty_tpl);?>
" language="javascript" type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript">
    <!--
    registerLoadAction(generateAttachmentList);
    registerLoadAction(composeSizer);
    initDnDUpload(EBID('mainContent'), 'start.php?action=supportsystem&do=new&action=uploadDnDAttachment&sid=' + currentSID, false, dndAttachmentUploaded, dndAttachmentURLAddition);
    //-->
<?php echo '</script'; ?>
>
<?php }
}
