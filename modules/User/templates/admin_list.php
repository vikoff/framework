
<div class="options-row">
	<a href="<?= href('admin/users/create'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="grid wide tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['login']; ?></th>
		<th><?= $this->sorters['password']; ?></th>
		<th><?= $this->sorters['surname']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['patronymic']; ?></th>
		<th><?= $this->sorters['sex']; ?></th>
		<th><?= $this->sorters['birthdate']; ?></th>
		<th><?= $this->sorters['country']; ?></th>
		<th><?= $this->sorters['city']; ?></th>
		<th><?= $this->sorters['level']; ?></th>
		<th><?= $this->sorters['active']; ?></th>
		<th><?= $this->sorters['regdate']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['login']; ?></td>
		<td><?= $item['password']; ?></td>
		<td><?= $item['surname']; ?></td>
		<td><?= $item['name']; ?></td>
		<td><?= $item['patronymic']; ?></td>
		<td><?= $item['sex']; ?></td>
		<td><?= $item['birthdate']; ?></td>
		<td><?= $item['country']; ?></td>
		<td><?= $item['city']; ?></td>
		<td><?= $item['level']; ?></td>
		<td><?= $item['active']; ?></td>
		<td><?= $item['regdate']; ?></td>
			
		<td class="center" style="width: 90px;">
			<div class="tr-hover-visible options">
				<a href="<?= href('user/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/users/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/users/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>