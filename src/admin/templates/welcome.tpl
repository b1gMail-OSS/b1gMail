<fieldset>
	<legend>b1gMail</legend>

	<table width="100%">
		<tr>
			<td rowspan="2" width="40" align="center" valign="top"><img src="{$tpldir}images/about_logo.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="version"}:</td>
			<td class="td2" width="26%">{$version}</td>

			<td colspan="3" rowspan="2">&nbsp;</td>
		</tr>
	</table>
</fieldset>

{if $adminRow.type==0||$adminRow.privileges.overview}
<fieldset>
	<legend>{lng p="overview"}</legend>

	<table width="100%">
		<!-- user stuff -->
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_users.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%"><a href="users.php?sid={$sid}">{lng p="users"}</a>:</td>
			<td class="td2" width="26%">{$userCount}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_system.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1" width="24%">{lng p="phpversion"}:</td>
			<td class="td2" width="26%">{$phpVersion}</td>
		</tr>
		<tr>
			<td class="td1"><a href="users.php?filter=true&statusNotActivated=true&allGroups=true&sid={$sid}">{lng p="notactivated"}</a>:</td>
			<td class="td2">{$notActivatedUserCount}</td>

			<td class="td1">{lng p="webserver"}:</td>
			<td class="td2">{$webserver}</td>
		</tr>
		<tr>
			<td class="td1"><a href="users.php?filter=true&statusLocked=true&allGroups=true&sid={$sid}">{lng p="locked"}</a>:</td>
			<td class="td2">{$lockedUserCount}</td>

			<td class="td1">{lng p="load"}:</td>
			<td class="td2">{$loadAvg}</td>
		</tr>

		<!-- mail stuff -->
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_email.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="emailsize"}:</td>
			<td class="td2">{if $emailSize!==false}{size bytes=$emailSize}{else}-{/if}</td>

			<td rowspan="3" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_data.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="mysqlversion"}:</td>
			<td class="td2">{$mysqlVersion}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="emails"}:</td>
			<td class="td2">{$emailCount}</td>

			<td class="td1">{lng p="tables"}:</td>
			<td class="td2">{$tableCount}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="folders"}:</td>
			<td class="td2">{$folderCount}</td>

			<td class="td1">{lng p="dbsize"}:</td>
			<td class="td2">{size bytes=$dbSize}</td>
		</tr>

		<!-- webdisk stuff -->
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td rowspan="3" width="40" align="center" valign="top"><img src="{$tpldir}images/ico_disk.png" border="0" alt="" width="32" heigh="32" /></td>
			<td class="td1">{lng p="disksize"}:</td>
			<td class="td2" colspan="3">{if $diskSize!==false}{size bytes=$diskSize}{else}-{/if}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="files"}:</td>
			<td class="td2" colspan="3">{$diskFileCount}</td>
		</tr>
		<tr>
			<td class="td1">{lng p="folders"}:</td>
			<td class="td2" colspan="3">{$diskFolderCount}</td>
		</tr>
	</table>
</fieldset>
{/if}

{if $showActivation}
<table width="100%" style="border-collapse:collapse;">
	<tr>
		<td style="width:50%;vertical-align:top;">
			{/if}
			<fieldset>
				<legend>{lng p="notes"}</legend>
				<form action="welcome.php?sid={$sid}&do=saveNotes" method="post" onsubmit="spin(this)">
					<textarea style="width:100%;height:94px;" name="notes">{text value=$notes allowEmpty=true}</textarea>
					<p align="right"><input type="submit" value=" {lng p="save"} " class="button" /></p>
				</form>
			</fieldset>
			{if $showActivation}
		</td>
		<td style="width:50%;vertical-align:top;">
			<fieldset>
				<legend>{lng p="activatepayment"}</legend>

				<table>
					<tr>
						<td align="left" rowspan="3" valign="top" width="40"><img src="templates/images/ico_prefs_payments.png" border="0" alt="" width="32" height="32" /></td>
						<td colspan="2">{lng p="activate_desc"}</td>
					</tr>
					<tr>
						<td class="td1" width="120">{lng p="vkcode"}:</td>
						<td class="td2"><input type="text" name="vkCode" id="vkCode" value="VK-" size="26" onkeypress="return handleActivatePaymentInput(event, 0);"  /></td>
					</tr>
					<tr>
						<td class="td1">{lng p="amount"}:</td>
						<td class="td2"><input type="text" name="amount" id="amount" value="" size="10" onkeypress="return handleActivatePaymentInput(event, 1);" /> {text value=$bm_prefs.currency}</td>
					</tr>
				</table>

				<p>
					<div style="float:left;font-weight:bold;padding-top:4px;" id="activationResult">&nbsp;</div>
					<div style="float:right">
						<input class="button" type="button" onclick="activatePayment()" id="activateButton" value=" {lng p="activate"} " />
					</div>
				</p>
			</fieldset>
		</td>
	</tr>
</table>
{/if}

{if $adminRow.type==0}
<fieldset>
	<legend>{lng p="notices"}</legend>

	<table width="100%" id="noticeTable">
	{foreach from=$notices item=notice}
		<tr>
			<td width="20" valign="top"><img src="{$tpldir}images/{$notice.type}.png" width="16" height="16" border="0" alt="" align="absmiddle" /></td>
			<td valign="top">{$notice.text}</td>
			<td align="right" valign="top" width="20">{if $notice.link}<a href="{$notice.link}sid={$sid}"><img src="{$tpldir}images/go.png" border="0" alt="" width="16" height="16" /></a>{else}&nbsp;{/if}</td>
		</tr>
	{/foreach}
	</table>
</fieldset>
{/if}
