<fieldset>
	<legend>{lng p="edit"}</legend>

	<form action="prefs.sms.php?action=gateways&do=edit&save=true&id={$gateway.id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="title"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="titel" value="{if isset($gateway.titel)}{text value=$gateway.titel}{/if}" placeholder="{lng p="title"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="getstring"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" id="getstring" name="getstring" placeholder="{lng p="getstring"}">{text value=$gateway.getstring}</textarea>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="returnvalue"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="success" value="{if isset($gateway.success)}{text value=$gateway.success allowEmpty=true}{/if}" placeholder="{lng p="returnvalue"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="user"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="user" value="{if isset($gateway.user)}{text value=$gateway.user allowEmpty=true}{/if}" placeholder="{lng p="user"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="password"}</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="pass" value="{if isset($gateway.pass)}{text value=$gateway.pass allowEmpty=true}{/if}" placeholder="{lng p="password"}" autocomplete="off">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>