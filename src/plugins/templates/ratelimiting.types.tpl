<form action="{$pageURL}&sid={$sid}&action=types&save=true" method="post" onsubmit="spin(this)">
	<fieldset>
		<legend>Rate Limiting</legend>

		<table class="list">
			<tr>
				<th>{lng p="ratelimiting_event"}</th>
				<th>{lng p="ratelimiting_max_events"}</th>
				<th>{lng p="ratelimiting_in_seconds"}</th>
			</tr>

			{foreach from=$types item=type}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
                <td>{text value=$type.type}</td>
                <td>
                    <input type="number" name="types[{$type.type}][max_events]" value="{$type.max_events}" size="18" />
                </td>
                <td>
                    <input type="number" name="types[{$type.type}][in_seconds]" value="{$type.in_seconds}" size="18" />
                </td>
			</tr>
			{/foreach}
		</table>
	</fieldset>

	<p>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>
