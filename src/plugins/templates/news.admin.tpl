<fieldset>
	<legend>{lng p="news_news"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th width="150">{lng p="type"}</th>
			<th width="55">&nbsp;</th>
		</tr>
		
		{foreach from=$news item=item}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/news_icon.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$item.title cut=55}<br />
				<small>{date timestamp=$item.date dayonly=true}</small></td>
			<td>{lng p=$item.loggedin}</td>
			<td>
				<a href="{$pageURL}&action=news&do=edit&id={$item.newsid}&sid={$sid}" title="{lng p="edit"}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="{$pageURL}&action=news&delete={$item.newsid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" title="{lng p="delete"}"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
	</table>
</fieldset>

<fieldset>
	<legend>{lng p="news_addnews"}</legend>
	
	<form action="{$pageURL}&action=news&add=true&sid={$sid}" method="post" onsubmit="EBID('title').focus();if(EBID('title').value.length<2) return(false);editor.submit();spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="6"><img src="../plugins/templates/images/news_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="180">{lng p="title"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="title" id="title" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="type"}:</td>
				<td class="td2"><select name="loggedin">
							<option value="nli">{lng p="nli"}</option>
							<option value="li">{lng p="li"}</option>
							<option value="both">{lng p="both"}</option>
						</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="groups"}:</td>
				<td class="td2">
					<input type="checkbox" name="all_groups" id="all_groups" checked="checked" />
					<label for="all_groups">{lng p="all"}</label>
					&nbsp;
					{foreach from=$groups item=group}
					<input type="checkbox" name="groups[]" value="{$group.id}" id="group_{$group.id}" onclick="if(this.checked) EBID('all_groups').checked=false;" />
					<label for="group_{$group.id}">{text value=$group.title}</label>
					{/foreach}
				</td>
			</tr>
			<tr>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:180px;"></textarea>
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
			<input class="button" type="submit" value=" {lng p="news_addnews"} " />
		</p>
	</form>
</fieldset>
