	<? if(!is_null($del)) { ?>
	<div id="dbox">
		<p>Удалить позицию из справочника?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/specs/refbooks/<?=$sclass->id ?>" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<a href="/admin/specs/refbooks/<?=$sclass->id ?>/add" class="add">Добавить</a>
	<p>
	<table class="tablica1">
		<tr>
			<th colspan=8>Справочник «<?=$sclass->ht_name ?>»</th>
		</tr>
		<tr>
			<th>Наименование</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;">
				<?=$itm->ht_name ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>/<?=$itm->id ?>/edit" class="edit">Ред.</a>
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>/<?=$itm->id ?>/delete" class="delete">Удал.</a>
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>/<?=$itm->id ?>/move?dir=up" class="up">Вверх</a>
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>/<?=$itm->id ?>/move?dir=down" class="down">Вниз</a>
			</td>
		</tr>
		<? } ?>
		<tr>
			<td colspan=8 style="text-align: center;">
				<a href="/admin/specs/refbooks" class="left">Вернуться к справочникам</a>
				<a href="/admin/specs/classes/<?=$sclass->group_id ?>" class="left">Вернуться к спецификациям</a>
			</td>
		</tr>
	</table>