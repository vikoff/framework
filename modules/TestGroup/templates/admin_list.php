
<div class="options-row">
	<a href="<?= href('admin/content/test-group/new'); ?>">Добавить запись</a>
</div>

<? if($this->collection): ?>

	<?= $this->pagination; ?>

	<table class="grid wide tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['name']; ?></td>
		<td><?= $item['date_str']; ?></td>
			
		<td class="center" style="width: 90px;">
			<div class="tr-hover-visible options">
				<a href="<?= href('test-group/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/test-group/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/test-group/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>

	<?= $this->pagination; ?>	
	
<? else: ?>

	<p>Сохраненных записей пока нет.</p>
	
<? endif; ?>
