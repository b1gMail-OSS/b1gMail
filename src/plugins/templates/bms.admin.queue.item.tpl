<fieldset>
	<legend>{lng p="bms_queue"}: {$item.id} ({$item.hexID})</legend>

	<form action="{$pageURL}&action=msgqueue&do=queue&delByAttr={$item.id}&sid={$sid}" method="post">
	<table width="100%">
		<tr>
			<td align="left" rowspan="10" valign="top" width="40"><img src="../plugins/templates/images/bms_{$item.typeIcon}.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td1" width="24">&nbsp;</td>
			<td class="td1" width="200">{lng p="id"} / {lng p="type"}:</td>
			<td class="td2">{$item.id} ({$item.hexID}) ({if $item.type==0}{lng p="bms_inbound1"}{else}{lng p="bms_outbound1"}{/if})</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">{lng p="date"}:</td>
			<td class="td2">{date timestamp=$item.date nice=true}</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">{lng p="size"}:</td>
			<td class="td2">{size bytes=$item.size}</td>
		</tr>
		<tr>
			<td class="td1">
				<input type="radio" name="item_attribute" value="from"
					{if !$item.from}disabled="disabled"{/if} />
			</td>
			<td class="td1">{lng p="from"}:</td>
			<td class="td2">&lt;{text value=$item.from}&gt;</td>
		</tr>
		<tr>
			<td class="td1">
				<input type="radio" name="item_attribute" value="to" />
			</td>
			<td class="td1">{lng p="to2"}:</td>
			<td class="td2">&lt;{text value=$item.to}&gt;</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">{lng p="bms_attempts"}:</td>
			<td class="td2">{$item.attempts}</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">{lng p="bms_last_attempt"}:</td>
			<td class="td2">{if $item.last_attempt==0}-{else}{date timestamp=$item.last_attempt nice=true}{if $item.last_attempt>0} ({$item.last_status}; <code>{text value=$item.last_status_info}</code>){/if}{/if}</td>
		</tr>
		<tr>
			<td class="td1">
				<input type="radio" name="item_attribute" value="b1gmail_user"
					{if $item.b1gmail_user<=0}disabled="disabled"{/if} />
			</td>
			<td class="td1">{lng p="bms_enqueued_for"}:</td>
			<td class="td2">
				{if $item.b1gmail_user>0}
					<a href="users.php?do=edit&id={$item.b1gmail_user}&sid={$sid}">{$item.b1gmail_user_mail}</a>
				{elseif $item.b1gmail_user==-1}
					{lng p="bms_systemuser"}
				{elseif $item.b1gmail_user==-2}
					{lng p="bms_adminuser"}
				{else}
					{lng p="unknown"}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="td1">
				<input type="radio" name="item_attribute" value="smtp_user"
					{if $item.smtp_user<=0}disabled="disabled"{/if} />
			</td>
			<td class="td1">{lng p="bms_smtp_user"}:</td>
			<td class="td2">{if $item.smtp_user>0}<a href="users.php?do=edit&id={$item.smtp_user}&sid={$sid}">{$item.smtp_user_mail}</a>{else}{lng p="unknown"}{/if}</td>
		</tr>
		<tr>
			<td class="td2" colspan="3">
				<input type="submit" class="button" style="margin-top:0.5em;" value=" {lng p="bms_delbyattr"} " onclick="return confirm('{lng p="bms_reallydelbyattr"}');" />
			</td>
		</tr>
	</table>
	</form>
</fieldset>

{if $queueRunning}
<fieldset>
	<legend>{lng p="bms_headers"}</legend>

	<table width="100%">
		<tr>
			<td align="left" valign="top" width="40"><img src="../plugins/templates/images/bms_signature.png" border="0" alt="" width="32" height="32" /></td>
			<td class="td2">
				<textarea id="headers" readonly="true" style="width:80%;height:240px;display:none;font-family:courier;"></textarea>
				<img id="headersLoading" src="{$tpldir}images/load_16.gif" style="display:none;" border="0" alt="" />
				<input id="headersButton" type="button" class="button" value=" {lng p="show"} " onclick="showQueueItemHeaders({$item.id});" />
			</td>
		</tr>
	</table>
</fieldset>
{/if}

<p>
	<div style="float:left" class="buttons">
		<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=msgqueue&do=queue&sid={$sid}';" />
	</div>

	<div style="float:right" class="buttons">
		{if $queueRunning}
		<input class="button" type="button" value=" {lng p="download"} " onclick="document.location.href='{$pageURL}&action=msgqueue&do=downloadQueueItem&id={$item.id}&sid={$sid}';" />
		{/if}
	</div>
</p>

{literal}<script>
<!--
	function showQueueItemHeaders(id)
	{
		EBID('headersLoading').style.display = '';
		EBID('headersButton').style.display = 'none';

		MakeXMLRequest('{/literal}{$pageURL}&sid={$sid}{literal}&action=msgqueue&do=getQueueItemHeaders&id='+id, function(http)
		{
			if(http.readyState == 4)
			{
				EBID('headers').value = http.responseText;
				EBID('headers').style.display = '';
				EBID('headersLoading').style.display = 'none';
			}
		});
	}
//-->
</script>{/literal}
