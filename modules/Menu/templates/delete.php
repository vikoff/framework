
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Название: <?= $this->name; ?>, 
		Дата создания: <?= $this->create_date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/menu/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/menu/menu'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
