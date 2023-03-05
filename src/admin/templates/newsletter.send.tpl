<fieldset>
	<legend>{lng p="newsletter"}</legend>

	<div class="alert alert-info">{lng p="sendingletter"}: <span id="status">0 / {$recpCount}</span></div>
</fieldset>

<script type="text/javascript">
<!--
	rc_perpage = {$perPage};
	rc_id = '{$id}';
	rc_all = {$recpCount};
	registerLoadAction('sendNewsletter()');
//-->
</script>
