
<div class="options-row">
	<a href="<?= href('admin/content/page/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>

	<table class="std-grid tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['title']; ?></th>
		<th><?= $this->sorters['alias']; ?></th>
		<th><?= $this->sorters['type']; ?></th>
		<th><?= $this->sorters['modif_date']; ?></th>
		<th><?= $this->sorters['published']; ?></th>
		<th>Опции</th>
	</tr>
	
	<? foreach($this->collection as $item): ?>
	
	<tr <? if(!$item['published']): ?>class="unpublished"<? endif; ?>>
		<td class="center"><?= $item['id']; ?></td>
		<td><a href="<?=href('admin/content/page/edit/'.$item['id']);?>"><?=$item['title'];?></a></td>
		<td><?= $item['alias']; ?></td>
		<td class="center"><?= $item['type_str']; ?></td>
		<td class="center"><?= $item['modif_date']; ?></td>
		<td class="center" style="height: 18px; width: 120px; text-align: center;">
		
			<div class="tr-hover-opened" style="height: 18px;">
				<form class="inline" action="" method="post">
					<input type="hidden" name="id" value="<?= $item['id']; ?>" />
					<?= FORMCODE; ?>
					<? if($item['published']): ?>
						<input class="button-small" type="submit" name="action[admin/page/unpublish]" value="Скрыть" />
					<? else: ?>
						<input class="button-small" type="submit" name="action[admin/page/publish]" value="Опубликовать" />
					<? endif; ?>
				</form>
			</div>
			
			<div class="tr-hover-closed">
				<? if($item['published']): ?> Опубл. <? else: ?> Скрыт <? endif; ?>
			</div>
		</td>
		<td class="center" style="width: 90px;">
			<div class="tr-hover-visible options">
			
				<a href="<?= href('page/'.$item['alias']); ?>" class="item" title="Просмотреть">
					<img src="images/backend/icon-view.png" alt="Просмотреть" />
				</a>
				
				<a href="<?= href('admin/content/page/edit/'.$item['id']); ?>" class="item" title="Редактировать">
					<img src="images/backend/icon-edit.png" alt="Редактировать" />
				</a>
				
				<a href="<?= href('admin/content/page/delete/'.$item['id']); ?>" class="item" title="Удалить">
					<img src="images/backend/icon-delete.png" alt="Удалить" />
				</a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>
	</table>
	
<? else: ?>

	<p>Сохраненных записей пока нет.</p>
	
<? endif; ?>

<?= $this->pagination; ?>

