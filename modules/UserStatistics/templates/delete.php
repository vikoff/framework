<p style="margin-top: 30px;">

	<form action="" method="post" align="center">
		<?=FORMCODE;?>
		
		Удалить статистику посещений сайта старше:
		<select name="expire">
			<option value=""></option>
			<option value="1day">1 дня</option>
			<option value="1week">1 недели</option>
			<option value="1month">1 месяца</option>
			<option value="3month">3 месяцев</option>
			<option value="6month">6 месяцев</option>
			<option value="9month">9 месяцев</option>
			<option value="1year">1 года</option>
		</select>
		
		<p>
			<input class="button" type="submit" name="action[user-statistics/delete]" value="Удалить" />
			<a class="button" href="<?=App::href('admin/root/user-statistics');?>">Отмена</a>
		</p>
	</form>

</p>