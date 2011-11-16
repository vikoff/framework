
<style type="text/css">
	td.checkbox{
		text-align: center;
		padding: 0px;
	}
	td.checkbox label{
		display: block;
		width: 100%;
		height: 100%;
	}
</style>

<? if ($this->resourcesList): ?>
	
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="user/save-acl" />
		
		<table class="grid tr-highlight">
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
						<label>
						<?= Html_Form::checkbox(array(
							'name' => 'items['.$item['module'].'|'.$item['resource'].'|'.$role['id'].']',
							'value' => 1,
							'checked' => !empty($this->accessRules[ $item['module'] ][ $item['resource'] ][ $role['id'] ]))); ?>
						</label>
					</td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
		</table>
		
		<div class="paragraph" style="text-align: center;">
			<input type="submit" value="Сохранить" />
		</div>
		
	</form>
<? else: ?>
	Сохраненных записей пока нет.
<? endif; ?>
	