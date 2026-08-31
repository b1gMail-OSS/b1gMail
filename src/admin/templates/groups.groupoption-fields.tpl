{foreach from=$groupOptionsList key=fieldKey item=fieldInfo}
	<div class="mb-3 row">
		<label class="col-sm-4 col-form-label">{$fieldInfo.desc}</label>
		<div class="col-sm-8">
			{if $fieldInfo.type==16}
				<textarea class="form-control" name="{$fieldKey}">{text value=$fieldInfo.value allowEmpty=true}</textarea>
			{elseif $fieldInfo.type==8}
				{foreach from=$fieldInfo.options item=optionValue key=optionKey}
					<div class="form-check">
						<input type="radio" class="form-check-input" name="{$fieldKey}" id="{$fieldKey}_{$optionKey}" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
						<span class="form-check-label">{text value=$optionValue}</span>
					</div>
				{/foreach}
			{elseif $fieldInfo.type==4}
				<select name="{$fieldKey}" class="form-select">
					{foreach from=$fieldInfo.options item=optionValue key=optionKey}
						<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
					{/foreach}
				</select>
			{elseif $fieldInfo.type==2}
				<div class="form-check">
					<input type="checkbox" class="form-check-input" name="{$fieldKey}" value="1"{if $fieldInfo.value} checked="checked"{/if} />
				</div>
			{elseif $fieldInfo.type==1}
				<input type="text" class="form-control" name="{$fieldKey}" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}" />
			{/if}
		</div>
	</div>
{/foreach}
