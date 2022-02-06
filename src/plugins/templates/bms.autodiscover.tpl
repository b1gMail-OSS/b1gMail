<?xml version="1.0" encoding="utf-8" ?>

<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
	<Response xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a">
		<Account>
			<AccountType>email</AccountType>
			<Action>settings</Action>
			<ServiceHome>{$serviceURL}</ServiceHome>

			{foreach from=$protocols item=prot}
			<Protocol>
				<Type>{$prot.type}</Type>
				<Server>{$prot.server}</Server>
				<Port>{$prot.port}</Port>
				<LoginName>{$userName}</LoginName>
				<SPA>off</SPA>
				{if !$prot.ssl&&$prot.tls}<Encryption>TLS</Encryption>{/if}
				<SSL>{if $prot.ssl}on{else}off{/if}</SSL>
				<AuthRequired>on</AuthRequired>
				{if $prot.type=='SMTP'}<UsePOPAuth>on</UsePOPAuth>{/if}
			</Protocol>
			{/foreach}
		</Account>
	</Response>
</Autodiscover>
