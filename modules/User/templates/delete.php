
<p style="text-align: center;">
	Вы уверены, что хотите удалить пользователя #<?= $this->instanceId; ?> безвозвратно?
</p>

<table class="grid">
<tr>
	<th>email</th>
	<td><?= $this->email; ?></td>
</tr>
<tr>
	<th>Фамилия</th>
	<td><?= $this->surname; ?></td>
</tr>
<tr>
	<th>Имя</th>
	<td><?= $this->name; ?></td>
</tr>
<tr>
	<th>Права</th>
	<td><?= $this->role_str; ?></td>
</tr>
<tr>
	<th>Дата регистрации</th>
	<td><?= $this->regdate; ?></td>
</tr>
</table>

<form action="<?= href('admin/users/list'); ?>" method="post" style="margin: 1em 0; text-align: center;">
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<?= FORMCODE; ?>
	
	<input class="button" type="submit" name="action[admin/user/delete]" value="Удалить безвозвратно" />
	<input class="button" type="submit" name="cancel" value="Отмена" />
</form>
