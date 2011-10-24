
<form id="edit-form" action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$id}" />
	<input type="hidden" name="redirect" value="admin/users/list" />
	
	<table class="form-admin-edit-user">
	<tr>
		<th class="title" colspan="2">
			{if !$id}
				Создание нового пользователя
			{else}
				Редактирование данных пользователя {$login} (#{$id})
			{/if}
		</th>
	</tr>
	<tr>
		<th>Логин</th>
		<td>
			{if $action == 'registration'}
				<input id="input-login" type="text" name="login" value="{$login}" />
			{else}
				{$login}
			{/if}
		</td>
	</tr>
	
	{if $action == 'registration'}
	<tr>
		<th>Пароль</th>
		<td>
			<input type="radio" id="password-type-input" name="pass_type" value="input"> <label for="password-type-input">Ввести</label><br />
			<input type="radio" id="password-type-generate" name="pass_type" value="generate"> <label for="password-type-generate">Сгенерировать автоматически</label><br />
			
			<div id="password-input-box" style="display: none;"> 
				<table>
					<tr><td>пароль</td><td><input type="password" name="password" value="{$password}" /></td></tr>
					<tr><td>подтверждение</td><td><input type="password" name="password_confirm" value="{$password_confirm}" /></td></tr>
				</table>
			</div>
			
			<div id="password-generate-box" style="display: none;"> 
				<input type="text" name="password" value="{$password}" readonly="readonly" />
				<input type="hidden" name="password_confirm" value="{$password_confirm}" readonly="readonly" />
				<input type="button" id="generate-pass-btn" name="" value="Сгенерировать" />
			</div>
			
		</td>
	</tr>
	{/if}
	
	<tr>
		<th>Фамилия</th>
		<td><input type="text" name="surname" value="{$surname}" /></td>
	</tr>
	<tr>
		<th>Имя</th>
		<td><input type="text" name="name" value="{$name}" /></td>
	</tr>
	<tr>
		<th>Права</th>
		<td>
			<select name="level">
				{foreach from=$levels key='l' item='t'}
				<option value="{$l}" {if $l == $level}selected="selected"{/if}>{$t}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<th>Действия</th>
		<td class="actions">
		
			<input class="button" type="submit" name="action[user/admin-{if $action == 'edit'}edit{else}create{/if}]" value="Сохранить" />
			<a class="button" href="{a href=admin/users/list}">отмена</a>
			{if $action == 'edit'}<a class="button" href="{a href=admin/users/delete/$id}">удалить</a>{/if}
			
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">

$(function(){
	
	$("#edit-form").validate({{$validation}});
	
	{{if $action == 'registration'}}
	
		// login check
		$('#input-login').rules("add", {
			remote: "user/check-login-unique",
			messages: {
				remote: 'Данный логин уже занят'
			}
		});
	
	{{/if}}

	function generatePass(){
		$.get('user/generate-password', function(response){
			$('#password-generate-box input[name="password"]').val(response);
			$('#password-generate-box input[name="password_confirm"]').val(response);
		});
	}

	$('input[name="pass_type"]').change(function(){
		switch($(this).val()){
			case 'input':
				$('#password-input-box').show();
				$('#password-generate-box').hide();
				$('#password-input-box input').removeAttr('disabled');
				$('#password-generate-box input').attr('disabled', 'disabled');
				break;
			case 'generate':
				// сгенерировать пароль, если его там нет
				if(!$('#password-generate-box input[name="password"]').val())
					generatePass();
				$('#password-input-box').hide();
				$('#password-generate-box').show();
				$('#password-input-box input').attr('disabled', 'disabled');
				$('#password-generate-box input').removeAttr('disabled');
				break;
		}
	});
	$('#generate-pass-btn').click(generatePass);
	
	{{if $pass_type}}
		$('input[name="pass_type"][value="{{$pass_type}}"]').attr('checked', 'checked').change();
	{{/if}}

});

</script>
