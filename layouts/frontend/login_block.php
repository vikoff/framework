	<?= $this->error; ?>
	
	<form class="login-form" action="" method="post">
		<?=FORMCODE;?>
		<div>
			<label>email</label><br />
			<input type="text" class="input" name="login" value="" />
		</div>
		<div>
			<label>пароль</label>
			<input type="password" class="input" name="pass" value="" />
		</div>
		<div style="overflow: hidden; margin-top: 5px;">
			<div style="float: left;">
				<label><input type="checkbox" name="remember" value="1" />запомнить меня</label>
			</div>
			<div style="float: right;">
				<input type="submit" class="button submit" name="action[user/profile/login]" value="Войти">
			</div>
		</div>
	
		<div style="text-align: center; margin-top: 5px;">
			<? if ($this->_topMenu->activeItem['id'] != 'registration'): ?>
				<a href="<?= href('user/profile/registration'); ?>">Регистрация</a><br />
			<? endif; ?>
			<a href="<?= href('user/profile/forget-password'); ?>">Напомнить пароль</a>
		</div>
	</form>
