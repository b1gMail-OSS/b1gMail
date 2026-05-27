<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:49:26
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.faq.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a26771654_51044981',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '045bd9663de22f99abc4f548f28ba5842e7376b3' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.faq.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a26771654_51044981 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div id="contentHeader">
    <div class="left">
        <img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/ico_faq.png" width="16" height="16" border="0" alt="" align="absmiddle" />
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSidebar"), $_smarty_tpl);?>

    </div>
</div>

<div class="scrollContainer">
    <?php if ($_smarty_tpl->getValue('ItemContent')['State'] == 'Off') {?>
        <div class="draftNote" id="draftNote">
            <div><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQEditMode"), $_smarty_tpl);?>
</div>
            <br class="clear" />
        </div>
    <?php }?>

    <div style="padding: 20px;">
    <?php if ($_smarty_tpl->getValue('ItemID')) {?>
        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBreadcrumb"), $_smarty_tpl);?>
: <a href="prefs.php?action=faq&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">FAQ</a> > <a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getValue('DepName');?>
</a>

        <h3><?php echo $_smarty_tpl->getValue('ItemContent')['Name'];?>
</h3>
        <?php echo $_smarty_tpl->getValue('ItemContent')['Content'];?>


        <?php if ($_smarty_tpl->getValue('voteMSG')) {?>
            <div class="infostate-info" style="margin-top: 40px;">
                <?php echo $_smarty_tpl->getValue('voteMSG');?>

            </div>
        <?php } else { ?>
            <form action="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');
if ($_smarty_tpl->getValue('KatID')) {?>cat=<?php echo $_smarty_tpl->getValue('KatID');
}?>&item=<?php echo $_smarty_tpl->getValue('ItemID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" method="post">
                <div class="infostate-info" style="margin-top: 40px;">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVote"), $_smarty_tpl);?>
<br />
                    <button type="submit" name="faqvote" value="yes" class="btn btn-xs btn-default"><img src="<?php echo $_smarty_tpl->getValue('selfurl');?>
plugins/templates/images/supportsystem_handup.png" width="14" height="14" style="margin-top: 0px;" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVoteYes"), $_smarty_tpl);?>
" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVoteYes"), $_smarty_tpl);?>
</button> &nbsp;
                    <button type="submit" name="faqvote" value="no" class="btn btn-xs btn-default"><img src="<?php echo $_smarty_tpl->getValue('selfurl');?>
plugins/templates/images/supportsystem_handdown.png" width="14" height="14" title="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVoteNo"), $_smarty_tpl);?>
" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVoteNo"), $_smarty_tpl);?>
</button>
                </div>
            </form>
        <?php }?>
    <?php } else { ?>
        <?php if ($_smarty_tpl->getValue('ShowDepList')) {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFrontSelectDep"), $_smarty_tpl);?>

            <div class="row">
                <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('Departments')) > 1) {?>
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach1DoElse = false;
?>
                    <div class="col-md-6"><a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('depart')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('depart')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('depart')['Description'];?>
</div></a></div>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php } else { ?>
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach2DoElse = false;
?>
                    <?php echo '<script'; ?>
>
                        window.location.href = "prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('depart')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
";
                    <?php echo '</script'; ?>
>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php }?>
            </div>
        <?php } else { ?>

            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBreadcrumb"), $_smarty_tpl);?>
: <a href="prefs.php?action=faq&sid=<?php echo $_smarty_tpl->getValue('sid');?>
">FAQ</a> > <a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getValue('DepName');?>
</a>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('faq_breadcrumb'), 'category', false, NULL, 'category', array (
));
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('category')->value) {
$foreach3DoElse = false;
?>
                <?php if ($_smarty_tpl->getValue('tcsup_categoryActive') == $_smarty_tpl->getValue('category')['ID']) {?>
                    <b> &gt; <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('category')['Name']), $_smarty_tpl);?>
</b>
                <?php } else { ?>
                    &gt; <a href="<?php echo $_smarty_tpl->getValue('pageURL');?>
&do=faq&amp;cat=<?php echo $_smarty_tpl->getValue('category')['ID'];?>
&amp;sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('category')['Name']), $_smarty_tpl);?>
</a>
                <?php }?>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

            <div class="row">
                <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllKats')) > 1) {?>
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllKats'), 'kats');
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('kats')->value) {
$foreach4DoElse = false;
?>
                        <div class="col-md-6"><a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');?>
&cat=<?php echo $_smarty_tpl->getValue('kats')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('kats')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('kats')['Description'];?>
</div></a></div>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php } else { ?>
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllKats'), 'kats');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('kats')->value) {
$foreach5DoElse = false;
?>
                        <div class="col-md-12"><a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');?>
&cat=<?php echo $_smarty_tpl->getValue('kats')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('kats')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('kats')['Description'];?>
</div></a></div>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php }?>
            </div>

            <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllKats')) == 0 && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllContent')) == 0) {?>
                <div class="col-md-12"><a href="javascript:history.back()" class="depListContent"><div class="depListNoItems"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQNoItems"), $_smarty_tpl);?>
</div></a></div>
            <?php }?>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllContent'), 'con');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('con')->value) {
$foreach6DoElse = false;
?>
                <a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('DepID');
if ($_smarty_tpl->getValue('KatID')) {?>cat=<?php echo $_smarty_tpl->getValue('KatID');
}?>&item=<?php echo $_smarty_tpl->getValue('con')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="depListContent"><div><strong><?php echo $_smarty_tpl->getValue('con')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')(preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->getValue('con')['Content']),300);?>
</div></a>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <?php if ($_smarty_tpl->getValue('ShowTicketSystem') == 'yes') {?>
                <a href="start.php?action=supportsystem&do=new&sid=<?php echo $_smarty_tpl->getValue('sid');?>
" class="depListContent"><div class="depListNewTicket"><img src="<?php echo $_smarty_tpl->getValue('selfurl');?>
plugins/templates/images/supportsystem_tickets.png" width="50" height="50" title="" /><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQNotSuitable"), $_smarty_tpl);?>
</div></a>
            <?php }?>
        <?php }?>
    <?php }?>
    </div>
</div><?php }
}
