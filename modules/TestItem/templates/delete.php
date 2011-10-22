
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		login: <?= $this->login; ?>, 
		password: <?= $this->password; ?>, 
		text: <?= $this->text; ?>, 
		type: <?= $this->type; ?>, 
		is_active: <?= $this->is_active; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/test-item/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/test-item'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
