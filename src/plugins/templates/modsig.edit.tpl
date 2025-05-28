<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=edit&id={$sig.signatureid}&save=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<div class="mb-3 row">
			<div class="col-sm-12">
				<textarea name="text" id="text" class="plainTextArea" style="width:100%;height:120px;font-family:courier;">{text value=$sig.text allowEmpty=true}</textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="modsig_html"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="html"{if $sig.html} checked="checked"{/if}>
				</label>
			</div>
		</div>
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