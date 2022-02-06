<div id="vSep1">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-key" aria-hidden="true"></i>
			{lng p="owncerts"}
		</div>
	</div>
	
	<form name="f1" method="post" action="prefs.php?action=keyring&do=action&sid={$sid}">
	<div class="scrollContainer withBottomBar">
		<table class="bigTable">
			<tr>
				<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'cert');" /></th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=cn&order={$sortOrderInv}">{lng p="name"}</a>
					{if $sortColumn=='cn'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=email&order={$sortOrderInv}">{lng p="email"}</a>
					{if $sortColumn=='email'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=validto&order={$sortOrderInv}">{lng p="validto"}</a>
					{if $sortColumn=='validto'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th width="70">&nbsp;</th>
			</tr>
			
			{if $ownCerts}
			<tbody class="listTBody">
			{foreach from=$ownCerts key=certID item=cert}
			{cycle values="listTableTD,listTableTD2" assign="class"}
			<tr>
				<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="cert[{$cert.hash}]" name="cert[]" value="{$cert.hash}" /></td>
				<td class="{if $sortColumn=='cn'}listTableTDActive{else}{$class}{/if}" width="40%">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="fa fa-certificate" aria-hidden="true"></i> {text value=$cert.cn cut=35}</a></td>
				<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if}">&nbsp;{text value=$cert.email cut=35}</td>
				<td class="{if $sortColumn=='validto'}listTableTDActive{else}{$class}{/if}" width="100">&nbsp;{if $cert.validto<$now}<font color="red">{/if}{date timestamp=$cert.validto dayonly=true}{if $cert.validto<$now}</font>{/if}</td>
				<td class="{$class}" nowrap="nowrap">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="fa fa-eye" aria-hidden="true"></i></a>
					{if $pkcs12Support}<a href="javascript:void(0);" onclick="exportPrivateCert('{$cert.hash}');"><i class="fa fa-download" aria-hidden="true"></i></a>{/if}
					<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=keyring&do=delete&type=2&hash={$cert.hash}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
				</td>
			</tr>
			{/foreach}
			</tbody>
			{/if}
			
			<tr>
				<td colspan="5" class="listTableFoot">
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td align="left">
							</td>
							<td align="right">
								{if $uploadCerts}<a href="javascript:void(0);" onclick=""></a>{/if}
								&nbsp;&nbsp;&nbsp;
								{if $issueCerts}<a href=""></a>{/if}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="contentFooter">
		<div class="left">
			<select class="smallInput" name="do2">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<input class="smallInput" type="submit" value="{lng p="ok"}" />
		</div>
		
		<div class="right">
			{if $uploadCerts}<button type="button" onclick="addPrivateCert({if $pkcs12Support}true{else}false{/if});">
				<i class="fa fa-upload" aria-hidden="true"></i>
				{lng p="addcert"}
			</button>{/if} 
			{if $issueCerts}<button type="button" onclick="document.location.href='prefs.php?action=keyring&do=issuePrivateCertificate&sid={$sid}';">
				<i class="fa fa-certificate" aria-hidden="true"></i>
				{lng p="requestcert"}
			</button>{/if}
		</div>
	</div>
	</form>
</div>
<div id="vSepSep"></div>
<div id="vSep2">
	<div class="contentHeader">
		<div class="left">
			<i class="fa fa-key" aria-hidden="true"></i>
			{lng p="publiccerts"}
		</div>
	</div>
	
	<form name="f2" method="post" action="prefs.php?action=keyring&do=action&sid={$sid}">
	<div class="scrollContainer withBottomBar">
		<table class="bigTable">
			<tr>
				<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f2, 'cert');" /></th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=cn&order={$sortOrderInv}">{lng p="name"}</a>
					{if $sortColumn=='cn'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=email&order={$sortOrderInv}">{lng p="email"}</a>
					{if $sortColumn=='email'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th>
					<a href="prefs.php?sid={$sid}&action=keyring&sort=validto&order={$sortOrderInv}">{lng p="validto"}</a>
					{if $sortColumn=='validto'}<img src="{$tpldir}images/li/{$sortOrder}.gif" border="0" alt="" align="absmiddle" />{/if}
				</th>
				<th width="70">&nbsp;</th>
			</tr>
			
			{if $publicCerts}
			<tbody class="listTBody">
			{foreach from=$publicCerts key=certID item=cert}
			{cycle values="listTableTD,listTableTD2" assign="class"}
			<tr>
				<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="cert[{$cert.hash}]" name="cert[]" value="{$cert.hash}" /></td>
				<td class="{if $sortColumn=='cn'}listTableTDActive{else}{$class}{/if}" width="40%">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="fa fa-certificate" aria-hidden="true"></i> {text value=$cert.cn cut=35}</a></td>
				<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if}">&nbsp;{text value=$cert.email cut=35}</td>
				<td class="{if $sortColumn=='validto'}listTableTDActive{else}{$class}{/if}" width="100">&nbsp;{if $cert.validto<$now}<font color="red">{/if}{date timestamp=$cert.validto dayonly=true}{if $cert.validto<$now}</font>{/if}</td>
				<td class="{$class}" nowrap="nowrap">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="fa fa-eye" aria-hidden="true"></i></a>
					<a onclick="return confirm('{lng p="realdel"}');" href="prefs.php?action=keyring&do=delete&type=1&hash={$cert.hash}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
				</td>
			</tr>
			{/foreach}
			</tbody>
			{/if}
		</table>
	</div>
	
	<div class="contentFooter">
		<div class="left">
			<select class="smallInput" name="do2">
				<option value="-">------ {lng p="selaction"} ------</option>
				<option value="delete">{lng p="delete"}</option>
			</select>
			<input class="smallInput" type="submit" value="{lng p="ok"}" />
		</div>
		<div class="right">
			<button type="button" onclick="addPublicCert();">
				<i class="fa fa-upload" aria-hidden="true"></i>
				{lng p="addcert"}
			</button>
		</div>
	</div>
	</form>
</div>

<script>
<!--
	registerLoadAction('initVSep()');
//-->
</script>
