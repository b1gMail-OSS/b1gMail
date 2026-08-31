{if $nliCompactLayout|default:false}
<h2 class="h3 mb-3">{if $signUp}{lng p="signup"}{elseif $nliPackages}{lng p="pacc_packages"}{else}{lng p="login"}{/if}</h2>
<p class="text-secondary mb-4">{$orderText}</p>
{include file="plugins/templates/pacc.nli.packages.matrix.tpl"}
{else}
<div class="page-body bm-pacc-nli-page">
	<div class="container-xl py-4">
		<div class="card bm-pacc-nli-card">
			<div class="card-body">
				<h2 class="h3 mb-3">{if $signUp}{lng p="signup"}{elseif $nliPackages}{lng p="pacc_packages"}{else}{lng p="login"}{/if}</h2>
				<p class="text-secondary mb-4">{$orderText}</p>
				{include file="plugins/templates/pacc.nli.packages.matrix.tpl"}
			</div>
		</div>
	</div>
</div>
{/if}
