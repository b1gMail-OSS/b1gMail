<div data-role="header" data-position="fixed">
	<h1>{$pageTitle}</h1>
</div>

<div data-role="content">
	<h2>{lng p="login"}</h2>
	
	<form action="index.php?action=login" method="post" data-ajax="false">
		<input type="hidden" name="do" value="login" />
	
		<div data-role="fieldcontain">
			<label for="email">{lng p="email"}:</label>
			<input type="email" name="email" id="email" value="{text value=$smarty.cookies.bm_msavedUser allowEmpty=true}"  />
		</div>

		<div data-role="fieldcontain">
			<label for="password">{lng p="password"}:</label>
			<input type="password" name="password" id="password" value="{text value=$smarty.cookies.bm_msavedPassword allowEmpty=true}"  />
		</div>
	
		<div data-role="fieldcontain" class="ui-hide-label">
		    <fieldset data-role="controlgroup">
			   <legend>{lng p="savelogin"}:</legend>
			   <input type="checkbox" name="savelogin" id="savelogin"{if $smarty.cookies.bm_msavedUser} checked="checked"{/if} />
			   <label for="savelogin">{lng p="savelogin"}</label>
		    </fieldset>
		</div>
	
		<button type="submit">{lng p="login"}</button>
	</form>
	
	<div class="bottomLink">
		<a href="{$selfurl}?noMobileRedirect=true" rel="external">{lng p="desktopversion"}</a>
	</div>
</div>
