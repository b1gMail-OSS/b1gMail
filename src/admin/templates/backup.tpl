<fieldset>
	<legend>{lng p="createbackup"}</legend>
	
	<form action="backup.php?sid={$sid}&do=createBackup" method="post">
	<table>
		<tr>
			<td align="left" rowspan="3" valign="top" width="40"><img src="{$tpldir}images/backup32.png" border="0" alt="" width="32" height="32" /></td>
			<td>
				{lng p="backupitems"}:
				<blockquote>
					<input type="checkbox" name="backup_prefs" id="backupPrefs" checked="checked" />
						<label for="backupPrefs"><b>{lng p="prefs"}</b></label> ({lng p="approx"} {size bytes=$sizes.prefs})<br />
					<input type="checkbox" name="backup_stats" id="backupStats" checked="checked" />
						<label for="backupStats"><b>{lng p="statsdata"}</b></label> ({lng p="approx"} {size bytes=$sizes.stats})<br />
					<input type="checkbox" name="backup_users" id="backupUsers" checked="checked" />
						<label for="backupUsers"><b>{lng p="userdata"}</b></label> ({lng p="approx"} {size bytes=$sizes.users})<br />
					<input type="checkbox" name="backup_organizer" id="backupOrganizer" checked="checked" />
						<label for="backupOrganizer"><b>{lng p="organizerdata"}</b></label> ({lng p="approx"} {size bytes=$sizes.organizer})<br />
					<input type="checkbox" name="backup_mails" id="backupMails" checked="checked" />
						<label for="backupMails"><b>{lng p="maildata"}</b></label> ({lng p="approx"} {size bytes=$sizes.mails})<br />
					<input type="checkbox" name="backup_webdisk" id="backupWebdisk" checked="checked" />
						<label for="backupWebdisk"><b>{lng p="webdiskdata"}</b></label> ({lng p="approx"} {size bytes=$sizes.webdisk})<br />
				</blockquote>
			</td>
		</tr>
	</table>
	<p>
		<div style="float:left;" class="buttons">
			<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
			{lng p="backupwarn"}
		</div>
		<div style="float:right;" class="buttons">
			<input class="button" type="submit" value=" {lng p="createbackup"} " />
		</div>
	</p>
	</form>
</fieldset>