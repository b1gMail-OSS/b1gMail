{if $plzMsg}
<div class="alert alert-{$plzMsg.type} mb-3" role="alert">{$plzMsg.text}</div>
{/if}

<div class="row g-3">
	<div class="col-md-6">
		<form action="{sessionurl file='plugin.page.php' params="plugin={$plzPlugin}&do=editor&action=test"}" method="post" onsubmit="spin(this)">
			{csrffield}
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">
						<img src="../plugins/templates/images/plzeditor_test.png" alt="" width="24" height="24" class="me-2 align-text-bottom" />
						{lng p="plzeditor_test"}
					</h3>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="form-label" for="plz_test_country">{lng p="country"}</label>
						<select name="country" id="plz_test_country" class="form-select">
							{foreach from=$plzFiles item=countryName key=countryID}
								<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
							{/foreach}
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label" for="plz_test_zip">{lng p="plzeditor_zip"}</label>
						<input type="text" class="form-control" name="zip" id="plz_test_zip" value="" placeholder="{lng p="plzeditor_zip"}" required="required" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="plz_test_city">{lng p="plzeditor_city"}</label>
						<input type="text" class="form-control" name="city" id="plz_test_city" value="" placeholder="{lng p="plzeditor_city"}" required="required" />
					</div>
				</div>
				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">
						<i class="ti ti-search me-1"></i>
						{lng p="plzeditor_test"}
					</button>
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6">
		<form action="{sessionurl file='plugin.page.php' params="plugin={$plzPlugin}&do=editor&action=add"}" method="post" onsubmit="spin(this)">
			{csrffield}
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">
						<img src="../plugins/templates/images/plzeditor_add.png" alt="" width="24" height="24" class="me-2 align-text-bottom" />
						{lng p="plzeditor_add"}
					</h3>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="form-label" for="plz_add_country">{lng p="country"}</label>
						<select name="country" id="plz_add_country" class="form-select">
							{foreach from=$plzFiles item=countryName key=countryID}
								<option value="{$countryID}"{if $countryID==$defaultCountryID} selected="selected"{/if}>{$countryName}</option>
							{/foreach}
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label" for="plz_add_zip">{lng p="plzeditor_zip"}</label>
						<input type="text" class="form-control" name="zip" id="plz_add_zip" value="" placeholder="{lng p="plzeditor_zip"}" required="required" />
					</div>
					<div class="mb-3">
						<label class="form-label" for="plz_add_city">{lng p="plzeditor_city"}</label>
						<input type="text" class="form-control" name="city" id="plz_add_city" value="" placeholder="{lng p="plzeditor_city"}" required="required" />
					</div>
				</div>
				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">
						<i class="ti ti-plus me-1"></i>
						{lng p="plzeditor_add"}
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
