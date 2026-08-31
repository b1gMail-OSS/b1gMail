<form action="{$pageURL}{$sessionUrlSuffixHtml}" name="f1" method="post" onsubmit="spin(this)">
	{csrffield}
	<input type="hidden" name="save" value="1" />

<fieldset>
	<legend>{lng p="modfax_gateways_simple"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped card-table">
				<thead>
				<tr>
					<th>{lng p="title"}</th>
					<th style="width: 120px;">{lng p="modfax_protocol"}</th>
					<th style="width: 200px;">{lng p="user"}</th>
					<th style="width: 200px;">{lng p="password"}</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$gateways item=gateway}
				<tr>
					<td>{text value=$gateway.title}</td>
					<td>{if $gateway.protocol==1}{lng p="modfax_email"}{else}{lng p="modfax_http"}{/if}</td>
					<td><input type="text" class="form-control form-control-sm" name="gateways[{$gateway.faxgateid}][user]" value="{if isset($gateway.user)}{text value=$gateway.user allowEmpty=true}{/if}" /></td>
					<td><input type="password" class="form-control form-control-sm" name="gateways[{$gateway.faxgateid}][pass]" value="{if isset($gateway.pass)}{text value=$gateway.pass allowEmpty=true}{/if}" autocomplete="off" /></td>
				</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

<div class="d-flex justify-content-between align-items-center mt-3">
	<a href="{$advancedUrl}{$sessionUrlSuffixHtml}" class="btn btn-outline-secondary">
		<i class="ti ti-settings me-1"></i>
		{lng p="modfax_advancedmode"}
	</a>
	<button type="submit" class="btn btn-primary">
		<i class="ti ti-device-floppy me-1"></i>
		{lng p="save"}
	</button>
</div>
</form>
