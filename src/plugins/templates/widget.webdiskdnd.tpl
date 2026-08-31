<div class="innerWidget" style="text-align:center;">
	<div id="wdDnDArea">
		<i class="fa fa-folder-open-o fa-5x" aria-hidden="true"></i>
	</div>
	
	<script src="./clientlib/dndupload.js" type="text/javascript"></script>
	
	<script>
	<!--
	(function() {
		var uploadUrl = bmAppendSession('{sessionurl file='webdisk.php' params='folder=0&action=dndUpload'|escape:'javascript'}');
		var doneUrl = bmAppendSession('{sessionurl file='webdisk.php' params='folder=0'|escape:'javascript'}');
		initDnDUpload(EBID('wdDnDArea'), uploadUrl, function() { document.location.href = doneUrl; });
	})();
	//-->
	</script>
</div>