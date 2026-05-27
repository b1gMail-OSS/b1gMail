<?php
/* Smarty version 5.8.0, created on 2026-05-24 19:49:26
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a133a26763b87_93345977',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4efb1e07977061a69ea0c7d2b0e0de5f9678b20c' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.sidebar.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a133a26763b87_93345977 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
if ($_smarty_tpl->getValue('ShowTicketSystem') == 'yes') {?>
    <div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemSupportSidebar"), $_smarty_tpl);?>
</div>
    <div class="contentMenuIcons">
        <a href="start.php?action=supportsystem&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><img src="plugins/templates/images/supportsystem_tickets.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemSupportSidebarAll"), $_smarty_tpl);?>
</a><br />
        <a href="start.php?action=supportsystem&do=new&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><img src="<?php echo $_smarty_tpl->getValue('tpldir');?>
images/li/ico_add.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemSupportSidebarNew"), $_smarty_tpl);?>
</a><br />
    </div>
<?php }?>

<?php if ($_smarty_tpl->getValue('ShowFAQSystem') == 'yes') {?>
    <div class="sidebarHeading"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemFAQSidebar"), $_smarty_tpl);?>
</div>
    <div class="contentMenuIcons">
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('Departments'), 'depart');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('depart')->value) {
$foreach0DoElse = false;
?>
        <a href="prefs.php?action=faq&dep=<?php echo $_smarty_tpl->getValue('depart')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('sid');?>
"><img src="plugins/templates/images/supportsystem_depart.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getValue('depart')['Name'];?>
</a><br />
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </div>
<?php }
}
}
