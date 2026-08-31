<form action="{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common&action=banlist"}" method="post" onsubmit="spin(this)" name="f1">
	{csrffield}
	<input type="hidden" name="page" id="page" value="{$pageNo}" />
	
	<fieldset class="mb-4">
		<legend class="h4 mb-3">{lng p="bms_failban"}</legend>
		
		<div class="card mb-3"><div class="table-responsive"><table class="table table-vcenter table-striped card-table">
			<tr>
				<th>{lng p="bms_ip"}</th>
				<th class="text-nowrap">{lng p="bms_fb_banneduntil"}</th>
				<th class="text-nowrap">{lng p="bms_fb_lastupdate"}</th>
				<th width="160">{lng p="bms_fb_type"}</th>
				<th width="70">{lng p="delete"}</th>
			</tr>
			
			{foreach from=$banlist item=item key=key}
			<tr>
				{if $item.ip6}
				<td><span data-bms-ip="{$item.ip6|escape:'html'}"><a href="#" onclick="bms_lookupIP(this); return false;" data-bms-ip="{$item.ip6|escape:'html'}">{$item.ip6}</a></span></td>
				{else}
				<td><span data-bms-ip="{$item.ip|escape:'html'}"><a href="#" onclick="bms_lookupIP(this); return false;" data-bms-ip="{$item.ip|escape:'html'}">{$item.ip}</a></span></td>
				{/if}
				<td class="text-nowrap">{date timestamp=$item.banned_until}</td>
				<td class="text-nowrap">{date timestamp=$item.last_update}</td>
				<td>{$item.type_text}</td>
				<td class="text-center"><label class="form-check justify-content-center mb-0"><input class="form-check-input" type="checkbox" name="delete[]" value="{$key}" /></label></td>
			</tr>
			{/foreach}
			
			<tr>
				<td class="footer" colspan="5">
					<div style="float:right;padding-top:3px;padding-bottom:3px;">
						{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <span class=\"pageNav\"><b>[.t]</b></span> " off=" <span class=\"pageNav\"><a href=\"javascript:updatePage(.s);\">.t</a></span> "}&nbsp;
					</div>
				</td>
			</tr>
		</table></div></div>
	</fieldset>
	
	<div class="d-flex justify-content-between mt-3 mb-2">
		<button type="button" class="btn btn-outline-secondary" onclick="document.location.href='{sessionurl file='plugin.page.php' params="plugin={$bmsPlugin}&do=common"}';"><i class="ti ti-chevron-left me-1"></i> {lng p="back"}</button>
		<button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> {lng p="save"}</button>
	</div>
</form>

<span id="bmsLookupIpUrl" class="d-none" data-url="{text value=$bmsLookupIpUrl noentities=true}" data-wait="{lng p="pleasewait"}"></span>
<script>
{literal}
	function _bms_lookupIPReset(el)
	{
		if(!el)
			return;
		el.classList.remove('pe-none', 'text-muted');
		el.removeAttribute('data-bms-busy');
		var spin = el.parentNode ? el.parentNode.querySelector('.bms-lookup-spin') : null;
		if(spin)
			spin.remove();
	}

	function _bms_lookupIP(e, el)
	{
		if(e.readyState != 4)
			return;
		var data = null;
		if(e.status == 200)
		{
			try { data = JSON.parse(e.responseText); } catch (ex) { data = null; }
		}
		if(data && data.ip)
		{
			var wrap = el && el.parentNode ? el.parentNode : document.querySelector('span[data-bms-ip="' + String(data.ip).replace(/"/g, '') + '"]');
			if(wrap)
				wrap.textContent = data.host + ' (' + data.ip + ')';
			return;
		}
		_bms_lookupIPReset(el);
	}

	function bms_lookupIP(el)
	{
		if(!el || el.getAttribute('data-bms-busy'))
			return;
		var ip = el.getAttribute('data-bms-ip');
		var holder = document.getElementById('bmsLookupIpUrl');
		var url = holder ? holder.getAttribute('data-url') : '';
		if(!url || !ip)
			return;
		if(/[?&]ip=/.test(url))
			url = url.replace(/([?&]ip=)[^&]*/, '$1' + encodeURIComponent(ip));
		else
			url += (url.indexOf('?') >= 0 ? '&' : '?') + 'ip=' + encodeURIComponent(ip);
		el.setAttribute('data-bms-busy', '1');
		el.classList.add('pe-none', 'text-muted');
		var spin = document.createElement('span');
		spin.className = 'spinner-border spinner-border-sm text-secondary ms-2 bms-lookup-spin';
		spin.setAttribute('role', 'status');
		var wait = holder ? holder.getAttribute('data-wait') : '';
		if(wait)
			spin.setAttribute('title', wait);
		el.after(spin);
		MakeXMLRequest(url, _bms_lookupIP, el);
	}
{/literal}
</script>
