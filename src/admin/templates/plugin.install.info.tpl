<fieldset>
	<legend>{lng p="installplugin"}: {text value=$meta.name}</legend>
	
	<form action="plugins.php?action=install&do=installPlugin&id={$id}&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>
			{lng p="install_desc2"}
		</p>
		
		<table>
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/plugin_add.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td1" width="150">{lng p="name"}:</td>
				<td class="td2"><b>{text value=$meta.name}</b></td>
			</tr>
			<tr>
				<td class="td1">{lng p="version"}:</td>
				<td class="td2">{text value=$meta.version}</td>
			</tr>
			<tr>
				<td class="td1">{lng p="vendor"}:</td>
				<td class="td2"><a target="_blank" href="{text value=$meta.vendor_url escape=true}">{text value=$meta.vendor}</a> (<a href="mailto:{text value=$meta.vendor_mail escape=true}">{text value=$meta.vendor_mail}</a>)</td>
			</tr>
			<tr>
				<td class="td1">{lng p="forb1gmail"}:</td>
				<td class="td2">
					<img src="{$tpldir}images/{if !$versionsMatch}warning{else}ok{/if}.png" border="0" alt="" align="absmiddle" width="16" height="16" />
					{text value=$meta.for_b1gmail}
					{if !$versionsMatch}({lng p="yourversion"}: {$b1gmailVersion}){/if}
				</td>
			</tr>
			<tr>
				<td class="td2" colspan="2" align="center">
					<br />
					
					<div id="sigLayer">
						<img src="{$tpldir}images/load_16.gif" align="absmiddle" border="0" alt="" />
						{lng p="checkingsig"}
					</div>
					
					<script>
					<!--
						registerLoadAction('checkPluginSignature(\'{$signature}\')');
					//-->
					</script>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="install"} " />
			</div>
		</p>
	</form>
</fieldset>
