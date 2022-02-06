<fieldset>
	<legend>{lng p="mailorphans"}</legend>
	
	<form action="maintenance.php?action=orphans&do=exec&sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/orphans32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="orphans_desc"}
					</p>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="undowarn"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="execute"} " />
			</div>
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="diskorphans"}</legend>
	
	<form action="maintenance.php?action=orphans&do=diskExec&sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/ico_disk.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="diskorphans_desc"}
					</p>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="undowarn"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="execute"} " />
			</div>
		</p>
	</form>
</fieldset>
