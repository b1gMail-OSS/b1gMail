<form action="users.php?action=search&do=search&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
	<fieldset>
		<legend>{lng p="search"}</legend>
	
		<table width="100%">
			<tr>
				<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/user_search.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="searchfor"}:</td>
				<td class="td2"><input type="text" name="q" value="" size="36" style="width:85%;" /></td>
			</tr>
			<tr>
				<td class="td1" valign="top">{lng p="searchin"}:</td>
				<td class="td2">
					<table width="100%">
						<tr>
							<td width="33%"><input type="checkbox" name="searchIn[id]" id="searchIn_id" checked="checked" />
								<label for="searchIn_id"><b>{lng p="id"}</b></label></td>
							<td width="33%"><input type="checkbox" name="searchIn[email]" id="searchIn_email" checked="checked" />
								<label for="searchIn_email"><b>{lng p="email"}</b></label></td>
							<td><input type="checkbox" name="searchIn[altmail]" id="searchIn_altmail" checked="checked" />
								<label for="searchIn_altmail"><b>{lng p="altmail"}</b></label></td>
						</tr>
						<tr>
							<td><input type="checkbox" name="searchIn[name]" id="searchIn_name" checked="checked" />
								<label for="searchIn_name"><b>{lng p="name"}</b></label></td>
							<td><input type="checkbox" name="searchIn[address]" id="searchIn_address" checked="checked" />
								<label for="searchIn_address"><b>{lng p="address"}</b></label></td>
							<td><input type="checkbox" name="searchIn[telfaxmobile]" id="searchIn_telfaxmobile" checked="checked" />
								<label for="searchIn_telfaxmobile"><b>{lng p="tel"} / {lng p="fax"} / {lng p="cellphone"}</b></label></td>
						</tr>
						<tr>
							<td><input type="checkbox" id="searchIn_all" checked="checked" onchange="invertSelection(document.forms.f1,'searchIn',true,this.checked)" />
								<label for="searchIn_all"><b>{lng p="all"}</b></label></td>
							<td colspan="2">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="search"} " />
		</div>
	</p>
</form>
