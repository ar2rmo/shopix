<form name="settings" method="post">
	<table class="tablica1">
		<tr>
			<th colspan=2 style="text-align: center;">
				Заказ №<?=$order->ht_num ?> от <?=$order->ht_created ?> (<?=$order->ht_c_name ?> <?=$order->ht_c_telephone ?>)<br/>
				Позиция: <?=$item->p_name ?><? if ($item->p_variant) {?> - <?=$item->ht_p_variant?><?}?> (код товара: <?=$item->p_code ?>, штрихкод: <?=$item->p_barcode ?>, бренд: <?=$item->b_brand_name ?>)
			</th>
		</tr>
		<tr>
			<td><?=$frm['qty']['label']?></td>
			<td><?=$frm['qty']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['price']['label']?></td>
			<td><?=$frm['price']['input']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/orders/<?=$order->num ?>/items/<?=$item->id ?>/delete" class="delete">Удалить</a>
				<a href="/admin/orders/<?=$order->num ?>/items" class="left">Вернуться к заказу</a>
			</td>
		</tr>
	</table>
</form>