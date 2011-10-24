
{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.id}</th>
		<th>{$sorters.login}</th>
		<th>{$sorters.name}</th>
		<th>{$sorters.level}</th>
		<th>{$sorters.group}</th>
		<th>{$sorters.regdate}</th>
		<th>Опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
		<td>{$item.id}</td>
		<td>{$item.login}</td>
		<td>{$item.fio}</td>
		<td>{$item.level_string}</td>
		<td>{$item.group_string}</td>
		<td>{$item.regdate}</td>
		<td>
			<div class="tr-hover-visible options">
				<a href="{a href=admin/users/view/`$item.id`}" class="item" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="{a href=admin/users/edit/`$item.id`}" class="item" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="{a href=admin/users/delete/`$item.id`}" class="item" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

