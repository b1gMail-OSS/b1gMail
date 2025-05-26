<fieldset>
	<legend>{lng p="usagebycategory"}</legend>

	<div class="text-center">
		<img src="stats.php?action=usage&do=showSpaceByCategory&sid={$sid}" border="0" alt="" class="graph" />
	</div>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th>{lng p="category"}</th>
					<th style="width: 200px;">{lng p="size"} ({lng p="useraverage"})</th>
					<th style="width: 200px;">{lng p="size"} ({lng p="overall"})</th>
				</tr>
				</thead>
				<tbody>
				{assign var=overallSize value=0}
				{foreach from=$byCategory item=catSize key=catKey}
					{cycle name=class values="td1,td2" assign=class}
					{assign var=overallSize value=$overallSize+$catSize}
					<tr class="{$class}">
						<td>{lng p=$catKey}</td>
						<td>{if $userCount>0&&$catKey!='prefs'}<small>&empty;</small> {size bytes=$catSize/$userCount}{else}-{/if}</td>
						<td>{size bytes=$catSize}</td>
					</tr>
				{/foreach}
				</tbody>
				{cycle name=class values="td1,td2" assign=class}
				<tr class="{$class}_dl">
					<td>&nbsp;</td>
					<td>{if $userCount>0}<small>&empty;</small> {size bytes=$overallSize/$userCount}{/if}</td>
					<td>{size bytes=$overallSize}</td>
				</tr>
			</table>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>{lng p="usagebygroup"} ({lng p="withoutmeta"})</legend>

	<div class="text-center">
		<img src="stats.php?action=usage&do=showSpaceByGroup&sid={$sid}" border="0" alt="" class="graph" />
		<img src="stats.php?action=usage&do=showSpaceAverageByGroup&sid={$sid}" border="0" alt="" class="graph" />
	</div>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter table-striped">
				<thead>
				<tr>
					<th>{lng p="group"}</th>
					<th style="width: 200px;">{lng p="size"} ({lng p="useraverage"})</th>
					<th style="width: 200px;">{lng p="size"} ({lng p="overall"})</th>
				</tr>
				</thead>
				<tbody>
				{assign var=overallSize value=0}
				{assign var=overallCount value=0}
				{foreach from=$byGroup item=group key=groupID}
					{cycle name=class values="td1,td2" assign=class}
					{assign var=overallSize value=$overallSize+$group.size}
					{assign var=overallCount value=$overallCount+$group.users}
					<tr class="{$class}">
						<td>{text value=$group.title}</td>
						<td>{if $group.users>0}<small>&empty;</small> {size bytes=$group.size/$group.users}{else}-{/if}</td>
						<td>{size bytes=$group.size}</td>
					</tr>
				{/foreach}
				</tbody>
				{cycle name=class values="td1,td2" assign=class}
				<tr class="{$class}_dl">
					<td>&nbsp;</td>
					<td>{if $overallCount>0}<small>&empty;</small> {size bytes=$overallSize/$overallCount}{else}-{/if}</td>
					<td>{size bytes=$overallSize}</td>
				</tr>
			</table>
</fieldset>