
<div><a href="<?= href('admin/root/user-statistics/list'); ?>">Вернуться к списку</a></div>

<h2>Запись #<?= $this->id; ?></h2>

<h3>Общая информация</h3>
<table class="grid" style="text-align: center; margin: 1em 0;">
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
	<td><a href="<?= href('admin/users/view/'.$this->uid); ?>"><?= $this->uid; ?></a></td>
	<td><?= $this->user_ip; ?></td>
	<td><?= $this->referer; ?></td>
	<td><?= $this->has_js ? $this->browser_name.' '.$this->browser_version : '-'; ?></td>
	<td><?= $this->has_js ? '<span class="green">✔</span>' : '<span class="red">✘</span>'; ?></td>
	<td><?= $this->has_js ? $this->screen_width.'x'.$this->screen_height : '-'; ?></td>
</td>
</tbody>
</table>

<h3>Посещенные страницы (всего <?= $this->num_pages; ?>)</h3>
<table class="grid tr-highlight" style="text-align: left; margin: 1em 0;">
<tr>
	<th>Дата</th>
	<th>URL</th>
	<th>Ajax</th>
</tr>
<? foreach ($this->pages as $p): ?>
	<tr>
		<td><?= $p['date']; ?></td>
		<td><?= $p['url']; ?></td>
		<td style="text-align: center;"><?= $p['is_ajax'] ? '<span class="green">✔</span>' : '-'; ?></td>
	</tr>
<? endforeach; ?>
</table>
