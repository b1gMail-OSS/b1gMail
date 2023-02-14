<form action="prefs.common.php?action=captcha&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
	<div class="row">
		<div class="col-md-6">
			<fieldset>
				<legend>{lng p="safecode"}</legend>

				<div class="mb-3 row">
					<label class="col-sm-4 col-form-label">{lng p="captchaprovider"}</label>
					<div class="col-sm-8">
						<select name="captcha_provider"  class="form-select" onchange="changeCaptchaProvider(this)">
							{foreach from=$providers item=prov key=key}
								<option value="{$key}"{if $defaultProvider==$key} selected="selected"{/if}>{text value=$prov.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			{foreach from=$providers item=prov key=key}{if $prov.configFields}
				<fieldset id="cp_{$key}" style="display:{if $key!=$defaultProvider}none{/if};">
					<legend>{lng p="prefs"}: {text value=$prov.title}</legend>

					{foreach from=$prov.configFields item=fieldInfo key=fieldKey}
						<div class="mb-3 row">
							<label class="col-sm-4 col-form-label">{$fieldInfo.title}</label>
							<div class="col-sm-8">
								{if $fieldInfo.type==16}
									<textarea class="form-control" name="prefs[{$key}][{$fieldKey}]">{text value=$fieldInfo.value allowEmpty=true}</textarea></td>
								{elseif $fieldInfo.type==8}
									{foreach from=$fieldInfo.options item=optionValue key=optionKey}
										<label>
											<input type="radio" name="prefs[{$key}][{$fieldKey}]" value="{$optionKey}"{if $fieldInfo.value==$optionKey} checked="checked"{/if} />
											{text value=$optionValue}
										</label>
									{/foreach}
								{elseif $fieldInfo.type==4}
									<select class="form-select" name="prefs[{$key}][{$fieldKey}]">
										{foreach from=$fieldInfo.options item=optionValue key=optionKey}
											<option value="{$optionKey}"{if $fieldInfo.value==$optionKey} selected="selected"{/if}>{text value=$optionValue}</option>
										{/foreach}
									</select>
								{elseif $fieldInfo.type==2}
									<input type="checkbox" class="form-check" name="prefs[{$key}][{$fieldKey}]" value="1"{if $fieldInfo.value} checked="checked"{/if} />
								{elseif $fieldInfo.type==1}
									<input type="text" class="form-control" name="prefs[{$key}][{$fieldKey}]" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}" />
								{/if}
							</div>
						</div>
					{/foreach}

				</fieldset>
			{/if}
			{/foreach}
		</div>
	</div>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
