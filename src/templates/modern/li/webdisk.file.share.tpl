<div id="contentHeader">
	<div class="left">
		<i class="fa fa-share-square-o" aria-hidden="true"></i>
		{lng p="webdisk"}-{lng p="sharing"} ({text value=$fileName})
	</div>
</div>

<div class="scrollContainer"><div class="pad">
{if isset($shareError) && $shareError != ''}
<div class="mailWarning" style="margin-bottom:10px;">{$shareError}</div>
{/if}
<form action="{sessionurl file='webdisk.php' params="action=saveFileShareSettings&id={$id}"}" method="post">
	{csrffield}
	<table class="listTable">
		<tr>
			<th class="listTableHead" colspan="2"> {lng p="sharing"}</th>
		</tr>
		<tr>
			<td class="listTableLeft">{lng p="file"}:</td>
			<td class="listTableRight">{text value=$fileName}</td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="shareFile">{lng p="share"}:</label></td>
			<td class="listTableRight"><input type="checkbox" name="shareFile" id="shareFile" {if $fileShared}checked="checked" {/if}/></td>
		</tr>
		<tr>
			<td class="listTableLeft"><label for="password">{lng p="password"}{if $sharePasswordRequired|default:false} *{/if}:</label></td>
			<td class="listTableRight">
				<input type="password" id="password" name="sharePW" value="{if isset($filePW)}{text value=$filePW allowEmpty=true}{/if}" size="30" autocomplete="new-password" />
				<button type="button" onclick="toggleSharePasswordVisibility('password', this);" title="{lng p="show"}" style="margin-left:6px;">
					<i class="fa fa-eye" aria-hidden="true"></i>
				</button>
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
			<td class="listTableLeft"><label for="shareSingleUse">{lng p="wd_share_single_use"}:</label></td>
			<td class="listTableRight"><input type="checkbox" name="shareSingleUse" id="shareSingleUse" {if $shareSingleUse|default:false}checked="checked" {/if}/></td>
		</tr>
		{if $fileShareURL|default:'' != ''}
		<tr>
			<td class="listTableLeft">{lng p="wd_share_file_link"}:</td>
			<td class="listTableRight"><a href="{$fileShareURL}" target="_blank">{$fileShareURL}</a></td>
		</tr>
		{/if}
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
