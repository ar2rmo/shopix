<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
<form name="settings" method="post">
	<table class="tablica1">
		<tr>
			<th colspan=2>Заказ №<?=$order->ht_num?> от <?=$order->ht_created?></th>
		</tr>
		<tr>
			<td><?=$frm['c_name']['label']?></td>
			<td><?=$frm['c_name']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['c_email']['label']?></td>
			<td><?=$frm['c_email']['input']?><? if ($order->c_email) {?><br/><small><a href="mailto:<?=$order->tx_c_email ?>">Написать</a></small><?}?></td>
		</tr>
		<tr>
			<td><?=$frm['c_telephone']['label']?></td>
			<td><?=$frm['c_telephone']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['c_country']['label']?></td>
			<td><?=$frm['c_country']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['c_city']['label']?></td>
			<td><?=$frm['c_city']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['c_region']['label']?></td>
			<td><?=$frm['c_region']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['c_address']['label']?></td>
			<td><?=$frm['c_address']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['payment_id']['label']?></td>
			<td><?=$frm['payment_id']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['shipping_id']['label']?></td>
			<td><?=$frm['shipping_id']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['status_id']['label']?></td>
			<td><?=$frm['status_id']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['message']['label']?></td>
			<td><?=$frm['message']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['coupon']['label']?></td>
			<td><?=$frm['coupon']['input']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/orders/<?=$order->num ?>/delete" class="delete">Удалить</a>
				<a href="/admin/orders" class="left">Вернуться к заказам</a>
			</td>
		</tr>
	</table>
</form>