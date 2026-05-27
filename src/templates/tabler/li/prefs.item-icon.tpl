{* Tabler-Icon für Einstellungsbereich – erwartet $item *}
{if isset($prefsIcons[$item])}
	<img src="{$prefsIcons[$item]}" width="20" height="20" class="bm-prefs-item-icon-img" alt="" />
{elseif isset($prefsfaIcons[$item])}
	{if $prefsfaIcons[$item] == 'fa-user' || $prefsfaIcons[$item] == 'fa-user-o'}<i class="ti ti-at icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-cogs' || $prefsfaIcons[$item] == 'fa-cog'}<i class="ti ti-settings icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-reply'}<i class="ti ti-mail-forward icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-ban'}<i class="ti ti-ban icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-bug'}<i class="ti ti-bug icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-filter'}<i class="ti ti-filter icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-id-badge'}<i class="ti ti-ticket icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-id-card' || $prefsfaIcons[$item] == 'fa-id-card-o'}<i class="ti ti-id icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-compress'}<i class="ti ti-mail-down icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-key'}<i class="ti ti-key icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-quote-right'}<i class="ti ti-quote icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-download'}<i class="ti ti-download icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-shopping-cart'}<i class="ti ti-shopping-cart icon icon-1" aria-hidden="true"></i>
	{elseif $prefsfaIcons[$item] == 'fa-question-circle' || $prefsfaIcons[$item] == 'fa-question-circle-o'}<i class="ti ti-help icon icon-1" aria-hidden="true"></i>
	{else}{include file="li/icon.tpl" faIcon=$prefsfaIcons[$item]}
	{/if}
{elseif $item == 'aliases'}<i class="ti ti-at icon icon-1" aria-hidden="true"></i>
{elseif $item == 'common'}<i class="ti ti-settings icon icon-1" aria-hidden="true"></i>
{elseif $item == 'autoresponder'}<i class="ti ti-mail-forward icon icon-1" aria-hidden="true"></i>
{elseif $item == 'antispam'}<i class="ti ti-ban icon icon-1" aria-hidden="true"></i>
{elseif $item == 'antivirus'}<i class="ti ti-bug icon icon-1" aria-hidden="true"></i>
{elseif $item == 'filters'}<i class="ti ti-filter icon icon-1" aria-hidden="true"></i>
{elseif $item == 'coupons'}<i class="ti ti-ticket icon icon-1" aria-hidden="true"></i>
{elseif $item == 'membership'}<i class="ti ti-id icon icon-1" aria-hidden="true"></i>
{elseif $item == 'extpop3'}<i class="ti ti-mail-down icon icon-1" aria-hidden="true"></i>
{elseif $item == 'keyring'}<i class="ti ti-key icon icon-1" aria-hidden="true"></i>
{elseif $item == 'signatures'}<i class="ti ti-quote icon icon-1" aria-hidden="true"></i>
{elseif $item == 'software'}<i class="ti ti-download icon icon-1" aria-hidden="true"></i>
{elseif $item == 'contact'}<i class="ti ti-address-book icon icon-1" aria-hidden="true"></i>
{elseif $item == 'faq'}<i class="ti ti-help icon icon-1" aria-hidden="true"></i>
{elseif $item == 'orders'}<i class="ti ti-shopping-cart icon icon-1" aria-hidden="true"></i>
{else}<i class="ti ti-point icon icon-1" aria-hidden="true"></i>
{/if}
