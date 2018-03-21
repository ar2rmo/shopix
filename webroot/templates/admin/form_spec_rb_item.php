<form method="post">
	<table class="tablica1">
		<tr>
			<th colspan="2" style="text-align: center;"></th>
		</tr>
		<tr>
			<td><?=$frm['name']['label']?></td>
			<td><?=$frm['name']['input']?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>/<?=$item->id ?>/delete" class="delete">Удалить</a>
				<a href="/admin/specs/refbooks/<?=$sclass->id ?>" class="left">Вернуться к справочнику</a>
			</td>
		</tr>
	</table>
</form>