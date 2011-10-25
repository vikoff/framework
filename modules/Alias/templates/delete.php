
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Псевдоним: <?= $this->alias; ?>, 
		Путь: <?= $this->path; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/alias/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/config/alias'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
