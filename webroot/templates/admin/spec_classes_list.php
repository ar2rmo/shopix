<? if(isset($del)) { ?>
<div id="dbox">
	<p>Удалить параметр <?=$del->ht_name ?>?</p>
	<form name="item" method="post" action="">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/specs/classes/<?=$spec_group->id ?>" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<a href="/admin/specs/classes/<?=$spec_group->id ?>/add" class="add">Добавить</a>
<p>
<table class="tablica1">
	<tr>
		<th colspan="2">«<?=$spec_group->ht_name ?>»</th>
	</tr>
	<tr>
		<th></th>
		<th>Наименование</th>
	</tr>
	<? foreach ($collect as $itm) { ?>
	<tr>
		<td style="text-align: center;">
			<a href="/admin/specs/classes/<?=$spec_group->id ?>/<?=$itm->id ?>/edit" class="edit">Редактировать</a>
			<a href="/admin/specs/classes/<?=$spec_group->id ?>/<?=$itm->id ?>/delete" class="delete">Удалить</a>
			<a href="/admin/specs/classes/<?=$spec_group->id ?>/<?=$itm->id ?>/move?dir=up" class="up">Вверх</a>
			<a href="/admin/specs/classes/<?=$spec_group->id ?>/<?=$itm->id ?>/move?dir=down" class="down">Вниз</a>
		</td>
		<td><b><? if ($itm->is_refbook) { ?><a href="/admin/specs/refbooks/<?=$itm->id ?>"><?=$itm->ht_name ?></a><? } else { ?><?=$itm->ht_name ?><? } ?></b></td>
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
	<tr>
		<td colspan="2" style="text-align: center;">
			<a href="/admin/specs/classes" class="left">Вернуться</a>
		</td>
	</tr>
</table>