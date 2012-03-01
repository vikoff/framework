
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<? if($this->instanceId): ?>
		<div class="info-block">
		
			<h3>[Page_Model INFO]</h3>
			
			<? if($this->type == Page_Model::TYPE_FULL): ?>
				<div class="paragraph">
					<label class="title">URL текущей страницы</label>
					<?= App::href('pages/'.$this->alias); ?>
				</div>
				<div class="paragraph">
					<label class="title">Вставка ссылки в php-код</label>
					<code>&lt;a href="&lt;?= App::href('pages/<?= $this->alias; ?>'); ?&gt;"&gt;<?= $this->title; ?>&lt;/a&gt;</code>
				</div>
				<div class="paragraph">
					<label class="title">Вставка ссылки в smarty-код</label>
					<code>&lt;a href="{a href='pages/<?= $this->alias; ?>'}"&gt;<?= $this->title; ?>&lt;/a&gt;</code>
				</div>
				<div class="paragraph">
					<label class="title">Вставка ссылки в html-код страницы контента</label>
					<code>&lt;a href="{href('pages/<?= $this->alias; ?>')}"&gt;<?= $this->title; ?>&lt;/a&gt;</code>
				</div>
			<? else: ?>
				<div class="paragraph">
					<label class="title">Получение страницы из php-кода</label>
					<span class="description-inline">более производительный, но менее наглядный способ:</span><br />
					<code>Page_Model::load(<?= $this->id; ?>)->getAllFieldsPrepared();</code>
					<br />
					<span class="description-inline">чуть менее производительный, но более наглядный способ:</span><br />
					<code>Page_Model::loadByAlias('<?= $this->alias; ?>')->getAllFieldsPrepared();</code>
					<br />
					<br />
					Оба метода возвращают ассоциативный массив с ключами:<br />
					<code><?= implode(', ', $this->instanceFields); ?></code>
				</div>
			<? endif; ?>
		</div>
	<? endif; ?>
	
	<div class="paragraph">
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
	</div>

	<div class="paragraph">
		<label class="title">Заголовок <span class="required">*</span></label>
		<input type="text" name="title" value="<?= $this->title; ?>" style="width: 300px;" />
	</div>
	
	<div class="paragraph">
		<label class="title">Псевдоним</label>
		<div class="description">
			уникальный идентификатор страницы [a-z, 0-9].<br />
			Если не заполнен, система автоматически создаст псевдоним,<br />
			соответствующий id страницы.
		</div>
		<input type="text" name="alias" value="<?= $this->alias; ?>" style="width: 300px;" />
	</div>
	
	<div class="paragraph">
		<label class="title-inline" <? if(!$this->stored_in_file || !$this->instanceId): ?>
				title="Текст страницы будет храниться в файле вместо БД. Расположение файла можно будет увидеть после сохранения страницы"
			<? endif; ?>> 
			<?= Html_Form::checkbox(array('id' => 'stored-in-file-checkbox', 'name' => 'stored_in_file', 'value' => '1', 'checked' => $this->stored_in_file)); ?>
			Хранить текст страницы в файле
		</label>
		<? if($this->instanceId && $this->stored_in_file): ?>
			<div class="description">
				Файл, содержащий текст страницы, расположен в:
				<code><?= Page_Model::FILE_PAGES_PATH.$this->instanceId.'.php' ?></code>
			</div>
		<? endif; ?>
	</div>
	
	<div id="text-stored-in-db" <? if($this->stored_in_file): ?>style="display: none;"<? endif; ?>>
		<div class="paragraph">
			<label class="title">Текст</label>
			<textarea id="editor1" class="editor" style="width: 98%; height: 400px;" name="body"><?= $this->body; ?></textarea>
		</div>
		
	<button type="button" onclick="Editor.enable('tinymce');">enable tinymce</button>
	<button type="button" onclick="Editor.enable('php');">enable php</button>
	<button type="button" onclick="Editor.disable();">disable</button>
		
		<div class="paragraph">
			<label class="title-inline">Формат:</label>
			<?= HtmlForm::select(
				array('name' => 'format'),
				array('html', 'php'),
				$this->format,
				array('keyEqVal' => true)); ?>
		</div>
	</div>
	
	<div class="paragraph">
		<label class="title">meta description</label>
		<textarea style="width: 300px; height: 60px;" name="meta_description"><?= $this->meta_description; ?></textarea>
	</div>
	
	<div class="paragraph">
		<label class="title">meta keywords</label>
		<textarea style="width: 300px; height: 60px;" name="meta_keywords"><?= $this->meta_keywords; ?></textarea>
	</div>
	
	<div class="paragraph">
		<label class="title">
			<input type="checkbox" name="published" value="1" <? if($this->published !== FALSE): ?>checked="checked"<? endif; ?> />
			Опубликовать
		</label>
	</div>
	
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

<script type="text/javascript">

var Editor = {
	
	/** id текстарий, которые будут превращены в редакторы */
	textareasIds: [
		'editor1',
	],
	
	enabled: false,
	type: null,
	
	/**
	 * ВКЛЮЧИТЬ РЕДАКТОР
	 * @param string type - тип [php|tinymce]
	 */
	enable: function(type){
		
		// выход при попытке создать такой же редактор, как уже созданный
		if(this.enabled && this.type == type)
			return;
		
		// удаление предыдущего редактора (если есть)
		if(this.enabled && this.type != type)
			this.disable();
		
		// применение нового редактора
		for(var i in this.textareasIds)
			this._editors[type].apply( document.getElementById(this.textareasIds[i]) );
			
		this.type = type;
		this.enabled = true;
	},
	
	disable: function(){
		
		if(this.enabled){
			for(var i in this.textareasIds)
				this._editors[this.type].remove( document.getElementById(this.textareasIds[i]) );
			this.type = null;
			this.enabled = false;
		}
	},
	
	_editors: {
		php: {
			_inited: false,
			_jsSrc: [
				'libs/codemirror/lib/codemirror.js',
				'libs/codemirror/mode/php/php.js',
				'libs/codemirror/mode/xml/xml.js',
				'libs/codemirror/mode/javascript/javascript.js',
				'libs/codemirror/mode/css/css.js',
				'libs/codemirror/mode/clike/clike.js',
			],
			_cssHref: [
				'libs/codemirror/lib/codemirror.css',
				'libs/codemirror/theme/default.css',
			],
			_instance: null,
			
			_init: function(){
				for(var i in this._jsSrc)
					Editor._require(this._jsSrc[i]);
				for(var i in this._cssHref)
					Editor._loadCss(this._cssHref[i]);
				this._inited = true;
			},
			apply: function(textarea){
				
				if(!this._inited)
					this._init();
				
				var editorHeight = $(textarea).height();
				textarea.spellcheck = false;
				this._instance = CodeMirror.fromTextArea(textarea, {
					lineNumbers: true,
					matchBrackets: true,
					mode: "php",
					indentWithTabs: true,
					enterMode: "keep",
					tabMode: "shift",
				});
				$('.CodeMirror-scroll').css('height', editorHeight);
			},
			remove: function(textarea){
				if(this._instance){
					textarea.spellcheck = true;
					this._instance.toTextArea();
					this._instance = null;
				}
			}
		},
		
		tinymce: {
			_inited: false,
			_jsSrc: 'libs/tiny_mce/tiny_mce.js',
			_enabledNum: 0,
			
			_init: function(){
				Editor._require(this._jsSrc);
				tinyMCE.baseURL = WWW_ROOT + 'libs/tiny_mce';
				tinyMCE.init(getDefaultTinyMceSettings());
				this._inited = true;
			},
			apply: function(textarea){
				if(!this._inited)
					this._init();
				tinyMCE.execCommand('mceAddControl', false, textarea.id);
			},
			remove: function(textarea){
				tinyMCE.execCommand('mceRemoveControl', false, textarea.id);
			}
		}
	},
	
	_require: function(file){
		$.ajax({
			url: file,
			dataType: 'script',
			async: false
		});
	},
	_loadCss: function(href){
		$('<link rel="stylesheet" type="text/css" href="' + href + '" />').appendTo('head');
	},
}

$(function(){
	
	$('#stored-in-file-checkbox').change(function(){
		$('#text-stored-in-db')[$(this).attr('checked') ? 'slideUp' : 'slideDown']();
	});
	
	Editor.enable('tinymce');
	
	enableFloatingSubmits();
});

</script>
