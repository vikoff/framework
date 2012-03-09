
<style type="text/css">
	table.grid td.checkbox{
		text-align: center;
		padding: 0px;
	}
	table.grid td.checkbox label{
		display: block;
		width: 100%;
		height: 100%;
	}
	table.grid td.module{
		font-weight: bold;
		text-align: left;
		font-size: 13px;
		background-color: #F8F8F8;
	}
</style>

<? if ($this->resourcesList): ?>
	
	<form action="" method="post">
		<?= FORMCODE; ?>
		<input type="hidden" name="action" value="user/save-acl" />
		
		<table class="grid tr-highlight">
		<tr>
			<th>Ресурс</th>
			<? foreach($this->rolesList as $role): ?>
				<th>
					<?= $role['title']; ?><br />
					(<?= $role['level']; ?>)
				</th>
			<? endforeach; ?>
		</tr>
		
		<? $module = null; ?>
		<? $numCols = 1 + count($this->rolesList); ?>
		
		<? foreach($this->resourcesList as $item): ?>
				
			<? if ($item['module'] !== $module): ?>
				
				<? $module = $item['module']; ?>
				<tr>
					<td colspan="<?= $numCols; ?>" class="module">
						<span title="модуль <?= $item['module']; ?>"><?= $item['module_title']; ?></span>
					</td>
				</tr>
			<? endif; ?>
			
			<? if ($item['resource'] == 'public' || $item['resource'] == 'root'): ?>
				<? continue; ?>
			<? endif; ?>
			
			<tr>
				<td style="text-align: left;"><span title="ресурс <?= $item['resource']; ?>"><?= $item['resource_title']; ?></span></td>
				
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
	