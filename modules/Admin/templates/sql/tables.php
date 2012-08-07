
<h3>
	Список таблиц БД
	<? if (count($this->connections) > 1): ?>
		<?= Html_Form::openTag(array('action' => 'admin/sql', 'class' => 'inline')); ?>
		<?= Html_Form::select(array('name' => 'conn', 'onchange' => 'this.form.submit();'),
			$this->connections, $this->conn, array('keyEqVal' => TRUE)); ?>
		<?= Html_Form::closeTag(); ?>
	<? endif; ?>
</h3>

<? if ($this->tables): ?>
	<ul class="real-list tables-list" style="font-size: 15px;">
	<? foreach ($this->tables as $t): ?>
		<li><a href="<?= $this->href('admin/sql/tables/'.$t.'?conn='.$this->conn); ?>"><?= $t; ?></a></li>
	<? endforeach; ?>
	</ul>
<? else: ?>
	в базе данных нет таблиц.
<? endif; ?>