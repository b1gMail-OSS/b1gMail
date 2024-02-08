<fieldset>
	<legend>{lng p="user"}: {email value=$userRow.email}</legend>

	<div action="abuse.php?do=show&userid={$userID}&sid={$sid}" method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="status"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{if $userRow.gesperrt=='no'}<i class="fa-solid fa-lock"></i>{elseif $userRow.gesperrt=='yes'}<i class="fa-solid fa-lock-open"></i>{elseif $userRow.gesperrt=='locked'}<i class="fa-solid fa-lock-open"></i>{elseif $userRow.gesperrt=='delete'}<i class="fa-solid fa-hammer"></i>{/if}&nbsp; {if $userRow.gesperrt=='no'}{lng p="active"}
							{elseif $userRow.gesperrt=='yes'}{lng p="locked"}
							{elseif $userRow.gesperrt=='locked'}{lng p="notactivated"}
							{elseif $userRow.gesperrt=='delete'}{lng p="deleted"}{/if}
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="email"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<small>{$emailMails} {lng p="emails"}, {$emailFolders} {lng p="folders"}</small>
							{progressBar value=$userRow.mailspace_used max=$groupRow.storage width=200}
							<small>{size bytes=$userRow.mailspace_used} / {size bytes=$groupRow.storage} {lng p="used"}</small>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="webdisk"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							<small>{$diskFiles} {lng p="files"}, {$diskFolders} {lng p="folders"}</small>
							{progressBar value=$userRow.diskspace_used max=$groupRow.webdisk width=200}
							<small>{size bytes=$userRow.diskspace_used} / {size bytes=$groupRow.webdisk} {lng p="used"}</small>
						</div>
					</div>
				</div>
				{if $groupRow.sms_monat>0}
					<div class="row">
						<label class="col-sm-4 col-form-label">{lng p="monthasset"}</label>
						<div class="col-sm-8">
							<div class="form-control-plaintext">
								{progressBar value=$usedMonthSMS max=$groupRow.sms_monat width=200}
								<small>{$usedMonthSMS} / {$groupRow.sms_monat} {lng p="credits"} {lng p="used2"}</small>
							</div>
						</div>
					</div>
				{/if}
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="lastlogin"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{date timestamp=$userRow.lastlogin nice=true nozero=true}<br /><small>{text value=$userRow.ip}</small></div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="regdate"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{date timestamp=$userRow.reg_date nice=true nozero=true}<br /><small>{text value=$userRow.reg_ip}</small></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="group"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext"><a href="groups.php?do=edit&id={$groupRow.id}&sid={$sid}">{text value=$groupRow.titel}</a></div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="receivedmails"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{$userRow.received_mails}</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="sentmails"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{$userRow.sent_mails}</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="wdtraffic"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">
							{if $groupRow.traffic>0}{progressBar value=$userRow.traffic_down+$userRow.traffic_up max=$groupRow.traffic width=200}{/if}
							<small>{size bytes=$userRow.traffic_down+$userRow.traffic_up}{if $groupRow.traffic>0} / {size bytes=$groupRow.traffic}{/if} {lng p="used2"}</small>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="lastimap"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{date timestamp=$userRow.last_imap nice=true nozero=true}</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="lastpop3"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{date timestamp=$userRow.last_pop3 nice=true nozero=true}</div>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-4 col-form-label">{lng p="lastsmtp"}</label>
					<div class="col-sm-8">
						<div class="form-control-plaintext">{date timestamp=$userRow.last_smtp nice=true nozero=true}</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="notes"}</label>
			<div class="col-sm-10">
				<textarea class="form-control" name="notes" placeholder="{lng p="notes"}">{text value=$userRow.notes allowEmpty=true}</textarea>
			</div>
		</div>

		<div class="mb-3">
			<div class="input-group">
				<button class="btn btn-outline-secondary" type="submit" name="save"><i class="fa-regular fa-floppy-disk"></i>&nbsp; {lng p="save"}</button>
				<button class="btn btn-outline-secondary" onclick="document.location.href='users.php?do=edit&id={$userID}&sid={$sid}';return(false);"><i class="fa-regular fa-id-card"></i>&nbsp; {lng p="profile"}</button>
				<button class="btn btn-outline-secondary" onclick="window.singleAction('{if $userRow.gesperrt=='no'}lock{elseif $userRow.gesperrt=='yes'}unlock{elseif $userRow.gesperrt=='locked'}activate{elseif $userRow.gesperrt=='delete'}recover{/if}User', '{$userRow.id}');return(false);">{if $userRow.gesperrt=='no'}<i class="fa-solid fa-lock"></i>{elseif $userRow.gesperrt=='yes'}<i class="fa-solid fa-lock-open"></i>{elseif $userRow.gesperrt=='locked'}<i class="fa-solid fa-lock-open"></i>{elseif $userRow.gesperrt=='delete'}<i class="fa-solid fa-hammer"></i>{/if}&nbsp; {if $userRow.gesperrt=='no'}{lng p="lock"}{elseif $userRow.gesperrt=='yes'}{lng p="unlock"}{elseif $userRow.gesperrt=='locked'}{lng p="activate"}{elseif $userRow.gesperrt=='delete'}&nbsp; {lng p="recover"}{/if}</button>
				<button class="btn btn-outline-secondary" onclick="window.singleAction('deleteUser', '{$userRow.id}');return(false);">{if $userRow.gesperrt=='delete'}<i class="fa-regular fa-trash-can text-danger"></i>{else}<i class="fa-regular fa-trash-can"></i>{/if}&nbsp; {lng p="delete"}</button>
				<button class="btn btn-outline-secondary" onclick="if(confirm('{lng p="loginwarning"}')) window.open('users.php?do=login&id={$userID}&sid={$sid}');return(false);"><i class="fa-solid fa-house-chimney-user"></i>&nbsp; {lng p="login"}</button>
			</div>
		</div>
		</form>
</fieldset>

{if $sendStats OR $recvStats}
	<fieldset>
		<legend>{lng p="stats"}</legend>
		<div class="accordion accordion-flush" id="sendstats">
			{if $sendStats}
				<div class="accordion-item">
					<div class="accordion-header" id="heading-sendstats">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-sendstats" aria-expanded="false">{lng p="sendstats"} ({lng p="last7d"})</button>
					</div>
					<div id="collapse-sendstats" class="accordion-collapse collapse" data-bs-parent="#sendstats" style="">
						<div class="accordion-body pt-0">
							<div class="table-responsive card">
								<table class="table table-vcenter table-striped">
									<thead>
									<tr>
										<th>{lng p="timeframe"}</th>
										<th style="width: 20%;">{lng p="mails"}</th>
										<th style="width: 20%;">&sum; {lng p="recipients"}</th>
									</tr>
									</thead>
									<tbody>
									{foreach from=$sendStats item=stat}
									{cycle name=class values="td1,td2" assign=class}
									<tr class="{$class}" data-time-stamp="{$stat.timeStamp}">
										<td><i class="fa fa-chevron-right" style="font-size:10px;" id="sendStats_{$stat.timeStamp}_chevron"></i>
											<a href="javascript:void(0);" onclick="expandStatsDay('send', {$userID}, {$stat.timeStamp});">
												{date timestamp=$stat.timeStamp dayonly=true}
											</a></td>
										<td>{$stat.mails}</td>
										<td>{$stat.recipients}</td>
									</tr>
									<tbody id="sendStats_{$stat.timeStamp}" style="display:none;"></tbody>
									{/foreach}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			{/if}
			{if $recvStats}
				<div class="accordion-item">
					<div class="accordion-header" id="heading-recvstats">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-recvstats" aria-expanded="false">{lng p="recvstats"} ({lng p="last7d"})</button>
					</div>
					<div id="collapse-recvstats" class="accordion-collapse collapse" data-bs-parent="#sendstats" style="">
						<div class="accordion-body pt-0">
							<div class="table-responsive card">
								<table class="table table-vcenter table-striped">
									<thead>
									<tr>
										<th>{lng p="timeframe"}</th>
										<th style="width: 20%;">{lng p="mails"}</th>
										<th style="width: 20%;">&sum; {lng p="recipients"}</th>
									</tr>
									</thead>
									<tbody>
									{foreach from=$recvStats item=stat}
									{cycle name=class values="td1,td2" assign=class}
									<tr class="{$class}" data-time-stamp="{$stat.timeStamp}">
										<td><i class="fa fa-chevron-right" style="font-size:10px;" id="recvStats_{$stat.timeStamp}_chevron"></i>
											<a href="javascript:void(0);" onclick="expandStatsDay('recv', {$userID}, {$stat.timeStamp});">
												{date timestamp=$stat.timeStamp dayonly=true}
											</a></td>
										<td>{$stat.mails}</td>
										<td>{size bytes=$stat.size}</td>
									</tr>
									<tbody id="recvStats_{$stat.timeStamp}" style="display:none;"></tbody>
									{/foreach}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</fieldset>
{/if}

<form action="abuse.php?do=show&userid={$userID}&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />

	<fieldset>
		<legend>{lng p="points"}</legend>

		<div class="table-responsive card">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 40px;">&nbsp;</th>
					<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'entries[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
					<th style="width: 80px;">{lng p="points"}</th>
					<th>{lng p="type"}</th>
					<th>{lng p="comment"}</th>
					<th style="width: 150px;">{lng p="date"}&nbsp; <img src="{$tpldir}images/sort_desc.png" border="0" alt="" width="7" height="6" align="absmiddle" /></th>
					<th style="width: 80px;">{lng p="sum"}</th>
					<th style="width: 32px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$points item=entry}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}"{if $entry.expired} style="text-decoration:line-through;"{/if}>
						<td class="text-center"><i class="fa-regular fa-circle text-{$entry.indicator}"></i></td>
						<td class="text-center"><input type="checkbox" name="entries[]" value="{$entry.entryid}" /></td>
						<td>{$entry.points}</td>
						<td>{$entry.typeText}</td>
						<td>{text value=$entry.comment}</td>
						<td>{date timestamp=$entry.date nice=true}</td>
						<td>{$entry.sum}</td>
						<td>
							<a href="javascript:singleAction('delete', '{$entry.entryid}');" title="{lng p="delete"}" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>
	</fieldset>
</form>
