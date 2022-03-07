<table width="100%">
    <tbody>
        <tr>
            <td style="font-family:Arial" align="left">{$service_title}</td>
            <td style="font-family:Arial" align="right">{$service_title}<br />Bitte passen<br />Sie die
                Absender-Adresse<br />in der Rechnungsvorlage an.</td>
        </tr>
        <tr style="font-family:Arial">
            <td colspan="2">
                <hr style="height:1px" width="100%" noshade="noshade" color="#666666" />
            </td>
        </tr>
        <tr>
            <td style="font-family:Arial" align="left">
                <table style="border:1px solid #000000" width="100%" cellspacing="0" cellpadding="10">
                    <tbody>
                        <tr>
                            <td>{$vorname} {$nachname}<br />{if $firma}{$firma}<br />{/if}{$strasse} {$nr}<br />{$plz}
                                {$ort}<br />{$land}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="font-family:Arial" align="right"><strong>{lng p="date"}: </strong><span
                    style="font-family:Arial">{$datum}</span><br /><strong>{lng p="invoiceno"}: </strong><span
                    style="font-family:Arial">{$rgnr}</span><br /><strong>{lng p="customerno"}: </strong><span
                    style="font-family:Arial">{$kdnr}</span>{if $taxid}<br /><strong>{lng p="yourtaxid"}:</strong>
                {$taxid}{/if}</td>
        </tr>
        <tr style="font-family:Arial">
            <td colspan="2">
                <p> </p><br /><strong>{lng p="yourinvoice"}</strong>
                <p>{lng p="dearsirormadam"},</p>
                <p>{lng p="invtext"}:</p>
                <p>{foreach from=$cart item=pos} {/foreach}</p>
                <table width="100%" cellspacing="0" cellpadding="4">
                    <tbody>
                        <tr>
                            <td width="10%">{lng p="pos"}</td>
                            <td width="10%">{lng p="count"}</td>
                            <td width="50%">{lng p="descr"}</td>
                            <td width="15%">{lng p="ep"} ({$currency})</td>
                            <td width="15%">{lng p="gp"} ({$currency})</td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <hr style="height:1px" width="100%" noshade="noshade" color="#666666" />
                            </td>
                        </tr>
                        <tr>
                            <td>{$pos.pos}</td>
                            <td>{$pos.count}</td>
                            <td>{text value=$pos.text}</td>
                            <td>{$pos.amount}</td>
                            <td>{$pos.total}</td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <hr style="height:1px" width="100%" noshade="noshade" color="#666666" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">{lng p="gb"} ({lng p="net"}):</td>
                            <td>{$netto}</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">{lng p="vat"} {$mwstsatz}%:</td>
                            <td>{$mwst}</td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right">{lng p="gb"} ({lng p="gross"}):</td>
                            <td>{$brutto}</td>
                        </tr>
                    </tbody>
                </table>
                <p>{$zahlungshinweis}</p>
                <p>{lng p="kindregards"}</p>
                <p>{$service_title}</p>
                <p> </p>
            </td>
        </tr>
        <tr style="font-family:Arial">
            <td colspan="2">
                <hr style="height:1px" width="100%" noshade="noshade" color="#666666" />
            </td>
        </tr>
        <tr style="font-family:Arial">
            <td colspan="2">{lng p="invfooter"}<br /><br />{if $ktonr}<strong>{lng p="bankacc"}: </strong>{lng
                p="kto_nr"} {$ktonr} ({lng p="kto_inh"} {$ktoinhaber}), {lng p="kto_blz"} {$ktoblz} ({$ktoinstitut}){if
                $ktoiban}, {lng p="kto_iban"} {$ktoiban}, {lng p="kto_bic"} {$ktobic}{/if}{/if}</td>
        </tr>
    </tbody>
</table>