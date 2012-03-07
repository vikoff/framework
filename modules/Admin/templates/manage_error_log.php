
<?= $this->pagination; ?>

<? if ($this->collection): ?>

	<? foreach ($this->collection as $item): ?>
		<?= $item; ?>
	<? endforeach; ?>
	
<? else: ?>
	Запесей не найдено
<? endif; ?>

<?= $this->pagination; ?>

