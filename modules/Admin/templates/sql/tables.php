
<h3>Список таблиц БД</h3>

<? if ($this->tables): ?>
	<ul class="real-list tables-list" style="font-size: 15px;">
	<? foreach ($this->tables as $t): ?>
		<li><a href="<?= $this->href('admin/sql/tables/'.$t); ?>"><?= $t; ?></a></li>
	<? endforeach; ?>
	</ul>
<? else: ?>
	в базе данных нет таблиц.
<? endif; ?>