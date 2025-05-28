<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<form action="prefs.common.php?action=taborder&save=true&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="taborder"}</legend>

		<div class="card">
			<div class="table-responsive">
				<table class="table table-vcenter table-striped">
					<thead>
					<tr>
						<th colspan="2">{lng p="title"}</th>
						<th style="width: 80px;">{lng p="pos"}</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$pageTabs item=tab key=tabKey}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td style="width: 20px; text-align: center;">
								<i class="fa {$tab.faIcon}" aria-hidden="true"></i></td>
							<td>{if isset($tab.text)}{text value=$tab.text}{else}-{/if}</td>
							<td><input type="text" name="order[{$tabKey}]" value="{$tab.order}" size="6" /></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			<div class="card-footer text-end">
				<input class="btn btn-sm btn-primary" type="submit" value="{lng p="save"}" />
			</div>
		</div>
	</fieldset>
</form>