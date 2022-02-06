<fieldset>
	<legend>{lng p="templates"}</legend>
	
	<form name="f1" action="newsletter.php?action=templates&sid={$sid}" method="post">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'tpl_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="title"}</th>
			<th>{lng p="subject"}</th>
			<th width="70">&nbsp;</th>
		</tr>
		
		{foreach from=$templates item=tpl}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/template.png" border="0" width="16" height="16" alt="" /></td>
			<td align="center"><input type="checkbox" name="tpl_{$tpl.templateid}" /></td>
			<td>{text value=$tpl.title cut=35}</td>
			<td>{text value=$tpl.subject cut=35}</td>
			<td>
				<a href="newsletter.php?action=templates&do=edit&templateID={$tpl.templateid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="newsletter.php?action=templates&delete={$tpl.templateid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="5">
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
	<legend>{lng p="addtemplate"}</legend>
	
	<form name="f1" action="newsletter.php?action=templates&add=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="7"><img src="{$tpldir}images/newsletter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="120">{lng p="title"}:</td>
			<td class="td2"><input type="text" id="subject" name="title" value="" size="42" /></td>
		</tr>
		<tr>
			<td class="td1" >{lng p="mode"}:</td>
			<td class="td2">
				<input type="radio" name="mode" value="html" id="mode_html" checked="checked" onclick="if(this.checked) return editor.switchMode('html');" />
				<label for="mode_html"><b>{lng p="htmltext"}</b></label>
				
				<input type="radio" name="mode" value="text" id="mode_text" onclick="if(this.checked) return editor.switchMode('text');" />
				<label for="mode_text"><b>{lng p="plaintext"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="from"}:</td>
			<td class="td2"><input type="text" id="from" name="from" value="{text value=$from}" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="subject"}:</td>
			<td class="td2"><input type="text" id="subject" name="subject" value="" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="priority"}:</td>
			<td class="td2"><select name="priority" id="priority">
							<option value="1">{lng p="prio_1"}</option>
							<option value="0" selected="selected">{lng p="prio_0"}</option>
							<option value="-1">{lng p="prio_-1"}</option>
						</select></td>
		</tr>
		<tr>
			<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
				<textarea name="emailText" id="emailText" class="plainTextArea" style="width:100%;height:400px;"></textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
				<!--
					var editor = new htmlEditor('emailText');
					editor.height = 400;
					editor.init();
					registerLoadAction('editor.start()');
				//-->
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<select class="smallInput" onchange="editor.insertText(this.value);">
					<option value="">-- {lng p="vars"} --</option>
					<option value="%%email%%">%%email%% ({lng p="email"})</option>
					<option value="%%greeting%%">%%greeting%% ({lng p="greeting"})</option>
					<option value="%%salutation%%">%%salutation%% ({lng p="salutation"})</option>
					<option value="%%firstname%%">%%firstname%% ({lng p="firstname"})</option>
					<option value="%%lastname%%">%%lastname%% ({lng p="lastname"})</option>
				</select>
			</td>
		</tr>
	</table>

	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</div>
	</p>
	</form>
</fieldset>
