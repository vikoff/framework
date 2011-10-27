
<form id="regForm" name="regForm" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="profile/{$action}" />
	
	<h3>Регистрация нового пользователя</h3>
	
	<table class="reg-box" border>

		<tr>
			<td class="left">Логин:<span class="required">*</span></td>
			<td><?= Html_Form::inputText(array('name' => 'login', 'value' => $this->login)); ?></td>
		</tr>
		<tr>
			<td class="left">Пароль<span class="required">*</span><br />(не менее 5 символов):</td>
			<td><?= Html_Form::inputText(array('name' => 'password', 'value' => $this->password)); ?></td>
		</tr>
		<tr>
			<td class="left">Подтверждение пароля:<span class="required">*</span></td>
			<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
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
			<td class="left">Введите цифры с картинки:</td>
			<td>
				<div class="captcha-box">
					<img id="captcha" src="includes/captcha/captcha.php"/>
					<a href="#" onclick="captcha_reload(); return false;">Обновить</a>
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
	
	<script type="text/javascript">
		
		$(function(){
			
			$("#regForm").validate({ <?= $this->jsRules; ?> });
			
			// EMAIL CHECK //
			$(document.regForm.email).rules("add", {
				remote: href('user/profile/check-email'),
				messages: {
					remote: 'Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="{{$WWW_PREFIX}}profile/forget-password">восстановления учетной записи</a>'
				}
			});
			
		});
		
	</script>
</form>
