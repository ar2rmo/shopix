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
			<td><?=$frm['name']['label']?></td>
			<td><?=$frm['name']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['currency_code']['label']?></td>
			<td><?=$frm['currency_code']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['rate']['label']?></td>
			<td><?=$frm['rate']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['mark']['label']?></td>
			<td><?=$frm['mark']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['incr']['label']?></td>
			<td><?=$frm['incr']['input']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/prices/<?=$obj->id ?>?delete" class="delete">Удалить</a>
				<a href="/admin/prices" class="left">Вернуться к списку</a>
			</td>
		</tr>
	</table>
</form>