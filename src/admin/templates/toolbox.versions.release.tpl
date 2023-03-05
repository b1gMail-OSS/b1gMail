<fieldset>
	<legend>{lng p="releaseversion"}: {$versionNo}</legend>

	<p>{lng p="toolboxrelease"}</p>

	<div class="alert alert-warning">{lng p="toolboxonlinenote"}</div>

	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="test"}</label>
		<div class="col-sm-10">
			<button class="btn btn-muted" id="testButton_win" onclick="tbxTest({$versionID},'win');"><i class="fa-brands fa-windows"></i>&nbsp; {lng p="test"} (Windows)</button>
			<span id="testLoad_win" style="display:none;"><img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />&nbsp; {lng p="preparing"}...</span>
			<span id="testLink_win" style="display:none;"><a href="toolbox.php?do=downloadVersion&versionid={$versionID}&os=win&sid={$sid}" target="_blank"><i class="fa-solid fa-download"></i>&nbsp; {lng p="download"} (Windows)</a></span>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">&nbsp;</label>
		<div class="col-sm-10">
			<button class="btn btn-muted" id="testButton_mac" onclick="tbxTest({$versionID},'mac');"><i class="fa-brands fa-apple"></i>&nbsp; {lng p="test"} (Mac)</button>
			<span id="testLoad_win" style="display:none;"><img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />&nbsp; {lng p="preparing"}...</span>
			<span id="testLink_mac" style="display:none;"><a href="toolbox.php?do=downloadVersion&versionid={$versionID}&os=mac&sid={$sid}" target="_blank"><i class="fa-solid fa-download"></i>&nbsp; {lng p="download"} (Mac)</a></span>
		</div>
	</div>
	<div class="mb-3 row">
		<label class="col-sm-2 col-form-label">{lng p="release"}</label>
		<div class="col-sm-10">
			<button class="btn btn-muted" id="releaseButton" onclick="if(confirm('{lng p="reallyrelease"}')) tbxRelease({$versionID});"><i class="fa-solid fa-up-right-from-square"></i>&nbsp; {lng p="release"}</button>
			<span id="releaseLoad" style="display:none;"><img src="{$tpldir}images/load_16.gif" border="0" alt="" align="absmiddle" />&nbsp; {lng p="preparing"}...</span>
		</div>
	</div>
</fieldset>
