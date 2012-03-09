
<ul id="submit-box-floating"></ul>

<h2>Редактирование личных данных</h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>	
	
	<table id="edit-form-table" class="grid" style="margin: 1em auto; text-align: left;">
	<tr>
		<th>id</th>
		<td>#<?= $this->id; ?></td>
	</tr>
	<tr>
		<th>логин</th>
		<td><?= $this->login; ?></td>
	</tr>
	<tr>
		<th>роль</th>
		<td><?= $this->role_str; ?></td>
	</tr>
	<tr>
		<th>Фамилия</th>
		<td><?= Html_Form::inputText(array('name' => 'surname', 'value' => $this->surname)); ?></td>
	</tr>
	<tr>
		<th>Имя</th>
		<td><?= Html_Form::inputText(array('name' => 'name', 'value' => $this->name)); ?></td>
	</tr>
	<tr>
		<th>Отчество</th>
		<td><?= Html_Form::inputText(array('name' => 'patronymic', 'value' => $this->patronymic)); ?></td>
	</tr>
	<tr>
		<th>Пол</th>
		<td><?= Html_Form::select(array('name' => 'gender'), array('m' => 'мужчина', 'f' => 'женщина'), $this->gender); ?></td>
	</tr>
	<tr>
		<th>Дата рождения</th>
		<td>
			число <select name="birth[day]"><option value=""></option><?= $this->birth->getDaysListCurActive(); ?></select>
			месяц <select name="birth[month]"><option value=""></option><?= $this->birth->getMonthsListCurActive(); ?></select>
			год <select name="birth[year]"><option value=""></option><?= $this->birth->getYearsListCurActive(); ?></select>
		</td>
	</tr>
	<tr>
		<th>Страна</th>
		<td><?= Html_Form::inputText(array('name' => 'country', 'value' => $this->country)); ?></td>
	</tr>
	<tr>
		<th>Город</th>
		<td><?= Html_Form::inputText(array('name' => 'city', 'value' => $this->city)); ?></td>
	</tr>
	<tr>
		<th>Адрес</th>
		<td><?= Html_Form::textarea(array('name' => 'address', 'value' => $this->address, 'style' => 'width: 300px; height: 50px;')); ?></td>
	</tr>
	<tr>
		<th>Почтовый индекс</th>
		<td><?= Html_Form::inputText(array('name' => 'post_index', 'value' => $this->post_index)); ?></td>
	</tr>
	<tr>
		<th>Телефон</th>
		<td><?= Html_Form::inputText(array('name' => 'tel', 'value' => $this->tel)); ?></td>
	</tr>
	<tr>
		<th>ICQ</th>
		<td><?= Html_Form::inputText(array('name' => 'icq', 'value' => $this->icq)); ?></td>
	</tr>
	<tr>
		<th>Skype</th>
		<td><?= Html_Form::inputText(array('name' => 'skype', 'value' => $this->skype)); ?></td>
	</tr>
	<tr>
		<th>Jabber</th>
		<td><?= Html_Form::inputText(array('name' => 'jabber', 'value' => $this->jabber)); ?></td>
	</tr>
	<tr>
		<th>Страница vkontakte.ru</th>
		<td><?= Html_Form::inputText(array('name' => 'vk_page', 'value' => $this->vk_page)); ?></td>
	</tr>
	<tr>
		<th>Сайт</th>
		<td><?= Html_Form::inputText(array('name' => 'site', 'value' => $this->site)); ?></td>
	</tr>

	<tr>
		<td colspan="2" style="text-align: center;">
			<input class="button" type="submit" name="action[user/profile/edit]" value="Сохранить" />
			<a class="button" href="<?= href(''); ?>">отмена</a>
		</td>
	</tr>
	</table>
</form>

<br />

<h2>Изменение пароля</h2>


<? if ($this->passwordMessage): ?>
	<div id="password-change-message"><?= $this->passwordMessage; ?></div>
	<script type="text/javascript">
	$(function(){
		$(document).scrollTop($('#password-change-message').offset().top);
	});
	</script>
<? endif; ?>

<form action="" method="post">
	<?= FORMCODE; ?>	
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<table class="grid" style="margin: 1em auto; text-align: left;">
		<tr>
			<th>Старый пароль</th>
			<td><input type="password" name="old-password" value="" />
		</tr>
		<tr>
			<th>Новый пароль</th>
			<td><input type="password" name="new-password" value="" />
		</tr>
		<tr>
			<th>Подтверждения пароля</th>
			<td><input type="password" name="new-password-confirm" value="" />
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<input type="submit" name="action[user/profile/change-password]" class="button" value="Применить" />
			</td>
	</table>
</form>
