
<ul id="submit-box-floating"></ul>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<table id="edit-form-table">
	<tr>
		<td class="title" colspan="2"><?= $this->pageTitle; ?></td>
	</tr>
	<tr>
		<th>login</th>
		<td><?= Html_Form::inputText(array('name' => 'login', 'value' => $this->login)); ?></td>
	</tr>
	<tr>
		<th>password</th>
		<td><?= Html_Form::inputText(array('name' => 'password', 'value' => $this->password)); ?></td>
	</tr>
	<tr>
		<th>surname</th>
		<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
	</tr>
	<tr>
		<th>name</th>
		<td><?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?></td>
	</tr>
	<tr>
		<th>patronymic</th>
		<td><?= Html_Form::inputText(array('name' => 'patronymic', 'value' => $this->patronymic)); ?></td>
	</tr>
	<tr>
		<th>sex</th>
		<td><?= Html_Form::inputText(array('name' => 'sex', 'value' => $this->sex)); ?></td>
	</tr>
	<tr>
		<th>birthdate</th>
		<td><?= Html_Form::inputText(array('name' => 'birthdate', 'value' => $this->birthdate)); ?></td>
	</tr>
	<tr>
		<th>country</th>
		<td><?= Html_Form::inputText(array('name' => 'country', 'value' => $this->country)); ?></td>
	</tr>
	<tr>
		<th>city</th>
		<td><?= Html_Form::inputText(array('name' => 'city', 'value' => $this->city)); ?></td>
	</tr>
	<tr>
		<th>level</th>
		<td><?= Html_Form::inputText(array('name' => 'level', 'value' => $this->level)); ?></td>
	</tr>
	<tr>
		<th>active</th>
		<td><?= Html_Form::inputText(array('name' => 'active', 'value' => $this->active)); ?></td>
	</tr>
	<tr>
		<th>regdate</th>
		<td><?= Html_Form::inputText(array('name' => 'regdate', 'value' => $this->regdate)); ?></td>
	</tr>

	<tr id="submit-box">
		<td class="actions" colspan="2">
			<input id="submit-save" class="button" type="submit" name="action[admin/user/save][admin/content/user/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
			<input id="submit-apply" class="button" type="submit" name="action[admin/user/save][admin/content/user/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)' ; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
			<a id="submit-cancel" class="button" href="<?= href('admin/content/user/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
			<? if($this->instanceId): ?>			
				<a id="submit-delete" class="button" href="<?= href('admin/content/user/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
				<a id="submit-copy" class="button" href="<?= href('admin/content/user/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
			<? endif; ?>		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">

$(function(){
	$("#edit-form").validate( { <?= $this->validation; ?> } );
	enableFloatingSubmits();
});

</script>
