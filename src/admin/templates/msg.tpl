<fieldset>
	<legend>{$msgTitle}</legend>
	
	{if !empty($msgIcon)}
		{if $msgIcon == 'add32'}
			<div class="alert alert-success"><i class="fa-regular fa-circle-check"></i> {$msgText}</div>
		{elseif $msgIcon == 'error32'}
			<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> {$msgText}</div>
		{else}
			<div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> {$msgText}</div>
		{/if}
	{else}
		<div class="alert">{$msgText}</div>
	{/if}
	
	{if !empty($backLink)}
	<div class="text-end">
		<input class="btn btn-primary" type="button" onclick="document.location.href='{$backLink}sid={$sid}';" value="{lng p="back"}" />
	</div>
	{else}
		<div class="text-end">
		<input class="btn btn-primary" type="button" onclick="history.back(1);" value="{lng p="back"}" />
	</div>
	{/if}
</fieldset>

{if isset($reloadMenu)}
<script>
<!--
	parent.frames['menu'].location.href = 'main.php?action=menu&item=4&sid={$sid}';
//-->
</script>
{/if}
