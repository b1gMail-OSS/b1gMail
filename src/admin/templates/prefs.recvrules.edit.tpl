<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.recvrules.php?do=edit&id={$rule.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="field"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="field" value="{if isset($rule.field)}{text value=$rule.field}{/if}" placeholder="{lng p="field"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="expression"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="expression" value="{if isset($rule.expression)}{text value=$rule.expression allowEmpty=true}{/if}" placeholder="{lng p="expression"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="action"}</label>
			<div class="col-sm-10">
				<select name="ruleAction" class="form-select">
					{foreach from=$ruleActionTable key=id item=text}
						<option value="{$id}"{if $rule.action==$id} selected="selected"{/if}>{$text}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="value"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="value" value="{$rule.value}" placeholder="{lng p="value"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<select name="type" class="form-select">
					{foreach from=$ruleTypeTable key=id item=text}
						<option value="{$id}"{if $rule.type==$id} selected="selected"{/if}>{$text}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
