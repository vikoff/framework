
<div style="text-align: center;">

	<div class="paragraph">
		Хотите удалить роль <b><?= $this->title; ?></b>?
	</div>
	
	<div class="paragraph">
		<form action="<?= href('admin/users/roles'); ?>" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[admin/users/roles/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/users/roles'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
