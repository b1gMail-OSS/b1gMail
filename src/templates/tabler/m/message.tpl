<div data-role="header" data-position="fixed">
	<a href="{if $backLink}{$backLink}{else}javascript:history.back(1);{/if}" data-rel="back" data-icon="arrow-l" data-direction="reverse" data-transition="slide">{lng p="back"}</a>
	<h1>{lng p="notice"}</h1>
</div>

<div data-role="content">
	{$msg}
</div>
