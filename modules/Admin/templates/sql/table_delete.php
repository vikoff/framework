
<div class="paragraph" align="center">
	Удалить таблицу <b><?= $this->table; ?></b>?
</div>
<div class="paragraph" align="center">
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="admin/sql/drop-table" />
		<input type="hidden" name="table" value="<?= $this->table; ?>" />
		<input type="submit" class="button" value="Удалить" />
		<a href="<?= $this->href('admin/sql/tables/'.$this->table); ?>" class="button">Отмена</a>
	</form>
</div>