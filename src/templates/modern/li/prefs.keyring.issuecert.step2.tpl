<div id="contentHeader">
	<div class="left">
		<i class="fa fa-certificate" aria-hidden="true"></i>
		{lng p="requestcert"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">

<form name="f1" method="post" action="prefs.php?action=keyring&do=issuePrivateCertificate&sid={$sid}">
	<input type="hidden" name="step" value="3" />
	<input type="hidden" name="address" value="{text value=$address}" />

	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="requestcert"}</th>
		</tr>
		
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">{lng p="issuecert_passdesc"}</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">{lng p="certificate"}:</td>
			<td class="listTableRight">
				<fieldset style="width:390px;">
					<table>
						<tr>
							<td rowspan="4" valign="top" width="36"><i class="fa fa-certificate" aria-hidden="true"></i></td>
						</tr>
						<tr>
							<td><b>{lng p="city"}:</b></td>
							<td>{text value=$userRow.ort}</td>
						</tr>
						<tr>
							<td width="110"><b>{lng p="commonname"}:</b></td>
							<td>{text value=$userRow.vorname} {text value=$userRow.nachname}</td>
						</tr>
						<tr>
							<td><b>{lng p="email"}:</b></td>
							<td>{text value=$address}</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		
		<tr>
			<td class="listTableLeft">* <label for="password">{lng p="password"}:</label></td>
			<td class="listTableRight">
				<input type="password" name="password" id="password" value="" size="32" />
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
</form>

</div></div>
