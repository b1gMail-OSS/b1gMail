<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.45;color:#1d273b;">
	<tbody>
		<tr>
			<td valign="top" style="padding:0 16px 18px 0;font-size:22px;line-height:1.2;font-weight:bold;">{$service_title}</td>
			<td valign="top" align="right" width="46%" style="padding:0 0 18px 16px;font-size:12px;line-height:1.45;color:#626976;text-align:right;">
				{$service_title}<br />
				Bitte passen Sie die Absender-Adresse<br />
				in der Rechnungsvorlage an.
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-top:2px solid #1d273b;font-size:0;line-height:0;height:2px;">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" style="padding:22px 16px 8px 0;">
				<table width="100%" cellspacing="0" cellpadding="12" style="border:1px solid #d0d5dd;border-collapse:collapse;">
					<tbody>
						<tr>
							<td style="font-size:13px;line-height:1.45;">
								{$vorname} {$nachname}<br />
								{if $company}{$company}<br />{elseif $firma}{$firma}<br />{/if}
								{$strasse} {$nr}<br />
								{$plz} {$ort}<br />
								{$land}
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td valign="top" align="right" style="padding:22px 0 8px 16px;">
				<table cellspacing="0" cellpadding="0" align="right" style="border-collapse:collapse;font-size:13px;">
					<tbody>
						<tr>
							<td align="right" style="padding:2px 12px 2px 0;color:#626976;text-align:right;">{lng p="date"}</td>
							<td align="right" style="padding:2px 0;font-weight:bold;text-align:right;">{$datum}</td>
						</tr>
						<tr>
							<td align="right" style="padding:2px 12px 2px 0;color:#626976;text-align:right;">{lng p="invoiceno"}</td>
							<td align="right" style="padding:2px 0;font-weight:bold;text-align:right;">{$rgnr}</td>
						</tr>
						<tr>
							<td align="right" style="padding:2px 12px 2px 0;color:#626976;text-align:right;">{lng p="customerno"}</td>
							<td align="right" style="padding:2px 0;font-weight:bold;text-align:right;">{$kdnr}</td>
						</tr>
						{if $taxid}
						<tr>
							<td align="right" style="padding:2px 12px 2px 0;color:#626976;text-align:right;">{lng p="yourtaxid"}</td>
							<td align="right" style="padding:2px 0;font-weight:bold;text-align:right;">{$taxid}</td>
						</tr>
						{/if}
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding:26px 0 0 0;">
				<div style="font-size:16px;font-weight:bold;margin:0 0 12px 0;">{lng p="yourinvoice"}</div>
				<p style="margin:0 0 8px 0;">{lng p="dearsirormadam"},</p>
				<p style="margin:0 0 14px 0;">{lng p="invtext"}:</p>
				<table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-size:13px;">
					<tbody>
						<tr>
							<td width="8%" style="border-bottom:1px solid #d0d5dd;background:#f4f6f8;font-weight:bold;">{lng p="pos"}</td>
							<td width="12%" style="border-bottom:1px solid #d0d5dd;background:#f4f6f8;font-weight:bold;">{lng p="count"}</td>
							<td width="44%" style="border-bottom:1px solid #d0d5dd;background:#f4f6f8;font-weight:bold;">{lng p="descr"}</td>
							<td width="18%" align="right" style="border-bottom:1px solid #d0d5dd;background:#f4f6f8;font-weight:bold;text-align:right;">{lng p="ep"} ({$currency})</td>
							<td width="18%" align="right" style="border-bottom:1px solid #d0d5dd;background:#f4f6f8;font-weight:bold;text-align:right;">{lng p="gp"} ({$currency})</td>
						</tr>
						{foreach from=$cart item=pos}
						<tr style="page-break-inside:avoid;">
							<td style="border-bottom:1px solid #eef0f2;">{$pos.pos}</td>
							<td style="border-bottom:1px solid #eef0f2;">{$pos.count}</td>
							<td style="border-bottom:1px solid #eef0f2;">{text value=$pos.text}</td>
							<td align="right" style="border-bottom:1px solid #eef0f2;text-align:right;">{$pos.amount}</td>
							<td align="right" style="border-bottom:1px solid #eef0f2;text-align:right;">{$pos.total}</td>
						</tr>
						{/foreach}
						<tr>
							<td colspan="4" align="right" style="padding:12px 8px 3px 8px;text-align:right;">{lng p="gb"} ({lng p="net"})</td>
							<td align="right" style="padding:12px 0 3px 8px;text-align:right;">{$netto}</td>
						</tr>
						<tr>
							<td colspan="4" align="right" style="padding:3px 8px;text-align:right;">{lng p="vat"} {$mwstsatz}%</td>
							<td align="right" style="padding:3px 0 3px 8px;text-align:right;">{$mwst}</td>
						</tr>
						{if $transaktionskosten > 0}
						<tr>
							<td colspan="4" align="right" style="padding:3px 8px;text-align:right;">{lng p="payment_surcharge"}</td>
							<td align="right" style="padding:3px 0 3px 8px;text-align:right;">{$transaktionskosten}</td>
						</tr>
						{/if}
						<tr>
							<td colspan="4" align="right" style="border-top:2px solid #1d273b;padding:8px 8px 0 8px;font-weight:bold;text-align:right;">{lng p="gb"} ({lng p="gross"})</td>
							<td align="right" style="border-top:2px solid #1d273b;padding:8px 0 0 8px;font-weight:bold;text-align:right;">{$brutto}</td>
						</tr>
					</tbody>
				</table>
				<p style="margin:18px 0 8px 0;">{$zahlungshinweis}</p>
				<p style="margin:16px 0 0 0;">{lng p="kindregards"}</p>
				<p style="margin:4px 0 0 0;">{$service_title}</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding:28px 0 0 0;border-top:1px solid #d0d5dd;font-size:11px;line-height:1.45;color:#626976;">
				{lng p="invfooter"}<br /><br />
				{if $ktonr}<strong>{lng p="bankacc"}: </strong>{lng p="kto_nr"} {$ktonr} ({lng p="kto_inh"} {$ktoinhaber}), {lng p="kto_blz"} {$ktoblz} ({$ktoinstitut}){if $ktoiban}, {lng p="kto_iban"} {$ktoiban}, {lng p="kto_bic"} {$ktobic}{/if}{/if}
			</td>
		</tr>
	</tbody>
</table>
