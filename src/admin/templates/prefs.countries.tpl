<fieldset>
	<legend>{lng p="countries"}</legend>

	<form action="prefs.countries.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'country_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="country"}</th>
			<th width="80" style="text-align:center;">{lng p="plzdb"}?</th>
			<th width="80" style="text-align:center;">{lng p="eucountry"}?</th>
			<th width="80" style="text-align:center;">{lng p="vatrate"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$countries item=country}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/country.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="country_{$country.id}" /></td>
			<td>{$country.land}</td>
			<td style="text-align:center;">{if $country.plzDB}<img src="{$tpldir}images/ok.png" border="0" alt="" width="16" height="16" />{/if}</td>
			<td style="text-align:center;">{if $country.is_eu}<img src="{$tpldir}images/ok.png" border="0" alt="" width="16" height="16" />{/if}</td>
			<td style="text-align:center;">{if $country.vat}{$country.vat} %{/if}</td>
			<td>
				<a href="prefs.countries.php?do=edit&id={$country.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.countries.php?delete={$country.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="delete"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
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
	<legend>{lng p="addcountry"}</legend>
	
	<form action="prefs.countries.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="1"><img src="{$tpldir}images/country_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="country"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="land" value="" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>