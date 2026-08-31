<fieldset>
	<legend>{lng p="layout_addremove"}</legend>

	<form action="{sessionurl file='prefs.widgetlayouts.php' params="action={$action}&do=addremove"}" method="post" onsubmit="spin(this)">
		{csrffield}
		<input type="hidden" name="save" value="true" />

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th width="20">&nbsp;</th>
						<th>{lng p="title"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$possibleWidgets key=widget item=info}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><input type="checkbox" id="widget_{$widget}" name="widget_{$widget}"{if $info.active} checked="checked"{/if} /></td>
							<td>
								<label for="widget_{$widget}">{$info.title}</label>
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer text-end">
				<input class="btn btn-sm btn-primary" type="submit" value="{lng p="save"}" />
			</div>
		</div>
	</form>
</fieldset>
