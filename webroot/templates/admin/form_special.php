<? if($del_conf) { ?>
<div id="dbox">
	<p>Удалить акцию &laquo;<?=$name?>&raquo;?</p>
	<form name="item" method="post" action="/admin/aedit/specials">
		<input type="submit" class= "ok" name="delete" value="Да">
		<input type="hidden" name="artn" value="<?=$artn?>">
		<a href="?" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<form name="settings" method="post">
	<? if ($reloc) { ?>
	<a href="/admin/aedit/specials" class="left">Вернуться к акциям</a>
	<? } else { ?>
	<input type="submit" name="article_save" value="Сохранить" class="save"><br/><br/>
	<table class="tablica1">
		<? if (isset($artn)) { ?>
		<tr>
			<th colspan=2>Акция &laquo;<?=$article['caption']?>&raquo; от <?=$article['date']?></th>
		</tr>
		<? } ?>
		<tr>
			<td><?=$date['lable']?></td>
			<td><?=$date['cont']?></td>
		</tr>
		<tr>
			<td><?=$fshow['lable']?></td>
			<td><?=$fshow['cont']?></td>
		</tr>
		<tr>
			<td><?=$caption['lable']?></td>
			<td><?=$caption['cont']?></td>
		</tr>
		<tr>
			<td><?=$image['lable']?></td>
			<td><?=$image['cont']?></td>
		</tr>
		<tr>
			<td><?=$keywords['lable']?></td>
			<td><?=$keywords['cont']?></td>
		</tr>
		<tr>
			<td><?=$short['lable']?></td>
			<td><?=$short['cont']?></td>
		</tr>
		<tr>
			<td><?=$full['lable']?></td>
			<td><?=$full['cont']?></td>
		</tr>
		<tr>
			<td><?=$reference['lable']?></td>
			<td><?=$reference['cont']?></td>
		</tr>
		<tr>
			<td><?=$link['lable']?></td>
			<td><?=$link['cont']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;">
				<input type="submit" name="article_save" value="Сохранить" class="save">
				<? if (isset($artn)) { ?><a href="/admin/aedit/specials/<?=$artn?>?delete" class="delete">Удалить</a><? } ?>
				<a href="/admin/aedit/specials" class="left">Вернуться к статьям</a>
			</td>
		</tr>
	</table>
	<? } ?>
</form>