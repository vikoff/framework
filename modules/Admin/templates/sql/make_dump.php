
<div class="paragraph" style="text-align: center;">

	<div class="paragraph">
		Создать и сохранить дамп базы данных?
	</div>
	
	<form action="" method="post">
		<?= FORMCODE; ?>
		<div class="paragraph">
			<table class="grid">
				<tr>
					<th>База данных</th>
					<td><?= HtmlForm::select(array('id'=>'input-database', 'name'=>'database'), $this->databases, $this->curDatabase, array('keyEqVal' => TRUE)); ?></td>
				</tr>
				<tr>
					<th>Таблицы</th>
					<td style="text-align: left;">
						<input id="tit-radio-all" type="radio" name="tables-input-type" value="all" checked="checked"> <label for="tit-radio-all">Все</label><br />
						<input id="tit-radio-text" type="radio" name="tables-input-type" value="text"> <label for="tit-radio-text">Ввести вручную</label><br />
						<input id="tit-radio-select" type="radio" name="tables-input-type" value="select"> <label for="tit-radio-select">Выбрать из списка</label><br />
						
						<div class="tit paragraph" id="tit-box-text" style="display: none;">
							<textarea name="tables-text" cols="30" rows="3"></textarea>
						</div>
						<div class="tit paragraph" id="tit-box-select" style="display: none;">
							<select id="tit-input-select" name="tables-select[]" multiple="multiple" size="10"></select>
						</div>
						
					</td>
				</tr>
				<tr>
					<th>Кодировка</th>
					<td><input type="text" name="encoding" value="<?= $this->encoding; ?>" /></td>
				</tr>
			</table>
			
			<script type="text/javascript">
			$(function(){
				
				function fillTablesSelect(db){
					
					var s = $('#tit-input-select');
					if(s.data('db') == db)
						return;
					
					s.data('db', db);
					
					$.post(href('admin/sql/get-tables'), {db: db}, function(response){
						s.empty();
						for(var i = 0, l = response.length; i < l; i++)
							s.append('<option value="' + response[i] + '">' + response[i] + '</option>');
					}, 'json');
				}
				
				$('input[name="tables-input-type"]').change(function(){
					$('.tit').hide();
					var val = $(this).val();
					
					if(val == 'text'){
						$('#tit-box-text').show();
					}
					else if(val == 'select'){
						$('#tit-box-select').show();
						fillTablesSelect($('#input-database').val());
					}
				});
				
				$('#input-database').change(function(){
					if($('#tit-radio-select').attr('checked'))
						fillTablesSelect($(this).val());
				});
			});
			</script>
		</div>

		<div class="paragraph">
			<input type="submit" class="button" name="action[admin/sql/make-dump]" value="Создать" />
		</div>
	</form>
	
</div>
