<fieldset>
	<legend>{lng p="license"}</legend>

	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_adminplugin"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$adminVersion}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_core"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{if $coreVersion}{$coreVersion}{else}<i>({lng p="unknown"})</i>{/if}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_licstatus"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">
						<a href="{$pageURL}&action=common&sid={$sid}">{if $bms_prefs.licstate==2}
								<p class="text-red">{lng p="bms_expired"}</font>
							{elseif $bms_prefs.licstate==0}
								<p class="text-red">{lng p="bms_invalid"}</p>
							{elseif $bms_prefs.licstate==1}
								<p class="text-green">{lng p="bms_valid"}</p>
							{else}
								{lng p="bms_validating"}
							{/if}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="overview"}</legend>

	{if ($bms_prefs.licfeatures&4)!=0}
	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_pop3today"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$pop3Today}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_imaptoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$imapToday}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_smtptoday"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$smtpToday}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_pop3traffic"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{size bytes=$pop3Traffic}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_imaptraffic"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{size bytes=$imapTraffic}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_smtptraffic"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{size bytes=$smtpTraffic}</div>
				</div>
			</div>
		</div>
	</div>
	{/if}

	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_queueentries"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$queueEntries}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_inbound"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$queueInbound}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_outbound"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{$queueOutbound}</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_queue"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{if $queueRunning}<i class="fa-solid fa-circle-check text-green"></i> {lng p="bms_running"} ({$threadCount} {lng p="bms_threads"}){else}<i class="fa-solid fa-circle-xmark text-red"></i> {lng p="bms_not_running"}{/if}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_feature_tls"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{if ($bms_prefs.core_features&1)!=0}<i class="fa-solid fa-circle-check text-green"></i>{else}<i class="fa-solid fa-circle-xmark text-red"></i>{/if}</div>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">{lng p="bms_feature_sig"}</label>
				<div class="col-sm-8">
					<div class="form-control-plaintext">{if ($bms_prefs.core_features&2)!=0}<i class="fa-solid fa-circle-check text-green"></i>{else}<i class="fa-solid fa-circle-xmark text-red"></i>{/if}</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>


{if $notices}
	</div>
	<div class="card-footer" style="padding: 10px 0px 0px 0px;">
		<h3 style="margin: 10px 30px 10px 30px;">{lng p="notices"}</h3>
		<table class="table" style="margin-bottom: 0px;">
			{foreach from=$notices item=notice}
				<tr>
					<td class="align-top text-end" style="width: 60px;">
						{if $notice.type == 'debug'}
							<i class="fa-solid fa-bug text-danger"></i>
						{elseif $notice.type == 'info'}
							<i class="fa-solid fa-circle-info text-info"></i>
						{elseif $notice.type == 'warning'}
							<i class="fa-solid fa-triangle-exclamation text-warning"></i>
						{elseif $notice.type == 'error'}
							<i class="fa-regular fa-circle-xmark text-red"></i>
						{else}
							<i class="fa-solid fa-puzzle-piece text-cyan"></i>
						{/if}
					</td>
					<td class="align-top">{$notice.text}</td>
					<td class="align-top" style="width: 60px;">{if isset($notice.link)}<a href="{$notice.link}sid={$sid}"><i class="fa-solid fa-square-arrow-up-right"></i></a>{else}&nbsp;{/if}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/if}

<!--<script src="https://service.b1gmail.org/b1gmailserver/updates/?do=noticeJS&adminVersion={$adminVersion}&coreVersion={$coreVersion}&lang={$lang}"></script>-->
