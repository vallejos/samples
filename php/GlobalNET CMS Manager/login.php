<?php


?>
<div id="content">
<div id="loginbox">
<form id="loginform" action="index.php" method="post">
   <fieldset><legend>Login</legend>
	<div>
		<label for="log">Email
		<input type="text" name="email" id="email" size="20" /></label>
	</div>
	<div>
		<label for="pwd">Password
		<input type="password" name="password" id="password" size="20" /></label>
	</div>
	<div>
		<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> Remember Me</label>
	</div>
	<div>
		<input type="submit" name="submit" id="submit" value="Log In" />
		<input type="hidden" name="redirect_to" value="" />
	</div>
    </fieldset>
</form>
</div>
</div>