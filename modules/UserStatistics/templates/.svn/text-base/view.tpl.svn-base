
<div>{a href=admin/root/user-statistics/list text="Вернуться к списку"}</div>

<h2>Запись #{$id}</h2>

<h3>Общая информация</h3>
<table class="std-grid">
<thead>
<tr>
	<th>Пользователь</th>
	<th>IP</th>
	<th>Referer</th>
	<th>Браузер</th>
	<th>JS</th>
	<th>Разрешение</th>
</tr>
</thead>
<tbody>
<tr>
	<td>{$user}</td>
	<td>{$user_ip}</td>
	<td>{$referer}</td>
	<td>{$browser}</td>
	<td>{$has_js_text}</td>
	<td>{$screen_resolution}</td>
</td>
</tbody>
</table>

<h3>Посещенные страницы</h3>
<table class="std-grid tr-highlight">
<tr>
	<th>Дата</th>
	<th>URL</th>
</tr>
{foreach from=$request_urls item='url'}
	<tr><td>{$url.date}</td><td>{$url.url}</td></tr>
{/foreach}
</table>
