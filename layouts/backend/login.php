<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getHtmlTitle();?></title>
	<base href="<?=$this->_getHtmlBaseHref();?>" />
	<style type="text/css">
		html, body{
			padding: 0;
			margin: 0;
			width: 100%;
			height: 100%;
			font-family: Tahoma, Verdana, sans-serif;
			font-size: 13px;
		}
		a{
			color: #3763FB;
			text-decoration: none;
		}
		a:hover{
			text-decoration: underline;
		}
		#login-screen{
			position: relative;
			margin: auto;
			margin-top: -80px;
			top: 50%;
			width: 300px;
			border: solid 5px #AAA;
			padding: 10px;
			-moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
		}
		#login-screen .explain{
			text-align: center;
			font-style: italic;
			margin-top: 5px;
		}
	</style>
</head>
<body>

<div id="login-screen">
	
	<div class="explain">Пожалуйста, авторизуйтесь.</div>
	
	<?=$this->errorMessage;?>
	
	<form action="" method="post">
		<input type="hidden" name="action" value="user-profile/login" />
		<?=FORMCODE;?>
		
		<table style="width: 100%;">
		<tr><td>Логин</td><td><input type="text" name="login" value="" style="width: 98%;" /></td></tr>
		<tr><td>Пароль</td><td><input type="password" name="pass" value="" style="width: 98%;" /></td></tr>
		<tr>
			<td></td>
			<td>
				<input type="checkbox" name="remember" id="rememberme-checkbox" value="1" />
				<label for="rememberme-checkbox">Запомнить меня</label>
				<div style="float: right;"><input type="submit" class="submit" value="Войти" style="border: solid 1px #999;"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center; padding: 0;"><a href="<?= App::href('registration'); ?>">Регистрация</a></td>
		</tr>
		</table>
	</form>
	
</div>

</body>
</html>