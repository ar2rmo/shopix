<form method="post">
	<table class="tablica1">
		<tr>
			<td>Выберите раздел для просмотра товаров</td>
			<td><?=$catsel?></td>
		</tr>
		<? if (isset($item)) { ?>
		<tr>
			<td><?=$frm['p_id']['label']?></td>
			<td><?=$frm['p_id']['input']?></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan=2 style="text-align: center;">
				<? if (isset($item)) { ?><input type="submit" name="item_add" value="Добавить" class="save"><? } ?>
				<a href="/admin/orders/<?=$order->num ?>/items" class="left">Вернуться к заказу</a>
			</td>
		</tr>
	</table>
</form>