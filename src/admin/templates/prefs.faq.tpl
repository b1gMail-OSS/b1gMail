<fieldset>
	<legend>{lng p="faq"}</legend>
	
	<form action="prefs.faq.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'faq_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="question"}</th>
			<th width="100">{lng p="language"}</th>
			<th width="120">{lng p="type"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$faqs item=faq}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/faq.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="faq_{$faq.id}" /></td>
			<td><a href="prefs.faq.php?do=edit&id={$faq.id}&sid={$sid}">{$faq.frage}</a><br /><small>{lng p="requires"}: {if $faq.required}{$requirements[$faq.required]}{else}-{/if}</small></td>
			<td>{text value=$faq.lang}</td>
			<td>{text value=$faq.typ}</td>
			<td>
				<a href="prefs.faq.php?do=edit&id={$faq.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.faq.php?delete={$faq.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
			
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="6">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addfaq"}</legend>
	
	<form action="prefs.faq.php?add=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="{$tpldir}images/faq32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="question"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="frage" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="typ">
					<option value="nli">{lng p="nli"}</option>
					<option value="li">{lng p="li"}</option>
					<option value="both">{lng p="both"}</option>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="language"}:</td>
				<td class="td2"><select name="lang">
					<option value=":all:">{lng p="all"}</option>
					<optgroup label="{lng p="languages"}">
						{foreach from=$languages item=lang key=langID}
						<option value="{$langID}">{text value=$lang.title}</option>
						{/foreach}
					</optgroup>
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="requires"}:</td>
				<td class="td2"><select name="required">
					<option value="">------------</option>
					<optgroup label="{lng p="services"}">
						{foreach from=$requirements item=req key=reqID}
						<option value="{$reqID}">{$req}</option>
						{/foreach}
					</optgroup>
				</select></td>
			</tr>
			<tr>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="antwort" id="antwort" class="plainTextArea" style="width:100%;height:220px;"></textarea>
					<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
					<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
					<script>
					<!--
						var editor = new htmlEditor('antwort');
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
					<input class="button" type="submit" value=" {lng p="add"} " />
				</td>
			</tr>
		</table>
	</form>
</fieldset>