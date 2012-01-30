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

<form action="" method="get">
	Сортировать по:
	<?= Html_Form::select(array('name' => 'sort'), $this->sortArr, getVar($_GET['sort'])); ?>
	<label>
		<?= Html_Form::checkbox(array('name' => 'sort-desc', 'value' => 1, 'checked' => 1)); ?>
		В обратном порядке
	</label>
	<input class="button" type="submit" value="Сортировать" />
</form>

<?= $this->pagination; ?>

<? if ($this->collection): ?>

	<table class="grid tr-highlight" style="text-align: center;">
	<tr>
		<th><?= $this->sorters['uid']; ?></th>
		<th>Посещенные страницы</th>
		<th><?= $this->sorters['user_ip']; ?></th>
		<th><?= $this->sorters['browser']; ?></th>
		<th>Опции</th>
	</tr>
	
	<? foreach ($this->collection as $item): ?>
	<tr>
		<td>
			<a href="<?= href('users/view/'.$item['uid']); ?>"><?= $item['login']; ?></a>
		</td>
		<td>
			<table class="small-grid statistics-nested">
			<? if ($item['referer']): ?>
			<tr>
				<td class="grey">Referer</td>
				<td><?= $item['referer']; ?></td>
			<tr>
			<? endif; ?>
			<tr>
				<td class="grey">Всего</td>
				<td><?= $item['num_pages']; ?> страниц</td>
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
		<td><?= $item['user_ip']; ?></td>
		<td>
			<?= $item['has_js'] ?
				$item['browser_name'].'&nbsp;'.$item['browser_version'].'<br />'
				.$item['screen_width'].'x'.$item['screen_height']
				: '-'; ?>
		</td>
		<td style="font-size: 11px;">
			<a href="<?= href('admin/manage/user-statistics/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>

