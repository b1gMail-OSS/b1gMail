<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.sms.php?action=types&do=edit&save=true&id={$type.id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="titel" value="{if isset($type.titel)}{text value=$type.titel}{/if}" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="gateway"}</label>
			<div class="col-sm-10">
				<select name="gateway" class="form-select">
					<option value="0">({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gateway}
						<option value="{$gateway.id}"{if $type.gateway==$gateway.id} selected="selected"{/if}>{text value=$gateway.titel}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="type"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="typ" value="{if isset($type.typ)}{text value=$type.typ allowEmpty=true}{/if}" placeholder="{lng p="type"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="price"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price" value="{$type.price}" placeholder="{lng p="price"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="maxlength"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="maxlength" value="{$type.maxlength}" placeholder="{lng p="maxlength"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="prefs"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="flags[1]" value="true"{if $type.flags&1} checked="checked"{/if}>
					<span class="form-check-label">{lng p="disablesender"}</span>
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value=" {lng p="save"} " />
		</div>
	</form>
</fieldset>