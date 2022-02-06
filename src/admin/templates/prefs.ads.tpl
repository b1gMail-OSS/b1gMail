<fieldset>
	<legend>{lng p="banners"}</legend>
	
	<form action="prefs.ads.php?sid={$sid}" method="post" name="f1" onsubmit="spin(this)">
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th width="25" style="text-align:center;"><a href="javascript:invertSelection(document.forms.f1,'ad_');"><img src="{$tpldir}images/dot.png" border="0" alt="" width="10" height="8" /></a></th>
			<th>{lng p="banner"}</th>
			<th width="75">{lng p="category"}</th>
			<th width="72">{lng p="weight"}</th>
			<th width="45">{lng p="views"}</th>
			<th width="100">&nbsp;</th>
		</tr>
		
		{foreach from=$ads item=ad}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center"><img src="{$tpldir}images/ad.png" border="0" alt="" width="16" height="16" /></td>
			<td align="center"><input type="checkbox" name="ad_{$ad.id}" /></td>
			<td>{$ad.code}</td>
			<td>{text value=$ad.category cut=10}</td>
			<td>{$ad.weight}%</td>
			<td>{$ad.views}</td>
			<td>
				<a href="prefs.ads.php?{if !$ad.paused}de{/if}activate={$ad.id}&sid={$sid}"><img src="{$tpldir}images/{if !$ad.paused}ok{else}error{/if}.png" width="16" height="16" alt="{if $ad.paused}{lng p="continue"}{else}{lng p="pause"}{/if}" border="0" /></a>
				<a href="prefs.ads.php?reset={$ad.id}&sid={$sid}" onclick="return confirm('{lng p="reallyresetstats"}');" title="{lng p="resetstats"}"><img src="{$tpldir}images/reset_stats.png" border="0" alt="{lng p="resetstats"}" width="16" height="16" /></a>
				<a href="prefs.ads.php?do=edit&id={$ad.id}&sid={$sid}"><img src="{$tpldir}images/edit.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
				<a href="prefs.ads.php?delete={$ad.id}&sid={$sid}" onclick="return confirm('{lng p="realdel"}');"><img src="{$tpldir}images/delete.png" border="0" alt="{lng p="edit"}" width="16" height="16" /></a>
			</td>
		</tr>
		{/foreach}
		
		<tr>
			<td class="footer" colspan="8">
				<div style="float:left;">
					{lng p="action"}: <select name="massAction" class="smallInput">
						<option value="-">------------</option>
						
						<optgroup label="{lng p="actions"}">
							<option value="pause">{lng p="pause"}</option>
							<option value="continue">{lng p="continue"}</option>
							<option value="delete">{lng p="delete"}</option>
						</optgroup>
					</select>&nbsp;
				</div>
				<div style="float:left;">
					<input type="submit" name="executeMassAction" value=" {lng p="execute"} " class="smallInput" />
				</div>
			</td>
		</tr>
	</table>
	</form>
</fieldset>

<fieldset>
	<legend>{lng p="addbanner"}</legend>
	
	<form action="prefs.ads.php?add=true&sid={$sid}" method="post" onsubmit="spin(this);">
		<table width="100%">
			<tr>
				<td width="40" valign="top" rowspan="5"><img src="{$tpldir}images/ad32.png" border="0" alt="" width="32" height="32" /></td>
				<td colspan="2" style="border: 1px solid #DDDDDD;background-color:#FFFFFF;">
					<textarea name="code" id="code" class="plainTextArea" style="width:100%;height:120px;font-family:courier;"></textarea>
				</td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="category"}:</td>
				<td class="td2"><input type="text" name="category" size="36" value="" /></td>
			</tr>
			<tr>
				<td class="td1" width="160">{lng p="weight"}:</td>
				<td class="td2"><input type="text" name="weight" size="4" value="100" />%</td>
			</tr>
			<tr>
				<td class="td1">{lng p="paused"}?</td>
				<td class="td2"><input type="checkbox" name="paused" /></td>
			</tr>
			<tr>
				<td class="td1">{lng p="comment"}:</td>
				<td class="td2"><textarea style="width:100%;height:65px;" name="comments"></textarea></td>
			</tr>
		</table>
	
		<p align="right">
			<input class="button" type="submit" value=" {lng p="add"} " />
		</p>
	</form>
</fieldset>