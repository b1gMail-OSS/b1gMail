<form action="{$pageURL}&action=gateways&simple=true&save=true&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="modfax_gateways_simple"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th>{lng p="title"}</th>
						<th width="120">{lng p="modfax_protocol"}</th>
						<th width="200">{lng p="user"}</th>
						<th width="200">{lng p="password"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$gateways item=gateway}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td>{text value=$gateway.title}</td>
							<td>{if $gateway.protocol==1}{lng p="modfax_email"}{else}{lng p="modfax_http"}{/if}</td>
							<td><input type="text" class="form-control form-control-sm" name="gateways[{$gateway.faxgateid}][user]" value="{if isset($gateway.user)}{text value=$gateway.user allowEmpty=true}{/if}" style="width:90%;" /></td>
							<td><input type="password" class="form-control form-control-sm" name="gateways[{$gateway.faxgateid}][pass]" value="{if isset($gateway.pass)}{text value=$gateway.pass allowEmpty=true}{/if}" style="width:90%;" /></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6"><a href="{$pageURL}&action=gateways&sid={$sid}" class="btn btn-danger">{lng p="modfax_advancedmode"}</a></div>
		<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
	</div>
</form>