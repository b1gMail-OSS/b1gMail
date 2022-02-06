<form action="prefs.common.php?action=captcha&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="safecode"}</legend>
	
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/captcha32.png" border="0" alt="" width="32" height="32" /></td>				
				<td class="td1" width="220">{lng p="captchaprovider"}:</td>
				<td class="td2"><select name="captcha_provider" onchange="changeCaptchaProvider(this)">
					{foreach from=$providers item=prov key=key}
					<option value="{$key}"{if $defaultProvider==$key} selected="selected"{/if}>{text value=$prov.title}</option>
					{/foreach}
				</select></td>
			</tr>
		</table>
	</fieldset>

	{foreach from=$providers item=prov key=key}{if $prov.configFields}
	<fieldset id="cp_{$key}" style="display:{if $key!=$defaultProvider}none{/if};">
		<legend>{lng p="prefs"}: {text value=$prov.title}</legend>

		<table width="100%">
		{foreach from=$prov.configFields item=fieldInfo key=fieldKey}
			<tr>
				<td width="220" class="td1">{$fieldInfo.title}</td>
				<td class="td2">
					{if $fieldInfo.type==16}
						<textarea style="width:100%;height:80px;" name="prefs[{$key}][{$fieldKey}]">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
					{elseif $fieldInfo.type==8}
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
						<label>
							<input type="radio" name="prefs[{$key}][{$fieldKey}]" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
							{text value=$optionValue}
						</label>
						{/foreach}
					{elseif $fieldInfo.type==4}
						<select name="prefs[{$key}][{$fieldKey}]">
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
							<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
						{/foreach}									
						</select>
					{elseif $fieldInfo.type==2}
						<input type="checkbox" name="prefs[{$key}][{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if} />
					{elseif $fieldInfo.type==1}
						<input type="text" style="width:85%;" name="prefs[{$key}][{$fieldKey}]" value="{text value=$fieldInfo.value allowEmpty=true}" />
					{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</fieldset>
	{/if}{/foreach}
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
