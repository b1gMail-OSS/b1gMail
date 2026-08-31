<fieldset>
	<legend>{lng p="modfax_prefixes"}</legend>
	
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" name="f1" method="post" onsubmit="spin(this)">
		{csrffield}
	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped card-table">
				<thead>
				<tr>
					<th style="width: 25px;"><a href="javascript:invertSelection(document.forms.f1,'prefixes[]');" class="text-secondary"><i class="ti ti-selector"></i></a></th>
					<th>{lng p="modfax_prefix"}</th>
					<th>{lng p="gateway"}</th>
					<th>{lng p="modfax_price_firstpage"}</th>
					<th>{lng p="modfax_price_nextpages"}</th>
					<th style="width: 90px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$prefixes item=prefix}
				<tr>
					<td class="text-center"><input type="checkbox" class="form-check-input m-0" name="prefixes[]" value="{$prefix.prefixid}" /></td>
					<td>{if $prefix.prefix!='*'}(0){/if}{$prefix.prefix}<br /><small class="text-secondary">{lng p="modfax_country_prefix"}: {if $prefix.country_prefix!='*'}+{/if}{$prefix.country_prefix}</small></td>
					<td>{if $prefix.faxgateid==-1}({lng p="modfax_forbidno"}){elseif $prefix.faxgateid==0}({lng p="defaultgateway"}){else}{text value=$gateways[$prefix.faxgateid]}{/if}</td>
					<td>{if $prefix.faxgateid==-1} &mdash; {else}{$prefix.price_firstpage} {lng p="credits"}{/if}</td>
					<td>{if $prefix.faxgateid==-1} &mdash; {else}{$prefix.price_nextpages} {lng p="credits"}{/if}</td>
					<td class="text-nowrap">
						<div class="btn-group btn-group-sm">
							<a href="{$prefix.editUrl}{$sessionUrlSuffixHtml}" class="btn btn-sm" title="{lng p="edit"}"><i class="fa-regular fa-pen-to-square"></i></a>
							<button type="submit" name="delete" value="{$prefix.prefixid}" class="btn btn-sm" title="{lng p="delete"}" onclick="return confirm('{lng p="realdel"}');"><i class="fa-regular fa-trash-can"></i></button>
						</div>
					</td>
				</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	<div class="d-flex flex-wrap align-items-center gap-2 mt-3">
		<label class="mb-0">{lng p="action"}:</label>
		<select name="massAction" class="form-select form-select-sm" style="width: auto;">
			<option value="-">------------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<button type="submit" name="executeMassAction" value="1" class="btn btn-sm btn-primary">{lng p="execute"}</button>
	</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modfax_addprefix"}</legend>
	
	<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" onsubmit="spin(this)">
		{csrffield}
		<input type="hidden" name="add" value="1" />
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="../plugins/templates/images/modfax_prefix.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="modfax_country_prefix"}:</td>
				<td class="td2"><input type="text" size="8" name="country_prefix" value="{if isset($faxPrefs.default_country_prefix)}{text value=$faxPrefs.default_country_prefix allowEmpty=true}{/if}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_prefix"}:</td>
				<td class="td2"><input type="text" size="16" name="prefix" value="" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="gateway"}:</td>
				<td class="td2"><select name="faxgateid">
					<option value="-1">({lng p="modfax_forbidno"})</option>
					<option value="0" selected="selected">({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gwTitle key=gwID}
					<option value="{$gwID}">{text value=$gwTitle}</option>
					{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_price_firstpage"}:</td>
				<td class="td2"><input type="text" size="6" name="price_firstpage" value="3" />
								{lng p="credits"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_price_nextpages"}:</td>
				<td class="td2"><input type="text" size="6" name="price_nextpages" value="2" />
								{lng p="credits"}</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
