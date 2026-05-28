<div id="contentHeader">
	<div class="left">
		<i class="fa fa-share-square-o" aria-hidden="true"></i>
		{lng p="webdisk"}-{lng p="sharing"} ({text value=$folderName})
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{if isset($shareError) && $shareError != ''}
<div class="mailWarning" style="margin-bottom:10px;">{$shareError}</div>
{/if}
<form action="webdisk.php?action=saveShareSettings&folder={$folderID}&id={$id}&sid={$sid}" method="post">
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="sharing"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="folder"}:</td>
			<td class="listTableRight">{text value=$folderName}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareFolder">{lng p="share"}:</label></td>
			<td class="listTableRight"><input type="checkbox" name="shareFolder" id="shareFolder" {if $folderShared}checked="checked" {/if}/></td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="password">{lng p="password"}{if $sharePasswordRequired|default:false} *{/if}:</label></td>
			<td class="listTableRight">
				<input type="password" id="password" name="sharePW" value="{if isset($folderPW)}{text value=$folderPW allowEmpty=true}{/if}" size="30" autocomplete="new-password" />
				<button type="button" onclick="toggleSharePasswordVisibility('password', this);" title="{lng p="show"}" style="margin-left:6px;">
					<i class="fa fa-eye" aria-hidden="true"></i>
				</button>
				{if $sharePasswordRequired|default:false}<br /><small><strong>{lng p="required"}</strong> ({lng p="min"} 12)</small>{/if}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareUntil">{lng p="wd_share_expiry"}{if $shareExpiryRequired|default:false} *{/if}:</label></td>
			<td class="listTableRight">
				<input type="date" id="shareUntil" name="shareUntil" value="{$shareUntilDate|default:''}" min="{$shareExpiryMinDate|default:''}"{if $shareExpiryMaxDate|default:'' != ''} max="{$shareExpiryMaxDate}"{/if} />
				{if $shareExpiryMaxDays|default:0 > 0}<br /><small>({lng p="max"}: {$shareExpiryMaxDays} {lng p="days"})</small>{/if}
			</td>
		</tr>
		<tr>
			<td class="listTableLeft">&nbsp;</td>
			<td class="listTableRight">
				<input type="submit" class="primary" value="{lng p="ok"}" />
				<input type="reset" value="{lng p="reset"}" />
			</td>
		</tr>
	</table>
</form>
</div></div>

<script>
<!--
function toggleSharePasswordVisibility(inputID, btn)
{
	var input = EBID(inputID),
		icon;

	if(!input || !btn)
		return;

	if(input.type === 'password')
	{
		input.type = 'text';
		icon = btn.getElementsByTagName('i')[0];
		if(icon)
			icon.className = 'fa fa-eye-slash';
	}
	else
	{
		input.type = 'password';
		icon = btn.getElementsByTagName('i')[0];
		if(icon)
			icon.className = 'fa fa-eye';
	}
}
//-->
</script>
