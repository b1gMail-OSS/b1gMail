<section class="main-title-section-wrapper breadcrumb-right">
	<div class="main-title-section-bg"></div>
	<div class="container">
		<div class="main-title-section">
			<h1>{if $signUp}{lng p="signup"}{else}{lng p="pacc_packages"}{/if}</h1>
		</div>
	</div>
</section>
</div>

<div id="main" class="bm-pacc-nli-page bm-pacc-nli-km">
	<div class="container">
		<section id="primary" class="content-full-width">
			<div class="wpb_wrapper bm-pacc-nli-km-body">
				<p class="bm-pacc-nli-intro">{$orderText}</p>
				{include file="plugins/templates/pacc.nli.packages.matrix.tpl"}
			</div>
		</section>
	</div>
</div>
