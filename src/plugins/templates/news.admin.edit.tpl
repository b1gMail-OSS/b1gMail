<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=news&do=edit&id={$news.newsid}&sid={$sid}" method="post" onsubmit="EBID('title').focus();if(EBID('title').value.length<2) return(false);editor.submit();spin(this)">
		<input type="hidden" name="save" value="true" />
		
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="../plugins/templates/images/news_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="180">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="title" id="title" value="{text value=$news.title}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="loggedin">
							<option value="nli"{if $news.loggedin=='nli'} selected="selected"{/if}>{lng p="nli"}</option>
							<option value="li"{if $news.loggedin=='li'} selected="selected"{/if}>{lng p="li"}</option>
							<option value="both"{if $news.loggedin=='both'} selected="selected"{/if}>{lng p="both"}</option>
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="all_groups" id="all_groups"{if $news.groups=='*'} checked="checked"{/if} />
					<label for="all_groups">{lng p="all"}</label>
					&nbsp;
					{foreach from=$groups item=group}
					<input type="checkbox" name="groups[]" value="{$group.id}" id="group_{$group.id}"{if $group.checked} checked="checked"{/if} onclick="if(this.checked) EBID('all_groups').checked=false;" />
					<label for="group_{$group.id}">{text value=$group.title}</label>
					{/foreach}
				</td>
			</tr>
			<tr>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:180px;">{text value=$news.text}</textarea>
					<script src="../clientlib/wysiwyg.js"></script>
					<script>
					<!--
						var editor = new htmlEditor('text', '{$usertpldir}/images/editor/');
						editor.init();
						registerLoadAction('editor.start()');
					//-->
					</script>
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
