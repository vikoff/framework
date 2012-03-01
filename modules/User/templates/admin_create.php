
<ul id="submit-box-floating"></ul>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	
	<table class="grid" style="margin: 1em 0;">
	<tr>
		<th class="title" colspan="2">Создание нового пользователя</th>
	</tr>
	<tr>
		<th>Логин</th>
		<td><?= Html_Form::inputText(array('id' => 'input-login', 'name' => 'login', 'value' => $this->login)); ?></td>
	</tr>
	<tr>
		<th>Пароль</th>
		<td>
			<label><input type="radio" name="pass_type" value="input"> Ввести</label><br />
			<label><input type="radio" name="pass_type" value="generate"> Сгенерировать автоматически</label><br />
			<br />
			
			<div id="password-input-box" style="display: none;"> 
				<table>
				<tr>
					<td>пароль</td>
					<td><?= Html_Form::input(array('type' => 'password', 'name' => 'password', 'value' => $this->password)); ?></td>
				</tr><tr>
					<td>подтверждение</td>
					<td><?= Html_Form::input(array('type' => 'password', 'name' => 'password_confirm', 'value' => $this->password_confirm)); ?></td>
				</tr>
				</table>
			</div>
			
			<div id="password-generate-box" style="display: none;">
				<?= Html_Form::inputText(array('name' => 'password', 'value' => $this->password, 'readonly' => 'readonly')); ?>
				<?= Html_Form::input(array('type' => 'hidden', 'name' => 'password_confirm', 'value' => $this->password_confirm, 'readonly' => 'readonly')); ?>
				<input type="button" id="generate-pass-btn" name="" value="Сгенерировать" />
			</div>
			
		</td>
	</tr>
	<tr>
		<th>email</th>
		<td><?= Html_Form::input(array('type' => 'text', 'name' => 'email', 'value' => $this->email)); ?></td>
	</tr>
	<tr>
		<th>Имя</th>
		<td><?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?></td>
	</tr>
	<tr>
		<th>Фамилия</th>
		<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
	</tr>
	<tr>
		<th>Роль</th>
		<td><?= Html_Form::select(array('name' => 'role_id'), $this->rolesList, $this->role_id); ?></td>
	</tr>

	<tr>
		<td id="submit-box" class="actions" colspan="2">
			<input id="submit-save" class="button" type="submit" name="action[admin/user/create][admin/users/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
			<a id="submit-cancel" class="button" href="<?= href('admin/users/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">

$(function(){
	
	enableFloatingSubmits();
	
	function generatePass(){
		$.get(href('admin/users/generate-password'), function(response){
			$('#password-generate-box input[name="password"]').val(response);
			$('#password-generate-box input[name="password_confirm"]').val(response);
		});
	}

	$('input[name="pass_type"]').change(function(){
		switch($(this).val()){
			case 'input':
				$('#password-input-box').show();
				$('#password-generate-box').hide();
				$('#password-input-box input').removeAttr('disabled');
				$('#password-generate-box input').attr('disabled', 'disabled');
				break;
			case 'generate':
				// сгенерировать пароль, если его там нет
				if(!$('#password-generate-box input[name="password"]').val())
					generatePass();
				$('#password-input-box').hide();
				$('#password-generate-box').show();
				$('#password-input-box input').attr('disabled', 'disabled');
				$('#password-generate-box input').removeAttr('disabled');
				break;
		}
	});
	$('#generate-pass-btn').click(generatePass);
	
	<? if ($this->pass_type): ?>
		$('input[name="pass_type"][value="<?= $this->pass_type; ?>"]').attr('checked', 'checked').change();
	<? endif; ?>
	
});

</script>
