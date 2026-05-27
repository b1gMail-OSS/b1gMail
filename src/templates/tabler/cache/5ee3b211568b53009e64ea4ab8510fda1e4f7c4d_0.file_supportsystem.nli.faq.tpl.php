<?php
/* Smarty version 5.8.0, created on 2026-05-25 08:26:13
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.nli.faq.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1407a5ed8859_89940721',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ee3b211568b53009e64ea4ab8510fda1e4f7c4d' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.nli.faq.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1407a5ed8859_89940721 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><ol class="container">
    <div class="page-header"><h1><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSidebar"), $_smarty_tpl);?>
</h1></div>

    <?php if ($_smarty_tpl->getValue('SearchContent')) {?>
        <form action="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq<?php } else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq<?php }?>" method="post" style="margin-bottom: 20px;">
            <div class="input-group">
                <input type="text" class="form-control" name="faqsearch" id="faqsearch" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSearch"), $_smarty_tpl);?>
" value="<?php echo $_smarty_tpl->getValue('SearchContent');?>
" required>
                <span class="input-group-btn">
                <button type="submit" name="faqsearchstart" id="faqsearchstart" class="btn btn-block btn-default"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSearchBTN"), $_smarty_tpl);?>
</button>
              </span>
            </div>
        </form>

        <div class="row">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllContent'), 'con');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('con')->value) {
$foreach0DoElse = false;
?>
                <div class="col-md-12"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('con')['DepShort'];?>
/<?php echo $_smarty_tpl->getValue('con')['Url'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
if ($_smarty_tpl->getValue('KatID')) {?>cat=<?php echo $_smarty_tpl->getValue('KatID');
}?>&item=<?php echo $_smarty_tpl->getValue('con')['ID'];
}?>" class="depListContent"><div><strong><?php echo $_smarty_tpl->getValue('con')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('con')['Content'],300);?>
</div></a></div>
            <?php
}
if ($foreach0DoElse) {
?>
                <div class="col-md-12"><a href="javascript:history.back()" class="depListContent"><div class="depListNoItems"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSearchNoItems"), $_smarty_tpl);?>
</div></a></div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>

            <?php if ($_smarty_tpl->getValue('ShowTicketSystem') == 'yes') {?>
                <div class="col-md-12"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
ticket<?php if ($_smarty_tpl->getValue('DepShort')) {?>/<?php echo $_smarty_tpl->getValue('DepShort');
}
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=support<?php if ($_smarty_tpl->getValue('DepShort')) {?>&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
}
}?>" class="depListContent"><div class="depListNewTicket"><img src="<?php echo $_smarty_tpl->getValue('selfurl');?>
plugins/templates/images/supportsystem_tickets.png" width="64" height="64" title="" /><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQNotSuitable"), $_smarty_tpl);?>
</div></a></div>
            <?php }?>
        </div>
    <?php } elseif ($_smarty_tpl->getValue('ItemID')) {?>
        <ol class="breadcrumb">
            <li><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBreadcrumb"), $_smarty_tpl);?>
: <a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq<?php } else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq<?php }?>">FAQ</a></li>
            <?php if (!$_smarty_tpl->getValue('ItemUrl')) {?>
                <li class="active"><?php echo $_smarty_tpl->getValue('DepName');?>
</li>
            <?php } else { ?>
                <li><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
}?>"><?php echo $_smarty_tpl->getValue('DepName');?>
</a></li>
            <?php }?>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('faq_breadcrumb'), 'item', false, 'key');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('item')->value) {
$foreach1DoElse = false;
?>
                <?php if ($_smarty_tpl->getValue('ItemUrl') == $_smarty_tpl->getValue('item')['Url']) {?>
                    <li class="active"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['Name']), $_smarty_tpl);?>
</li>
                <?php } else { ?>
                    <li><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');?>
/<?php echo $_smarty_tpl->getValue('item')['Url'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');?>
&url=<?php echo $_smarty_tpl->getValue('item')['Url'];
}?>"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['Name']), $_smarty_tpl);?>
</a></li>
                <?php }?>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </ol>

        <?php if ($_smarty_tpl->getValue('ItemContent')['State'] == 'Off') {?>
            <div class="alert alert-warning">
                <span class="glyphicon glyphicon-info-sign"></span>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQEditMode"), $_smarty_tpl);?>

            </div>
        <?php }?>

        <h3><?php echo $_smarty_tpl->getValue('ItemContent')['Name'];?>
</h3>
            <?php echo $_smarty_tpl->getValue('ItemContent')['Content'];?>


        <?php if ($_smarty_tpl->getValue('voteMSG')) {?>
            <div class="alert alert-info" style="margin-top: 40px;">
                <?php echo $_smarty_tpl->getValue('voteMSG');?>

            </div>
        <?php } else { ?>
            <form action="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');?>
/<?php echo $_smarty_tpl->getValue('ItemUrl');
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');?>
&item=<?php echo $_smarty_tpl->getValue('ItemID');
}?>" method="post">
                <div class="alert alert-info" style="margin-top: 40px;">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQVote"), $_smarty_tpl);?>
<br />
                    <input type="hidden" class="form-control hpot" width="100%;" name="faqvoteopt" id="faqvoteopt" value="<?php echo $_smarty_tpl->getValue('VoteHoneypot');?>
" />
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
        <form action="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php } else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq<?php }?>" method="post" style="margin-bottom: 20px;">
            <div class="input-group">
                <input type="text" class="form-control" name="faqsearch" id="faqsearch" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSearch"), $_smarty_tpl);?>
" value="<?php echo $_smarty_tpl->getValue('SearchContent');?>
" required>
                <span class="input-group-btn">
                <button type="submit" name="faqsearchstart" id="faqsearchstart" class="btn btn-block btn-default"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSearchBTN"), $_smarty_tpl);?>
</button>
              </span>
            </div>
        </form>

        <?php if ($_smarty_tpl->getValue('ShowDepList')) {?>
            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFrontSelectDep"), $_smarty_tpl);?>

            <?php if ($_smarty_tpl->getValue('Departments')) {?>
                <div class="row">
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart', false, NULL, 'foo', array (
));
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach2DoElse = false;
?>
                    <div class="<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('Departments')) > 1) {?>col-md-6<?php } else { ?>col-md-12<?php }?>"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('depart')['Short'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('depart')['Short'];
}?>" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('depart')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('depart')['Description'];?>
</div></a></div>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                </div>
            <?php }?>
        <?php } else { ?>
            <ol class="breadcrumb">
                <li><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemBreadcrumb"), $_smarty_tpl);?>
: <a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq<?php } else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq<?php }?>">FAQ</a></li>
                <?php if (!$_smarty_tpl->getValue('ItemUrl')) {?>
                    <li class="active"><?php echo $_smarty_tpl->getValue('DepName');?>
</li>
                <?php } else { ?>
                    <li><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
}?>"><?php echo $_smarty_tpl->getValue('DepName');?>
</a></li>
                <?php }?>
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('faq_breadcrumb'), 'item', false, 'key');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('item')->value) {
$foreach3DoElse = false;
?>
                    <?php if ($_smarty_tpl->getValue('ItemUrl') == $_smarty_tpl->getValue('item')['Url']) {?>
                        <li class="active"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['Name']), $_smarty_tpl);?>
</li>
                    <?php } else { ?>
                        <li><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');?>
/<?php echo $_smarty_tpl->getValue('item')['Url'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');?>
&url=<?php echo $_smarty_tpl->getValue('item')['Url'];
}?>"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('item')['Name']), $_smarty_tpl);?>
</a></li>
                    <?php }?>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            </ol>

            <?php if ($_smarty_tpl->getValue('AllKats')) {?>
                <div class="row">
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllKats'), 'kats', false, NULL, 'foo', array (
));
$foreach4DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('kats')->value) {
$foreach4DoElse = false;
?>
                    <div class="<?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllKats')) > 1) {?>col-md-6<?php } else { ?>col-md-12<?php }?>"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('DepShort');?>
/<?php echo $_smarty_tpl->getValue('kats')['Url'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');?>
&url=<?php echo $_smarty_tpl->getValue('kats')['Url'];
}?>" class="depList"><div><strong><?php echo $_smarty_tpl->getValue('kats')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getValue('kats')['Description'];?>
</div></a></div>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                </div>
            <?php }?>

            <div class="row">
                <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllKats')) == 0 && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('AllContent')) == 0) {?>
                    <div class="col-md-12"><a href="javascript:history.back()" class="depListContent"><div class="depListNoItems"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQNoItems"), $_smarty_tpl);?>
</div></a></div>
                <?php }?>
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllContent'), 'con');
$foreach5DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('con')->value) {
$foreach5DoElse = false;
?>
                    <div class="col-md-12"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
faq/<?php echo $_smarty_tpl->getValue('con')['DepShort'];?>
/<?php echo $_smarty_tpl->getValue('con')['Url'];
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=faq&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
if ($_smarty_tpl->getValue('KatID')) {?>cat=<?php echo $_smarty_tpl->getValue('KatID');
}?>&url=<?php echo $_smarty_tpl->getValue('con')['Url'];
}?>" class="depListContent"><div><strong><?php echo $_smarty_tpl->getValue('con')['Name'];?>
</strong><br /><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('truncate')($_smarty_tpl->getValue('con')['Content'],300);?>
</div></a></div>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                <?php if ($_smarty_tpl->getValue('ShowTicketSystem') == 'yes') {?>
                    <div class="col-md-12"><a href="<?php if ($_smarty_tpl->getValue('ShowHtaccess') == 'yes') {
echo $_smarty_tpl->getValue('selfurl');?>
ticket/<?php echo $_smarty_tpl->getValue('DepShort');
} else {
echo $_smarty_tpl->getValue('selfurl');?>
index.php?action=support&depshort=<?php echo $_smarty_tpl->getValue('DepShort');
}?>" class="depListContent"><div class="depListNewTicket"><img src="<?php echo $_smarty_tpl->getValue('selfurl');?>
plugins/templates/images/supportsystem_tickets.png" width="64" height="64" title="" /><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQNotSuitable"), $_smarty_tpl);?>
</div></a></div>
                <?php }?>
            </div>
        <?php }?>
    <?php }?>
</div><?php }
}
