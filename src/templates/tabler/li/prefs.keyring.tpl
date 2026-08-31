<div class="bm-prefs-page bm-prefs-page-keyring">
<div id="vSep1">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
		<div class="left">
			<i class="ti ti-key icon icon-sm" aria-hidden="true"></i>
			{lng p="owncerts"}
		</div>
	</div>
	
	<form name="f1" method="post" action="{sessionurl file='prefs.php' params='action=keyring&do=action'}">
		{csrffield}
	<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
	<div class="card bm-prefs-table-card">
	<div class="table-responsive bm-prefs-table-wrap">
		<table class="bigTable table table-vcenter table-hover bm-prefs-table">
			<tr>
				<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allCheckerOwn" onclick="checkAll(this.checked, document.forms.f1, 'cert');" aria-label="{lng p="selaction"}" /></label></th>
				<th>
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=cn&order={$sortOrderInv}"}">{lng p="name"}</a>
					{if $sortColumn=='cn'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-email">
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=email&order={$sortOrderInv}"}">{lng p="email"}</a>
					{if $sortColumn=='email'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-validto">
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=validto&order={$sortOrderInv}"}">{lng p="validto"}</a>
					{if $sortColumn=='validto'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-actions">&nbsp;</th>
			</tr>
			
			{if $ownCerts}
			<tbody class="listTBody">
			{foreach from=$ownCerts key=certID item=cert}
			{cycle values="listTableTD,listTableTD2" assign="class"}
			<tr>
				<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="cert[{$cert.hash}]" name="cert[]" value="{$cert.hash}" aria-label="{text value=$cert.cn}" /></label></td>
				<td class="{if $sortColumn=='cn'}listTableTDActive{else}{$class}{/if}">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="ti ti-certificate icon icon-sm text-secondary me-1" aria-hidden="true"></i>{text value=$cert.cn cut=35}</a></td>
				<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if} bm-prefs-col-email">{text value=$cert.email cut=35}</td>
				<td class="{if $sortColumn=='validto'}listTableTDActive{else}{$class}{/if} bm-prefs-col-validto">{if $cert.validto<$now}<span class="text-danger">{/if}{date timestamp=$cert.validto dayonly=true}{if $cert.validto<$now}</span>{/if}</td>
				<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
					<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
						<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');" class="btn btn-outline-secondary btn-icon" title="{lng p="view"}" aria-label="{lng p="view"}"><i class="ti ti-eye icon" aria-hidden="true"></i></a>
						{if $pkcs12Support}<a href="javascript:void(0);" onclick="exportPrivateCert('{$cert.hash}');" class="btn btn-outline-secondary btn-icon" title="{lng p="download"}" aria-label="{lng p="download"}"><i class="ti ti-download icon" aria-hidden="true"></i></a>{/if}
						<a onclick="return confirm('{lng p="realdel"}');" href="{sessionurl file='prefs.php' params="action=keyring&do=delete&type=2&hash={$cert.hash}"}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
					</div>
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
	</div>
	</div>

	<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
		<div class="left">
			<div class="input-group input-group-sm bm-prefs-action-group">
				<select class="form-select bm-prefs-action-select" name="do2">
					<option value="-">------ {lng p="selaction"} ------</option>
					<option value="delete">{lng p="delete"}</option>
				</select>
				<input class="btn btn-primary" type="submit" value="{lng p="ok"}" />
			</div>
		</div>
		<div class="right">
			{if $uploadCerts}<button type="button" class="btn btn-sm btn-primary" onclick="addPrivateCert({if $pkcs12Support}true{else}false{/if});">
				<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="addcert"}
			</button>{/if}
			{if $issueCerts}<button type="button" class="btn btn-sm btn-primary" onclick="document.location.href='{sessionurl file='prefs.php' params='action=keyring&do=issuePrivateCertificate'}';">
				<i class="ti ti-certificate icon icon-sm me-1" aria-hidden="true"></i>
				{lng p="requestcert"}
			</button>{/if}
		</div>
	</div>
	</form>
</div>
<div id="vSepSep"></div>
<div id="vSep2">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-prefs-header">
		<div class="left">
			<i class="ti ti-key icon icon-sm" aria-hidden="true"></i>
			{lng p="publiccerts"}
		</div>
	</div>
	
	<form name="f2" method="post" action="{sessionurl file='prefs.php' params='action=keyring&do=action'}">
		{csrffield}
	<div class="scrollContainer withBottomBar bm-prefs-body bm-prefs-list-body">
	<div class="card bm-prefs-table-card">
	<div class="table-responsive bm-prefs-table-wrap">
		<table class="bigTable table table-vcenter table-hover bm-prefs-table">
			<tr>
				<th class="bm-prefs-col-check"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="allCheckerPublic" onclick="checkAll(this.checked, document.forms.f2, 'cert');" aria-label="{lng p="selaction"}" /></label></th>
				<th>
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=cn&order={$sortOrderInv}"}">{lng p="name"}</a>
					{if $sortColumn=='cn'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-email">
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=email&order={$sortOrderInv}"}">{lng p="email"}</a>
					{if $sortColumn=='email'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-validto">
					<a href="{sessionurl file='prefs.php' params="action=keyring&sort=validto&order={$sortOrderInv}"}">{lng p="validto"}</a>
					{if $sortColumn=='validto'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
				</th>
				<th class="bm-prefs-col-actions">&nbsp;</th>
			</tr>
			
			{if $publicCerts}
			<tbody class="listTBody">
			{foreach from=$publicCerts key=certID item=cert}
			{cycle values="listTableTD,listTableTD2" assign="class"}
			<tr>
				<td class="{$class} bm-prefs-col-check" nowrap="nowrap"><label class="form-check mb-0"><input type="checkbox" class="form-check-input m-0" id="cert[{$cert.hash}]" name="cert[]" value="{$cert.hash}" aria-label="{text value=$cert.cn}" /></label></td>
				<td class="{if $sortColumn=='cn'}listTableTDActive{else}{$class}{/if}">
					<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');"><i class="ti ti-certificate icon icon-sm text-secondary me-1" aria-hidden="true"></i>{text value=$cert.cn cut=35}</a></td>
				<td class="{if $sortColumn=='email'}listTableTDActive{else}{$class}{/if} bm-prefs-col-email">{text value=$cert.email cut=35}</td>
				<td class="{if $sortColumn=='validto'}listTableTDActive{else}{$class}{/if} bm-prefs-col-validto">{if $cert.validto<$now}<span class="text-danger">{/if}{date timestamp=$cert.validto dayonly=true}{if $cert.validto<$now}</span>{/if}</td>
				<td class="{$class} bm-prefs-col-actions text-end" nowrap="nowrap">
					<div class="btn-group btn-group-sm bm-prefs-row-actions" role="group" aria-label="{lng p="actions"}">
						<a href="javascript:void(0);" onclick="showCertificate('{$cert.hash}');" class="btn btn-outline-secondary btn-icon" title="{lng p="view"}" aria-label="{lng p="view"}"><i class="ti ti-eye icon" aria-hidden="true"></i></a>
						<a onclick="return confirm('{lng p="realdel"}');" href="{sessionurl file='prefs.php' params="action=keyring&do=delete&type=1&hash={$cert.hash}"}" class="btn btn-outline-secondary btn-icon text-danger" title="{lng p="delete"}" aria-label="{lng p="delete"}"><i class="ti ti-trash icon" aria-hidden="true"></i></a>
					</div>
				</td>
			</tr>
			{/foreach}
			</tbody>
			{/if}
		</table>
	</div>
	</div>
	</div>

	<div id="contentFooter" class="contentFooter bm-organizer-footer bm-prefs-footer">
		<div class="left">
			<div class="input-group input-group-sm bm-prefs-action-group">
				<select class="form-select bm-prefs-action-select" name="do2">
					<option value="-">------ {lng p="selaction"} ------</option>
					<option value="delete">{lng p="delete"}</option>
				</select>
				<input class="btn btn-primary" type="submit" value="{lng p="ok"}" />
			</div>
		</div>
		<div class="right">
			<button type="button" class="btn btn-sm btn-primary" onclick="addPublicCert();">
				<i class="ti ti-upload icon icon-sm me-1" aria-hidden="true"></i>
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
</div>
