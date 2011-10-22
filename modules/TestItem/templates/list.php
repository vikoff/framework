
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<? foreach($this->collection as $item): ?>	
	<p>
		<h3>id</h3>
		<?= $item['id']; ?>
		<h3>login</h3>
		<?= $item['login']; ?>
		<h3>password</h3>
		<?= $item['password']; ?>
		<h3>text</h3>
		<?= $item['text']; ?>
		<h3>type</h3>
		<?= $item['type']; ?>
		<h3>is_active</h3>
		<?= $item['is_active']; ?>
		<div><a href="<?= href('test-item/view/'.$item['id']); ?>">Подробней</a></div>
	</p>
	<? endforeach; ?>	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>
<?= $this->pagination; ?>
