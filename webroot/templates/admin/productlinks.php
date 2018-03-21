<? /* #XPD= */ ?>
<table class="tablica1">
	<!--<tr>
		<th colspan="2"></th>
	</tr>-->
	<tr>
		<th>Фото</th>
		<th>Название</th>
	</tr>
	<tr>
		<td style="text-align: center;"><a href="/admin/products<?=$prod->uri ?>"><img width=70 src="<?=$prod->pict_uri_small ?>"></a></td>
		<td>
			<b><a class="tovar" href="/admin/products<?=$prod->uri ?>"><?=$prod->ht_name ?></a></b>
			<? if ($prod->code) {?><br/>Код: <?=$prod->ht_code ?><?}?>
			<? if ($prod->barcode) {?><br/>Штрихкод: <?=$prod->ht_barcode ?><?}?>
			<? if ($prod->brand_id) {?><br/>Бренд: <?=$prod->ht_brand_name ?><?}?>
		</td>
	</tr>
</table>
<br/>
<table class="tablica1">
	<tr>
		<th>Фото</th>
		<th>Название</th>
		<th>Управление</th>
	</tr>
	<? foreach ($collect as $itm) { ?>
	<tr id="p<?=$itm->id ?>">
		<td style="text-align: center;"><a href="/admin/products<?=$itm->uri ?>"><img width=70 src="<?=$itm->pict_uri_small ?>"></a></td>
		<td>
			<b><a class="tovar" href="/admin/products<?=$itm->uri ?>"><?=$itm->ht_name ?></a></b>
			<? if ($itm->code) {?><br/>Код: <?=$itm->ht_code ?><?}?>
			<? if ($itm->barcode) {?><br/>Штрихкод: <?=$itm->ht_barcode ?><?}?>
			<? if ($itm->brand_id) {?><br/>Бренд: <?=$itm->ht_brand_name ?><?}?>
		</td>
		<td style="text-align: center;">
			<a href="/admin/plinks/<?=$prod->id ?>/<?=$itm->id ?>/del" class="delete">Отвязать</a><br/><br/>
		</td>
	</tr>
	<?}?>
	<? /* if (count($pages)>1) { ?>
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
	<? } */ ?>
	<tr>
		<td colspan=3>
			<a href="/admin/plinks/<?=$prod->id ?>/add" class="add">Привязать товар</a>
		</td>
	</tr>
</table>