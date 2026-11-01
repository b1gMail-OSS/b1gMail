<fieldset>
	<legend>{$modeTitle}</legend>

	<form action="{sessionurl file='stats.php' params="action={$mode}"}" method="post">
		{csrffield}
		<div class="row">
			<div class="col-md-6">
				<div class="input-group mb-2">
					<span class="input-group-text"><i class="fa-solid fa-chart-pie"></i></span>
					<select name="statType" class="form-select">
						{foreach from=$statTypes item=type}
							<option value="{$type}"{if $statType==$type} selected="selected"{/if}>{lng p="stat_$type"}</option>
						{/foreach}
					</select>
					<span class="input-group-text"><i class="icon ti ti-calendar"></i></span>
					<input type="text" class="form-control" id="statPeriod" name="statPeriod" data-bs-toggle="datepicker" data-bs-position-element="#statPeriod" data-bm-month="1" data-bs-date-format="MM.YYYY" data-bs-locale="de" autocomplete="off" value="{date timestamp=$time format="m.Y"}" placeholder="MM.JJJJ" aria-label="{lng p="date"}">

				</div>
			</div>
			<div class="col-md-6 text-end"><input class="btn btn-primary" type="submit" value="{lng p="show"} &raquo;" /></div>
		</div>
	</form>
</fieldset>

{foreach from=$stats item=stat}
	<fieldset>
		<legend>{$stat.title}</legend>

		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-end mb-2">
					<span class="text-secondary">{lng p="sum"}: <span class="fw-semibold text-body">{$stat.sum}</span></span>
				</div>
				<div class="bm-stat-chart" role="img" aria-label="{text value=$stat.title}">
					{foreach from=$stat.data item=values key=day}
						{if $values[$stat.key]===false}
							{assign var=barState value="is-future"}
							{assign var=barPct value=0}
						{elseif $stat.maxVal>0 && $values[$stat.key]>0}
							{assign var=barState value=""}
							{math equation="x/y*100" x=$values[$stat.key] y=$stat.maxVal format="%.2f" assign=barPct}
						{else}
							{assign var=barState value="is-zero"}
							{assign var=barPct value=0}
						{/if}
						<div class="bm-stat-col {$barState}" title="{lng p="day"} {$day}: {if $values[$stat.key]===false}–{else}{$values[$stat.key]}{/if}">
							<div class="bm-stat-track"><div class="bm-stat-bar" style="height: {$barPct}%;"></div></div>
							<span>{$day}</span>
						</div>
					{/foreach}
				</div>
			</div>
			<div class="card-body border-top">
				<div class="bm-stat-days">
					{foreach from=$stat.data item=values key=day}
						<div class="bm-stat-day{if $values[$stat.key]===false} is-future{elseif $values[$stat.key]>0} is-hot{/if}">
							<span class="bm-stat-day__n">{$day}</span>
							<span class="bm-stat-day__v">{if $values[$stat.key]===false}–{else}{$values[$stat.key]}{/if}</span>
						</div>
					{/foreach}
				</div>
			</div>
		</div>
	</fieldset>
{/foreach}