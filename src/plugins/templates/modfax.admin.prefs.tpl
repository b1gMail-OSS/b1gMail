<form action="{$pageURL}{$sessionUrlSuffixHtml}" method="post" id="prefsForm" onsubmit="spin(this)">
	{csrffield}
	<input type="hidden" name="save" value="1" />

	<fieldset>
		<legend>{lng p="common"}</legend>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="send_safecode"{if $faxPrefs.send_safecode} checked="checked"{/if} />
				<span class="form-check-label">{lng p="safecode"}?</span>
			</label>
		</div>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="refund_on_error"{if $faxPrefs.refund_on_error} checked="checked"{/if} />
				<span class="form-check-label">{lng p="modfax_refund_on_error"}?</span>
			</label>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="defaults"}</legend>
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label" for="default_country_prefix">{lng p="modfax_country_prefix"}</label>
				<input type="text" class="form-control" name="default_country_prefix" id="default_country_prefix" value="{if isset($faxPrefs.default_country_prefix)}{text value=$faxPrefs.default_country_prefix allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-6">
				<label class="form-label" for="default_faxgateid">{lng p="defaultgateway"}</label>
				<select class="form-select" name="default_faxgateid" id="default_faxgateid">
				{foreach from=$gateways item=gwTitle key=gwID}
					<option value="{$gwID}"{if $gwID==$faxPrefs.default_faxgateid} selected="selected"{/if}>{text value=$gwTitle}</option>
				{/foreach}
				</select>
			</div>
			<div class="col-md-6">
				<label class="form-label" for="default_name">{lng p="modfax_fromname"}</label>
				<input type="text" class="form-control" name="default_name" id="default_name" value="{if isset($faxPrefs.default_name)}{text value=$faxPrefs.default_name allowEmpty=true}{/if}" />
			</div>
			<div class="col-md-6">
				<label class="form-label" for="default_no">{lng p="modfax_fromno"}</label>
				<input type="text" class="form-control" name="default_no" id="default_no" value="{if isset($faxPrefs.default_no)}{text value=$faxPrefs.default_no allowEmpty=true}{/if}" />
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="modfax_perms"}</legend>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="allow_ownname"{if $faxPrefs.allow_ownname} checked="checked"{/if} />
				<span class="form-check-label">{lng p="modfax_allow_ownname"}?</span>
			</label>
		</div>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="allow_ownno"{if $faxPrefs.allow_ownno} checked="checked"{/if} />
				<span class="form-check-label">{lng p="modfax_allow_ownno"}?</span>
			</label>
		</div>
		<div class="mb-3">
			<label class="form-check">
				<input class="form-check-input" type="checkbox" name="allow_pdf"{if $faxPrefs.allow_pdf} checked="checked"{/if} />
				<span class="form-check-label">{lng p="modfax_allow_pdf"}?</span>
			</label>
		</div>
	</fieldset>

	<fieldset>
		<legend>{lng p="modfax_faxtpl"}</legend>
		<div class="row g-3">
		{foreach from=$tplBlocks item=block key=blockID}
			<div class="col-md-6 col-lg-4">
				<label class="form-label">{lng p="modfax_block"} {$blockID+1}</label>
				<select class="form-select" name="tpl_blocks[{$blockID}]">
					<option value="-1"{if $block==-1} selected="selected"{/if}>--------</option>
					<option value="0"{if $block==0} selected="selected"{/if}>{lng p="modfax_textblock"}</option>
					<option value="1"{if $block==1} selected="selected"{/if}>{lng p="modfax_pagebreak"}</option>
					<option value="2"{if $block==2} selected="selected"{/if}>{lng p="modfax_cover"}</option>
					<option value="3"{if $block==3} selected="selected"{/if}>{lng p="modfax_pdffile"}</option>
				</select>
			</div>
		{/foreach}
		</div>
	</fieldset>

	<div class="text-end mt-3">
		<button type="submit" class="btn btn-primary">
			<i class="ti ti-device-floppy me-1"></i>
			{lng p="save"}
		</button>
	</div>
</form>
