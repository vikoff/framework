
<? if ($this->collection): ?>
	
	<?= $this->pagination; ?>

	<? foreach ($this->collection as $item): ?>
		<?= $item; ?>
	<? endforeach; ?>
	
	<?= $this->pagination; ?>
	
<? else: ?>
	Запесей не найдено
<? endif; ?>
