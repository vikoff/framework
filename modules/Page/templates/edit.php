
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<?
/*
Html_Form::create('std-div')
	->method('post')
	->fields(array(
		'id' => array('type' => 'hidden'),
		'title' => array(
			'type' => 'text',
			'label' => 'Заголовок',
			'required' => true,
		    'attrs' => array('style' => 'width: 300px;')),
		'alias' => array(
			'type' => 'text',
			'label' => 'Псевдоним',
			'attrs' => array('style' => 'width: 300px;'),
		    'description' => '
				уникальный идентификатор страницы [a-z, 0-9].<br />
				Если не заполнен, система автоматически создаст псевдоним,<br />
				соответствующий id страницы.'),
		'body' => array(
			'type' => 'textarea',
			'label' => 'Текст',
			'wysiwyg' => true,
			'attrs' => array('style' => 'width: 98%; height: 400px;'),
		)
	))
	->values(array(
		'id' => $this->instanceId,
		'title' => $this->title,
		'body' => $this->body,
	))
	->render();
	*/	
?>
<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<? if($this->instanceId): ?>
		<div class="info-block">
		
			<h3>[Page_Model INFO]</h3>
			
			<? if($this->type == Page_Model::TYPE_FULL): ?>
				<p>
					<label class="title">URL текущей страницы</label>
					<?= App::href('pages/'.$this->alias); ?>
				</p>
				<p>
					<label class="title">Вставка ссылки в php-код</label>
					<code>&lt;a href="&lt;?= App::href('pages/<?= $this->alias; ?>'); ?&gt;"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
				<p>
					<label class="title">Вставка ссылки в smarty-код</label>
					<code>&lt;a href="{a href='pages/<?= $this->alias; ?>'}"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
				<p>
					<label class="title">Вставка ссылки в html-код страницы контента</label>
					<code>&lt;a href="{href('pages/<?= $this->alias; ?>')}"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
			<? else: ?>
				<p>
					<label class="title">Получение страницы из php-кода</label>
					<span class="description-inline">более производительный, но менее наглядный способ:</span><br />
					<code>Page_Model::load(<?= $this->id; ?>)->getAllFieldsPrepared();</code>
					<br />
					<span class="description-inline">чуть менее производительный, но более наглядный способ:</span><br />
					<code>Page_Model::loadByAlias('<?= $this->alias; ?>')->getAllFieldsPrepared();</code>
					<br />
					<br />
					Оба метода возвращают ассоциативный массив с ключами:<br />
					<code><?= implode(', ', $instanceFields); ?></code>
				</p>
			<? endif; ?>
		</div>
	<? endif; ?>
	
	<p>
		<label class="title">Тип страницы <span class="required">*</span></label>
		
		<label>
			<input type="radio" name="type" value="<?= Page_Model::TYPE_FULL ?>"
				<? if($this->type == Page_Model::TYPE_FULL || empty($this->type)): ?>checked="checked"<? endif; ?> />
			<?= Page_Model::getPageTypeTitle(Page_Model::TYPE_FULL); ?>
			</label>
		<span class="description-inline"> - такая страница отображается как основной контент.</span>
		<br />
		<label>
		<input type="radio" name="type" value="<?= Page_Model::TYPE_CHUNK ?>"
				<? if($this->type == Page_Model::TYPE_CHUNK): ?>checked="checked"<? endif; ?> />
			<?= Page_Model::getPageTypeTitle(Page_Model::TYPE_CHUNK); ?>
		</label>
		<span class="description-inline">
		- такая страница не отображается как основной контент, но может быть выведена
		как текстовый фрагмент в произвольном месте.
		</span>
		<br />
	</p>

	<p>
		<label class="title">Заголовок <span class="required">*</span></label>
		<input type="text" name="title" value="<?= $this->title; ?>" style="width: 300px;" />
	</p>
	
	<p>
		<label class="title">Псевдоним</label>
		<span class="description">
			уникальный идентификатор страницы [a-z, 0-9].<br />
			Если не заполнен, система автоматически создаст псевдоним,<br />
			соответствующий id страницы.
		</span>
		<input type="text" name="alias" value="<?= $this->alias; ?>" style="width: 300px;" />
	</p>
	
	<p>
		<label class="title-inline"> <input id="stored-in-file-checkbox" type="checkbox" name="stored_in_file" value="1" /> Хранить текст страницы в файле</label>
	</p>
	
	<div id="text-stored-in-db" <? if($this->stored_in_file): ?>style="display: none;"<? endif; ?>>
		<p>
			<label class="title">Текст</label>
			<textarea class="wysiwyg" style="width: 98%; height: 400px;" name="body"><?= $this->body; ?></textarea>
		</p>
		
		<p>
			<label class="title-inline">Формат:</label>
			<?= HtmlForm::select(
				array('name' => 'format'),
				array('html', 'php'),
				$this->format,
				array('keyEqVal' => true)); ?>
		</p>
	</div>
	
	<p>
		<label class="title">meta description</label>
		<textarea style="width: 300px; height: 60px;" name="meta_description"><?= $this->meta_description; ?></textarea>
	</p>
	
	<p>
		<label class="title">meta keywords</label>
		<textarea style="width: 300px; height: 60px;" name="meta_keywords"><?= $this->meta_keywords; ?></textarea>
	</p>
	
	<p>
		<label class="title">
			<input type="checkbox" name="published" value="1" <? if($this->published !== FALSE): ?>checked="checked"<? endif; ?> />
			Опубликовать
		</label>
	</p>
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[admin/page/<? if($this->instanceId): ?>save<? else: ?>create<? endif; ?>][admin/content/page]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		
		<? if($this->instanceId): ?>
			<input id="submit-apply" class="button" type="submit" name="action[admin/page/save]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<? endif; ?>
		
		<a id="submit-cancel" class="button" href="<?= href('admin/content/page/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		
		<? if($this->instanceId): ?>
			<a id="submit-delete" class="button" href="<?= href('admin/content/page/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
		<? endif; ?>
		
		<? if($this->instanceId): ?>
			<a id="submit-copy" class="button" href="<?= href('admin/content/page/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
		<? endif; ?>
		
	</div>
</form>

<script type="text/javascript" src="libs/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

$(function(){
	
	$('#stored-in-file-checkbox').change(function(){
		$('#text-stored-in-db')[$(this).attr('checked') ? 'slideUp' : 'slideDown']();
	});
	
	tinyMCE.init($.extend(getDefaultTinyMceSettings('<?= WWW_ROOT; ?>'), {
		mode : 'specific_textareas',
		editor_selector : 'wysiwyg'
	}));
	
	enableFloatingSubmits();
});

</script>
