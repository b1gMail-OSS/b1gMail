<fieldset>
	<legend>{lng p="edittemplate"}</legend>
	
	<form name="f1" action="newsletter.php?action=templates&do=edit&templateID={$tpl.templateid}&save=true&sid={$sid}" method="post" onsubmit="editor.submit();spin(this);">
	<table width="100%">
		<tr>
			<td width="40" valign="top" rowspan="7"><img src="{$tpldir}images/newsletter.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="120">{lng p="title"}:</td>
			<td class="td2"><input type="text" id="subject" name="title" value="{text value=$tpl.title allowEmpty=true}" size="42" /></td>
		</tr>
		<tr>
			<td class="td1" >{lng p="mode"}:</td>
			<td class="td2">
				<input type="radio" name="mode" value="html" id="mode_html"{if $tpl.mode=='html'} checked="checked"{/if} onclick="if(this.checked) return editor.switchMode('html');" />
				<label for="mode_html"><b>{lng p="htmltext"}</b></label>
				
				<input type="radio" name="mode" value="text" id="mode_text"{if $tpl.mode=='text'} checked="checked"{/if} onclick="if(this.checked) return editor.switchMode('text');" />
				<label for="mode_text"><b>{lng p="plaintext"}</b></label>
			</td>
		</tr>
		<tr>
			<td class="td1">{lng p="from"}:</td>
			<td class="td2"><input type="text" id="from" name="from" value="{text value=$tpl.from allowEmpty=true}" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="subject"}:</td>
			<td class="td2"><input type="text" id="subject" name="subject" value="{text value=$tpl.subject allowEmpty=true}" size="42" /></td>
		</tr>
		<tr>
			<td class="td1">{lng p="priority"}:</td>
			<td class="td2"><select name="priority" id="priority">
							<option value="1"{if $tpl.priority==1} selected="selected"{/if}>{lng p="prio_1"}</option>
							<option value="0"{if $tpl.priority==0} selected="selected"{/if}>{lng p="prio_0"}</option>
							<option value="-1"{if $tpl.priority==-1} selected="selected"{/if}>{lng p="prio_-1"}</option>
						</select></td>
		</tr>
		<tr>
			<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
				<textarea name="emailText" id="emailText" class="plainTextArea" style="width:100%;height:400px;">{text allowEmpty=true value=$tpl.body}</textarea>
				<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
				<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>
				<script>
				<!--
					var editor = new htmlEditor('emailText');
					editor.height = 400;
					editor.disableIntro = true;
					editor.init();
					registerLoadAction('editor.start()');
					registerLoadAction('editor.switchMode("{if $tpl.mode=='text'}text{else}html{/if}", true);');
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
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
	</form>
</fieldset>
