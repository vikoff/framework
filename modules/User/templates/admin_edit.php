
<h2>Пользователь #<?= $this->instanceId; ?> <?= $this->login; ?></h2>

<h3>Редактирование данных</h3>
<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<table class="grid" style="margin: 1em 0;">
	<tr>
		<th>id</th>
		<td><?= $this->id; ?></td>
	</tr>
	<tr>
		<th>Логин</th>
		<td><?= $this->login; ?></td>
	</tr>
	<tr>
		<th>Дата регистрации</th>
		<td><?= $this->regdate; ?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?= Html_Form::inputText(array('name' => 'email', 'value' => $this->email)); ?></td>
	</tr>
	<tr>
		<th>Фамилия</th>
		<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
	</tr>
	<tr>
		<th>Имя</th>
		<td><?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?></td>
	</tr>
	<tr>
		<th>Роль</th>
		<td><?= Html_Form::select(array('name' => 'role_id'), $this->rolesList, $this->role_id); ?></td>
	</tr>
	<tr>
		<th>Доп. опции</th>
		<td>
			<a href="<?= href('admin/users/ban/'.$this->instanceId); ?>">заблокировать</a>
			<a href="<?= href('admin/users/delete/'.$this->instanceId); ?>">удалить</a>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="text-align: center;">
			<input class="button" type="submit" name="action[admin/user/save]" value="Сохранить" />
			<a class="button" href="<?= href('admin/users/list'); ?>">назад</a>
		</td>
	</tr>
	</table>
</form>

<br />

<h3>Изменение пароля пользователя</h3>
<?= Messenger::get()->ns('password-change')->getAll(); ?>
<form action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<table class="grid" style="margin: 1em 0;">
		<tr>
			<th>Новый пароль</th>
			<td><input type="password" name="password" value="" />
		</tr>
		<tr>
			<th>Подтверждения пароля</th>
			<td><input type="password" name="password_confirm" value="" />
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<input type="submit" name="action[admin/user/change-password]" class="button" value="Применить" />
			</td>
	</table>
</form>


<br />
<h3>Блокирование пользователя</h3>
<div><a class="button" href="<?= href('admin/users/ban/'.$this->instanceId); ?>">Заблокировать</a></div>


<br />
<h3>Удаление пользователя</h3>
<div><a class="button" href="<?= href('admin/users/delete/'.$this->instanceId); ?>">Удалить</a></div>

<script type="text/javascript">

$(function(){
	// enableFloatingSubmits();
});

</script>
