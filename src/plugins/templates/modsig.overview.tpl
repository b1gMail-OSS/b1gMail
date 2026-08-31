<fieldset>
	<legend>{lng p="modsig_signatures"}</legend>

	<form id="modsigMassForm" action="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=list"}" method="post" style="display:none;" aria-hidden="true">
		{csrffield}
		<input type="hidden" name="op" value="massaction" />
	</form>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th style="width: 25px; text-align:center;"><a href="javascript:invertSelection(document.getElementById('modsigMassForm'),'sigs[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
					<th>{lng p="modsig_signature"}</th>
					<th style="width: 80px;">{lng p="modsig_html"}?</th>
					<th style="width: 75px;">{lng p="weight"}</th>
					<th style="width: 70px;">{lng p="modsig_used"}</th>
					<th style="width: 60px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$signatures item=sig}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td class="text-center"><input type="checkbox" name="sigs[]" value="{$sig.signatureid}" form="modsigMassForm" /></td>
						<td>{$sig.displayText}</td>
						<td><input type="checkbox" disabled="disabled"{if $sig.html} checked="checked"{/if} /></td>
						<td>{$sig.weight}%</td>
						<td>{$sig.counter}</td>
						<td class="text-nowrap">
							<div class="btn-group btn-group-sm">
								<form method="post" action="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=list"}" class="d-inline">
									{csrffield}
									<input type="hidden" name="op" value="{if $sig.paused}activate{else}deactivate{/if}" />
									<input type="hidden" name="id" value="{$sig.signatureid}" />
									<button type="submit" class="btn btn-sm">{if !$sig.paused}<i class="fa-solid fa-mug-saucer"></i>{else}<i class="fa-solid fa-play"></i>{/if}</button>
								</form>
								<a href="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=edit&id={$sig.signatureid}"}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
								<form method="post" action="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=list"}" class="d-inline" onsubmit="return confirm('{lng p="realdel"}');">
									{csrffield}
									<input type="hidden" name="op" value="delete" />
									<input type="hidden" name="id" value="{$sig.signatureid}" />
									<button type="submit" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></button>
								</form>
							</div>
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
		<div class="card-footer">
			<div style="float: left;">{lng p="action"}:&nbsp;</div>
			<div style="float: left;">
				<div class="btn-group btn-group-sm">
					<select name="massAction" class="form-select form-select-sm" form="modsigMassForm">
						<option value="-">------------</option>
						<optgroup label="{lng p="actions"}">
							<option value="pause">{lng p="pause"}</option>
							<option value="continue">{lng p="continue"}</option>
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>
					<button type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" form="modsigMassForm" onclick="spin(document.getElementById('modsigMassForm'));">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="modsig_add"}</legend>

	<form action="{sessionurl file='plugin.page.php' params="plugin={$sigPlugin}&do=list"}" method="post" onsubmit="return modsigFormSubmit(this);">
		{csrffield}
		<input type="hidden" name="op" value="add" />
		{include file=$modsigTextFieldTpl}
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="weight"}</label>
			<div class="col-sm-10">
				<div class="input-group mb-2">
					<input type="text" class="form-control" name="weight" value="100">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="groups"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="groups[]" value="*" id="group_all" checked="checked">
					<span class="form-check-label">{lng p="all"}</span>
				</label>
				{foreach from=$groups item=group key=groupID}
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}">
						<span class="form-check-label">{text value=$group.title}</span>
					</label>
				{/foreach}
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="paused"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input class="form-check-input" type="checkbox" name="paused">
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>
