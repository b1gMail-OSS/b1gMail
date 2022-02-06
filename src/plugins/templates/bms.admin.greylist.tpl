<form action="{$pageURL}&action=smtp&do=greylist&sid={$sid}" method="post" onsubmit="spin(this)" name="f1">
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	
	<fieldset>
		<legend>{lng p="bms_greylist"}</legend>
		
		<table class="list">
			<tr>
				<th>{lng p="bms_ip"}</th>
				<th width="160">{lng p="bms_grey_date"}</th>
				<th width="100">{lng p="bms_grey_confirmed"}</th>
				<th width="70">{lng p="delete"}</th>
			</tr>
			
			{foreach from=$greylist item=item key=key}
			{cycle name=class values="td1,td2" assign=class}
			<tr class="{$class}">
				{if $item.ip6}
				<td><span id="ip_{$item.ip6}"><a href="javascript:bms_lookupIP('{$item.ip6}');">{$item.ip6}</a></span></td>
				{else}
				<td><span id="ip_{$item.ip}"><a href="javascript:bms_lookupIP('{$item.ip}');">{$item.ip}</a></span></td>
				{/if}
				<td>{date timestamp=$item.time}</td>
				<td><input type="checkbox" disabled="disabled"{if $item.confirmed} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="delete[]" value="{$key}" /></td>
			</tr>
			{/foreach}
			
			<tr>
				<td class="footer" colspan="4">
					<div style="float:right;padding-top:3px;padding-bottom:3px;">
						{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<p>
		<div style="float:left" class="buttons">
			<input class="button" type="button" value=" &laquo; {lng p="back"} " onclick="document.location.href='{$pageURL}&action=smtp&sid={$sid}';" />
		</div>
		<div style="float:right" class="buttons">
			<input class="button" type="submit" value=" {lng p="save"} " />
		</div>
	</p>
</form>

<script>
{literal}<!--
	function _bms_lookupIP(e)
	{
		if(e.readyState == 4)
		{
			var text = e.responseText;
			text = text.split('/');

			if(text.length == 2)
			{
				var ip = text[0], hostName = text[1];
				if(EBID('ip_'+ip))
					EBID('ip_'+ip).innerHTML = hostName + ' (' + ip + ')';
			}
		}
	}

	function bms_lookupIP(ip)
	{
		MakeXMLRequest('{/literal}{$pageURL}{literal}&sid=' + currentSID
							+ '&action=lookupIP'
							+ '&ip=' + escape(ip),
						_bms_lookupIP);
	}
//-->{/literal}
</script>
