<p>
	<div style="float: right;">
		<a class="button" href="{a href=admin/root/user-statistics/delete}">Очистить статистику</a>
	</div>
	<div class="clear"> </div>
</p>

{$pagination}

{if $collection}
	<table class="std-grid tr-highlight">
	<tr>
		<th>{$sorters.id}</th>
		<th>{$sorters.uid}</th>
		<th>Посещенные страницы</th>
		<th>{$sorters.user_ip}</th>
		<th>{$sorters.referer}</th>
		<th>{$sorters.has_js}</th>
		<th>{$sorters.browser}</th>
		<th>{$sorters.screen_resolution}</th>
		<th>Опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
		<td>{$item.id}</td>
		<td>{$item.user}</td>
		<td>
			{if $item.has_pages}
				Всего: {$item.num_pages} страниц<br />
				<div style="color: #999;">
					<div style="margin-top: 2px;">Первая: {$item.first_page.date}</div>
					<div style="font-size: 10px;">{$item.first_page.url}</div>
					<div style="margin-top: 2px;">Последняя: {$item.last_page.date}</div>
					<div style="font-size: 10px;">{$item.last_page.url}</div>
				</div>
			{else}
				-
			{/if}
		</td>
		<td>{$item.user_ip|default:'-'}</td>
		<td>{$item.referer|default:'-'}</td>
		<td>{$item.has_js_text}</td>
		<td>{$item.browser}</td>
		<td>{$item.screen_resolution}</td>
		<td style="font-size: 11px;">
			{a href=admin/root/user-statistics/view/`$item.id` text="Подробней"}<br />
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

