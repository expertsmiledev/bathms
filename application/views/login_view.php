<h2><?php echo $header; ?></h2>
<form id="frmLogin" method="POST" action="/login/validate" class="clear">
	<label for="email">Email Address</label>
	<input type="text" value="" name="email" id="email">
	<label for="password">Password</label>
	<input type="password" value="" name="password" id="password">
	<input type="submit" class="submit" value="Login" name="batLoginSubmit" id="loginSubmit">
</form>