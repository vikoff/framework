
<div class="paragraph" align="center">
	Очистить таблицу <b><?= $this->table; ?></b>?
</div>
<div class="paragraph" align="center">
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="admin/sql/truncate-table" />
		<input type="hidden" name="table" value="<?= $this->table; ?>" />
		<input type="submit" class="button" value="Очистить" />
		<a href="<?= $this->href('admin/sql/tables/'.$this->table); ?>" class="button">Отмена</a>
	</form>
</div>