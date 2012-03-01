
<div>{a href="admin/users/list" text="Вернуться к списку"}</div>

<h2>
	Пользователь #{$instanceId}
</h2>

<table class="std-grid">
<tr>
	<td class="title">email</td>
	<td class="data">{$email}</td>
</tr>
<tr>
	<td class="title">Фамилия</td>
	<td class="data">{$surname}</td>
</tr>
<tr>
	<td class="title">Имя</td>
	<td class="data">{$name}</td>
</tr>
<tr>
	<td class="title">Отчество</td>
	<td class="data">{$patronymic}</td>
</tr>
<tr>
	<td class="title">Пол</td>
	<td class="data">{$sex}</td>
</tr>
<tr>
	<td class="title">Дата рождения</td>
	<td class="data">{$birthdate}</td>
</tr>
<tr>
	<td class="title">Страна</td>
	<td class="data">{$country}</td>
</tr>
<tr>
	<td class="title">Город</td>
	<td class="data">{$city}</td>
</tr>
<tr>
	<td class="title">Права</td>
	<td class="data">
	{if $perms.allowEdit}
		<form class="inline" action="" method="post">
			{$formcode}
			<input type="hidden" name="instance-id" value="{$instanceId}" />
			<select name="level">{$perms.list}</select>
			<input type="submit" name="action[user/save-perms]" value="Сохранить" />
		</form>
	{else}
		{$perms.curTitle}
	{/if}
	</td>
</tr>
<tr>
	<td class="title">Активация</td>
	<td class="data">{$active}</td>
</tr>
<tr>
	<td class="title">Дата регистрации</td>
	<td class="data">{$regdate}</td>
</tr>
<tr>
	<td>Действия</td>
	<td class="actions">
		<a class="button" href="{a href=admin/users/list}">Вернуться к списку</a>
		<a class="button" href="{a href=admin/users/delete/$instanceId}">Удалить пользователя</a>
	</td>
</tr>
</table>
