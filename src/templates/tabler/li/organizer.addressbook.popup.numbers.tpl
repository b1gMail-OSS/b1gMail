{capture assign="dialogTitleText"}{lng p="addressbook"}{/capture}
{include file="li/dialog.head.tpl" dialogTitle=$dialogTitleText dialogBodyClass="bm-dialog-addressbook bm-dialog-sms-numbers" dialogOnLoad="documentLoader()"}

<div class="bm-dialog-page bm-dialog-page-fill bm-addressbook-dialog bm-sms-numbers-dialog">
	<div class="bm-addressbook-scroll">
		<div class="bm-addressbook-picker">
			<label class="form-label">{lng p="addressbook"}</label>
			<div class="addressDiv bm-addressbook-list" id="addresses" data-empty="{lng p="nothingfound"}"></div>
		</div>
	</div>

	<div class="bm-dialog-actions">
		<button type="button" class="btn btn-ghost-secondary" onclick="parent.hideOverlay()">{lng p="cancel"}</button>
		<button type="button" class="btn btn-primary" onclick="submitNumberDialog()">
			<i class="ti ti-check icon" aria-hidden="true"></i>
			{lng p="ok"}
		</button>
	</div>
</div>

<script>
<!--
	registerLoadAction(initNumberDialog);

	var Addr = [];

	{literal}function initNumberDialog()
	{
		{/literal}{foreach from=$addresses item=address}
		Addr.push(["{text noentities=true escape=true value=$address.lastname}, {text noentities=true escape=true value=$address.firstname}",
			"{text noentities=true escape=true value=$address.handy}",
			"{text noentities=true escape=true value=$address.work_handy}"]);
		{/foreach}

		initNumbers(Addr);
		if(!Addr.length)
		{literal}{{/literal}
			var box = EBID('addresses');
			if(box)
				box.textContent = box.getAttribute('data-empty') || '';
		{literal}}{/literal}
		{literal}
	}{/literal}
//-->
</script>

{include file="li/dialog.foot.tpl"}
