<form action="prefs.payments.php?action=invoices&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);" id="prefsForm">
	<fieldset>
		<legend>{lng p="invoices"}</legend>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/ico_prefs_invoices.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="sendrg"}?</td>
				<td class="td2"><input name="sendrg"{if $bm_prefs.sendrg=='yes'} checked="checked"{/if} type="checkbox" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="rgnrfmt"}:</td>
				<td class="td2"><input type="text" name="rgnrfmt" value="{text allowEmpty=true value=$bm_prefs.rgnrfmt}" size="12" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="kdnrfmt"}:</td>
				<td class="td2"><input type="text" name="kdnrfmt" value="{text allowEmpty=true value=$bm_prefs.kdnrfmt}" size="12" /></td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>{lng p="rgtemplate"}</legend>
		
		<table width="100%">
			<tr>
			<td style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
			<textarea name="rgtemplate" id="rgtemplate" class="plainTextArea" style="width:100%;height:500px;">{text value=$bm_prefs.rgtemplate allowEmpty=true}</textarea>
			<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
			<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
			<script>
			<!--
				var editor = new htmlEditor('rgtemplate');
				editor.height = 500;
				editor.disableIntro = true;
				editor.init();
				registerLoadAction('editor.start()');
			//-->
			</script>
			</td></tr></table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
