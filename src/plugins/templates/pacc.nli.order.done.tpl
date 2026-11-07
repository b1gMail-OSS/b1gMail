<h2 class="h3 mb-3">{lng p="pacc_order"}</h2>

{if $paccActivated}
<p class="text-secondary mb-4">{lng p="pacc_activated"}</p>
{else}
<p class="text-secondary mb-4">{lng p="pacc_thanks"}</p>

{include file="li/payment.pay.tpl"}
{/if}
