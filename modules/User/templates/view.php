
<div><a href="<?= href('user/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<table>
<tr>
	<td class="title">id</td>
	<td class="data"><?= $this->id; ?></td>
</tr>
<tr>
	<td class="title">login</td>
	<td class="data"><?= $this->login; ?></td>
</tr>
<tr>
	<td class="title">password</td>
	<td class="data"><?= $this->password; ?></td>
</tr>
<tr>
	<td class="title">surname</td>
	<td class="data"><?= $this->surname; ?></td>
</tr>
<tr>
	<td class="title">name</td>
	<td class="data"><?= $this->name; ?></td>
</tr>
<tr>
	<td class="title">patronymic</td>
	<td class="data"><?= $this->patronymic; ?></td>
</tr>
<tr>
	<td class="title">sex</td>
	<td class="data"><?= $this->sex; ?></td>
</tr>
<tr>
	<td class="title">birthdate</td>
	<td class="data"><?= $this->birthdate; ?></td>
</tr>
<tr>
	<td class="title">country</td>
	<td class="data"><?= $this->country; ?></td>
</tr>
<tr>
	<td class="title">city</td>
	<td class="data"><?= $this->city; ?></td>
</tr>
<tr>
	<td class="title">level</td>
	<td class="data"><?= $this->level; ?></td>
</tr>
<tr>
	<td class="title">active</td>
	<td class="data"><?= $this->active; ?></td>
</tr>
<tr>
	<td class="title">regdate</td>
	<td class="data"><?= $this->regdate; ?></td>
</tr>
</table>
