<fieldset>
	<legend>{lng p="phpinfo"}</legend>

	<iframe src="welcome.php?action=phpinfo&do=phpinfo&sid={$sid}" style="width:100%;height:440px;border:1px inset #CCC;"></iframe>
	<p align="right">
		<img src="{$tpldir}images/ico_download.png" width="16" height="16" border="0" alt="" align="absmiddle" />
		<a href="welcome.php?action=phpinfo&do=phpinfo&download=true&sid={$sid}" target="_blank">{lng p="download"}</a>
	</p>
</fieldset>