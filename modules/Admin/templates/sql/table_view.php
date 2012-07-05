
<h3>Просмотр таблицы <?= $this->table; ?></h3>

<?= $this->tableData['pagination']; ?>

<table class="grid table-data">
<thead class="thead-floatblock">
<tr>
<? foreach ($this->tableData['structure'] as $col): ?>
	<th>
		<?= $col['name']; ?>
		<div class="col-type"><?= $col['type']; ?></div>
	</th>
<? endforeach; ?>
</tr>
</thead>

<? if ($this->tableData['rows']): ?>
	<? foreach($this->tableData['rows'] as $row): ?>
		<tr>
		<? foreach ($row as $col): ?>
			<td><?= $col; ?></td>
		<? endforeach; ?>
		</tr>
	<? endforeach; ?>
<? else: ?>
<tr><td colspan="<?= count($this->tableData['structure']); ?>">Таблица пуста</td></tr>
<? endif; ?>

</table>

<?= $this->tableData['pagination']; ?>
