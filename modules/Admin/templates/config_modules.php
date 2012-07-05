
<p>
	При каждом добавлении нового модуля или изменении конфигурации существующего, необходимо
	загрузить изменившиеся данные в систему.
</p>

<? if($this->log): ?>
	<b>Лог выполнения</b><br />
	<div style="background-color: #FFFEF2; border: solid 1px #E6E5DB; padding: 1em;">
		<?= implode( '<br />', array_merge($this->log['error'], $this->log['info']) ); ?>
	</div>
<? endif; ?>

<p>
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="admin/read-modules-config" />
		<input type="submit" class="button" value="Получить данные о модулях" />
	</form>
</p>