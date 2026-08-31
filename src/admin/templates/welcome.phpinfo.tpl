<h1>{lng p="phpinfo"}</h1>

<iframe src="welcome.php?action=phpinfo&do=phpinfo{$sessionUrlSuffix}" style="width:100%; height:440px; border:0px; margin: 0px;"></iframe>
</div>
<div class="card-footer text-end">
	<a href="{sessionurl file='welcome.php' params="action=phpinfo&do=phpinfo&download=true"}" class="btn btn-primary" target="_blank"><img src="{$tpldir}images/ico_download.png" width="16" height="16" border="0" alt="" align="absmiddle" />&nbsp; {lng p="download"}</a>
</div>