<form action="toolbox.php?do=saveVersionConfig&versionid={$versionID}&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
<input type="hidden" name="save" value="true" />

	{foreach from=$configGroups key=groupName item=group}
	<fieldset>
		<legend>{$group.title}</legend>

		{foreach from=$group.options key=fieldKey item=fieldInfo}
			<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{$fieldInfo.title}</label>
			<div class="col-sm-10">
			{if $fieldInfo.type==64}
				<div class="input-group mb-2">
                              <span class="input-group-text">
                                <input class="form-check-input" type="radio" name="prefs[{$groupName}][{$fieldKey}][mode]" value="keep" checked="checked" id="keepRadio_{$groupName}_{$fieldKey}">
                              </span>
					<span class="input-group-text">{lng p="keepcurrentimg"}</span>
					<span class="input-group-text"><a href="toolbox.php?do=editVersionConfig&versionid={$versionID}&showImage=true&group={$groupName}&key={$fieldKey}&sid={$sid}" target="_blank">{lng p="show"}</a></span>
				</div>
				<div class="input-group mb-2">
                              <span class="input-group-text">
                                <input class="form-check-input" type="radio" name="prefs[{$groupName}][{$fieldKey}][mode]" value="upload" id="uploadRadio_{$groupName}_{$fieldKey}">
                              </span>
					<input type="file" class="form-control" name="prefs[{$groupName}][{$fieldKey}][file]">
					<span class="input-group-text">PNG, {$fieldInfo.imgSize} px</span>
				</div>

			{elseif $fieldInfo.type==16}
				<textarea class="form-control" name="prefs[{$groupName}][{$fieldKey}]">{text value=$fieldInfo.value allowEmpty=true}</textarea>
			{elseif $fieldInfo.type==8}
				{foreach from=$fieldInfo.options item=optionValue key=optionKey}
					<input type="radio" class="form-check-input" name="prefs[{$groupName}][{$fieldKey}]" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
					<label for="{$fieldKey}_{$optionKey}">{text value=$optionValue}</label>
				{/foreach}
			{elseif $fieldInfo.type==4}
				<select name="prefs[{$groupName}][{$fieldKey}]" class="form-select">
					{foreach from=$fieldInfo.options item=optionValue key=optionKey}
						<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
					{/foreach}
				</select>
			{elseif $fieldInfo.type==2}
				<input type="checkbox" class="form-check-input" name="prefs[{$groupName}][{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if} />
			{elseif $fieldInfo.type==1}
				<input type="text" class="form-control" name="prefs[{$groupName}][{$fieldKey}]" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}" />
			{/if}
			</div>
			</div>
		{/foreach}
	</fieldset>
	{/foreach}

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
