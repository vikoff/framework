
<p>
	При каждом добавлении нового модуля или изменении конфигурации существующего, необходимо
	загрузить изменившиеся данные в систему.
</p>

<p>
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="admin/modules/read-config" />
		<input type="submit" value="Получить данные о модулях" />
	</form>
</p>