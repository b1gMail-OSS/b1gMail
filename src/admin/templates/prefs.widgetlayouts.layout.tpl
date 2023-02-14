<fieldset>
	<legend>{lng p="defaultlayout"}</legend>

	<form action="prefs.widgetlayouts.php?action={$action}&saveOrder=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<input type="hidden" name="order" id="order" value="{$widgetOrder}" />
		
		<div id="dashboard" style="border: 0px; background-color: #FEFEFE; padding: 10px; position: relative;">
		</div>
		<div id="dashboard_elems" style="display: none">
			{foreach from=$widgets item=widget key=key}
				<div title="{text value=$widget.title}" id="{$key}"><div style="padding:5px;"><i>({text value=$widget.title})</i></div></div>
			{/foreach}
		</div>
		
		<script src="../clientlib/dragcontainer.js?{fileDateSig file="../../clientlib/dragcontainer.js"}" type="text/javascript"></script>
		<script>
		<!--
			var dc = new dragContainer('dashboard', 3, 'dc');
			dc.order = '{$widgetOrder}';
			dc.onOrderChanged = dashboardOrderChanged;
			dc.run();
		//-->
		</script>

		<div class="row mb-3" style="margin-top: 20px;">
			<div class="col-md-6"><a href="prefs.widgetlayouts.php?action={$action}&do=addremove&sid={$sid}" class="btn btn-muted"><i class="fa-solid fa-puzzle-piece"></i>&nbsp; {lng p="layout_addremove"}</a></div>
			<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="save"}" /></div>
		</div>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="reset"}</legend>

	<div class="alert alert-warning">{lng p="undowarn"}</div>

	<form action="prefs.widgetlayouts.php?action={$action}&resetOrder=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<p>{lng p="layout_resetdesc"}</p>

		{foreach from=$groups item=group key=groupID}
			<div class="mb-3 row">
				<div class="col-sm-12">
					<label class="form-check">
						<input class="form-check-input" type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if !$smarty.get.toGroup||$smarty.get.toGroup==$groupID} checked="checked"{/if}>
						<span class="form-check-label">{text value=$group.title}</span>
					</label>
				</div>
			</div>
		{/foreach}

		<div class="text-end">
			<input class="btn btn-primary" type="submit" value="{lng p="execute"}" />
		</div>
	</form>
</fieldset>
