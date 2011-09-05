
<div id="left-menu-list">
	<a href="{a href='admin/static/new'}" {if $leftMenu.activeItem == 'new'}class="active"{/if}>+ Добавить страницу</a>
	{foreach from=$leftMenu.collection item="item"}
		<a href='{a href="admin/static/edit/`$item.id`"}' {if $item.id == $leftMenu.activeItem}class="active"{/if}>{$item.title}</a>
	{foreachelse}
		<p>Нет записей</p>
	{/foreach}
</div>
