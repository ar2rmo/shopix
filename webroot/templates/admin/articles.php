	<? if(!is_null($del)) { ?>
	<div id="dbox">
		<p>Удалить статью &laquo;<?=$del->ht_caption ?>&raquo;?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/artedit/<?=$auri ?>" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<a href="/admin/artedit/<?=$auri ?>?create" class="add">Добавить</a>
	<p>
	<table class="tablica1">
		<tr>
			<th>Дата</th>
			<th>Фото</th>
			<th>Заголовок</th>
			<th><img src="/resources/adm/img/eye.png" title="Показывать" alt="Показывать"></th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;"><?=$itm->ht_date ?></td>
			<td style="text-align: center;"><?if($itm->ispict){?><a href="/admin/artedit/<?=$auri ?>/<?=$itm->id ?>"><img src="<?=$itm->pict_uri?>" width="50" height="50" /></a><?}?></td>
			<td><a href="/admin/artedit/<?=$auri ?>/<?=$itm->id ?>"><?=$itm->ht_caption ?></a></td>
			<td style="text-align: center;">
			<? if ($itm->fshow) { ?>
			<img src="/resources/adm/img/eye.png" title="Показывать" alt="Показывать">
			<? } else { ?>
			&mdash;
			<? } ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/artedit/<?=$auri ?>/<?=$itm->id ?>?delete" class="delete">Удалить</a>
			</td>
		</tr>
		<? }
		if (count($pages)>1) { ?>
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
		<? } ?>
	</table>