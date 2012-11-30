
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Название: <?= $this->name; ?>, 
		Дата создания: <?= $this->date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="<?= href('admin/content/test-group'); ?>" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/test-group/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/test-group'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
