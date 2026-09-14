<form action="{sessionurl file='prefs.templates.php' params="do=prefs&template={$template}"}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
	{csrffield}
<input type="hidden" name="save" value="true" />

	<fieldset>
		<legend>{text value=$templateInfo.title}: {lng p="prefs"}</legend>

		{if $templateSaved}
		<div class="alert alert-success" role="alert">{lng p="saveok"}</div>
		{/if}
		{if $templateAssetErrors}
		<div class="alert alert-danger" role="alert">{lng p="tplasseterror"}</div>
		{/if}

		{foreach from=$meta key=fieldKey item=fieldInfo}
			<div class="mb-3 row">
				<label class="col-sm-2 col-form-label"{if $fieldInfo.type==64} for="prefs_{$fieldKey}_file"{/if}>{$fieldInfo.title}</label>
				<div class="col-sm-10">
					{if $fieldInfo.type==64}
						{if $fieldInfo.previewUrl}
						<div class="mb-2 d-inline-flex align-items-center justify-content-center border rounded p-2{if $fieldKey=='customSplash' || $fieldKey=='customLogo'} bg-white{/if}"{if $fieldKey!='customSplash' && $fieldKey!='customLogo'} style="background-color:#1f2937;"{/if}>
							<img src="{$fieldInfo.previewUrl}" alt="" style="max-height:{if $fieldKey=='customSplash'}7rem{elseif $fieldKey=='customFavicon'}2.25rem{else}3.5rem{/if};max-width:{if $fieldKey=='customFavicon'}2.25rem{else}18rem{/if};object-fit:{if $fieldKey=='customSplash'}cover{else}contain{/if};" />
						</div>
						{if !$fieldInfo.hasCustom && ($fieldKey=='customLogo' || $fieldKey=='customLogoDark' || $fieldKey=='customFavicon')}<div class="form-hint mb-2">{lng p="usedefaultimg"}</div>{/if}
						{/if}
						<div class="d-flex flex-column gap-2">
							<label class="form-check mb-0">
								<input class="form-check-input" type="radio" name="prefs[{$fieldKey}][mode]" value="keep" checked="checked">
								<span class="form-check-label">{lng p="keepcurrentimg"}</span>
							</label>
							<label class="form-check mb-0">
								<input class="form-check-input" type="radio" name="prefs[{$fieldKey}][mode]" value="default">
								<span class="form-check-label">{lng p="usedefaultimg"}</span>
							</label>
							<div class="d-flex flex-wrap align-items-center gap-2">
								<label class="form-check mb-0">
									<input class="form-check-input" type="radio" name="prefs[{$fieldKey}][mode]" value="upload" id="prefs_{$fieldKey}_upload">
									<span class="form-check-label">{lng p="uploadnewimg"}</span>
								</label>
								<input type="file" class="form-control" id="prefs_{$fieldKey}_file" name="prefs[{$fieldKey}][file]" accept="image/png,image/jpeg,image/gif,image/webp" style="max-width:22rem;" onchange="document.getElementById('prefs_{$fieldKey}_upload').checked=true;" />
							</div>
						</div>
						{if $fieldInfo.hint}<small class="form-hint">{text value=$fieldInfo.hint}</small>{/if}
					{elseif $fieldInfo.type==16}
						<textarea class="form-control" name="prefs[{$fieldKey}]" placeholder="{$fieldInfo.title}">{text value=$fieldInfo.value allowEmpty=true}</textarea>
						{if $fieldInfo.hint}<small class="form-hint">{text value=$fieldInfo.hint}</small>{/if}
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
						{if isset($fieldInfo.input) && $fieldInfo.input=='color'}
							{assign var=colorVal value=$fieldInfo.value}
							{if $colorVal==''}{assign var=colorVal value='#066fd1'}{/if}
							<div class="d-flex flex-wrap align-items-center gap-2">
								<input type="color" class="form-control form-control-color" id="prefs_{$fieldKey}_picker" value="{text value=$colorVal}" oninput="document.getElementById('prefs_{$fieldKey}').value=this.value;" />
								<input type="text" class="form-control" name="prefs[{$fieldKey}]" id="prefs_{$fieldKey}" value="{text value=$colorVal}" maxlength="7" style="max-width:8rem;" oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)) document.getElementById('prefs_{$fieldKey}_picker').value=this.value;" />
							</div>
						{else}
							<input type="text" class="form-control" name="prefs[{$fieldKey}]" value="{if isset($fieldInfo.value)}{text value=$fieldInfo.value allowEmpty=true}{/if}"{if $fieldInfo.maxlength} maxlength="{$fieldInfo.maxlength}"{/if} />
						{/if}
						{if $fieldInfo.hint}<small class="form-hint">{text value=$fieldInfo.hint}</small>{/if}
					{/if}
				</div>
			</div>
		{/foreach}
	</fieldset>

	<div class="text-end">
		<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
	</div>
</form>
