{if $_tplname=='modern'}
<div id="contentHeader">
	<div class="left">
		<i class="fa fa-archive" aria-hidden="true"></i>
		{lng p="modfax_outbox"}
	</div>
</div>

<div class="scrollContainer withBottomBar">

<form name="f1" method="post" action="start.php?action=faxPlugin&do=outbox&sid={$sid}">
<table class="bigTable">
	<tr>
		<th width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'fax');" /></th>
		<th>
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=tono&order={$sortOrderInv}">{lng p="to"}</a>
			{if $sortColumn=='tono'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="60">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=pages&order={$sortOrderInv}">{lng p="modfax_pages"}</a>
			{if $sortColumn=='pages'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="130">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=date&order={$sortOrderInv}">{lng p="date"}</a>
			{if $sortColumn=='date'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="120">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=status&order={$sortOrderInv}">{lng p="status"}</a>
			{if $sortColumn=='status'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th width="55">&nbsp;</th>
	</tr>
{else}
<h1><i class="fa fa-archive" aria-hidden="true"></i> {lng p="modfax_outbox"}</h1>

<form name="f1" method="post" action="start.php?action=faxPlugin&do=outbox&sid={$sid}">
<table class="listTable">
	<tr>
		<th class="listTableHead" width="20"><input type="checkbox" id="allChecker" onclick="checkAll(this.checked, document.forms.f1, 'fax');" /></th>
		<th class="listTableHead">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=tono&order={$sortOrderInv}">{lng p="to"}</a>
			{if $sortColumn=='tono'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="60">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=pages&order={$sortOrderInv}">{lng p="modfax_pages"}</a>
			{if $sortColumn=='pages'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="130">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=date&order={$sortOrderInv}">{lng p="date"}</a>
			{if $sortColumn=='date'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="120">
			<a href="start.php?action=faxPlugin&do=outbox&sid={$sid}&sort=status&order={$sortOrderInv}">{lng p="status"}</a>
			{if $sortColumn=='status'}<i class="fa {$sortOrder}" aria-hidden="true"></i>{/if}
		</th>
		<th class="listTableHead" width="55">&nbsp;</th>
	</tr>
{/if}
	
	{if $outbox}
	<tbody class="listTBody">
	{foreach from=$outbox key=faxID item=fax}
	{cycle values="listTableTD,listTableTD2" assign="class"}
	<tr>
		<td class="{$class}" nowrap="nowrap"><input type="checkbox" id="fax_{$faxID}" value="{$faxID}" name="fax[]" /></td>
		<td class="{if $sortColumn=='tono'}listTableTDActive{else}{$class}{/if}">&nbsp;<a href="javascript:toggleGroup({$faxID});"><img id="groupImage_{$faxID}" src="{$tpldir}images/{if $smarty.request.show==$faxID}contract{else}expand{/if}.png" width="11" height="11" border="0" alt="" align="absmiddle" /></a>&nbsp;{text value=$fax.tono}</td>
		<td class="{if $sortColumn=='pages'}listTableTDActive{else}{$class}{/if}">&nbsp;{$fax.pages}</td>
		<td class="{if $sortColumn=='date'}listTableTDActive{else}{$class}{/if}">&nbsp;{date timestamp=$fax.date nice=true}</td>
		<td class="{if $sortColumn=='status'}listTableTDActive{else}{$class}{/if}">&nbsp;{$fax.statusText}</td>
		<td class="{$class}">
			{if $fax.fileAvailable}<a href="webdisk.php?action=downloadFile&id={$fax.diskfileid}&sid={$sid}" title="{lng p="download"}"><i class="fa fa-file-pdf-o" aria-hidden="true" /></i></a>{/if}
			<a onclick="return confirm('{lng p="realdel"}');" href="start.php?action=faxPlugin&do=outbox&delete={$faxID}&sid={$sid}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		</td>
	</tr>
	<tbody id="group_{$faxID}" style="display:{if $smarty.request.show!=$faxID}none{/if}">
	<tr>
		<td colspan="6" class="listTableTDText" style="padding:1em;">
			<table>
				<tr>
					<td><b>{lng p="from"}:</b> &nbsp;</td>
					<td>{text value=$fax.fromno} ({text value=$fax.fromname})</td>
				</tr>
				<tr>
					<td><b>{lng p="to"}:</b> &nbsp;</td>
					<td>{text value=$fax.tono}</td>
				</tr>
				<tr>
					<td><b>{lng p="price"}:</b> &nbsp;&nbsp;</td>
					<td>{$fax.price} <small>{if $fax.refunded}({lng p="modfax_refunded"}){/if}</small></td>
				</tr>
				<tr>
					<td><b>{lng p="status"}:</b> &nbsp;</td>
					<td>{$fax.statusText}</td>
				</tr>
				<tr>
					<td><b>{lng p="preview"}:</b> &nbsp;</td>
					<td>{if !$fax.fileAvailable}({lng p="modfax_filena"}){else}<i class="fa fa-file-pdf-o" aria-hidden="true" /></i> <a href="webdisk.php?action=downloadFile&id={$fax.diskfileid}&sid={$sid}">{lng p="download"}</a>{/if}</td>
				</tr>
			</table>
		</td>
	</tr>
	</tbody>
	{/foreach}
	</tbody>
	{/if}
	


{if $_tplname=='modern'}
</table>

</div>

<div id="contentFooter">
	<div class="left">
		<select class="smallInput" name="massAction">
			<option value="-">------ {lng p="selaction"} ------</option>
			<option value="delete">{lng p="delete"}</option>
		</select>
		<input class="smallInput" type="submit" value="{lng p="ok"}" />
	</div>
	<div class="right">
		{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <b>[.t]</b> " off=" <a class=\"pageNav\" href=\"start.php?action=faxPlugin&do=outbox&sort=$sortColumn&order=$sortOrder&page=.s&sid=$sid\">.t</a> "}
	</div>
</div>

</form>
{else}
	<tr>
		<td colspan="6" class="listTableFoot">
			<table cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left">
						<select class="smallInput" name="massAction">
							<option value="-">------ {lng p="selaction"} ------</option>
							<option value="delete">{lng p="delete"}</option>
						</select>
						<input class="smallInput" type="submit" value="{lng p="ok"}" />
					</td>
					<td align="right">
						{lng p="pages"}: {pageNav page=$pageNo pages=$pageCount on=" <b>[.t]</b> " off=" <a class=\"pageNav\" href=\"start.php?action=faxPlugin&do=outbox&sort=$sortColumn&order=$sortOrder&page=.s&sid=$sid\">.t</a> "}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
{/if}
