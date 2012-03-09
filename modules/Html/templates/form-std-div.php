
<form action="" method="">
	<?= FORMCODE; ?>
	<? foreach($this->_hiddens as $h): ?>
		<input type="hidden" name="<?= $h['name']; ?>" value="<?= $h['value'] ?>" />
	<? endforeach; ?>
	<? foreach($this->_fields as $h): ?>
		<input type="hidden" name="<?= $h['name']; ?>" value="<?= $h['value'] ?>" />
	<? endforeach; ?>
</form>