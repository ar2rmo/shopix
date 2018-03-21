	<? if(isset($del)) { ?>
	<div id="dbox">
		<p>Удалить заказ №<?=$del->ht_num ?>?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/orders" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<? if (!is_null($filter->from) && !is_null($filter->to)) { ?>
	<div id="info">Заказы с <?=$filter->ht_from ?> по <?=$filter->ht_to ?>.</div>
	<? } elseif (!is_null($filter->from)) { ?>
	<div id="info">Заказы с <?=$filter->ht_from ?>.</div>
	<? } elseif (!is_null($filter->to)) { ?>
	<div id="info">Заказы по <?=$filter->ht_to ?>.</div>
	<? } ?>
	<? if (isset($fcust)) { ?>
	<div id="info">Заказы пользователя <?=$fcust ?>.</div>
	<? } ?>
	<form method="POST" action="/admin/orders/filter">
		<?=$frm['status']['label'] ?>: <?=$frm['status']['input'] ?>,
		дата:
		<?=$frm['from']['label'] ?> <?=$frm['from']['input'] ?>
		<?=$frm['to']['label'] ?> <?=$frm['to']['input'] ?>
		<input type="submit" name="filter" value="OK">
	</form>
	<br/>
	<form method="POST" action="/admin/ordertable" target="_blank">
	<table class="tablica1">
		<tr>
			<th>Номер<br/><a href="/admin/orders/order-num-a">▲</a><a href="/admin/orders/order-num-d">▼<a/></th>
			<th>Дата<br/><a href="/admin/orders/order-date-a">▲</a><a href="/admin/orders/order-date-d">▼<a/></th>
			<th>Заказчик</th>
			<th>Товар</th>
			<th>Сумма<br/><a href="/admin/orders/order-summ-a">▲</a><a href="/admin/orders/order-summ-d">▼<a/></th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;">
				<a href="/admin/orders/<?=$itm->num ?>/edit" class="edit"><?=$itm->num ?></a><br/><br/>
				<a href="/admin/orders/<?=$itm->num ?>/delete" class="delete">Удалить</a><br/><br/>
				<? /*<input type="checkbox" name="order-<?=$itm->num ?>" id="order-<?=$itm->num ?>"> */ ?>
			</td>
			<td style="text-align: center;"><b><?=$itm->created->format('d.m.Y')?></b><br/><?=$itm->created->format('H:i:s')?><br/><br/><b><?=$itm->ht_status?></b></td>
			<td>
				<nobr><b><?=$itm->ht_c_name ?></b></nobr><br/><?=$itm->ht_c_telephone ?>
				<a href="/admin/orders/custphone-<?=urlencode($itm->c_telephone)?>">[*]</a><? if ($itm->c_email) { ?><br/><br/>
				<a href="mailto:<?=$itm->c_email ?>"><?=$itm->ht_c_email ?></a><? } if ($itm->coupon) { ?><br/><br/>
				[<?=$itm->ht_coupon ?>]<? } ?>
			</td>
			<td>
			<? foreach ($itm->col_items as $sitm) { ?>
				<?=$sitm->ht_p_name ?><? if ($sitm->p_variant) {?> - <?=$sitm->ht_p_variant?><?}?> &mdash; <?=$sitm->ht_price ?> <sup><?=$sitm->ht_qty?></sup><br/>
			<? }?>
			<br/><a href="/admin/orders/<?=$itm->num ?>/items" class="edit">Редактировать</a>
			<? if ($itm->c_email) {?><a href="/admin/orders/<?=$itm->num ?>/resend" class="right">Отправить клиенту</a><?}?>
			</td>
			<td style="text-align: center;"><b><?=$itm->ht_summ ?></b></td>
		</tr>
		<? } ?>
		<? /*<tr>
			<td colspan="5">
				С выделенными:
				<input type="submit" name="sel_ship" value="Таблица вывоза" class="add">
				<input type="submit" name="sel_ords" value="Таблица заказа" class="add">
				<input type="submit" name="sel_inv" value="Накладная" class="add">
			</td>
		</tr> */ ?>
		<? if (count($pages)>1) { ?>
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
	</form>