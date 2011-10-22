
<div class="options-row">
	<a href="<?= href('admin/content/test-item/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="grid wide tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['login']; ?></th>
		<th><?= $this->sorters['password']; ?></th>
		<th><?= $this->sorters['text']; ?></th>
		<th><?= $this->sorters['type']; ?></th>
		<th><?= $this->sorters['is_active']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['login']; ?></td>
		<td><?= $item['password']; ?></td>
		<td><?= $item['text']; ?></td>
		<td><?= $item['type']; ?></td>
		<td><?= $item['is_active']; ?></td>
			
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