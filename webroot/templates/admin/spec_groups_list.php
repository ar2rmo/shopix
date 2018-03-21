<? if(isset($del)) { ?>
<div id="dbox">
	<p>Удалить группу <?=$del->ht_name ?>?</p>
	<form name="item" method="post" action="">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/specs/classes" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<a href="/admin/specs/classes/add" class="add">Добавить</a>
<p>
<table class="tablica1">
	<tr>
		<th></th>
		<th>Наименование</th>
	</tr>
	<? foreach ($collect as $itm) { ?>
	<tr>
		<td style="text-align: center;">
			<a href="/admin/specs/classes/<?=$itm->id ?>/edit" class="edit">Редактировать</a>
			<a href="/admin/specs/classes/<?=$itm->id ?>/delete" class="delete">Удалить</a>
			<a href="/admin/specs/classes/<?=$itm->id ?>/move?dir=up" class="up">Вверх</a>
			<a href="/admin/specs/classes/<?=$itm->id ?>/move?dir=down" class="down">Вниз</a>
		</td>
		<td><b><a href="/admin/specs/classes/<?=$itm->id ?>"><?=$itm->ht_name ?></a></b></td>
	</tr>
	<? } ?>
	<? /* if (count($pages)>1) { ?>
	<tr>
		<td colspan="5">
			Страницы:
			<? foreach ($pages as $p=>$pt) { ?>
				<? switch ($pt) { case 'page': ?>
				<a href="?p=<?=$p ?>"><?=$p ?></a>
				<? break; case 'current': ?>
				<span class="current"><?=$p ?></span>
				<? break; case 'stub': ?>
				...
				<? } ?>
			<? } ?>
		</td>
	</tr>
	<? } */ ?>
</table>