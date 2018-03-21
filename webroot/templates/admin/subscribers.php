	<? if(isset($del)) { ?>
	<div id="dbox">
		<p>Удалить подписчика &laquo;<?=$del->ht_email?>&raquo;?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="del_conf" value="Да">
			<a href="/admin/subscribers" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<table class="tablica1">
		<tr>
			<th>Дата</th>
			<th>Адрес</th>
			<th>IP</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;"><b><?=$itm->subscribed->format('d.m.Y')?></b><br/><?=$itm->subscribed->format('H:i:s')?></td>
			<td><a href="mailto:<?=$itm->email?>"><?=$itm->ht_email?></a></td>
			<td><?=$itm->ht_ip?></td>
			<td style="text-align: center;">
				<a href="/admin/subscribers/<?=$itm->id?>?delete" class="delete">Удалить</a>
			</td>
		</tr>
		<? }
		if (count($pages)>1) { ?>
		<tr>
			<td colspan="4">
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