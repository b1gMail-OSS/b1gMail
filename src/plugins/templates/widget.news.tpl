<div class="innerWidget" style="max-height: 180px; overflow-y: auto;">
	<table cellspacing="0" width="100%">
{foreach from=$bmwidget_news_news key=newsID item=news}
	<tr>
		<td width="20" align="center"><img width="16" height="16" src="plugins/templates/images/news_icon.png" border="0" alt="" align="absmiddle" /></td>
		<td><a href="javascript:void(0);" onclick="javascript:openOverlay('start.php?action=newsPlugin&do=showNews&id={$newsID}&sid={$sid}', '{text value=$news.title cut=50 escape=true}', 500, 380);">{text value=$news.title cut=30}</a></td>
		<td align="left" width="60"><small>{date timestamp=$news.date dayonly=true}</small></td>
	</tr>
{/foreach}
	</table>
</div>