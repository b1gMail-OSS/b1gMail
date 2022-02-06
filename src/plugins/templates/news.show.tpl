<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{lng p="news_news"}</title>
    
	<!-- meta -->
	<meta http-equiv="content-type" content="text/html" />
	
	<!-- links -->
	<link rel="shortcut icon" type="image/png" href="res/favicon.png" />
	<link href="{$tpldir}style/dialog.css" rel="stylesheet" type="text/css" />
	
	<!-- client scripts -->
	<script>
	<!--
		var tplDir = '{$tpldir}';
	//-->
	</script>
	<script src="clientlang.php" type="text/javascript"></script>
	<script src="{$tpldir}js/common.js" type="text/javascript"></script>
	<script src="{$tpldir}js/loggedin.js" type="text/javascript"></script>
	<script src="{$tpldir}js/dialog.js" type="text/javascript"></script>
</head>

<body onload="documentLoader()">

	<h1>
		<img src="plugins/templates/images/news_icon.png" width="16" height="16" align="absmiddle" border="0" alt="" />
		{text value=$news.title cut=55}
	</h1>
	
	<fieldset style="margin-top:12px;margin-bottom:12px;">
		<legend>{date timestamp=$news.date dayonly=true}</legend>
		<div style="width:100%;height:265px;overflow:auto;">
			{$news.text}
		</div>
	</fieldset>
	
	<div>
		<div style="float:right">
			<input type="button" value=" {lng p="close"} " onclick="parent.hideOverlay();" />
		</div>
	</div>
	
</body>

</html>
