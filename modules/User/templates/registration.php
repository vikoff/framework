
<form id="regForm" name="regForm" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="user/profile/register" />
	
	<h3>Регистрация нового пользователя</h3>
	
	<table class="reg-box" border>

		<tr>
			<td class="left">Логин:<span class="required">*</span></td>
			<td><?= Html_Form::inputText(array('name' => 'login', 'value' => $this->login)); ?></td>
		</tr>
		<tr>
			<td class="left">Пароль:<span class="required">*</span></td>
			<td><?= Html_Form::input(array('type' => 'password', 'name' => 'password', 'value' => $this->password)); ?></td>
		</tr>
		<tr>
			<td class="left">Подтверждение<br />пароля:<span class="required">*</span></td>
			<td><?= Html_Form::input(array('type' => 'password', 'name' => 'password_confirm', 'value' => $this->password_confirm)); ?></td>
		</tr>
		<tr>
			<td class="left">Email:<span class="required">*</span></td>
			<td><?= Html_Form::inputText(array('name' => 'email', 'value' => $this->email)); ?></td>
		</tr>
		<tr>
			<td class="left">Фамилия:</td>
			<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
		</tr>
		<tr>
			<td class="left">Имя:</td>
			<td><?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?></td>
		</tr>
		
		<tr>
			<td class="left">Введите<br />цифры с картинки:<span class="required">*</span></td>
			<td>
				<div class="captcha-box">
					<img id="captcha" src="libs/captcha/captcha.php"/>
					<a href="#" onclick="var c=$('#captcha');c.attr('src',c.attr('src').split('?')[0]+'?'+(new Date().getTime()));return false;">Обновить</a>
				</div>
				<div class="captcha-input">
					<input type="text" name="captcha" value="" class="ignore input">
				</div>
			</td>
		</tr>

		<tr>
			<td align="center" colspan="2">
				<input type="submit" class="button" value="Зарегистрироваться">
				<a href="<?= href(''); ?>" class="button">Отмена</a>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	<script type="text/javascript">
		
		$(function(){
			
			// $("#regForm").validate({ <?= $this->jsRules; ?> });
			
			// EMAIL CHECK //
			// $(document.regForm.email).rules("add", {
				// remote: href('user/profile/check-email'),
				// messages: {
					// remote: 'Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="{{$WWW_PREFIX}}profile/forget-password">восстановления учетной записи</a>'
				// }
			// });
			
		});
		
	</script>
</form>
