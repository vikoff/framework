
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">Категория</label>
		<?= Html_Form::select(array('name' => 'category_id'), array('' => 'Выберите...', '1' => '1', '2' => '2'), $this->category_id); ?>
	</div>
	<div class="paragraph">
		<label class="title">Имя</label>
		<?= Html_Form::inputText(array('name' => 'item_name', 'value' => $this->item_name)); ?>
	</div>
	<div class="paragraph">
		<label class="title">Описание</label>
		<?= Html_Form::textarea(array('name' => 'item_text', 'value' => $this->item_text)); ?>
	</div>
	<div class="paragraph">
		<label class="title">Публикация</label>
		<label><?= Html_Form::checkbox(array('name' => 'published', 'value' => '1', 'checked' => $this->published)); ?></label>
	</div>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[admin/test-item/save][admin/content/test-item/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[admin/test-item/save][admin/content/test-item/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)' ; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="<?= href('admin/content/test-item/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/content/test-item/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
			<a id="submit-copy" class="button" href="<?= href('admin/content/test-item/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
		<? endif; ?>		
	</div>
</form>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { <?= $this->validation; ?> } );
	enableFloatingSubmits();
});

</script>
