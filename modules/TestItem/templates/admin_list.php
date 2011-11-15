
<div class="options-row">
	<a href="<?= href('admin/content/test-item/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="grid wide tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['category_id']; ?></th>
		<th><?= $this->sorters['item_name']; ?></th>
		<th><?= $this->sorters['item_text']; ?></th>
		<th><?= $this->sorters['published']; ?></th>
		<th><?= $this->sorters['date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['category_id']; ?></td>
		<td><?= $item['item_name']; ?></td>
		<td><?= $item['item_text']; ?></td>
		<td><?= $item['published']; ?></td>
		<td><?= $item['date']; ?></td>
			
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
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>