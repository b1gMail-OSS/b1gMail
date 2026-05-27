<?php
/* Smarty version 5.8.0, created on 2026-05-24 21:08:20
  from 'file:/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.support.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a134ca46e0bf0_73291978',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '42b6c98f06b6261f1180f60061ac88e4b55e029d' => 
    array (
      0 => '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates/supportsystem.li.support.tpl',
      1 => 1779636463,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a134ca46e0bf0_73291978 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/plugins/templates';
?><div class="contentHeader">
    <div class="left">
        <img src="plugins/templates/images/supportsystem_tickets.png" width="16" height="16" border="0" alt="" align="absmiddle" /> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemAllTickets"), $_smarty_tpl);?>

    </div>
    <div class="right">
        <select class="smallInput" onchange="if (this.value) window.location.href=this.value">
            <option value="start.php?action=supportsystem&shotick=all&sid=<?php echo $_smarty_tpl->getValue('SID');?>
" <?php if ($_smarty_tpl->getValue('ShowTicketStat') == 'All') {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsStatusAll"), $_smarty_tpl);?>
</option>
            <option value="start.php?action=supportsystem&shotick=open&sid=<?php echo $_smarty_tpl->getValue('SID');?>
" <?php if ($_smarty_tpl->getValue('ShowTicketStat') == 'Open') {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsStatusOpen"), $_smarty_tpl);?>
</option>
            <option value="start.php?action=supportsystem&shotick=closed&sid=<?php echo $_smarty_tpl->getValue('SID');?>
" <?php if ($_smarty_tpl->getValue('ShowTicketStat') == 'Closed') {?>selected<?php }?>><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsStatusClose"), $_smarty_tpl);?>
</option>
        </select>
    </div>
</div>

<form name="f1" method="post" action="start.php?action=supportsystem&sid=<?php echo $_smarty_tpl->getValue('SID');?>
">
    <div class="scrollContainer withBottomBar">
        <table class="bigTable">
            <tr>
                <th class="listTableHead" width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'ats');" /></th>
                <th class="listTableHead" width="180"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketNumber"), $_smarty_tpl);?>
 <a href="start.php?action=supportsystem&sort=tid&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-up"></i></a> <a href="start.php?action=supportsystem&sort=tid&desc&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-down"></i></a></th>
                <th class="listTableHead" width="200"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketDepartment"), $_smarty_tpl);?>
</th>
                <th class="listTableHead"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketSubject"), $_smarty_tpl);?>
 <a href="start.php?action=supportsystem&sort=subj&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-up"></i></a> <a href="start.php?action=supportsystem&sort=subj&desc&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-down"></i></a></th>
                <th class="listTableHead" width="200"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketUpdate"), $_smarty_tpl);?>
 <a href="start.php?action=supportsystem&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-up"></i></a> <a href="start.php?action=supportsystem&desc&sid=<?php echo $_smarty_tpl->getValue('SID');?>
"><i class="fa fa-angle-down"></i></a></th>
                <th class="listTableHead" width="100"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsStatus"), $_smarty_tpl);?>
</th>
                <th class="listTableHead" width="55"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemTicketsPriority"), $_smarty_tpl);?>
</th>
            </tr>
            <tbody class="listTBody">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('AllTickets'), 'at');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('at')->value) {
$foreach0DoElse = false;
?>
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('cycle')->handle(array('values'=>"listTableTD,listTableTD2",'assign'=>"class"), $_smarty_tpl);?>

                <tr class="tr-hover">
                    <td class="<?php echo $_smarty_tpl->getValue('class');?>
" nowrap="nowrap">&nbsp;<input type="checkbox" id="ats_<?php echo $_smarty_tpl->getValue('at')['ID'];?>
" name="ats[]" value="<?php echo $_smarty_tpl->getValue('at')['ID'];?>
" /></td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="listTableTDActive" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['StatusFont']), $_smarty_tpl);?>
" nowrap="nowrap">&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['TID']), $_smarty_tpl);?>
</td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="<?php echo $_smarty_tpl->getValue('class');?>
" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['StatusFont']), $_smarty_tpl);?>
" nowrap="nowrap">&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['Department']), $_smarty_tpl);?>
</td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="<?php echo $_smarty_tpl->getValue('class');?>
" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['StatusFont']), $_smarty_tpl);?>
">&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['Subject'],'cut'=>300), $_smarty_tpl);?>
</td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="<?php echo $_smarty_tpl->getValue('class');?>
" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['StatusFont']), $_smarty_tpl);?>
">&nbsp;<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('date')->handle(array('timestamp'=>$_smarty_tpl->getValue('at')['StatUpdate'],'nice'=>true,'elapsed'=>true), $_smarty_tpl);?>
</td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="<?php echo $_smarty_tpl->getValue('class');?>
" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['StatusFont']), $_smarty_tpl);?>
">&nbsp;<?php echo $_smarty_tpl->getValue('at')['StatusText'];?>
</td>
                    <td onclick="document.location = 'start.php?action=supportsystem&do=show&id=<?php echo $_smarty_tpl->getValue('at')['ID'];?>
&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';" class="<?php echo $_smarty_tpl->getValue('class');?>
" style="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('text')->handle(array('value'=>$_smarty_tpl->getValue('at')['PriorityColor']), $_smarty_tpl);?>
">&nbsp;<?php echo $_smarty_tpl->getValue('at')['PriorityText'];?>
</td>
                </tr>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
    </div>

    <div id="contentFooter">
        <div class="left">
            <select class="smallInput" name="maction">
                <option value="-">------ <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemAction"), $_smarty_tpl);?>
 ------</option>
                <option value="close"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemActionClose"), $_smarty_tpl);?>
</option>
                <?php if ($_smarty_tpl->getValue('TicketDelete') == 'yes') {?><option value="delete"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemActionDelete"), $_smarty_tpl);?>
</option><?php }?>
            </select>
            <input class="smallInput" type="submit" value="OK" />
        </div>
        <div class="right">
            <button type="button" onclick="document.location.href='start.php?action=supportsystem&do=new&sid=<?php echo $_smarty_tpl->getValue('SID');?>
';">
                <img src="plugins/templates/images/supportsystem_add.png" width="16" height="16" border="0" alt="" align="absmiddle" />
                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"SupportSystemNewTicket"), $_smarty_tpl);?>

            </button>
        </div>
    </div>

</form><?php }
}
