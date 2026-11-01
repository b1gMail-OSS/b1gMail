<fieldset>
	<legend>{lng p="languages"}</legend>

	<form action="{sessionurl file='prefs.languages.php'}" name="f1" method="post" onsubmit="spin(this)">
		{csrffield}
		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th class="text-center" style="width:3rem;"><a href="javascript:invertSelection(document.forms.f1,'lang_');">{acpicon file="dot.png" size="sm"}</a></th>
						<th>{lng p="language"}</th>
						<th>{lng p="author"}</th>
						<th width="60">&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$languages item=language key=langID}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td class="text-center">{if $language.writeable && !$language.default}<label class="form-check d-inline-block mb-0"><input class="form-check-input" type="checkbox" name="lang_{$langID}" /></label>{/if}</td>
							<td>{text value=$language.title}<br /><small>{text value=$language.charset}, {text value=$language.locale}</small></td>
							<td>{text value=$language.author}<br /><small>{text value=$language.authorWeb allowEmpty=true}</small></td>
							<td class="text-nowrap">
								<div class="btn-group btn-group-sm">
									<a href="{sessionurl file='prefs.languages.php' params="action=texts&lang={$langID}"}" class="btn btn-sm"><i class="fa-regular fa-comment"></i></a>
									{if $language.writeable && !$language.default}<a href="{sessionurl file='prefs.languages.php' params="delete={$langID}"}" onclick="return confirm('{lng p="realdel"}');" class="btn btn-sm"><i class="fa-regular fa-trash-can"></i></a>{/if}
								</div>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<div class="input-group input-group-sm bm-mass-action">
					<span class="input-group-text">{lng p="action"}</span>
					<select name="massAction" class="form-select form-select-sm">
							<option value="-">------------</option>
							<optgroup label="{lng p="actions"}">
								<option value="delete">{lng p="delete"}</option>
							</optgroup>
						</select>
					<button type="submit" name="executeMassAction" class="btn btn-dark-lt" value="1">{lng p="execute"}</button>
				</div>
			</div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addlanguage"}</legend>

	<form action="{sessionurl file='prefs.languages.php' params="add=true"}" method="post" enctype="multipart/form-data" onsubmit="spin(this)">
		{csrffield}
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