<form action="{$pageURL}&action=gateways&simple=true&save=true&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
<fieldset>
	<legend>{lng p="modfax_gateways_simple"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th width="120">{lng p="modfax_protocol"}</th>
			<th width="200">{lng p="user"}</th>
			<th width="200">{lng p="password"}</th>
		</tr>
		
		{foreach from=$gateways item=gateway}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/modfax_gateway.png" border="0" alt="" width="16" height="16" /></td>
			<td>{text value=$gateway.title}</td>
			<td>{if $gateway.protocol==1}{lng p="modfax_email"}{else}{lng p="modfax_http"}{/if}</td>
			<td><input type="text" name="gateways[{$gateway.faxgateid}][user]" value="{text value=$gateway.user allowEmpty=true}" style="width:90%;" /></td>
			<td><input type="password" name="gateways[{$gateway.faxgateid}][pass]" value="{text value=$gateway.pass allowEmpty=true}" style="width:90%;" /></td>
		</tr>
		{/foreach}
	</table>
</fieldset>
		
<p>
	<div style="float:left;" class="buttons">
		<img src="../plugins/templates/images/modfax_advanced.png" width="16" height="16" border="0" alt="" align="absmiddle" />
		<a href="{$pageURL}&action=gateways&sid={$sid}">{lng p="modfax_advancedmode"}</a>
	</div>
	<div style="float:right;" class="buttons">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</div>
</p>
</form>