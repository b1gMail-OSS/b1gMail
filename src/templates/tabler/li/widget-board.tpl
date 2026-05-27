<div class="bm-widget-board">
	<div id="startBoxes"></div>
	<div id="startBoxes_elems" style="display:none">
	{foreach from=$widgets item=widget key=key}
		<div title="{text value=$widget.title}" rel="{if $widget.hasPrefs}1{else}0{/if},{$widget.prefsW},{$widget.prefsH},{if $widget.icon}{$widget.icon}{else}0{/if}" id="{$key}">{include file=$widget.template}</div>
	{/foreach}
	</div>
</div>

<script src="./clientlib/dragcontainer.js?{fileDateSig file="../../clientlib/dragcontainer.js"}" type="text/javascript"></script>
<script src="{$tpldir}js/dashboard.js?{fileDateSig file="js/dashboard.js"}" type="text/javascript"></script>
<script>
<!--
	currentSID = '{$sid}';
	var dc = bmInitWidgetBoard('startBoxes', {$boardCols|default:3}, 'dc', '{$widgetOrder|escape:'javascript'}', {$boardSaveCallback});
	{if isset($autoSetPreviewPos)}autoSetPreviewPos();{/if}
//-->
</script>
