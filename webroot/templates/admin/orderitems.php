	<? if(!is_null($del)) { ?>
	<div id="dbox">
		<p>Удалить позицию из заказа?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/orders/<?=$order->num ?>/items" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<table class="tablica1">
		<tr>
			<th colspan=8>Заказ №<?=$order->ht_num ?> от <?=$order->ht_created ?> (<?=$order->ht_c_name ?> <?=$order->ht_c_telephone ?>)</th>
		</tr>
		<tr>
			<th>Код Товара</th>
			<th>Штрихкод</th>
			<th>Производитель</th>
			<th>Название</th>
			<th>Модификация</th>
			<th>Количество</th>
			<th>Цена</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;">
				<?=$itm->ht_p_code ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_p_barcode ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_b_brand_name ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_p_name ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_p_variant ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_qty ?>
			</td>
			<td style="text-align: center;">
				<?=$itm->ht_price ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/orders/<?=$order->num ?>/items/<?=$itm->id ?>/edit" class="edit">Редактировать</a><br/><br/>
				<a href="/admin/orders/<?=$order->num ?>/items/<?=$itm->id ?>/delete" class="delete">Удалить</a>
			</td>
		</tr>
		<? } ?>
		<tr>
			<td colspan=6>&nbsp;</td>
			<td style="text-align: center;"><?=$order->ht_summ ?></td>
			<td style="text-align: center;"><a href="/admin/orders/<?=$order->num ?>/items/add" class="edit">Добавить&nbsp;товар</a></td>
		</tr>
		<tr>
			<td colspan=8 style="text-align: center;">
				<a href="/admin/orders/<?=$order->num ?>/delete" class="delete">Удалить заказ</a>
				<a href="/admin/orders" class="left">Вернуться к заказам</a>
			</td>
		</tr>
	</table>