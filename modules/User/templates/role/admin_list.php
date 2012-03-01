
<div class="options-row">
	<a href="<?= href('admin/users/roles/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="grid tr-highlight">
	<tr>
		<th><?= $this->sorters['title']; ?></th>
		<th><?= $this->sorters['level']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['title']; ?></td>
		<td><?= $item['level']; ?></td>
			
		<td class="center">
			<a href="<?= href('admin/users/roles/edit/'.$item['id']); ?>">редактировать</a>
			<a href="<?= href('admin/users/roles/delete/'.$item['id']); ?>">удалить</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>