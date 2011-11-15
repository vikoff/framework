
<div><a href="<?= href('test-item/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<div class="paragraph">
	<h3>id</h3>
	<?= $this->id; ?>
</div>
<div class="paragraph">
	<h3>Категория</h3>
	<?= $this->category_id; ?>
</div>
<div class="paragraph">
	<h3>Имя</h3>
	<?= $this->item_name; ?>
</div>
<div class="paragraph">
	<h3>Описание</h3>
	<?= $this->item_text; ?>
</div>
<div class="paragraph">
	<h3>Публикация</h3>
	<?= $this->published; ?>
</div>
<div class="paragraph">
	<h3>Дата</h3>
	<?= $this->date; ?>
</div>
