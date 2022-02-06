<fieldset>
	<legend>{lng p="newsletter"}</legend>
	
	<center>
		<br />
		
		<img src="{$tpldir}images/load_32.gif" border="0" alt="" /><br /><br />
		{lng p="sendingletter"}<br />
		<span id="status">0 / {$recpCount}</span>
		
		<br /><br />
	</center>
</fieldset>

<script type="text/javascript">
<!--
	rc_perpage = {$perPage};
	rc_id = '{$id}';
	rc_all = {$recpCount};
	registerLoadAction('sendNewsletter()');
//-->
</script>
