<fieldset>
	<legend>{lng p="installplugin"}</legend>
	
	<form action="plugins.php?action=install&do=uploadPlugin&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		<p>
			{lng p="install_desc"}
		</p>
		
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/plugin_add.png" border="0" alt="" width="32" height="32" /></td>
				<td>{lng p="plugpackage"}:<br />
					<input type="file" name="package" accept=".bmplugin" style="width:440px;" /></td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" align="absmiddle" width="16" height="16" />
				{lng p="sourcewarning"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="install"} " />
			</div>
		</p>
	</form>
</fieldset>
