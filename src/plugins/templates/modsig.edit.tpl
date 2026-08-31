<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=edit&id={$sig.signatureid}"}" method="post" onsubmit="return modsigFormSubmit(this);">
		{csrffield}
		<input type="hidden" name="save" value="1" />
		{assign var=modsigTextValue value=$sig.text}
		{assign var=modsigHtmlMode value=($sig.html == 1)}
		{include file=$modsigTextFieldTpl}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="weight"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="weight" value="{$sig.weight}">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="groups"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="groups[]" value="*" id="group_all"{if $sig.groups=='*'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="all"}</span>
				</label>
				{foreach from=$groups item=group key=groupID}
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if $group.checked} checked="checked"{/if}>
						<span class="form-check-label">{text value=$group.title}</span>
					</label>
				{/foreach}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="paused"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="paused"{if $sig.paused} checked="checked"{/if}>
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
