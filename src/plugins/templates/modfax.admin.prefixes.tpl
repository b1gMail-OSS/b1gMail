<fieldset>
	<legend>{lng p="modfax_prefixes"}</legend>
	
	<form action="{$pageURL}&action=prefixes&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'prefixes[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="modfax_prefix"}</th>
			<th width="210">{lng p="gateway"}</th>
			<th width="145">{lng p="modfax_price_firstpage"}</th>
			<th width="145">{lng p="modfax_price_nextpages"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$prefixes item=prefix}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="../plugins/templates/images/modfax_prefix.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="prefixes[]" value="{$prefix.prefixid}" /></td>
			<td>{if $prefix.prefix!='*'}(0){/if}{$prefix.prefix}<br /><small>{lng p="modfax_country_prefix"}: {if $prefix.country_prefix!='*'}+{/if}{$prefix.country_prefix}</small></td>
			<td>{if $prefix.faxgateid==-1}({lng p="modfax_forbidno"}){elseif $prefix.faxgateid==0}({lng p="defaultgateway}){else}{text value=$gateways[$prefix.faxgateid]}{/if}</td>
			<td>{if $prefix.faxgateid==-1} - {else}{$prefix.price_firstpage} {lng p="credits"}{/if}</td>
			<td>{if $prefix.faxgateid==-1} - {else}{$prefix.price_nextpages} {lng p="credits"}{/if}</td>
			<td>
				<a href="{$pageURL}&action=prefixes&do=edit&id={$prefix.prefixid}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="{$pageURL}&action=prefixes&delete={$prefix.prefixid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>		
		{/foreach}
		
		<tr>
			<td class="footer" colspan="7">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="modfax_addprefix"}</legend>
	
	<form action="{$pageURL}&action=prefixes&add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="../plugins/templates/images/modfax_prefix.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="modfax_country_prefix"}:</td>
				<td class="td2"><input type="text" size="8" name="country_prefix" value="{text value=$faxPrefs.default_country_prefix allowEmpty=true}" /></td>
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
