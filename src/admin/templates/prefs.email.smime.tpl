<fieldset>
	<legend>{lng p="prefs"}</legend>
	
	<table width="90%">
		<tr>
			<td align="left" valign="top" width="40"><img src="{$tpldir}images/cert32.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="220">{lng p="cert_ca"}:</td>
			<td class="td2"><input class="button" type="button" value=" {lng p="setedit"} " onclick="document.location.href='prefs.email.php?action=smime&do=editca&sid={$sid}';" /></td>
		</tr>
	</table>
</fieldset>
	
<fieldset>
	<legend>{lng p="rootcerts"}</legend>

	<form action="prefs.email.php?action=smime&sid={$sid}" name="f1" method="post">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'certs[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="name"}</th>
			<th width="180">{lng p="validity"}</th>
			<th width="60">&nbsp;</th>
		</tr>
		
		{foreach from=$certs item=cert}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/cert_{if $cert.valid}ok{else}expired{/if}.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="certs[]" value="{$cert.certificateid}" /></td>
			<td>{text value=$cert.cn cut=45}</td>
			<td>{if !$cert.valid}<font color="red">{/if}{lng p="to"} {date timestamp=$cert.validto dayonly=true}<br /><small>{lng p="from"} {date timestamp=$cert.validfrom dayonly=true}</small>{if !$cert.valid}</font>{/if}</td>
			<td>
				<a href="prefs.email.php?action=smime&export={$cert.certificateid}&sid={$sid}"><img src="{$tpldir}images/cert_export.png" border="0" alt="{lng p="export"}" width="16" height="16" /></a>
				<a href="prefs.email.php?action=smime&delete={$cert.certificateid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="export">{lng p="export"}</option>
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addrootcert"}</legend>
	
	<form action="prefs.email.php?action=smime&add=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/certadd32.png" border="0" alt="" width="32" height="32" /></td>
				<td>{lng p="certfile"}:<br />
					<input type="file" name="certfile" style="width:440px;" /></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>
