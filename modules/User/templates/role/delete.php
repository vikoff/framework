
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Заголовок: <?= $this->title; ?>, 
		Уровень: <?= $this->level; ?>, 
		Описание: <?= $this->description; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/user/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/user/user'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
