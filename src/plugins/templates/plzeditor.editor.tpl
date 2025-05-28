{if $success}
	<div class="alert alert-success">{$success}</div>
{elseif $error}
	<div class="alert alert-danger">{$error}</div>
{/if}

<div class="row">
	<div class="col-md-6">
		<form action="{$pageURL}&sid={$sid}&action=editor&do=test" method="post" onsubmit="spin(this)">
			<fieldset>
				<legend>{lng p="plzeditor_test"}</legend>
				<div class="mb-3 row">
					<div class="col-sm-10"><img src="../plugins/templates/images/plzeditor_test.png" border="0" alt="" width="32" height="32" /</div>
				</div>

				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="country"}</label>
					<div class="col-sm-10">
						<select name="country" class="form-select">
							{foreach from=$plzFiles item=countryName key=countryID}
								<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="plzeditor_zip"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="zip" value="" placeholder="{lng p="plzeditor_zip"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="plzeditor_city"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="city" value="" placeholder="{lng p="plzeditor_city"}">
					</div>
				</div>

				<div class="text-end">
					<input class="btn btn-primary" type="submit" value="{lng p="plzeditor_test"}" />
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-md-6">
		<form action="{$pageURL}&sid={$sid}&action=editor&do=add" method="post" onsubmit="spin(this)">
			<fieldset>
				<legend>{lng p="plzeditor_add"}</legend>

				<div class="mb-3 row">
					<div class="col-sm-10"><img src="../plugins/templates/images/plzeditor_add.png" border="0" alt="" width="32" height="32" /</div>
				</div>

				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="country"}</label>
					<div class="col-sm-10">
						<select name="country" class="form-select">
							{foreach from=$plzFiles item=countryName key=countryID}
								<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="plzeditor_zip"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="zip" value="" placeholder="{lng p="plzeditor_zip"}">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">{lng p="plzeditor_city"}</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="city" value="" placeholder="{lng p="plzeditor_city"}">
					</div>
				</div>

				<div class="text-end">
					<input class="btn btn-primary" type="submit" value="{lng p="plzeditor_add"}" />
				</div>
			</fieldset>
		</form>
	</div>
</div>
