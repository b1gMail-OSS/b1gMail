<?xml version="1.0" encoding="UTF-8"?>

<clientConfig version="1.1">
	<emailProvider id="{$domain}">
		<domain>{$domain}</domain>
		<displayName>{text value=$serviceTitle}</displayName>
		<displayShortName>{text value=$serviceTitle}</displayShortName>

		{foreach from=$protocols item=prot}
		<{if $prot.type!='SMTP'}incomingServer{else}outgoingServer{/if} type="{$prot.type|lower}">
			<hostname>{$prot.server}</hostname>
			<port>{$prot.port}</port>
			<socketType>{if $prot.ssl}SSL{elseif $prot.tls}STARTTLS{else}plain{/if}</socketType>
			<authentication>password-cleartext</authentication>
			<username>%EMAILADDRESS%</username>
		</{if $prot.type!='SMTP'}incomingServer{else}outgoingServer{/if}>
		{/foreach}
	</emailProvider>
</clientConfig>
