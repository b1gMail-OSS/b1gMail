<form action="{$pageURL}&sid={$sid}&action=prefs&do=featureFields&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>{lng p="pacc_fields"}</legend>
		
		<table class="list">
			<tr>
				<th>{lng p="category"}</th>
				<th width="70">{lng p="show"}</th>
				<th width="70">{lng p="pacc_fieldpos"}</th>
			</tr>
			
			{foreach from=$fieldPositions item=value key=key}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				<td>{$fieldTitles[$key]}</td>
				<td><input type="checkbox" name="fields[]" value="{$key}"{if $fields[$key]} checked="checked"{/if} /></td>
				<td><input type="text" name="positions[{$key}]" value="{$fieldPositions[$key]}" size="3" /></td>
			</tr>
			{/foreach}
		</table>
	</fieldset>
	
	<p>
		<div style="float:left" class="buttons">
			<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=prefs&sid={$sid}';" />
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
