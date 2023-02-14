<fieldset>
	<legend>{lng p="languages"}</legend>

	<form action="prefs.languages.php?sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'lang_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
						<th>{lng p="language"}</th>
						<th>{lng p="author"}</th>
						<th width="60">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$languages item=language key=langID}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center">{if $language.writeable && !$language.default}<input type="checkbox" name="lang_{$langID}" />{/if}</td>
							<td>{text value=$language.title}<br /><small>{text value=$language.charset}, {text value=$language.locale}</small></td>
							<td>{text value=$language.author}<br /><small>{text value=$language.authorWeb allowEmpty=true}</small></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="prefs.languages.php?action=texts&lang={$langID}&sid={$sid}" class="btn btn-sm"><i class="fa-regular fa-comment"></i></a>
									{if $language.writeable && !$language.default}<a href="prefs.languages.php?delete={$langID}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
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
	<legend>{lng p="addlanguage"}</legend>

	<form action="prefs.languages.php?add=true&sid={$sid}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		<div class="alert alert-warning">{lng p="sourcewarning"}</div>
		<p>{lng p="addlang_desc"}</p>

		<div class="mb-3 row">
			<label class="col-sm-2 col-form-check-label">{lng p="langfile"}</label>
			<div class="col-sm-10">
				<label class="form-check">
					<input type="file" name="langfile" class="form-control" accept=".lang.php" />
				</label>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="install"}" />
		</div>
	</form>
</fieldset>