
<div><a href="<?= href('admin/manage/user-statistics/list'); ?>">Вернуться к списку</a></div>

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
<table class="grid tr-highlight user-stat-pages" style=" margin: 1em 0;">
<colgroup span="2" />
<colgroup span="2" align="center" />
<tr>
	<th>Дата</th>
	<th>URL</th>
	<th>is ajax</th>
	<th>is post</th>
	<th>post action</th>
</tr>
<? foreach ($this->pages as $p): ?>
	<tr>
		<td>
			<? if ($p['num_requests'] > 1): ?>
				<div class="grey"><?= $p['first_date']; ?></div>
				<?= $p['last_date']; ?>
			<? else: ?>
				<?= $p['first_date']; ?>
			<? endif; ?>
		</td>
		<td><?= $p['url'].($p['num_requests'] > 1 ? ' <sup>('.$p['num_requests'].')</sup>' : ''); ?></td>
		<?= $p['is_ajax'] ? '<td class="active">+</td>' : '<td>-</td>'; ?>
		<?= $p['is_post'] ? '<td class="active">+</td>' : '<td>-</td>'; ?>
		<td><?= $p['is_post'] ? $p['post_action'] : ''; ?></td>
	</tr>
<? endforeach; ?>
</table>
