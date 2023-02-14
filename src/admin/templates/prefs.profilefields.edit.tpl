<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.profilefields.php?do=edit&id={$field.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="field"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="feld" value="{if isset($field.feld)}{text value=$field.feld allowEmpty=true}{/if}" placeholder="{lng p="field"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="validityrule"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="rule" value="{if isset($field.rule)}{text value=$field.rule allowEmpty=true}{/if}" placeholder="{lng p="validityrule"}">
				<small>{lng p="pfrulenote"}</small>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<select name="typ" class="form-select">
					{foreach from=$fieldTypeTable key=id item=text}
						<option value="{$id}"{if $field.typ==$id} selected="selected"{/if}>{$text}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="oblig"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="pflicht"{if $field.pflicht=='yes'} checked="checked"{/if}>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="show"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="show_signup"{if $field.show_signup=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="signup"}</span>
				</label>
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="show_li"{if $field.show_li=='yes'} checked="checked"{/if}>
					<span class="form-check-label">{lng p="li"}</span>
				</label>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="options"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="extra" value="{if isset($field.extra)}{text value=$field.extra allowEmpty=true}{/if}" placeholder="{lng p="options"}">
				<small>{lng p="optionsdesc"}</small>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
