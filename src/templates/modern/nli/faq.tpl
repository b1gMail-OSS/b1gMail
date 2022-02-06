<div class="container">
	<div class="page-header"><h1>{lng p="faq"}</h1></div>

	<p>
		{lng p="faqtxt"}
	</p>

	<div class="panel-group" id="faq">
		{foreach from=$faq key=id item=item}
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"><a data-toggle="collapse" data-parent="#faq" href="#faq-{$id}">{$item.question}</a></h4>
			</div>
			<div id="faq-{$id}" class="panel-collapse collapse">
				<div class="panel-body">
					<p>{$item.answer}</p>
				</div>
			</div>
		</div>
		{/foreach}
	</div>
</div>
