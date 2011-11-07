
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />

	<table class="grid" style="text-align: center; margin: 1em 0;">
		<tr>
			<th>Реальный путь</th>
			<th>Псевдоним</th>
		</tr>
		<tr>
			<td><?= WWW_ROOT; ?><?= Html_Form::inputText(array('name' => 'path', 'value' => $this->path, 'style' => 'width: 150px;')); ?></td>
			<td><?= WWW_ROOT; ?><?= Html_Form::inputText(array('name' => 'alias', 'value' => $this->alias, 'style' => 'width: 200px;')); ?></td>
		</tr>
	</table>

	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[admin/alias/save][admin/config/alias/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<a id="submit-cancel" class="button" href="<?= href('admin/config/alias/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId): ?>		
			<a id="submit-delete" class="button" href="<?= href('admin/config/alias/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
		<? endif; ?>		
	</div>
</form>
