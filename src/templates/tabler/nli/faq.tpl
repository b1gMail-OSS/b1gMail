{include file="nli/page.open.tpl"}
<h1 class="mb-3">{lng p="faq"}</h1>

<p class="text-secondary mb-4">
	{lng p="faqtxt"}
</p>

	<div class="panel-group" id="faq">
		{foreach from=$faq key=id item=item}
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"><a data-bs-toggle="collapse" data-bs-parent="#faq" href="#faq-{$id}">{$item.question}</a></h4>
			</div>
			<div id="faq-{$id}" class="panel-collapse collapse">
				<div class="panel-body">
					<p>{$item.answer}</p>
				</div>
			</div>
		</div>
		{/foreach}
	</div>
{include file="nli/page.close.tpl"}
