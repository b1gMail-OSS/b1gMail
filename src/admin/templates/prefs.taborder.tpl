<link href="{$selfurl}clientlib/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<form action="prefs.common.php?action=taborder&save=true&sid={$sid}" name="f1" method="post" onsubmit="spin(this)">
<fieldset>
	<legend>{lng p="taborder"}</legend>
	
	<table class="list">
		<tr>
			<th width="20">&nbsp;</th>
			<th>{lng p="title"}</th>
			<th width="80">{lng p="pos"}</th>
		</tr>
		
		{foreach from=$pageTabs item=tab key=tabKey}
		{cycle name=class values="td1,td2" assign=class}
		<tr class="{$class}">
			<td align="center">
			<i class="fa {$tab.faIcon}" aria-hidden="true"></i></td>
			<td>{text value=$tab.text}</td>
			<td><input type="text" name="order[{$tabKey}]" value="{$tab.order}" size="6" /></td>
		</tr>
		{/foreach}
	</table>
</fieldset>
<p>
	<div style="float:right;" class="buttons">
		<input class="button" type="submit" value=" {lng p="save"} " />
	</div>
</p>
</form>
