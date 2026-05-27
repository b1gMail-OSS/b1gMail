{if isset($tab.iconDir) && isset($tab.icon)}
<img src="{$tab.iconDir}{$tab.icon}.png" width="16" height="16" alt="" class="bm-tab-icon-img" />
{elseif isset($tab.faIcon)}
{include file="li/icon.tpl" faIcon=$tab.faIcon}
{else}
{include file="li/icon.tpl" faIcon="fa-puzzle-piece"}
{/if}
