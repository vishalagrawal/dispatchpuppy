<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login | Dispatch Puppy</title>
	<link rel="stylesheet" type="text/css" href="<?php echo asset_url().'css/main.css'; ?>">

</head>
<body>

<div>
	<?php echo form_open('login/verify_login'); ?>
	<label for="email">Email</label>
	<input type="text" name="email" id="password"/>
	<label for="password">Password</label>
	<input type="password" name="password" id="password"/>
	<div><input type="submit" value="Log In" /></div>
	</form>
</div>

</body>
</html>