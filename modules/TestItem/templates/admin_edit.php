
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post" enctype="multipart/form-data">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">Группа</label>
		<?= Html_Form::select(array('name' => 'group_id'), array(0 => 'Выберите...') + $this->testGroups, $this->group_id); ?>
	</div>
	<div class="paragraph">
		<label class="title">Название</label>
		<?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?>
	</div>
	<div class="paragraph">
		<label class="title">Изображение</label>
		<?php if ($this->thumb_src) { ?>
			<div style="margin-bottom: 5px;">
				<img src="<?= $this->thumb_src; ?>" align="middle" />
				<form action="" class="inline" onsubmit="return confirm('Уверены?');">
					<?= FORMCODE; ?>
					<input class="button-small" type="submit" name="action[admin/test-item/delete-img]" value="удалить" />
				</form>
			</div>
		<?php } ?>
		<?= Html_Form::input(array('type' => 'file', 'name' => 'img')); ?>
	</div>
	<div class="paragraph">
		<label class="title">Описание</label>
		<?= Html_Form::textarea(array('name' => 'description', 'value' => $this->description)); ?>
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
