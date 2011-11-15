
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table>
	<tr>
		<th>id</th>
		<th>Категория</th>
		<th>Имя</th>
		<th>Описание</th>
		<th>Публикация</th>
		<th>Дата</th>
		
		<th>опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['category_id']; ?></td>
		<td><?= $item['item_name']; ?></td>
		<td><?= $item['item_text']; ?></td>
		<td><?= $item['published']; ?></td>
		<td><?= $item['date']; ?></td>
		
		<td style="font-size: 11px;">
			<a href="<?= href('test-item/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>