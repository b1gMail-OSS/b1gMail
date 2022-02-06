<fieldset>
	<legend>{lng p="layout_addremove"}</legend>
	
	<form action="prefs.widgetlayouts.php?action={$action}&do=addremove&sid={$sid}" method="post" onsubmit="spin(this)">
		<input type="hidden" name="save" value="true" />
		
		<table width="100%">
			<tr>
				<td width="40" valign="top"><img src="{$tpldir}images/wlayout_add32.png" border="0" alt="" width="32" height="32" /></td>
				<td class="td2">
					<table class="list">
						<tr>
							<th width="20">&nbsp;</th>
							<th>{lng p="title"}</th>
						</tr>
						
						{foreach from=$possibleWidgets key=widget item=info}
						{cycle name=class values="td1,td2" assign=class}
						<tr class="{$class}">
							<td align="center"><input type="checkbox" id="widget_{$widget}" name="widget_{$widget}"{if $info.active} checked="checked"{/if} /></td>
							<td>
								<label for="widget_{$widget}">{$info.title}</label>
							</td>
						</tr>
						{/foreach}
					</table>
				</td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</p>
	</form>
</fieldset>
