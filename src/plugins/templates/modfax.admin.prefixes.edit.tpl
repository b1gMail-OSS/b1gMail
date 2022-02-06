<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="{$pageURL}&action=prefixes&do=edit&id={$prefix.prefixid}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="../plugins/templates/images/modfax_prefix.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="modfax_country_prefix"}:</td>
				<td class="td2"><input type="text" size="8" name="country_prefix" value="{text value=$prefix.country_prefix allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_prefix"}:</td>
				<td class="td2"><input type="text" size="16" name="prefix" value="{text value=$prefix.prefix allowEmpty=true}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="gateway"}:</td>
				<td class="td2"><select name="faxgateid">
					<option value="-1"{if $prefix.faxgateid==-1} selected="selected"{/if}>({lng p="modfax_forbidno"})</option>
					<option value="0"{if $prefix.faxgateid==0} selected="selected"{/if}>({lng p="defaultgateway"})</option>
					{foreach from=$gateways item=gwTitle key=gwID}
					<option value="{$gwID}"{if $prefix.faxgateid==$gwID} selected="selected"{/if}>{text value=$gwTitle}</option>
					{/foreach}
				</select></td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_price_firstpage"}:</td>
				<td class="td2"><input type="text" size="6" name="price_firstpage" value="{$prefix.price_firstpage}" />
								{lng p="credits"}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="modfax_price_nextpages"}:</td>
				<td class="td2"><input type="text" size="6" name="price_nextpages" value="{$prefix.price_nextpages}" />
								{lng p="credits"}</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
