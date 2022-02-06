<fieldset>
	<legend>{lng p="error"}</legend>
	
	<table>
		<tr>
			<td width="36" valign="top"><img src="{$tpldir}images/error32.png" border="0" alt="" width="32" height="32" /></td>
			<td valign="top">
				{lng p="toolboxfileerr"}
				
				<ul>
				{foreach from=$fileErrors item=item}
					<li>{text value=$item[0]} &raquo; {text value=$item[1]}</li>
				{/foreach}
				</ul>
			</td>
		</tr>
	</table>
</fieldset>

<p align="right" class="buttons">
	<input class="button" type="button" onclick="document.location.href='toolbox.php?do=editVersionConfig&versionid={$versionID}&sid={$sid}';" value=" {lng p="back"} " />
</p>
