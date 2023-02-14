<form action="prefs.templates.php?do=prefs&template={$template}&sid={$sid}" method="post" onsubmit="spin(this)">
<input type="hidden" name="save" value="true" />

	<fieldset>
		<legend>{text value=$templateInfo.title}: {lng p="prefs"}</legend>

		{foreach from=$meta key=fieldKey item=fieldInfo}
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label">{$fieldInfo.title}</label>
				<div class="col-sm-10">
					{if $fieldInfo.type==16}
						<textarea class="form-control" name="prefs[{$fieldKey}]" placeholder="{$fieldInfo.title}">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
					{elseif $fieldInfo.type==8}
						<div>
						{foreach from=$fieldInfo.options item=optionValue key=optionKey}
							<label class="form-check">
								<input class="form-check-input" type="radio" name="prefs[{$fieldKey}]" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if}>
								<span class="form-check-label" for="{$fieldKey}_{$optionKey}">{text value=$optionValue}</span>
							</label>
						{/foreach}
						</div>
					{elseif $fieldInfo.type==4}
						<select name="prefs[{$fieldKey}]" class="form-select">
							{foreach from=$fieldInfo.options item=optionValue key=optionKey}
								<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
							{/foreach}
						</select>
					{elseif $fieldInfo.type==2}
						<label class="form-check">
							<input class="form-check-input" type="checkbox" name="prefs[{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if}>
						</label>
					{elseif $fieldInfo.type==1}
						<input type="text" class="form-control" name="prefs[{$fieldKey}]" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}" />
					{/if}
				</div>
			</div>
		{/foreach}
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
