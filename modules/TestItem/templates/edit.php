
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">login</label>
		<?= Html_Form::inputText(array('name' => 'login', 'value' => $this->login)); ?>
	</div>
	<div class="paragraph">
		<label class="title">password</label>
		<?= Html_Form::input(array('type' => 'password', 'name' => 'password', 'value' => $this->password)); ?>
	</div>
	<div class="paragraph">
		<label class="title">text</label>
		<?= Html_Form::textarea(array('name' => 'text', 'value' => $this->text)); ?>
	</div>
	<div class="paragraph">
		<label class="title">type</label>
		<?= Html_Form::select(array('name' => 'type'), array('' => 'Выберите...', '1' => 'первый'), $this->type); ?>
	</div>
	<div class="paragraph">
		<label class="title">is_active</label>
		<label><?= Html_Form::checkbox(array('name' => 'is_active', 'value' => '1', 'checked' => $this->is_active)); ?></label>
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
