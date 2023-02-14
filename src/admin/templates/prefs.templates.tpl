<fieldset>
	<legend>{lng p="templates"}</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th>{lng p="template"}</th>
					<th>{lng p="author"}</th>
					<th style="width: 60px;">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$templates item=templateInfo key=template}
					{cycle name=class values="td1,td2" assign=class}
					<tr class="{$class}">
						<td>{text value=$templateInfo.title}<br /><small>{text value=$template}</small></td>
						<td>{text value=$templateInfo.author}<br /><small>{text value=$templateInfo.website allowEmpty=true}</small></td>
						<td>
							{if $templateInfo.prefs}<a href="prefs.templates.php?do=prefs&template={$template}&sid={$sid}" class="btn btn-sm"><i class="fa-solid fa-gear"></i></a>{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="defaultemplate"}</legend>

	<form action="prefs.templates.php?save=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<div class="mb-3 row">
			<label class="col-sm-2 col-form-label">{lng p="template"}</label>
			<div class="col-sm-10">
				<select name="template" class="form-select"
				{foreach from=$templates item=templateInfo key=template}
					<option value="{$template}"{if $defaultTemplate==$template} selected="selected"{/if}>{text value=$templateInfo.title}</option>
				{/foreach}
				</select>
			</div>
		</div>

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="save"}" />
		</div>
	</form>
</fieldset>
