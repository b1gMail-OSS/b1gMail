<fieldset>
	<legend>{lng p="edit"}</legend>
	
	<form action="prefs.countries.php?do=edit&id={$country.id}&save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="3"><img src="{$tpldir}images/country_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="country"}:</td>
				<td class="td2"><input type="text" style="width:85%;" name="land" value="{$country.land}" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="eucountry"}?</td>
				<td class="td2"><input type="checkbox" name="is_eu"{if $country.is_eu=='yes'} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="vatrate"}:</td>
				<td class="td2"><input type="number" min="0" max="100" step="any" size="6" name="vat" value="{$country.vat}" /> %</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>