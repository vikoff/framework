
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Группа: <?= $this->group_id; ?>, 
		Название: <?= $this->name; ?>, 
		Изображение: <?= $this->img; ?>, 
		Описание: <?= $this->description; ?>, 
		Дата создания: <?= $this->date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="<?= href('admin/content/test-item'); ?>" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/test-item/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/test-item'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
