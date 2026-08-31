<h1 class="mb-3">{lng p="news_news"}</h1>

<p class="text-secondary mb-4">
	{lng p="news_text"}
</p>

<div class="accordion" id="news">
	{if !$news}
	<p class="text-secondary mb-0"><em>{lng p="news_nonews"}</em></p>
	{else}
	{foreach from=$news key=id item=item}
	<div class="accordion-item">
		<h2 class="accordion-header" id="news-heading-{$id}">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#news-{$id}" aria-expanded="false" aria-controls="news-{$id}">
				{text value=$item.title}
			</button>
		</h2>
		<div id="news-{$id}" class="accordion-collapse collapse" aria-labelledby="news-heading-{$id}" data-bs-parent="#news">
			<div class="accordion-body">
				{$item.text}
			</div>
		</div>
	</div>
	{/foreach}
	{/if}
</div>
