
<div><a href="<?= href('test-item/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<div class="paragraph">
	<h3>id</h3>
	<?= $this->id; ?>
</div>
<div class="paragraph">
	<h3>Группа</h3>
	<?= $this->group_id; ?>
</div>
<div class="paragraph">
	<h3>Название</h3>
	<?= $this->name; ?>
</div>
<div class="paragraph">
	<h3>Изображение</h3>
	<?= $this->img; ?>
</div>
<div class="paragraph">
	<h3>Описание</h3>
	<?= $this->description; ?>
</div>
<div class="paragraph">
	<h3>Дата создания</h3>
	<?= $this->date; ?>
</div>
