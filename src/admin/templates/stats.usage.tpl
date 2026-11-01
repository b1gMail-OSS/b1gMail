{assign var=catTotal value=0}
{foreach from=$byCategory item=catSize}
	{assign var=catTotal value=$catTotal+$catSize}
{/foreach}

<fieldset>
	<legend>{lng p="usagebycategory"}</legend>

	<div class="card mb-3">
		<div class="table-responsive">
			<table class="table table-vcenter card-table mb-0">
				<thead>
				<tr>
					<th>{lng p="category"}</th>
					<th style="width: 34%;">{lng p="size"}</th>
					<th class="text-end" style="width: 14rem;">{lng p="size"} ({lng p="useraverage"})</th>
					<th class="text-end" style="width: 10rem;">{lng p="size"} ({lng p="overall"})</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$byCategory item=catSize key=catKey}
					{if $catKey=='webdisk'}{assign var=catColor value='red'}
					{elseif $catKey=='organizer'}{assign var=catColor value='azure'}
					{elseif $catKey=='prefs'}{assign var=catColor value='green'}
					{elseif $catKey=='users'}{assign var=catColor value='blue'}
					{elseif $catKey=='mails'}{assign var=catColor value='purple'}
					{else}{assign var=catColor value='orange'}{/if}
					{if $catTotal>0}{math equation="x/y*100" x=$catSize y=$catTotal format="%.2f" assign=catPct}{else}{assign var=catPct value=0}{/if}
					<tr>
						<td>
							<span class="badge badge-dot bg-{$catColor} me-2"></span>
							{lng p=$catKey}
						</td>
						<td>
							<div class="d-flex align-items-center gap-2">
								<div class="progress bm-usage-meter flex-fill">
									<div class="progress-bar bg-{$catColor}" role="progressbar" style="width: {$catPct}%;" aria-label="{lng p=$catKey}"></div>
								</div>
								<span class="text-secondary small text-nowrap" style="width: 3rem;">{if $catTotal>0}{math equation="x/y*100" x=$catSize y=$catTotal format="%.0f"}{else}0{/if}&nbsp;%</span>
							</div>
						</td>
						<td class="text-end">{if $userCount>0&&$catKey!='prefs'}{size bytes=$catSize/$userCount}{else}<span class="text-secondary">-</span>{/if}</td>
						<td class="text-end">{size bytes=$catSize}</td>
					</tr>
				{/foreach}
				<tr class="bm-usage-sum">
					<td>{lng p="sum"}</td>
					<td></td>
					<td class="text-end">{if $userCount>0}{size bytes=$catTotal/$userCount}{/if}</td>
					<td class="text-end">{size bytes=$catTotal}</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</fieldset>

{assign var=groupTotal value=0}
{assign var=groupUsers value=0}
{assign var=groupMax value=0}
{foreach from=$byGroup item=group}
	{assign var=groupTotal value=$groupTotal+$group.size}
	{assign var=groupUsers value=$groupUsers+$group.users}
	{if $group.size>$groupMax}{assign var=groupMax value=$group.size}{/if}
{/foreach}

<fieldset>
	<legend>{lng p="usagebygroup"} ({lng p="withoutmeta"})</legend>

	<div class="card">
		<div class="table-responsive">
			<table class="table table-vcenter card-table mb-0">
				<thead>
				<tr>
					<th>{lng p="group"}</th>
					<th style="width: 34%;">{lng p="size"}</th>
					<th class="text-end" style="width: 14rem;">{lng p="size"} ({lng p="useraverage"})</th>
					<th class="text-end" style="width: 10rem;">{lng p="size"} ({lng p="overall"})</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$byGroup item=group}
					{if $groupMax>0}{math equation="x/y*100" x=$group.size y=$groupMax format="%.2f" assign=groupPct}{else}{assign var=groupPct value=0}{/if}
					<tr>
						<td>{text value=$group.title}</td>
						<td>
							<div class="d-flex align-items-center gap-2">
								<div class="progress bm-usage-meter flex-fill">
									<div class="progress-bar bg-red" role="progressbar" style="width: {$groupPct}%;" aria-label="{text value=$group.title}"></div>
								</div>
								<span class="text-secondary small text-nowrap" style="width: 3rem;">{if $groupTotal>0}{math equation="x/y*100" x=$group.size y=$groupTotal format="%.0f"}{else}0{/if}&nbsp;%</span>
							</div>
						</td>
						<td class="text-end">{if $group.users>0}{size bytes=$group.size/$group.users}{else}<span class="text-secondary">-</span>{/if}</td>
						<td class="text-end">{size bytes=$group.size}</td>
					</tr>
				{/foreach}
				<tr class="bm-usage-sum">
					<td>{lng p="sum"}</td>
					<td></td>
					<td class="text-end">{if $groupUsers>0}{size bytes=$groupTotal/$groupUsers}{else}<span class="text-secondary">-</span>{/if}</td>
					<td class="text-end">{size bytes=$groupTotal}</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</fieldset>
