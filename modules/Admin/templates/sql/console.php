
<h3 style="text-align: center;">SQL-консоль</h3>

<? if($this->sql_error): ?>
	<p class="userMessageError"><?=$this->sql_error;?></p>
<? endif; ?>

<form action="" method="post">

	<textarea
		id="sql-input"
		name="query"
		class="ctrlentersend"
		spellcheck="false"
		style="width: 98%; height: 150px; font-size: 14px; font-family: monospace;"><?=$this->query;?></textarea>
		
	<div style="text-align: right; margin-top: 10px;">
		<input type="submit" class="button" value="Выполнить запрос" />
	</div>
</form>

<script type="text/javascript">
	$(function(){
		$('#sql-input')
			.focus()
			.keydown(function(e){
				if(e.keyCode == 116){ // F5
					if($(this).val().length && confirm('Выполнить запрос?')){
						$(this.form).submit();
					}else{
						location.href = location.href;
					}
					return false;
				}
			});
	});
</script>

<? if(isset($this->data) && is_array($this->data)): ?>
	<? foreach($this->data as $index => $result): ?>

		<div class="paragraph">
		
			<? if($result['numrows']): ?>
			
				<div style="border: solid 1px #EED; background: #FFFFF6; margin: 15px 0 4px;padding: 2px 6px;">
					<div style="font-size: 11px; color: #777;">Запрос #<?= $index; ?> (<?= round($result['time'], 4); ?> сек.) <?= $result['numrows']; ?> строк</div>
					<div style="white-space: pre;" ><?= $result['sql']; ?></div>
				</div>
				
				<table class="grid" style="margin: 0px;">
				<thead class="thead-floatblock">
					<tr>
					<? foreach($result['result'][0] as $field => $val)
						echo '<th>'.$field.'</th>'; ?>
					</tr>
				</thead>
				<tbody>
				
				<? foreach($result['result'] as $row): ?>
					<tr>
					<? foreach($row as $val): ?>
						<td><?= htmlspecialchars($val); ?></td>
					<? endforeach; ?>
					</tr>
				<? endforeach; ?>
				</tbody>
				</table>
			<? else: ?>
				
				Запрос #<?= $index; ?> вернул пустой результат.
				
			<? endif; ?>
			
		</div>

	<? endforeach; ?>
<? endif; ?>
