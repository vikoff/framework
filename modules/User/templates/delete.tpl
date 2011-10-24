
<p>
	Вы уверены, что хотите удалить пользователя #{$instanceId} безвозвратно?
</p>

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
	<td class="data">{$level}</td>
</tr>
<tr>
	<td class="title">Активация</td>
	<td class="data">{$active}</td>
</tr>
<tr>
	<td class="title">Дата регистрации</td>
	<td class="data">{$regdate}</td>
</tr>
</table>

<p>
<form action='{a href="admin/users/list"}' method="post">
	<input type="hidden" name="id" value="{$instanceId}" />
	{$formcode}
	
	<input class="button" type="submit" name="action[user/delete]" value="Удалить безвозвратно" />
	<input class="button" type="submit" name="cancel" value="Отмена" />
</form>
</p>
