
<div class="options-row">
	<a href="<?= href('admin/config/alias/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="grid tr-highlight" style="text-align: center;">
	<tr>
		<th><?= $this->sorters['path']; ?></th>
		<th><?= $this->sorters['alias']; ?></th>
		<th><?= $this->sorters['is_bound']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['path']; ?></td>
		<td><?= $item['alias']; ?></td>
		<td><?= $item['is_bound'] ? 'да' : '-'; ?></td>
			
		<td class="center" style="width: 160px;">
			<div class="tr-hover-visible">
				<a href="<?= href('admin/config/alias/edit/'.$item['id']); ?>">редактировать</a>
				<a href="<?= href('admin/config/alias/delete/'.$item['id']); ?>">удалить</a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>