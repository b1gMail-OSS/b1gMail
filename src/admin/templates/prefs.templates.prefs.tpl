<form action="prefs.templates.php?do=prefs&template={$template}&sid={$sid}" method="post" onsubmit="spin(this)">
<input type="hidden" name="save" value="true" />

	<fieldset>
		<legend>{text value=$templateInfo.title}: {lng p="prefs"}</legend>
		
		<img src="{$tpldir}images/template32.png" border="0" alt="" width="32" height="32" style="float:left;" />
		<table style="float:left;margin-left:1em;">
		{foreach from=$meta key=fieldKey item=fieldInfo}
			<tr>
				<td class="td1" width="220">{$fieldInfo.title}</td>
				<td class="td2">
					{if $fieldInfo.type==16}
						<textarea style="width:100%;height:80px;" name="prefs[{$fieldKey}]">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
					{elseif $fieldInfo.type==8}
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
						<input type="radio" name="prefs[{$fieldKey}]" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
							<label for="{$fieldKey}_{$optionKey}">{text value=$optionValue}</label>
						{/foreach}
					{elseif $fieldInfo.type==4}
						<select name="prefs[{$fieldKey}]">
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
							<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
						{/foreach}									
						</select>
					{elseif $fieldInfo.type==2}
						<input type="checkbox" name="prefs[{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if} />
					{elseif $fieldInfo.type==1}
						<input type="text" style="width:85%;" name="prefs[{$fieldKey}]" value="{text value=$fieldInfo.value allowEmpty=true}" />
					{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</fieldset>
	
	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
