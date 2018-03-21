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
<form method="post">
	<table class="tablica1">
		<tr>
			<td>Выберите раздел для просмотра товаров</td>
			<td><?=$catsel?></td>
		</tr>
		<? if (isset($item)) { ?>
		<tr>
			<td><?=$frm['slave']['label']?></td>
			<td><?=$frm['slave']['input']?></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan=2 style="text-align: center;">
				<? if (isset($item)) { ?><input type="submit" name="item_add" value="Добавить" class="save"><? } ?>
				<? /*<a href="/admin/plinks/<?=$prod->id ?>" class="left">Вернуться к товару</a>*/ ?>
				<a href="/admin/products<?=$prod->uri ?>?tab=linked" class="left">Вернуться к товару</a>
			</td>
		</tr>
	</table>
</form>