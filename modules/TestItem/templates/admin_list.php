
<div class="options-row">
	<a href="<?= href('admin/content/test-item/new'); ?>">Добавить запись</a>
</div>

<? if($this->collection): ?>

	<?= $this->pagination; ?>

	<table class="grid wide tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['group_id']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['img']; ?></th>
		<th><?= $this->sorters['description']; ?></th>
		<th><?= $this->sorters['date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['group_id']; ?></td>
		<td><?= $item['name']; ?></td>
		<td><?= $item['img']; ?></td>
		<td><?= $item['description']; ?></td>
		<td><?= $item['date_str']; ?></td>
			
		<td class="center" style="width: 90px;">
			<div class="tr-hover-visible options">
				<a href="<?= href('test-item/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/test-item/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/test-item/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>

	<?= $this->pagination; ?>	
	
<? else: ?>

	<p>Сохраненных записей пока нет.</p>
	
<? endif; ?>
