<div id="contentHeader">
	<div class="left">
		<i class="fa fa-home" aria-hidden="true"></i> {lng p="welcome"}!
	</div>
	
	<div class="right">
		{if $templatePrefs.showUserEmail}
		<em>{$_userEmail}</em>
		{/if}
		<button onclick="document.location.href='start.php?action=customize&sid={$sid}';" type="button">
			<i class="fa fa-puzzle-piece" aria-hidden="true"></i>
			{lng p="customize"}
		</button>
	</div>
</div>

<div class="scrollContainer"><div class="pad">
	{hook id="start.page.tpl:head"}
	
	<div id="startBoxes">
	</div>
	<div id="startBoxes_elems" style="display:none">
	{foreach from=$widgets item=widget key=key}
		<div title="{text value=$widget.title}" rel="{if $widget.hasPrefs}1{else}0{/if},{$widget.prefsW},{$widget.prefsH},{if $widget.icon}{$widget.icon}{else}0{/if}" id="{$key}">{include file=$widget.template}</div>
	{/foreach}
	</div>
	
	{hook id="start.page.tpl:foot"}
</div></div>

<script src="./clientlib/dragcontainer.js" type="text/javascript"></script>
<script>
<!--
	currentSID = '{$sid}';
	var dc = new dragContainer('startBoxes', 3, 'dc');
	dc.order = '{$widgetOrder}';
	dc.onOrderChanged = startBoardOrderChanged;
	dc.run();
	{if $autoSetPreviewPos}autoSetPreviewPos();{/if}
//-->
</script>
