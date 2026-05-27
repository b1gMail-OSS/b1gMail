{* Map legacy Font Awesome tab/menu icons to Tabler Icons *}
{if $faIcon == 'fa-home'}<i class="ti ti-home icon icon-1"></i>
{elseif $faIcon == 'fa-envelope-o' || $faIcon == 'fa-envelope'}<i class="ti ti-mail icon icon-1"></i>
{elseif $faIcon == 'fa-calendar'}<i class="ti ti-calendar icon icon-1"></i>
{elseif $faIcon == 'fa-tasks'}<i class="ti ti-list-check icon icon-1"></i>
{elseif $faIcon == 'fa-address-book-o' || $faIcon == 'fa-address-book'}<i class="ti ti-address-book icon icon-1"></i>
{elseif $faIcon == 'fa-sticky-note-o' || $faIcon == 'fa-sticky-note'}<i class="ti ti-notes icon icon-1"></i>
{elseif $faIcon == 'fa-comments' || $faIcon == 'fa-comment'}<i class="ti ti-message-circle icon icon-1"></i>
{elseif $faIcon == 'fa-cloud'}<i class="ti ti-cloud icon icon-1"></i>
{elseif $faIcon == 'fa-file-o' || $faIcon == 'fa-file'}<i class="ti ti-file icon icon-1"></i>
{elseif $faIcon == 'fa-cog' || $faIcon == 'fa-gear'}<i class="ti ti-settings icon icon-1"></i>
{elseif $faIcon == 'fa-search'}<i class="ti ti-search icon icon-1"></i>
{elseif $faIcon == 'fa-bell'}<i class="ti ti-bell icon icon-1"></i>
{elseif $faIcon == 'fa-plus-square' || $faIcon == 'fa-plus'}<i class="ti ti-square-plus icon icon-1"></i>
{elseif $faIcon == 'fa-puzzle-piece'}<i class="ti ti-layout-grid-add icon icon-1"></i>
{elseif $faIcon == 'fa-folder-open-o' || $faIcon == 'fa-folder-open' || $faIcon == 'fa-folder-o' || $faIcon == 'fa-folder'}<i class="ti ti-folders icon icon-1"></i>
{elseif $faIcon == 'fa-inbox'}<i class="ti ti-inbox icon icon-1"></i>
{elseif $faIcon == 'fa-send' || $faIcon == 'fa-paper-plane'}<i class="ti ti-send icon icon-1"></i>
{elseif $faIcon == 'fa-ban'}<i class="ti ti-ban icon icon-1"></i>
{elseif $faIcon == 'fa-trash-o' || $faIcon == 'fa-trash'}<i class="ti ti-trash icon icon-1"></i>
{elseif $faIcon == 'fa-tachometer'}<i class="ti ti-dashboard icon icon-1"></i>
{elseif $faIcon == 'fa-question' || $faIcon == 'fa-question-circle'}<i class="ti ti-help icon icon-1"></i>
{elseif $faIcon == 'fa-sign-out' || $faIcon == 'fa-sign-out-alt'}<i class="ti ti-logout icon icon-1"></i>
{elseif $faIcon == 'fa-pencil' || $faIcon == 'fa-edit'}<i class="ti ti-pencil icon icon-1"></i>
{elseif $faIcon == 'fa-fax'}<i class="ti ti-printer icon icon-1"></i>
{elseif $faIcon == 'fa-paper-plane-o' || $faIcon == 'fa-paper-plane'}<i class="ti ti-send icon icon-1"></i>
{elseif $faIcon == 'fa-archive'}<i class="ti ti-archive icon icon-1"></i>
{elseif $faIcon == 'fa-id-card-o' || $faIcon == 'fa-id-card'}<i class="ti ti-id icon icon-1"></i>
{elseif $faIcon == 'fa-user-plus'}<i class="ti ti-user-plus icon icon-1"></i>
{elseif $faIcon == 'fa-exchange'}<i class="ti ti-arrows-exchange icon icon-1"></i>
{elseif $faIcon == 'fa-user'}<i class="ti ti-user icon icon-1"></i>
{elseif $faIcon == 'fa-users'}<i class="ti ti-users icon icon-1"></i>
{elseif $faIcon}<i class="fa {$faIcon|escape}" aria-hidden="true"></i>
{else}<i class="ti ti-point icon icon-1"></i>
{/if}
