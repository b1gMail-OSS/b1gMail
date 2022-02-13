<div class="container">
	<div class="page-header"><h1>{lng p="news_news"}</h1></div>

	<p>
		{lng p="news_text"}
	</p>

	<div class="panel-group" id="faq">
		{if !$news}
		<i>{lng p="news_nonews"}</i>
		{else}
		{foreach from=$news key=id item=item}
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"><a data-toggle="collapse" data-parent="#news" href="#news-{$id}">{text value=$item.title}</a></h4>
			</div>
			<div id="news-{$id}" class="panel-collapse collapse">
				<div class="panel-body">
					<p>{$item.text}</p>
				</div>
			</div>
		</div>
		{/foreach}
		{/if}
	</div>
</div>