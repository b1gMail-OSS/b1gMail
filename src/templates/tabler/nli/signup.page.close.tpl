{if $nliCompactLayout|default:false}
			</div>
			<div class="card-footer bm-signup-card-footer">
				<p id="signupFinishHint" class="text-secondary small d-none mb-2">{lng p="completesignup"}</p>
				<div class="alert alert-info mb-3" role="alert">
					<div class="alert-icon">
						{* http://tabler.io/icons/icon/info-circle *}
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon">
							<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
							<path d="M12 9h.01"></path>
							<path d="M11 12h1v4h1"></path>
						</svg>
					</div>
					{lng p="iprecord"}
				</div>
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
					<label class="form-check mb-0">
						<input type="checkbox" class="form-check-input" name="tos" value="true"{if isset($_safePost.tos) && $_safePost.tos=='true'} checked="checked"{/if} />
						<span class="form-check-label">
							{lng p="accepttos"}
							<a href="#" data-bs-toggle="modal" data-bs-target="#tosModal">{lng p="tos"}</a>
						</span>
					</label>
					<button type="submit" id="signupSubmit" class="btn btn-success">
						<i class="ti ti-check me-1" aria-hidden="true"></i>{lng p="submit"}
					</button>
				</div>
			</div>
			</form>
		</div>
		{include file="nli/msp.footer.tpl"}
	</div>
</div>
{else}
{include file="nli/page.close.tpl"}
{/if}
