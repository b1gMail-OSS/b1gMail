<div class="bm-prefs-page bm-prefs-page-keyring">
<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
	<div class="left">
		<i class="ti ti-key icon icon-sm" aria-hidden="true"></i>
		{lng p="requestcert"}
	</div>
</div>

<form name="f1" method="post" action="{sessionurl file='prefs.php' params='action=keyring&do=issuePrivateCertificate'}">
	{csrffield}
<div class="scrollContainer bm-prefs-body"><div class="pad bm-prefs-form-pad">
	<input type="hidden" name="step" value="2" />

	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="requestcert"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">{lng p="issuecert_addrdesc"}</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">* <label for="address">{lng p="email"}:</label></td>
			<td class="listTableRight">
				<select name="address" id="address">
				{foreach from=$availableAddresses item=address}
					<option value="{text value=$address}">{$address}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</div></div>
</form>
</div>
