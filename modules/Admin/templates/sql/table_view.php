<?

function printCell($text, $tags, $len) {


	switch ($tags) {
		case 'htmlspecialchars': $text = htmlspecialchars($text); break;
		case 'strip': $text = strip_tags($text); break;
	}
	if ($len != 'all') {
		$len = (int)$len;
		$text = mb_strlen($text) > $len ? mb_substr($text, 0, $len).'...' : $text;
	}

	return $text;
}
?>

<? if ($this->tableData): ?>

	<h3>
		Просмотр таблицы <?= $this->table; ?>
		<sup>
			<a href="<?= $this->href('admin/sql/tables/'.$this->table.'/truncate'); ?>" class="simple-text small">очистить</a>
			<span class="simple-text small">|</span>
			<a href="<?= $this->href('admin/sql/tables/'.$this->table.'/delete'); ?>" class="simple-text small">удалить</a>
			<span class="simple-text small">|</span>
			<a href="<?= $this->href('admin/sql/tables/'.$this->table.'/show-create'); ?>" class="simple-text small">show-create-table</a>
		</sup>
	</h3>

	<?= Html_Form::openTag(array('class' => "table-view-options", 'action' => 'admin/sql/tables/'.$this->table)); ?>
		<table class="small-grid no-border">
		<tr>
			<td>Теги:</td>
			<td>
				<?= Html_Form::select(array('name' => 'tags', 'style' => "width: 100%;"), array(
					'html' => 'выводить html',
					'htmlspecialchars' => 'htmlspecialchars',
					'strip' => 'strip_tags',
				), $this->tags); ?>
			</td>
		</tr>
		<tr>
			<td>Текст:</td>
			<td>
				<?= Html_Form::select(array('name' => 'len', 'style' => "width: 100%;"), array(
					'all' => 'весь',
					'100' => '100 символов',
					'500' => '500 символов',
					'1000' => '1000 символов',
					'2000' => '2000 символов',
					'5000' => '5000 символов',
				), $this->len); ?>
			</td>
		</tr>
		<tr><td colspan="2" style="text-align: center;">
			<input type="submit" class="button-small" name="" value="Применить">
			<a href="<?= $this->href('admin/sql/tables/'.$this->table); ?>" class="button-small">Сброс</a>
		</td></tr>
		</table>
	</form>

	<?= $this->tableData['pagination']; ?>

	<div style="clear: both;"></div>

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
				<td><?= printCell($col, $this->tags, $this->len); ?></td>
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