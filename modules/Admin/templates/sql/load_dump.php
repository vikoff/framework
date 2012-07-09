
<form action="" method="post" enctype="multipart/form-data" style="text-align: center;">
	<input type="hidden" name="action" value="admin/sql/load-dump" />
	<?= FORMCODE; ?>
	<p>Выберите файл, содержащий дамп базы данных в формате SQL.<p>
	
	<p>
	<input type="file" name="dump" />
	<input type="submit" class="button" value="Загрузить" />

</form>