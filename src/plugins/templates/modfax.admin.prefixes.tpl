<fieldset>
	<legend>{lng p="modfax_prefixes"}</legend>

	<form action="{$pageURL}&action=prefixes&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'prefixes[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="modfax_prefix"}</th>
						<th style="width: 210px;">{lng p="gateway"}</th>
						<th style="width: 145px;">{lng p="modfax_price_firstpage"}</th>
						<th style="width: 145px;">{lng p="modfax_price_nextpages"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$prefixes item=prefix}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center"><input type="checkbox" name="prefixes[]" value="{$prefix.prefixid}" /></td>
							<td>{if $prefix.prefix!='*'}(0){/if}{$prefix.prefix}<br /><small>{lng p="modfax_country_prefix"}: {if $prefix.country_prefix!='*'}+{/if}{$prefix.country_prefix}</small></td>
							<td>{if $prefix.faxgateid==-1}({lng p="modfax_forbidno"}){elseif $prefix.faxgateid==0}({lng p="defaultgateway"}){else}{text value=$gateways[$prefix.faxgateid]}{/if}</td>
							<td>{if $prefix.faxgateid==-1} - {else}{$prefix.price_firstpage} {lng p="credits"}{/if}</td>
							<td>{if $prefix.faxgateid==-1} - {else}{$prefix.price_nextpages} {lng p="credits"}{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{$pageURL}&action=prefixes&do=edit&id={$prefix.prefixid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									<a href="{$pageURL}&action=prefixes&delete={$prefix.prefixid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modfax_addprefix"}</legend>

	<form action="{$pageURL}&action=prefixes&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_country_prefix"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="country_prefix" value="{if isset($faxPrefs.default_country_prefix)}{text value=$faxPrefs.default_country_prefix allowEmpty=true}{/if}" placeholder="{lng p="modfax_country_prefix"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_prefix"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="prefix" value="" placeholder="{lng p="modfax_prefix"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="gateway"}</label>
			<div class="col-sm-10">
				<select name="faxgateid" class="form-select">
					<option value="-1">({lng p="modfax_forbidno"})</option>
					<option value="0" selected="selected">({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gwTitle key=gwID}
						<option value="{$gwID}">{text value=$gwTitle}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_price_firstpage"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price_firstpage" value="3" placeholder="{lng p="modfax_price_firstpage"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="modfax_price_nextpages"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="price_nextpages" value="2" placeholder="{lng p="modfax_price_nextpages"}">
					<span class="input-group-text">{lng p="credits"}</span>
				</div>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
