<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.faq.php?do=edit&id={$faq.id}&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/faq32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="question"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="frage" value="{$faq.frage}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="typ">
					<option value="nli"{if $faq.typ=='nli'} selected="selected"{/if}>{lng p="nli"}</option>
					<option value="li"{if $faq.typ=='li'} selected="selected"{/if}>{lng p="li"}</option>
					<option value="both"{if $faq.typ=='both'} selected="selected"{/if}>{lng p="both"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="language"}:</td>
				<td class="td2"><select name="lang">
					<option value=":all:"{if $faq.lang==':all:'} selected="selected"{/if}>{lng p="all"}</option>
					<optgroup label="{lng p="languages"}">
						{foreach from=$languages item=lang key=langID}
						<option value="{$langID}"{if $faq.lang==$langID} selected="selected"{/if}>{text value=$lang.title}</option>
						{/foreach}
					</optgroup>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="requires"}:</td>
				<td class="td2"><select name="required">
					<option value=""{if !$faq.required} selected="selected"{/if}>------------</option>
					<optgroup label="{lng p="services"}">
						{foreach from=$requirements item=req key=reqID}
						<option value="{$reqID}"{if $faq.required==$reqID} selected="selected"{/if}>{$req}</option>
						{/foreach}
					</optgroup>
				</select></td>
			</tr>
			<tr>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="antwort" id="antwort" class="plainTextArea" style="width:100%;height:220px;">{$faq.antwort}</textarea>
					<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
					<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
					<script>
					<!--
						var editor = new htmlEditor('antwort');
						editor.disableIntro = true;
						editor.init();
						registerLoadAction('editor.start()');
					//-->
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<select class="smallInput" onchange="editor.insertText(this.value);">
						<option value="">-- {lng p="vars"} --</option>
						<option value="%%user%%">%%user%% ({lng p="email"})</option>
						<option value="%%wddomain%%">%%wddomain%% ({lng p="wddomain"})</option>
						<option value="%%selfurl%%">%%selfurl%% ({lng p="selfurl"})</option>
						<option value="%%hostname%%">%%hostname%% ({lng p="hostname"})</option>
					</select>
				</td>
				<td align="right">
					<input class="button" type="submit" value=" {lng p="save"} " />
				</td>
			</tr>
		</table>
	</form>
</fieldset>