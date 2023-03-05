<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=prefixes&do=edit&id={$prefix.prefixid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_country_prefix"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="country_prefix" value="{if isset($prefix.country_prefix)}{text value=$prefix.country_prefix allowEmpty=true}{/if}" placeholder="{lng p="modfax_country_prefix"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_prefix"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="prefix" value="{if isset($prefix.prefix)}{text value=$prefix.prefix allowEmpty=true}{/if}" placeholder="{lng p="modfax_prefix"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="gateway"}</label>
			<div class="col-sm-10">
				<select name="faxgateid" class="form-select">
					<option value="-1"{if $prefix.faxgateid==-1} selected="selected"{/if}>({lng p="modfax_forbidno"})</option>
					<option value="0"{if $prefix.faxgateid==0} selected="selected"{/if}>({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gwTitle key=gwID}
						<option value="{$gwID}"{if $prefix.faxgateid==$gwID} selected="selected"{/if}>{text value=$gwTitle}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_price_firstpage"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price_firstpage" value="{$prefix.price_firstpage}" placeholder="{lng p="modfax_price_firstpage"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_price_nextpages"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price_nextpages" value="{$prefix.price_nextpages}" placeholder="{lng p="modfax_price_nextpages"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
