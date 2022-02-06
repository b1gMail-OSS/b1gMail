<fieldset>
	<legend>{lng p="languages"}</legend>

	<form action="prefs.languages.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'lang_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="language"}</th>
			<th>{lng p="author"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$languages item=language key=langID}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/language.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center">{if $language.writeable && !$language.default}<input type="checkbox" name="lang_{$langID}" />{/if}</td>
			<td>{text value=$language.title}<br /><small>{text value=$language.charset}, {text value=$language.locale}</small></td>
			<td>{text value=$language.author}<br /><small>{text value=$language.authorWeb allowEmpty=true}</small></td>
			<td>
				<a href="prefs.languages.php?action=texts&lang={$langID}&sid={$sid}"><img src="{$tpldir}images/phrases.png" border="0" alt="{lng p="customtexts"}" width="16" height="16" /></a>
				{if $language.writeable && !$language.default}<a href="prefs.languages.php?delete={$langID}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>{/if}
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
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
	<legend>{lng p="addlanguage"}</legend>
	
	<form action="prefs.languages.php?add=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		<p>
			{lng p="addlang_desc"}
		</p>
		
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/lang32.png" border="0" alt="" width="32" height="32" /></td>
				<td>{lng p="langfile"}:<br />
					<input type="file" name="langfile" style="width:440px;" accept=".lang.php" /></td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" align="absmiddle" width="16" height="16" />
				{lng p="sourcewarning"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="install"} " />
			</div>
		</p>
	</form>
</fieldset>