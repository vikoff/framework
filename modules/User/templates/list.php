
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table>
	<tr>
		<th>id</th>
		<th>login</th>
		<th>password</th>
		<th>surname</th>
		<th>name</th>
		<th>patronymic</th>
		<th>sex</th>
		<th>birthdate</th>
		<th>country</th>
		<th>city</th>
		<th>level</th>
		<th>active</th>
		<th>regdate</th>
		
		<th>опции</th>
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
		
		<td style="font-size: 11px;">
			<a href="<?= href('user/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>