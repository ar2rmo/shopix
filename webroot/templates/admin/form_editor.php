<? if (isset($frm)) { ?>
<div id="dbox">
	<form name="editor" method="post">
		<p><?=$frm['name']['label']?>
		<?=$frm['name']['input']?></p>
		<input type="submit" name="submt" class="save" value="Сохранить">
		<a href="/admin/refbooks/<?=$bk ?>" class="cancel">Отмена</a>
	</form>
</div>
<? }
elseif (isset($del)) { ?>
<div id="dbox">
	<form name="editor" method="post">
		<p>Вы действительно хотите удалить запись?</p>
		<input type="submit" name="del_conf" class="ok" value="Удалить">
		<a href="/admin/refbooks/<?=$bk ?>" class="cancel">Отмена</a>
	</form>
</div>
<? } ?>
<a href="/admin/refbooks/<?=$bk ?>?create" class="add">Добавить</a>
<p>
<table class="tablica1">
    <tr>
        <th>Наименование</th>
        <th colspan=<?=$ord?4:2 ?>>Управление</th>
    </tr>
	<? foreach ($collect as $itm) { ?>
    <tr>
        <td><?=$itm->ht_name ?></td>
        <td><a href="/admin/refbooks/<?=$bk ?>/<?=$itm->id ?>" class="edit">Редактировать</a></td>
        <td><a href="/admin/refbooks/<?=$bk ?>/<?=$itm->id ?>?delete" class="delete">Удалить</a></td>
		<? if ($ord) { ?>
		<td><a href="/admin/refbooks/<?=$bk ?>/<?=$itm->id ?>?up" class="up">Вверх</a></td>
        <td><a href="/admin/refbooks/<?=$bk ?>/<?=$itm->id ?>?down" class="down">Вниз</a></td>
		<? } ?>
    </tr>
	<? } ?>
</table>