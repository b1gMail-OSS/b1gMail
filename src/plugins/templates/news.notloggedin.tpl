<table class="nliTable">
	<!-- news -->
	<tr>
		<td class="nliIconTD"><i class="fa fa-newspaper-o fa-3x" aria-hidden="true"></i>
</td>
		<td class="nliTD">
					
			<h3>{lng p="news_news"}</h3>
			
			{lng p="news_text"}
			<br /><br />
			
			{if !$news}
			<i>{lng p="news_nonews"}</i>
			{else}
			{foreach from=$news key=id item=item}
			<div class="faqQuestion" onclick="toggleFAQItem({$id})">
				&nbsp;{text value=$item.title}
				<small>({date timestamp=$item.date dayonly=true})</small>
				<img id="faqAnswerImage_{$id}" src="{$tpldir}images/expand.png" width="11" height="11" border="0" alt="" class="faqExpand" />
			</div>
			<div class="faqAnswer" id="faqAnswer_{$id}" style="display:none;">
				<div style="padding:5px;">
					{$item.text}
				</div>
			</div>
			<br />
			{/foreach}
			{/if}
		</td>
	</tr>
</table>
