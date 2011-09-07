
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить страницу "<strong>{$title}</strong>" (#{$id}) безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="{$instanceId}" />
			{$formcode}
			
			<input class="button" type="submit" name="action[page/delete]" value="Удалить" />
			<a class="button" href="{a href=admin/content/page/list}">Отмена</a>
		</form>
	</div>
	
</div>

