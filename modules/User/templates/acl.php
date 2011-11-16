
<style type="text/css">
	td.checkbox{
		text-align: center;
	}
</style>
<script type="text/javascript" src="http://scripts.vik-off.net/plugins/jquery.simpleCheckbox.js"></script>
<script type="text/javascript">
$(function(){
	$('td.checkbox input[type="checkbox"]').simpleCheckbox();
});
</script>

<? if ($this->resourcesList): ?>
	
	<table class="grid">
	<tr>
		<th>Модуль</th>
		<th>Ресурс</th>
		<? foreach($this->rolesList as $role): ?>
			<th>
				<?= $role['title']; ?><br />
				(<?= $role['level']; ?>)
			</th>
		<? endforeach; ?>
	</tr>
	
	<? foreach($this->resourcesList as $item): ?>
		<tr>
			<td><?= $item['module_title']; ?></td>
			<td><?= $item['resource_title']; ?></td>
			<? foreach($this->rolesList as $role): ?>
				<td class="checkbox">
					<?= Html_Form::checkbox(array('checked' => !empty($this->accessRules[ $item['module'] ][ $item['resource'] ][ $role['id'] ]))); ?>
				</td>
			<? endforeach; ?>
		</tr>
	<? endforeach; ?>
	</table>
<? else: ?>
	Сохраненных записей пока нет.
<? endif; ?>
	