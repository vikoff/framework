<p>
	<div style="float: right;">
		<a class="button" href="{a href=admin/manage/user-statistics/delete; ?>">Очистить статистику</a>
	</div>
	<div class="clear"> </div>
</p>
<style type="text/css">
table.statistics-nested{
	margin: 0;
}
table.statistics-nested td{
	border: none;
}
</style>

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
		<td rowspan="2"><a href="<?= href('users/view/'.$item['uid']); ?>"><?= $item['uid']; ?></a></td>
		<td style="border-bottom: none;">
			<? if ($item['pages_info']): ?>
				Всего: <?= $item['num_pages']; ?> страниц<br />
			<? else: ?>
				-
			<? endif; ?>
		</td>
		<td><?= $item['user_ip']; ?></td>
		<td><?= $item['referer']; ?></td>
		<td><?= $item['has_js'] ? '<span class="green">✔</span>' : '<span class="red">✘</span>'; ?></td>
		<td><?= $item['has_js'] ? $item['browser_name'].'&nbsp;'.$item['browser_version'] : '-'; ?></td>
		<td><?= $item['has_js'] ? $item['screen_width'].'x'.$item['screen_height'] : '-'; ?></td>
		<td rowspan="2" style="font-size: 11px;">
			<a href="<?= href('admin/manage/user-statistics/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<tr style="border-bottom: solid 2px #BBB;">
		<td colspan="6" style="padding: 0; border-top: none;">
			<table class="small-grid statistics-nested">
			<tr>
				<td class="grey">Первая</td>
				<td><?= $item['pages_info']['first_page']; ?></td>
				<td class="grey"><?= $item['pages_info']['first_page_time']; ?></td>
			</tr>
			<tr>
				<td class="grey">Последняя</td>
				<td><?= $item['pages_info']['last_page']; ?></td>
				<td class="grey"><?= $item['pages_info']['last_page_time']; ?></td>
			</tr>
			</table>
		</td>
	</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>

