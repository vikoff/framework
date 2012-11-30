
<form action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="user/profile/forget-password" />
	<p>
		<label>Введите свой email:</label><br />
		<input type="text" name="email" value="" style="width: 200px;" />
	</p>
	<p>
		Код восстановления будет отправлен на указанный email-адрес.<br />
		<input type="submit" value="Отправить" />
	</p>
</form>