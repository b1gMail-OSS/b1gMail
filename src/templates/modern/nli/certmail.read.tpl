<div class="container">
	<div class="page-header"><h1>{lng p="readcertmail"}</h1></div>

	<div class="panel panel-default">
		<div class="panel-heading panel-title">{text value=$subject cut=45}</div>
		<table class="table">
			<tbody>
				<tr>
					<td><strong>{lng p="from"}:</strong></td>
					<td>{addressList list=$fromAddresses simple=true}</td>
				</tr>
				<tr>
					<td><strong>{lng p="subject"}:</strong></td>
					<td>{text value=$subject}</td>
				</tr>
				<tr>
					<td><strong>{lng p="date"}:</strong></td>
					<td>{date timestamp=$date elapsed=true}</td>
				</tr>
				<tr>
					<td><strong>{lng p="to"}:</strong></td>
					<td>{addressList list=$toAddresses simple=true}</td>
				</tr>
				{if $ccAddresses}<tr>
					<td><strong>{lng p="cc"}:</strong></td>
					<td>{addressList list=$ccAddresses simple=true}</td>
				</tr>{/if}
				{if $replyToAddresses}<tr>
					<td><strong>{lng p="replyto"}:</strong></td>
					<td>{addressList list=$replyToAddresses simple=true}</td>
				</tr>{/if}
				{if $priority!=0}<tr>
					<td><strong>{lng p="priority"}:</strong></td>
					<td>
						{if $priority==1}<i class="fa fa-exclamation" aria-hidden="true"></i>{/if}
						{lng p="prio_$priority"}
					</td>
				</tr>{/if}
			</tbody>
		</table>
	</div>
 	
	<div>
		<iframe width="100%" style="background-color: #FFFFFF;height:250px;border: 1px solid #DDDDDD;margin-bottom: 1em;" src="index.php?action=readCertMail&amp;id={$mailID}&amp;key={$key}&amp;showText=true" frameborder="no"></iframe>
	</div>
	
	{if $attachments}
	<div class="panel panel-default">
		<div class="panel-heading">{lng p="attachments"}</div>
		<ul class="list-group">
			{foreach from=$attachments item=attachment key=attID}
			<li class="list-group-item">
				<img src="{$tpldir}images/main/attachment.gif" alt="" />
				<a href="index.php?action=readCertMail&amp;id={$mailID}&amp;key={$key}&amp;downloadAttachment=true&amp;attachment={$attID}">{text value=$attachment.filename cut=45}</a>
			</li>
			{/foreach}
		</ul>
	</div>
	{/if}
</div>
