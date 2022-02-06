<form action="toolbox.php?do=saveVersionConfig&versionid={$versionID}&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
<input type="hidden" name="save" value="true" />

	{foreach from=$configGroups key=groupName item=group}
	<fieldset>
		<legend>{$group.title}</legend>
		
		<img src="{$tpldir}images/{if $group.icon}{$group.icon}{else}ico_prefs_common{/if}.png" border="0" alt="" width="32" height="32" style="float:left;" />
		<table style="float:left;margin-left:1em;width:80%;">
		{foreach from=$group.options key=fieldKey item=fieldInfo}
			<tr>
				<td class="td1" width="180">{$fieldInfo.title}</td>
				<td class="td2">
					{if $fieldInfo.type==64}
						<p>
							<table>
								<tr>
									<td><input type="radio" name="prefs[{$groupName}][{$fieldKey}][mode]" value="keep" checked="checked" id="keepRadio_{$groupName}_{$fieldKey}" /></td>
									<td><label for="keepRadio_{$groupName}_{$fieldKey}">{lng p="keepcurrentimg"}</label> &nbsp; 
										<small>[ <a href="toolbox.php?do=editVersionConfig&versionid={$versionID}&showImage=true&group={$groupName}&key={$fieldKey}&sid={$sid}" target="_blank">{lng p="show"}</a> ]</small></td>
								</tr>
								<tr>
									<td><input type="radio" name="prefs[{$groupName}][{$fieldKey}][mode]" value="upload" id="uploadRadio_{$groupName}_{$fieldKey}" /></td>
									<td><label for="uploadRadio_{$groupName}_{$fieldKey}"><input type="file" name="prefs[{$groupName}][{$fieldKey}][file]" /></label>
										<small>PNG, {$fieldInfo.imgSize} px</small></td>
								</tr>
							</table>
						</p>
					{elseif $fieldInfo.type==16}
						<textarea style="width:100%;height:80px;" name="prefs[{$groupName}][{$fieldKey}]">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
					{elseif $fieldInfo.type==8}
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
						<input type="radio" name="prefs[{$groupName}][{$fieldKey}]" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
							<label for="{$fieldKey}_{$optionKey}">{text value=$optionValue}</label>
						{/foreach}
					{elseif $fieldInfo.type==4}
						<select name="prefs[{$groupName}][{$fieldKey}]">
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
							<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
						{/foreach}									
						</select>
					{elseif $fieldInfo.type==2}
						<input type="checkbox" name="prefs[{$groupName}][{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if} />
					{elseif $fieldInfo.type==1}
						<input type="text" style="width:85%;" name="prefs[{$groupName}][{$fieldKey}]" value="{text value=$fieldInfo.value allowEmpty=true}" />
					{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</fieldset>
	{/foreach}
	
	<p>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
