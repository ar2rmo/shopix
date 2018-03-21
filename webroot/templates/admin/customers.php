	<table class="tablica1">
		<tr>
			<th>Телефон</th>
			<th>Заказов</th>
			<th>Сумма</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td><?=$itm->ht_telephone ?></td>
			<td style="text-align: center;"><b><?=$itm->ord_count ?></b></td>
			<td style="text-align: center;"><b><?=$itm->ord_summ ?></b></td>
			<td style="text-align: center;">
				<a href="/admin/orders/custphone-<?=urlencode($itm->telephone)?>" class="edit">Заказы</a><br/><br/>
			</td>
		</tr>
		<? } ?>
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