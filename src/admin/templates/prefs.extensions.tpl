<fieldset>
	<legend>{lng p="webdiskicons"}</legend>

	<form action="prefs.extensions.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th style="width: 25px; text-align: center;"><a href="javascript:invertSelection(document.forms.f1,'ext_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="filetypes"}</th>
						<th style="width: 200px;">{lng p="type"}</th>
						<th style="width: 60px;">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$extensions item=ext}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">{if $ext.ext[0]!='.'}<input type="checkbox" name="ext_{$ext.id}" />{/if}</td>
							<td>{text value=$ext.ext}</td>
							<td>{text value=$ext.ctype}</td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.extensions.php?do=edit&id={$ext.id}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-pen-to-square"></i></a>
									{if $ext.ext[0]!='.'}<a href="prefs.extensions.php?delete={$ext.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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

<fieldset>
	<legend>{lng p="addwebdiskicon"}</legend>
	
	<form action="prefs.extensions.php?add=true&sid={$sid}" method="post" onsubmit="spin(this)" enctype="multipart/form-data">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="filetypes"}</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="ext" value="" placeholder="{lng p="filetypes"}">
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="icon"}</label>
			<div class="col-sm-10">
				<input type="file" class="form-control" name="icon" value="{text allowEmpty=true value=$bm_prefs.ssl_url}" placeholder="{lng p="icon"}" accept="image/*">
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="add"}" />
		</div>
	</form>
</fieldset>