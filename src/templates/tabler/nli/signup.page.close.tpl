{if $nliCompactLayout|default:false}
			</div>
			<div class="card-footer bm-signup-card-footer">
				<p id="signupFinishHint" class="text-secondary small d-none mb-2">{lng p="completesignup"}</p>
				<div class="text-secondary small mb-3">
					<i class="ti ti-info-circle me-1" aria-hidden="true"></i>
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
