	<? if(!is_null($del)) { ?>
	<div id="dbox">
		<p>Удалить пользователя &laquo;<?=$del->ht_name ?>&raquo;?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/admusers" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<a href="/admin/admusers?create" class="add">Добавить</a>
	<p>
	<table class="tablica1">
		<tr>
			<th>Имя</th>
			<th><img src="/resources/adm/img/delete.png" title="Бан" alt="Бан"></th>
			<th><img src="/resources/adm/img/redstar.png" title="Суперадмин" alt="Суперадмин"></th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td><a href="/admin/admusers/<?=$itm->id ?>"><?=$itm->ht_name ?></a></td>
			<td style="text-align: center;">
			<? if ($itm->banned) { ?>
			<img src="/resources/adm/img/delete.png" title="Бан" alt="Бан">
			<? } else { ?>
			&mdash;
			<? } ?>
			</td>
			<td style="text-align: center;">
			<? if ($itm->role=='PL_ROOT') { ?>
			<img src="/resources/adm/img/redstar.png" title="Суперадмин" alt="Суперадмин">
			<? } else { ?>
			&mdash;
			<? } ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/admusers/<?=$itm->id ?>?delete" class="delete">Удалить</a>
			</td>
		</tr>
		<? } ?>
	</table>