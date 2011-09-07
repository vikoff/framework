
<ul id="submit-box-floating"></ul>

<h2>{$pageTitle}</h2>

<form id="edit-form" action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$instanceId}" />
	<input type="hidden" id="redirect-input" name="redirect" value="" />

	<p>
		<label class="biglabel">Заголовок <span class="required">*</span></label><br />
		<input type="text" name="title" value="{$title}" style="width: 300px;" />
	</p>
	
	<p>
		<label class="biglabel">Псевдоним</label><br />
		<span class="description">
			уникальный идентификатор страницы [a-z, 0-9].<br />
			Если не заполнен, система автоматически создаст псевдоним,<br />
			соответствующий id страницы.
		</span>
		<input type="text" name="alias" value="{$alias}" style="width: 300px;" />
	</p>
	
	<p>
		<label class="biglabel">Текст</label><br />
		<textarea class="wysiwyg" style="width: 98%; height: 400px;" name="body">{$body}</textarea>
	</p>
	
	<p>
		<label class="biglabel">meta description</label><br />
		<textarea style="width: 300px; height: 60px;" name="meta_description">{$meta_description}</textarea>
	</p>
	
	<p>
		<label class="biglabel">meta keywords</label><br />
		<textarea style="width: 300px; height: 60px;" name="meta_keywords">{$meta_keywords}</textarea>
	</p>
	
	<p>
		<input type="checkbox" id="publish-checkbox" name="published" value="1" {if $published}checked="checked"{/if} />
		<label for="publish-checkbox">Опубликовать</label>
	</p>
	
	{if $hasPermSuperadmin}
	<p>
		<input type="checkbox" id="lock-checkbox" name="locked" value="1" {if $locked}checked="checked"{/if} />
		<label for="lock-checkbox">Запретить удаление</label>
	</p>
	{/if}
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[page/save][admin/content/page/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		{if $instanceId}
		<input id="submit-apply" class="button" type="submit" name="action[page/save]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		{/if}
		<a id="submit-cancel" class="button" href="{a href=admin/content/page/list}" title="Отменить все изменения и вернуться к списку">отмена</a>
		{if $instanceId && !$locked}
		<a id="submit-delete" class="button" href="{a href=admin/content/page/delete/$instanceId}" title="Удалить запись">удалить</a>
		{/if}
		{if $instanceId}
		<a id="submit-copy" class="button" href="{a href=admin/content/page/copy/$instanceId}" title="Сделать копию записи">копировать</a>
		{/if}
	</div>
</form>

<script type="text/javascript" src="includes/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

$(function(){
	
	$("#edit-form").validate( { {{$validation}} } );
	
	tinyMCE.init($.extend(getDefaultTinyMceSettings('{{$WWW_ROOT}}'), {
		mode : 'specific_textareas',
		editor_selector : 'wysiwyg'
	}));
	
	enableFloatingSubmits();
});

</script>
