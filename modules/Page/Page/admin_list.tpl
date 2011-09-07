
<div class="options-row">
	{a href=admin/content/page/new text="Добавить запись"}
</div>

{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.id}</th>
		<th>{$sorters.title}</th>
		<th>{$sorters.alias}</th>
		<th>{$sorters.modif_date}</th>
		<th>{$sorters.published}</th>
		<th>Опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr {if !$item.published}class="unpublished"{/if}>
		<td>{$item.id}</td>
		<td>{a href=admin/content/page/edit/`$item.id` text=$item.title}</td>
		<td>{$item.alias}</td>
		<td class="center">{$item.modif_date}</td>
		<td class="center">
		
			<div class="tr-hover-opened" style="height: 18px;">
				<form class="inline" action="" method="post">
					<input type="hidden" name="id" value="{$item.id}" />
					{$formcode}
					{if $item.published}
						<input class="button-small" type="submit" name="action[page/unpublish]" value="Скрыть" />
					{else}
						<input class="button-small" type="submit" name="action[page/publish]" value="Опубликовать" />
					{/if}
				</form>
			</div>
			
			<div class="tr-hover-closed" style="height: 18px;">
				{if $item.published}Опубл.{else}Скрыт{/if}
			</div>
		</td>
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="{a href=page/`$item.alias`}" class="item" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="{a href=admin/content/page/edit/`$item.id`}" class="item" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="{a href=admin/content/page/delete/`$item.id`}" class="item" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

