<fieldset>
	<legend>{lng p="language"}</legend>
	
	<form action="prefs.languages.php?action=texts&sid={$sid}" method="post">
	<center>
		<table>
			<tr>
				<td>{lng p="language"}:</td>
				<td><select name="lang">
					{foreach from=$languages key=langID item=lang}
						<option value="{$langID}"{if $langID==$selectedLang} selected="selected"{/if}>{text value=$lang.title}</option>
					{/foreach}
					</select></td>
				<td><input class="button" type="submit" value=" {lng p="ok"} &raquo; " /></td>
			</tr>
		</table>
	</center>
	</form>
</fieldset>

{if $selectedLang}
<script src="../clientlib/wysiwyg.js?{fileDateSig file="../../clientlib/wysiwyg.js"}"></script>
<script type="text/javascript" src="../clientlib/ckeditor/ckeditor.js?{fileDateSig file="../../clientlib/ckeditor/ckeditor.js"}"></script>

<script>
<!--
	var editors = [];
//-->
</script>

<fieldset>
	<legend>{lng p="customtexts"}</legend>
	
	<form action="prefs.languages.php?action=texts&lang={$selectedLang}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<p align="right">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</p>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="220">{lng p="title"}</th>
			<th>{lng p="text"}</th>
		</tr>
		{foreach from=$texts item=text}
		{cycle name=class assign=class values="td1,td2"}
		<tr class="{$class}">
			<td><img src="{$tpldir}images/phrases.png" border="0" alt="" width="16" height="16" /></td>
			<td><a name="{$text.key}" />{$text.title}<br /><small>{text value=$text.key}</small></td>
			<td>
				{if $customTextsHTML[$text.key]}<div style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">{/if}
				<textarea onfocus="this.style.height='240px';" onblur="this.style.height='100px';" style="width:99%;height:{if $customTextsHTML[$text.key]}350{else}100{/if}px;" name="text-{$text.key}" id="text-{$text.key}">{text value=$text.text allowEmpty=true}</textarea>
				{if $customTextsHTML[$text.key]}
				</div>
				<script>
				<!--
					editors['{$text.key}'] = new htmlEditor('text-{$text.key}');
					editors['{$text.key}'].disableIntro = true;
					editors['{$text.key}'].init();
					registerLoadAction('editors[\'{$text.key}\'].start()');
				//-->
				</script>
				{/if}
			</td>
		</tr>
		{/foreach}
	</table>
	
	<p align="right">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</p>
	</form>
</fieldset>
{/if}