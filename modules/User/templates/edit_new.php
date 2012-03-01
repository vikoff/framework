
<form id="regForm" name="regForm" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="action" value="profile/{$action}" />
	
	<h3>Регистрация нового пользователя</h3>
	<table class="reg-box" border>
		<tr>
			<td colspan="2">
				<h3>
				{if $action == 'registration'}
					
				{else}
					Изменение личных данных
				{/if}
				</h3>
				
				{if $userError}
					{$userError}
				{/if}
			</td>
		</tr>

	{if $action == 'registration'}

		<tr>
			<td class="left">E-mail:<span class="required">*</span></td>
			<td><input type="text" class="input" name="email" value="{$email}"></td>
		</tr>
		<tr>
			<td class="left">Пароль<span class="required">*</span><br />(не менее 5 символов):</td>
			<td><input type="password" class="input" name="password" value="{$password}"></td>
		</tr>
		<tr>
			<td class="left">Подтверждение пароля:<span class="required">*</span></td>
			<td><input type="password" class="input" name="password_confirm" value="{$password_confirm}"></td>
		</tr>
		
	{/if}

		<tr>
			<td class="left">Фамилия:</td>
			<td><input type="text" class="input" name="surname" value="{$surname}"></td>
		</tr>
		<tr>
			<td class="left">Имя:</td>
			<td><input type="text" class="input" name="name" value="{$name}"></td>
		</tr>
		<tr>
			<td class="left">Отчество:</td>
			<td><input type="text" class="input" name="patronymic" value="{$patronymic}"></td>
		</tr>
		<tr>
			<td class="left">Пол:</td>
			<td>
				<select name="gender" class="input">
					<option value="-" {if $gender == '-'}selected="selected"{/if}></option>
					<option value="m" {if $gender == 'm'}selected="selected"{/if}>мужчина</option>
					<option value="w" {if $gender == 'w'}selected="selected"{/if}>женщина</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left">Дата рождения:</td>
			<td>
				Число <select name="birth[day]"   style="width: 4em;"><option value=""></option>{$days_list}</select>
				Месяц <select name="birth[month]" style="width: 4em;"><option value=""></option>{$months_list}</select>
				Год   <select name="birth[year]"  style="width: 5em;"><option value=""></option>{$years_list}</select>
			</td>
		</tr>
		<tr>
			<td class="left">Страна:</td>
			<td><select name="country" class="input"><option value="" class="grey">Выберите страну...</option>{$countries_list}</select></td>
		</tr>
		<tr>
			<td class="left">Город:</td>
			<td><input type="text" class="input" name="city" value="{$city}"></td>
		</tr>
		
	{if $action == 'registration'}
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
		
	{/if}

		<tr>
			<td align="center" colspan="2">
				<input class="button" type="submit" value="Сохранить">
				<a href="{a href=''}" class="button">Отмена</a>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	
	<script type="text/javascript">
		
		$(function(){
			
			$("#regForm").validate({
				ignore: ".ignore",
				{{$jsRules}}
			});
			
			{{if $action == 'registration'}}
			
			// EMAIL CHECK //
			
			$(document.regForm.email).rules("add", {
				remote: "ajax.php?r=profile/check_email",
				messages: {
					remote: 'Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="{{$WWW_PREFIX}}profile/forget-password">восстановления учетной записи</a>'
				}
			});
			
			{{/if}}
			
		});
		
	</script>
</form>

{if $action == 'edit'}

<form name="passwordForm" action="" method="POST">
	{$formcode}
	<input type="hidden" name="action" value="profile/set_new_password" />
	<table class='reg_box'>
		<tbody>
		<tr>
			<td colspan='2'>
				<h3>
					Изменение пароля
				</h3>
				{if $passwordError}
					{$passwordError}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="left">Старый пароль:</td>
			<td><input type="password" name="oldPassword" value=""></td>
		</tr>
		<tr>
			<td class="left">Новый пароль:</td>
			<td><input type="password" name="newPassword" value=""></td>
		</tr>
		<tr>
			<td class="left">Подтверждение пароля:</td>
			<td><input type="password" name="newPasswordConfirm" value=""></td>
		</tr>
		<tr>
			<td class="left"></td>
			<td><input type="submit" name="" value="Изменить пароль"></td>
		</tr>
		</tbody>
	</table>
</form>
{/if}

