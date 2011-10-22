
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">Название</label>
		<input type="text" name="name" value="<?= $this->name; ?>" />
	</div>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[admin/menu/save][admin/menu/menu/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[admin/menu/save][admin/menu/menu/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)' ; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?= href('admin/menu/menu/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/menu/menu/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
			<a id="submit-copy" class="button" href="<?= href('admin/menu/menu/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
		<? endif; ?>		
	</div>
</form>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { <?= $this->validation; ?> } );
	enableFloatingSubmits();
});

</script>
