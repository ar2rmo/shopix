	<? if($del_conf) { ?>
	<div id="dbox">
		<p>Удалить акцию &laquo;<?=$name?>&raquo;?</p>
		<form name="item" method="post" action="">
			<input type="submit" class= "ok" name="delete" value="Да">
			<input type="hidden" name="artn" value="<?=$artn?>">
			<a href="?" class= "cancel">Нет</a>
		</form>
	</div>
	<?}?>
	<table class="tablica1">
		<tr>
			<th>Дата</th>
			<th>Заголовок</th>
			<th><img src="/admin/templates/img/eye.png" title="Показывать" alt="Показывать"></th>
			<th>&nbsp;</th>
		</tr>
		<? foreach ($articles as $itm) { ?>
		<tr>
			<td style="text-align: center;"><?=$itm['date']?></td>
			<td><a href="/admin/aedit/specials/<?=$itm['ID']?>"><?=$itm['caption']?></a></td>
			<td style="text-align: center;">
			<? if ($itm['fshow']) { ?>
			<img src="/admin/templates/img/eye.png" title="Показывать" alt="Показывать">
			<? } else { ?>
			&mdash;
			<? } ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/aedit/specials?delete=<?=$itm['ID']?>" class="delete">Удалить</a>
			</td>
		</tr>
		<? }
		if ($pages['pages']>1) { ?>
		<tr>
			<td colspan="4">
				Страницы:
				<? for ($i=1;$i<=$pages['pages'];$i++) {
					if ($i==$pages['current']) { ?>
						<span class="current"><?=$i?></span>
					<? } else { ?>
						<a href="?p=<?=$i?>"><?=$i?></a>
					<? }
				} ?>
			</td>
		</tr>
		<? } ?>
	</table>