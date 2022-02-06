<fieldset>
	<legend>{lng p="defaultlayout"}</legend>

	<form action="prefs.widgetlayouts.php?action={$action}&saveOrder=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<input type="hidden" name="order" id="order" value="{$widgetOrder}" />
		
		<div id="dashboard" style="border:1px inset #CCCCCC;background-color:#FEFEFE;padding:10px;position:relative;">
		</div>
		<div id="dashboard_elems" style="display:none">
		{foreach from=$widgets item=widget key=key}
			<div title="{text value=$widget.title}" id="{$key}"><div style="padding:5px;"><i>({text value=$widget.title})</i></div></div>
		{/foreach}
		</div>
		
		<script src="../clientlib/dragcontainer.js" type="text/javascript"></script>
		<script>
		<!--
			var dc = new dragContainer('dashboard', 3, 'dc');
			dc.order = '{$widgetOrder}';
			dc.onOrderChanged = dashboardOrderChanged;
			dc.run();
		//-->
		</script>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/wlayout_add.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				<a href="prefs.widgetlayouts.php?action={$action}&do=addremove&sid={$sid}">{lng p="layout_addremove"}</a>
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="save"} " />
			</div>
		</p>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="reset"}</legend>
	
	<form action="prefs.widgetlayouts.php?action={$action}&resetOrder=true&sid={$sid}" method="post" onsubmit="spin(this)">
		<table>
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/wlayout_reset32.png" border="0" alt="" width="32" height="32" /></td>
				<td valign="top">
					<p>
						{lng p="layout_resetdesc"}
					</p>
					
					<blockquote>
						{foreach from=$groups item=group key=groupID}
							<input type="checkbox" name="groups[]" value="{$groupID}" id="group_{$groupID}"{if !$smarty.get.toGroup||$smarty.get.toGroup==$groupID} checked="checked"{/if} />
								<label for="group_{$groupID}"><b>{text value=$group.title}</b></label><br />
						{/foreach}
					</blockquote>
				</td>
			</tr>
		</table>
		
		<p>
			<div style="float:left;">
				<img src="{$tpldir}images/warning.png" border="0" alt="" width="16" height="16" align="absmiddle" />
				{lng p="undowarn"}
			</div>
			<div style="float:right;">
				<input class="button" type="submit" value=" {lng p="execute"} " />
			</div>
		</p>
	</form>
</fieldset>
