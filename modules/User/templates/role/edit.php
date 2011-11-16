
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<div class="paragraph">
		<label class="title">Заголовок</label>
		<?= Html_Form::inputText(array('name' => 'title', 'value' => $this->title)); ?>
	</div>
	<div class="paragraph">
		<label class="title">Уровень</label>
		<?= Html_Form::inputText(array('name' => 'level', 'value' => $this->level, 'style' => 'width: 60px;')); ?>
		<span class="description">Число от 1 до 49</span>
	</div>
	<div class="paragraph">
		<label class="title">Описание</label>
		<?= Html_Form::textarea(array('name' => 'description', 'value' => $this->description, 'style' => 'width: 200px; height: 50px;')); ?>
	</div>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[admin/user/roles/save][admin/users/roles/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<a id="submit-cancel" class="button" href="<?= href('admin/users/roles/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/users/roles/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
		<? endif; ?>		
	</div>
</form>

<script type="text/javascript">

$(function(){
	// enableFloatingSubmits();
});

</script>
