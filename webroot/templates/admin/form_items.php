<? if (isset($del_prod) && !is_null($del_prod)) { ?>
<div id="dbox">
	<p>Удалить <strong><?=$del_prod->ht_name ?></strong>?</p>
	<form name="fdel" method="post" enctype="multipart/form-data">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/products<?=$cat->uri ?>" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<? if (isset($cat)) { ?>
<form name="multy_items" method="post">
<div style="text-align: right; float: right;">
<h3 style="margin-bottom: 15px;">Выберите раздел для просмотра товаров</h3>
<?=$catsel ?>

<div style="margin:5px">
	<a href="?create" class="add">Добавить товар</a>
	<input type="submit" name="msubmt" value="Сохранить" class="save">
</div>
</div>
<h2><?=$cat->ht_name ?> (<?=$cat->ht_uri ?>)</h2>
<br/><br/><br/>
<table id="dnd-table" class="tablica1">
	<tr>
		<th>Фото</th>
		<th>Название</th>
		<th>Управление</th>
		<th>Цена<? if ($setts->mcurr_sname) { ?> (<?=$setts->ht_mcurr_sname ?>)<?}?></th>
		<th><img src="/resources/adm/img/star.png" title="Новинка" alt="Новинка"></th>
		<th><img src="/resources/adm/img/redstar.png" title="Акция" alt="Акция"></th>
		<th><img src="/resources/adm/img/redstar.png" title="Рекомендуем" alt="Рекомендуем"></th>
		<th><img src="/resources/adm/img/eye.png" title="Показывать" alt="Показывать"></th>
	</tr>
	<? foreach ($collect as $itm) { $frm=$frms[$itm->id]; ?>
	<tr id="p<?=$itm->id ?>">
		<td style="text-align: center;"><a href="/admin/products<?=$itm->uri ?>"><img width=70 src="<?=$itm->pict_uri_small ?>"></a></td>
		<td>
			<b><a class="tovar" href="/admin/products<?=$itm->uri ?>"><?=$itm->ht_name ?></a></b>
			<? if ($itm->code) {?><br/>Код: <?=$itm->ht_code ?><?}?>
			<? if ($itm->barcode) {?><br/>Штрихкод: <?=$itm->ht_barcode ?><?}?>
			<? if ($itm->brand_id) {?><br/>Бренд: <?=$itm->ht_brand_name ?><?}?>
		</td>
		<td style="text-align: center;">
			<a href="/admin/products<?=$itm->uri ?>" class="edit">Редактировать</a><br/><br/>
			<a href="/admin/products<?=$itm->uri ?>?delete" class="delete">Удалить</a><br/><br/>
			<a href="/admin/products<?=$itm->uri ?>?clone" class="add">Дублировать</a>
		</td>
		<td style="text-align: center;">
			<?=$frm['price']['input'] ?>
		</td>
		<td style="text-align: center;">
			<?=$frm['fnew']['input'] ?>
		</td>
		<td style="text-align: center;">
			<?=$frm['fspecial']['input'] ?>
		</td>
		<td style="text-align: center;">
			<?=$frm['frecomend']['input'] ?>
		</td>
		<td style="text-align: center;">
			<?=$frm['fshow']['input'] ?>
		</td>
	</tr>
	<?}?>
	<? if (count($pages)>1) { ?>
		<tr>
			<td colspan="7">
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
	<tr>
		<td colspan=8>
			<a href="?create" class="add">Добавить товар</a>
			<input type="submit" name="msubmt" value="Сохранить" class="save">
		</td>
	</tr>
</table>
</form>
<? } else { ?>
<div style="text-align: right; float: right;">
<h3 style="margin-bottom: 15px;">Выберите раздел для просмотра товаров</h3>
<?=$catsel ?>
</div>
<br/><br/><br/><br/>
<? } ?>