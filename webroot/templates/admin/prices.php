<? if(isset($del)) { ?>
<div id="dbox">
	<p>Удалить тип цен <?=$del->ht_name ?>?</p>
	<form name="item" method="post" action="">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/prices" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<a href="/admin/prices?create" class="add">Добавить</a>
<p>
<table class="tablica1">
	<tr>
		<th></th>
		<th>Наименование</th>
		<th>Валюта</th>
		<th>Курс</th>
		<th>Наценка</th>
	</tr>
	<? foreach ($collect as $itm) { ?>
	<tr>
		<td style="text-align: center;">
			<a href="/admin/prices/<?=$itm->id ?>" class="edit">Редактировать</a>
			<a href="/admin/prices/<?=$itm->id ?>?delete" class="delete">Удалить</a>
		</td>
		<td><b><?=$itm->ht_name ?></b></td>
		<td><?=$itm->ht_currency_code ?></td>
		<td><?=$itm->ht_rate ?></td>
		<td><?=$itm->ht_mark ?><? if (!is_null($itm->incr)) { ?>% + <?=$itm->ht_incr ?><? } ?></td>
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