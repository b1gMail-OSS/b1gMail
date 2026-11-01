<div class="bm-start-dashboard bm-dashboard-page">
	<div id="contentHeader" class="contentHeader bm-organizer-header bm-dashboard-header">
		<div class="left">
			<i class="ti ti-layout-grid-add icon icon-sm" aria-hidden="true"></i>
			{lng p="customize"}
		</div>
	</div>

	<div class="bm-dashboard-body">
		<div class="card bm-dashboard-card bm-customize-card">
			<form name="f1" method="post" action="{sessionurl file='start.php' params='action=saveCustomize'}">
				{csrffield}
				<div class="list-group list-group-flush">
					{foreach from=$possibleWidgets key=widget item=info}
					{if $widget=='BMPlugin_Widget_Websearch'}{assign var=widgetIcon value='ti-search'}
					{elseif $widget=='BMPlugin_Widget_Mailspace'}{assign var=widgetIcon value='ti-database'}
					{elseif $widget=='BMPlugin_Widget_Webdiskspace'}{assign var=widgetIcon value='ti-cloud'}
					{elseif $widget=='BMPlugin_Widget_Tasks'}{assign var=widgetIcon value='ti-list-check'}
					{elseif $widget=='BMPlugin_Widget_Welcome'}{assign var=widgetIcon value='ti-home'}
					{elseif $widget=='BMPlugin_Widget_WebdiskDND'}{assign var=widgetIcon value='ti-cloud-upload'}
					{elseif $widget=='BMPlugin_Widget_Calendar'}{assign var=widgetIcon value='ti-calendar'}
					{elseif $widget=='BMPlugin_Widget_Notes'}{assign var=widgetIcon value='ti-notes'}
					{elseif $widget=='BMPlugin_Widget_EMail'}{assign var=widgetIcon value='ti-mail'}
					{elseif $widget=='BMPlugin_Widget_Quicklinks'}{assign var=widgetIcon value='ti-link'}
					{elseif $widget=='Widget_Calculator'}{assign var=widgetIcon value='ti-calculator'}
					{else}{assign var=widgetIcon value='ti-puzzle'}{/if}
					<label class="list-group-item d-flex align-items-center gap-3 mb-0" for="widget_{$widget}">
						<input class="form-check-input m-0 flex-shrink-0" type="checkbox" id="widget_{$widget}" name="widget_{$widget}"{if !empty($info.active)} checked="checked"{/if} />
						<span class="bm-customize-icon" aria-hidden="true"><i class="ti {$widgetIcon} icon"></i></span>
						<span>{$info.title}</span>
					</label>
					{/foreach}
				</div>

				<div class="card-footer d-flex justify-content-end gap-2">
					<button type="submit" class="btn btn-sm btn-primary">{lng p="ok"}</button>
					<button type="reset" class="btn btn-sm">{lng p="reset"}</button>
				</div>
			</form>
		</div>
	</div>
</div>
