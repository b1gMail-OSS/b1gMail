{capture assign="dialogTitleText"}{lng p="addressbook"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-addressbook" dialogOnLoad="documentLoader()"}

<div class="bm-dialog-page bm-dialog-page-fill bm-addressbook-dialog">
	<div class="bm-addressbook-picker mb-3">
		<label class="form-label">{lng p="addressbook"}</label>
		<div class="addressDiv bm-addressbook-list" id="addresses"></div>
	</div>

	<div class="bm-addressbook-targets">
		<div class="mb-2">
			<label class="form-label" for="addrTarget_to">{lng p="to"}</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="to" role="textbox" aria-labelledby="addrTarget_to"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('to');" title="{lng p="to"}">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
		<div class="mb-2">
			<label class="form-label" for="addrTarget_cc">CC</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="cc" role="textbox" aria-labelledby="addrTarget_cc"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('cc');" title="CC">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
		<div class="mb-2">
			<label class="form-label" for="addrTarget_bcc">BCC</label>
			<div class="input-group">
				<div class="addressDiv bm-addressbook-target" id="bcc" role="textbox" aria-labelledby="addrTarget_bcc"></div>
				<button type="button" class="btn btn-primary" onclick="addAddr('bcc');" title="BCC">
					<i class="ti ti-arrow-right icon" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="bm-dialog-actions">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="button" class="btn btn-primary" onclick="submitAddressDialog('{$mode}')">
			<i class="ti ti-check icon" aria-hidden="true"></i>
			{lng p="ok"}
		</button>
	</div>
</div>

<script>
<!--
	registerLoadAction(initAddressDialog);

	var toAddr = [],
		ccAddr = [],
		bccAddr = [],
		Addr = [];

	{literal}function initAddressDialog()
	{
		{/literal}{foreach from=$addresses item=address}
		{if ($mode=='handy'&&$address.handy) || ($mode!='handy'&&($address.email1||$address.email2))}
		{$address.type}Addr.push(["{text noentities=true escape=true value=$address.name}",
									"{text noentities=true escape=true value=$address.email1}",
									"{text noentities=true escape=true value=$address.email2}",
									"{text noentities=true escape=true value=$address.handy}"]);
		{/if}
		{/foreach}

		{if $mode!='handy'}
		initEMailAddresses(Addr, toAddr, ccAddr, bccAddr);
		{else}
		initMobileAddresses(Addr, toAddr);
		{/if}
		{literal}
	}{/literal}
//-->
</script>

{include file="li/dialog.foot.tpl"}
