<form action="{sessionurl file='users.php' params="do=transactions&id={$user.id}&filter=true"}" method="post" name="f1">
	{csrffield}
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	<input type="hidden" name="sortBy" id="sortBy" value="{$sortBy}" />
	<input type="hidden" name="sortOrder" id="sortOrder" value="{$sortOrder}" />
	<input type="hidden" name="singleAction" id="singleAction" value="" />
	<input type="hidden" name="singleID" id="singleID" value="" />
	{if !empty($queryString)}<input type="hidden" name="query" id="query" value="{text value=$queryString}" />{/if}

	<fieldset>
		<legend>{lng p="transactions"} ({email value=$user.email}, #{$user.id})</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection2(document.forms.f1,'transactions[', ']');">{acpicon file="dot.png" size="sm"}</a></th>
						<th style="width: 64px;"><a href="javascript:updateSort('transactionid');">{lng p="id"}
								{if $sortBy=='transactionid'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th><a href="javascript:updateSort('description');">{lng p="description"}
								{if $sortBy=='description'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 100px; text-align: right;"><a href="javascript:updateSort('amount');">{lng p="credits"}
								{if $sortBy=='amount'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 120px;"><a href="javascript:updateSort('date');">{lng p="date"}
								{if $sortBy=='date'}{acpicon file="sort_{$sortOrder}.png" size="sm"}{/if}</a></th>
						<th style="width: 64px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$transactions item=tx}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}" style="{if $tx.status==2}text-decoration:line-through;color:#666;{/if}">
							<td align="center"><input type="checkbox" name="transactions[]" value="{$tx.transactionid}" /></td>
							<td>{$tx.transactionid}</td>
							<td>{text value=$tx.description cut=45}</td>
							<td style="text-align:right;">{if $tx.amount<0}<span style="color:red;">{$tx.amount}</span>{else}{$tx.amount}{/if}</td>
							<td>{date nice=true timestamp=$tx.date}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
								<a href="{sessionurl file='users.php' params="do=editTransaction&transactionid={$tx.transactionid}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
								<a href="javascript:if(confirm('{lng p="realdel"}')) singleAction('delete', '{$tx.transactionid}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
								<option value="cancel">{lng p="cancel"}</option>
								<option value="uncancel">{lng p="uncancel"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
				<div class="text-end">
					{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
				</div>
			</div>
		</div>

		<div class="mt-3 row">
			<div class="col-md-6">
				{lng p="assets"}:
				{if $staticBalance<0}<span style="color:red;">{$staticBalance}</span>{else}{$staticBalance}{/if}
				{lng p="credits"}
			</div>
			<div class="col-md-6 text-end">
				{lng p="perpage"}:
				<input type="text" name="perPage" value="{$perPage}" size="5" />
				<input class="btn btn-sm btn-primary" type="submit" value=" {lng p="apply"} " />
			</div>
		</div>
	</fieldset>
</form>

<form action="{sessionurl file='users.php' params="do=transactions&add=true&id={$user.id}"}" method="post" onsubmit="spin(this);">
	{csrffield}
	<fieldset>
		<legend>{lng p="addtransaction"}</legend>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="description"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="description" value="" required="required" placeholder="{lng p="description"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="credits"}</label>
			<div class="col-sm-10">
				<input type="number" class="form-control" min="-999999" max="999999" step="1" name="amount" value="0" placeholder="{lng p="credits"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="status"}</label>
			<div class="col-sm-10">
				<select name="status" class="form-select">
					<option value="1">{lng p="booked"}</option>
					<option value="2">{lng p="cancelled"}</option>
				</select>
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-md-6"><input class="btn btn-primary" type="button" value="&laquo; {lng p="back"}" onclick="document.location.href='users.php?do=edit&id={$user.id}{$sessionUrlSuffix}';" /></div>
		<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="add"}" /></div>
	</div>
</form>