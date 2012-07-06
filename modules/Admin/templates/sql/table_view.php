
	
<? if ($this->tableData): ?>

	<h3>
		Просмотр таблицы <?= $this->table; ?>
		<sup><a href="<?= $this->href('admin/sql/tables/'.$this->table.'/delete'); ?>" class="simple-text small">удалить</a></sup>
		<sup><a href="<?= $this->href('admin/sql/tables/'.$this->table.'/show-create'); ?>" class="simple-text small">show-create-table</a></sup>
	</h3>

	<?= $this->tableData['pagination']; ?>

	<table class="grid table-data" style="margin: 0;">
	<thead class="thead-floatblock">
	<tr>
	<? foreach ($this->tableData['structure'] as $col): ?>
		<th>
			<div><?= $this->tableData['sortableLinks'][ $col['name'] ]; ?></div>
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

<? else: ?>
	Таблица <b><?= $this->table; ?></b> не найдена
<? endif; ?>