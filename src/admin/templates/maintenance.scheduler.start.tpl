{include file=$tccrn_nav_tpl}

<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="sched.tasks_title"}</legend>

	{if !count($tccrn_tasks)}
		<div class="text-secondary">{lng p="sched.no_tasks"}</div>
	{else}
		<div class="table-responsive">
			<table class="table table-vcenter table-hover card-table">
				<thead>
					<tr>
						<th>{lng p="sched.task_label"}</th>
						<th>{lng p="sched.next_run"}</th>
						<th>{lng p="status"}</th>
						<th class="text-end w-1">{lng p="action"}</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$tccrn_tasks item=task}
					{assign var="details" value=""}
					<tr{if !$task.active} class="opacity-75"{/if}>
						<td>
							<div class="fw-medium">{$task.task_label}</div>
							{if $task.task|substr:0:8 == 'tccrn.db' && isset($task.taskdata.table) && is_array($task.taskdata.table)}
								{foreach from=$task.taskdata.table item=table name=f_task}
									{assign var="details" value="{$details}{$table}"}
									{if !$smarty.foreach.f_task.last}{assign var="details" value="{$details}, "}{/if}
								{/foreach}
							{elseif $task.task|substr:0:8 == 'tccrn.lg'}
								{capture assign="details"}{lng p="savearc"}: {if isset($task.taskdata.save) && $task.taskdata.save}{lng p="yes"}{else}{lng p="no"}{/if}{if isset($task.taskdata.keepDays) && $task.taskdata.keepDays && isset($task.taskdata.days)}; {lng p="sched.keep_last"} {$task.taskdata.days} {lng p="days"}{/if}{/capture}
							{elseif $task.task|substr:0:8 == 'tccrn.us'}
								{capture assign="details"}{if isset($task.taskdata.days)}{lng p="days"}: {$task.taskdata.days}; {/if}{if isset($task.taskdata.groups) && is_array($task.taskdata.groups)}{lng p="groups"}: {foreach from=$task.taskdata.groups item=gid name=f_groups}{if isset($groups[$gid])}{$groups[$gid].title}{/if}{if !$smarty.foreach.f_groups.last}, {/if}{/foreach}{/if}{if $task.task == 'tccrn.us_move' && isset($task.taskdata.moveGroup) && isset($groups[$task.taskdata.moveGroup])}; {lng p="sched.move_to_group"}: {$groups[$task.taskdata.moveGroup].title}{/if}{/capture}
							{elseif $task.task|substr:0:8 == 'tccrn.tr'}
								{capture assign="details"}{if isset($task.taskdata.groups) && is_array($task.taskdata.groups)}{lng p="groups"}: {foreach from=$task.taskdata.groups item=gid name=f_groups}{if isset($groups[$gid])}{$groups[$gid].title}{/if}{if !$smarty.foreach.f_groups.last}, {/if}{/foreach}{/if}{if !empty($task.taskdata.daysOnly) && isset($task.taskdata.days)}; {lng p="days"}: {$task.taskdata.days}{/if}{if !empty($task.taskdata.sizesOnly) && isset($task.taskdata.size)}; {lng p="size"}: {$task.taskdata.size} KB{/if}{/capture}
							{elseif ($task.task|substr:0:8 == 'tccrn.se') || ($task.task|substr:0:8 == 'tccrn.st')}
								{capture assign="details"}{if isset($task.taskdata.days)}{lng p="sched.keep_last"} {$task.taskdata.days} {lng p="days"}{/if}{/capture}
							{/if}
							{if $details}
								<div class="text-secondary small text-truncate" style="max-width:28rem;" title="{text value=$details noentities=1}">{text value=$details cut=55 noentities=1}</div>
							{/if}
						</td>
						<td>
							{if $task.nextcall != 0}
								{date timestamp=$task.nextcall}<br />
								<small class="text-secondary">{tccrn_countdown timestamp=$task.nextcall}</small>
							{/if}
						</td>
						<td>
							{if $task.active && $task.lastcall != 0}
								{if $task.status == 'started' && $task.lastcall + 30 > $smarty.now}
									<span class="badge bg-warning-lt">{lng p='sched.task_running'}</span>
								{elseif $task.status == 'started'}
									<span class="badge bg-danger-lt">{lng p='sched.task_failed'}</span>
								{else}
									<span class="badge bg-success-lt">{lng p='sched.task_ok'}</span>
								{/if}
								<div class="small text-secondary mt-1">{date timestamp=$task.lastcall nice=1}</div>
							{/if}
						</td>
						<td class="text-end text-nowrap tccrn-acp-actions">
							<div class="btn-group btn-group-sm" role="group">
								<form method="post" action="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=start"}" class="tccrn-acp-actions__form">
									{csrffield}
									<input type="hidden" name="op" value="switch" />
									<input type="hidden" name="id" value="{$task.taskid}" />
									<input type="hidden" name="active" value="{$task.active}" />
									<button type="submit" class="btn btn-sm{if $task.active} btn-success{else} btn-outline-secondary{/if}" title="{if $task.active}{lng p='sched.enabled'}{else}{lng p='sched.disabled'}{/if}">
										<i class="ti ti-{if $task.active}circle-check{else}circle-x{/if} icon"></i>
									</button>
								</form>
								<form method="post" action="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=start"}" class="tccrn-acp-actions__form">
									{csrffield}
									<input type="hidden" name="op" value="execute" />
									<input type="hidden" name="id" value="{$task.taskid}" />
									<button type="submit" class="btn btn-sm" title="{lng p="execute"}"><i class="ti ti-player-play icon"></i></button>
								</form>
								<a href="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=task&id={$task.taskid}"}" class="btn btn-sm" title="{lng p="edit"}"><i class="ti ti-edit icon"></i></a>
								<form method="post" action="{sessionurl file=$tccrn_admin_script params="action={$tccrn_admin_action}&do=start"}" class="tccrn-acp-actions__form" onsubmit="return confirm('{lng p="realdel"}');">
									{csrffield}
									<input type="hidden" name="op" value="delete" />
									<input type="hidden" name="id" value="{$task.taskid}" />
									<button type="submit" class="btn btn-sm btn-outline-danger" title="{lng p="delete"}"><i class="ti ti-trash icon"></i></button>
								</form>
							</div>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	{/if}
	<p class="text-secondary small mb-0 mt-2">{lng p="sched.server_time"}: {date timestamp=$smarty.now}</p>
</fieldset>

{if count($notices)}
<fieldset class="mb-4">
	<legend class="h4 mb-3">{lng p="notices"}</legend>
	<div class="list-group list-group-flush">
	{foreach from=$notices item=notice}
		<div class="list-group-item d-flex align-items-start gap-3 px-0">
			<i class="ti ti-{if $notice.type == 'error'}alert-circle text-danger{else}info-circle text-info{/if} mt-1"></i>
			<div class="flex-fill">{$notice.text}</div>
			{if $notice.link}
				<a href="{$notice.link}" class="btn btn-sm btn-ghost-secondary"><i class="ti ti-arrow-right"></i></a>
			{/if}
		</div>
	{/foreach}
	</div>
</fieldset>
{/if}

<img src="../cron.php?out=img&amp;key={$bm_prefs.cron_secret}" width="1" height="1" alt="" class="d-none" />
<script type="text/javascript">setTimeout(function() { location.reload(true); }, 60000);</script>
