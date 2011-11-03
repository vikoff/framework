<p>
	<div style="float: right;">
		<a class="button" href="{a href=admin/root/user-statistics/delete; ?>">Очистить статистику</a>
	</div>
	<div class="clear"> </div>
</p>

<?= $this->pagination; ?>

<? if ($this->collection): ?>

	<table class="grid tr-highlight" style="text-align: center;">
	<tr>
		<th><?= $this->sorters['uid']; ?></th>
		<th>Посещенные страницы</th>
		<th><?= $this->sorters['user_ip']; ?></th>
		<th><?= $this->sorters['referer']; ?></th>
		<th><?= $this->sorters['has_js']; ?></th>
		<th><?= $this->sorters['browser']; ?></th>
		<th><?= $this->sorters['screen_resolution']; ?></th>
		<th>Опции</th>
	</tr>
	
	<? foreach ($this->collection as $item): ?>
	<tr>
		<td><a href=""><?= $item['uid']; ?></a></td>
		<td>
			<? if ($item['pages_info']): ?>
				Всего: <?= $item['num_pages']; ?> страниц<br />
				<div style="color: #999;">
					<div style="margin-top: 2px;">Первая:<br /><span style="color: #000;"><?= $item['pages_info']['first_page']; ?></span></div>
					<div style="font-size: 10px;"><?= $item['pages_info']['first_page_time']; ?></div>
					<div style="margin-top: 2px;">Последняя:<br /><span style="color: #000;"><?= $item['pages_info']['last_page']; ?></span></div>
					<div style="font-size: 10px;"><?= $item['pages_info']['last_page_time']; ?></div>
				</div>
			<? else: ?>
				-
			<? endif; ?>
		</td>
		<td><?= $item['user_ip']; ?></td>
		<td><?= $item['referer']; ?></td>
		<td><?= $item['has_js'] ? '<span class="green">✔</span>' : '<span class="red">✘</span>'; ?></td>
		<td><?= $item['has_js'] ? $item['browser_name'].' '.$item['browser_version'] : '-'; ?></td>
		<td><?= $item['has_js'] ? $item['screen_width'].'x'.$item['screen_height'] : '-'; ?></td>
		<td style="font-size: 11px;">
			<a href="<?= href('admin/root/user-statistics/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>

