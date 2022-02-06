<div id="contentHeader">
	<div class="left">
		<i class="fa fa-user-o" aria-hidden="true"></i>
		{lng p="addalias"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=aliases&do=create&sid={$sid}" onsubmit="return(checkAliasForm(this));">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="addalias"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">* {lng p="type"}:</td>
			<td class="listTableRight">
				{if $senderAliases}
				<input type="radio" name="typ" value="1" id="typ_1" checked="checked" onclick="updateAliasForm()" /> <label for="typ_1">{lng p="aliastype_1"}</label>
				<br />
				{/if}
				<input type="radio" name="typ" value="3" id="typ_3" onclick="updateAliasForm()"{if !$senderAliases} checked="checked"{/if} /> <label for="typ_3">{lng p="aliastype_1"} + {lng p="aliastype_2"}</label>
			</td>
		</tr>
		
		<tbody id="tbody_1" style="display:{if !$senderAliases}none{/if};">
		<tr>
			<td class="listTableLeft">* <label for="typ_1_email">{lng p="email"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="typ_1_email" id="typ_1_email" value="" size="34" /><br />
				<small>{lng p="typ_1_desc"}</small>
			</td>
		</tr>
		</tbody>
		
		<tbody id="tbody_3" style="display:{if $senderAliases}none{/if};">
		<tr>
			<td class="listTableLeft">* <label for="email_local">{lng p="email"}:</label></td>
			<td class="listTableRight">
				<input type="text" name="email_local" id="email_local" value="" size="20" onblur="checkAddressAvailability()" />
				<select name="email_domain" id="email_domain" onblur="checkAddressAvailability()">
				{foreach from=$domainList item=domain}
					<option value="{$domain}">@{domain value=$domain}</option>
				{/foreach}
				</select>
				<span id="addressAvailabilityIndicator"></span>
			</td>
		</tr>
		</tbody>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>

</div></div>
