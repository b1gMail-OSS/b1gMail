<div class="row">
	<div class="col-md-6">
		<fieldset>
			<legend>{lng p="prefs"}</legend>

			<input class="btn btn-primary" type="button" value=" {lng p="setedit"} " onclick="document.location.href='prefs.email.php?action=smime&do=editca&sid={$sid}';" />
		</fieldset>
	</div>
	<div class="col-md-6">
		<fieldset>
			<legend>{lng p="addrootcert"}</legend>

			<form action="prefs.email.php?action=smime&add=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
				<div class="mb-3 row">
					<label class="col-sm-4 col-form-check-label">{lng p="certfile"}</label>
					<div class="col-sm-8">
						<label class="form-check">
							<input type="file" name="certfile" class="form-control" />
						</label>
					</div>
				</div>

				<div class="text-end">
					<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
				</div>
			</form>
		</fieldset>
	</div>
</div>




<fieldset>
	<legend>{lng p="rootcerts"}</legend>

	<form action="prefs.email.php?action=smime&sid={$sid}" name="f1" method="post">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'certs[]');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="name"}</th>
						<th width="180">{lng p="validity"}</th>
						<th width="60">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$certs item=cert}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td style="text-align: center;"><input type="checkbox" name="certs[]" value="{$cert.certificateid}" /></td>
							<td>{text value=$cert.cn cut=45}</td>
							<td>{if !$cert.valid}<font color="red">{/if}{lng p="to"} {date timestamp=$cert.validto dayonly=true}<br /><small>{lng p="from"} {date timestamp=$cert.validfrom dayonly=true}</small>{if !$cert.valid}</font>{/if}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.email.php?action=smime&export={$cert.certificateid}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-circle-down"></i></a>
									<a href="prefs.email.php?action=smime&delete={$cert.certificateid}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div style="float: left;">{lng p="action"}:&nbsp;</div>
				<div style="float: left;">
					<div class="btn-group btn-group-sm">
						<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="export">{lng p="export"}</option>
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
						<input type="submit" name="executeMassAction" value="{lng p="execute"}" class="btn btn-sm btn-dark-lt" />
					</div>
				</div>
			</div>
		</div>
	</form>
</fieldset>