{capture assign="dialogTitleText"}{lng p="addattendee"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-attendees" dialogOnLoad="documentLoader()"}

<div class="bm-dialog-page bm-attendees-dialog">
	<p class="bm-dialog-intro mb-0">{lng p="addressbook"} &rarr; {lng p="attendees"}</p>

	<div class="bm-attendees-transfer">
		<div class="card bm-attendees-panel">
			<div class="card-header py-2">
				<h3 class="card-title mb-0">
					<i class="ti ti-address-book icon icon-sm me-1 text-primary" aria-hidden="true"></i>
					{lng p="addressbook"}
				</h3>
			</div>
			<div class="card-body p-0">
				<div class="addressDiv bm-attendees-list" id="addresses"></div>
			</div>
		</div>

		<div class="bm-attendees-transfer-bar">
			<button type="button" class="btn btn-primary btn-icon btn-sm" onclick="addAttendee();" title="{lng p="add"}" aria-label="{lng p="add"}">
				<i class="ti ti-arrow-down icon" aria-hidden="true"></i>
			</button>
		</div>

		<div class="card bm-attendees-panel">
			<div class="card-header py-2">
				<h3 class="card-title mb-0">
					<i class="ti ti-users icon icon-sm me-1 text-primary" aria-hidden="true"></i>
					{lng p="attendees"}
				</h3>
			</div>
			<div class="card-body p-0">
				<div class="addressDiv bm-attendees-list bm-attendees-selected" id="attendees" data-empty='({lng p="none"})'></div>
			</div>
		</div>
	</div>

	<div class="bm-dialog-actions">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="button" class="btn btn-primary" onclick="submitAttendeeDialog()">
			<i class="ti ti-check icon" aria-hidden="true"></i>
			{lng p="ok"}
		</button>
	</div>
</div>

<script>
<!--
	registerLoadAction(initAttendeeDialog);

	var attAddr = [],
		Addr = [];

	{literal}function initAttendeeDialog()
	{
		{/literal}{foreach from=$addresses item=address}
		{$address.type}Addr.push(["{text noentities=true escape=true value=$address.id}",
									"{text noentities=true escape=true value=$address.firstname}",
									"{text noentities=true escape=true value=$address.lastname}"]);
		{/foreach}

		initAttendees(Addr, attAddr);
		enhanceAttendeeDialog();
		{literal}
	}

	function enhanceAttendeeDialog()
	{
		var source = EBID('addresses');
		if(!source)
			return;

		source.addEventListener('dblclick', function(e) {
			var item = e.target.closest('.addressItem, .addressItemActive');
			if(!item)
				return;
			selectAddressItem(item);
			addAttendee();
		});
	}{/literal}
//-->
</script>

{include file="li/dialog.foot.tpl"}
