<div id="contentHeader">
	<div class="left">
		<i class="fa fa-quote-right" aria-hidden="true"></i>
		{if $signature}{lng p="editsignature"}{else}{lng p="addsignature"}{/if}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=signatures&do={if $signature}saveSignature&id={$signature.id}{else}createSignature{/if}&sid={$sid}" onsubmit="{literal}if(checkSignatureForm(this)) { editor.submit(); return(true); } else return(false);{/literal}">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {if $signature}{lng p="editsignature"}{else}{lng p="addsignature"}{/if}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* <label for="titel">{lng p="title"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="titel" id="titel" value="{text value=$signature.titel allowEmpty=true}" style="width:100%;" />
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="text">{lng p="plaintext"}:</label></td>
			<td class="listTableRight">
				<textarea name="text" id="text" style="width:100%;height:150px;">{text value=$signature.text allowEmpty=true}</textarea>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="html">{lng p="htmltext"}:</label></td>
			<td class="listTableRight">
				<div style="border:1px solid #DDDDDD;">
					<textarea name="html" id="html" style="width:100%;height:150px;">{if $signature}{text value=$signature.html allowEmpty=true}{/if}</textarea>
					<script src="./clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
					<script type="text/javascript" src="./clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
					<script>
					<!--
						var editor = new htmlEditor('html', '{$tpldir}/images/editor/');
						editor.init();
						editor.switchMode('html', true);
						registerLoadAction('editor.start()');
					//-->
					</script>
				</div>
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
