<div id="contentHeader">
	<div class="left">
		<i class="fa fa-puzzle-piece" aria-hidden="true"></i>
		{lng p="customize"}
	</div>
</div>

<div class="scrollContainer"><div class="pad">
	<form name="f1" method="post" action="start.php?action=saveCustomize&sid={$sid}">
		<table class="listTable">
			<tr>
				<th class="listTableHead" colspan="2"> {lng p="activewidgets"}</th>
			</tr>
			
			{foreach from=$possibleWidgets key=widget item=info}
			<tr>
				<td class="listTableLeft"><input type="checkbox" id="widget_{$widget}" name="widget_{$widget}"{if $info.active} checked="checked"{/if} /></td>
				<td class="listTableRight">
					<label for="widget_{$widget}">{if $info.icon}<img src="{$info.icon}" border="0" alt="" width="16" height="16" /> {/if}{$info.title}</label>
				</td>
			</tr>
			{/foreach}
			
			<tr>
				<td class="listTableLeft">&nbsp;</td>
				<td class="listTableRight">
					<input type="submit" class="primary" value="{lng p="ok"}" />
					<input type="reset" value="{lng p="reset"}" />
				</td>
			</tr>
		</table>
	</form>
</div></div>
