
<h3 style="text-align: center;">SQL-консоль</h3>

<? if($this->sql_error): ?>
	<p class="userMessageError"><?=$this->sql_error;?></p>
<? endif; ?>

<form action="" method="post">

	<textarea
		id="sql-input"
		name="query"
		class="ctrlentersend"
		style="width: 98%; height: 150px; font-size: 14px; font-family: monospace;"><?=$this->query;?></textarea>
		
	<div style="text-align: right; margin-top: 10px;"><input type="submit" name="" value="Выполнить запрос" /></div>
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
		
			<? if(count(getVar($result, array()))): ?>
			
				<div>Запрос #<?= $index; ?></div>
				
				<table class="std-grid narrow" style="margin: 0px;">
				<thead class="thead-floatblock">
					<tr>
					<? foreach($result[0] as $field => $val)
						echo '<th>'.$field.'</th>'; ?>
					</tr>
				</thead>
				<tbody>
				
				<? foreach($result as $row){
					echo '<tr>';
					foreach($row as $val)
						echo '<td>'.$val.'</td>';
					echo '</tr>';
				} ?>
				</tbody>
				</table>
			<? else: ?>
				
				Запрос #<?= $index; ?> вернул пустой результат.
				
			<? endif; ?>
			
		</div>

	<? endforeach; ?>
<? endif; ?>
