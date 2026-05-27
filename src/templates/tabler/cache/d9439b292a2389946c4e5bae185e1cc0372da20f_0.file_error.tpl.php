<?php
/* Smarty version 5.8.0, created on 2026-05-25 11:53:27
  from 'file:nli/error.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.8.0',
  'unifunc' => 'content_6a1438370d1c37_94919418',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9439b292a2389946c4e5bae185e1cc0372da20f' => 
    array (
      0 => 'nli/error.tpl',
      1 => 1779526781,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6a1438370d1c37_94919418 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/mkleger/Entwicklung/Applikationen/b1gmail/src/templates/tabler/nli';
?><html>
<head>
	<title><?php echo $_smarty_tpl->getValue('service_title');?>
: <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"error"), $_smarty_tpl);?>
</title>
	<style>
	<!--
		*			{ font-family: tahoma, arial, verdana; font-size: 12px; }
		H1			{ font-size: 16px; font-weight: bold; border-bottom: 1px solid #DDDDDD; }
		H2			{ font-size: 14px; font-weight: normal; }
		.addInfo	{ font-family: courier, courier new; font-size: 10px; height: 100px; overflow: auto;
						border: 1px solid #DDDDDD; padding: 5px; }
		.box		{ width: 600px; border: 1px solid #CCC; border-radius: 10px; background-color: #FFF;
						padding: 30px 15px; margin-top: 3em; margin-left: auto; margin-right: auto; }
	//-->
	</style>
	<link href="<?php echo $_smarty_tpl->getValue('selfurl');?>
clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#F1F2F6">
	
	<div class="box">
		<table width="100%">
			<tr>
				<td align="center" width="80" valign="top"><i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i></td>
				<td valign="top" align="left">
				
					<h1><?php echo $_smarty_tpl->getValue('title');?>
</h1>
					<h2><?php echo $_smarty_tpl->getValue('description');?>
</h2>
					
					<hr size="1" color="#DDDDDD" width="100%" noshade="noshade" />
					<input type="button" value="&nbsp; <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('lng')->handle(array('p'=>"start"), $_smarty_tpl);?>
 &nbsp;" onclick="document.location.href='./';" style="padding: 1px;" />
					
				</td>
			</tr>
		</table>
	</div>

</body>
</html>
<?php }
}
