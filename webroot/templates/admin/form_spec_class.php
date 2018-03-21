<form method="post">
	<table class="tablica1">
		<tr>
			<th colspan="2" style="text-align: center;"></th>
		</tr>
		<? /*<tr>
			<td><?=$frm['code']['label']?></td>
			<td><?=$frm['code']['input']?></td>
		</tr>*/ ?>
		<tr>
			<td><?=$frm['name']['label']?></td>
			<td><?=$frm['name']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['datatype']['label']?></td>
			<td><?=$frm['datatype']['input']?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/specs/classes/<?=$spec_group->id ?>/<?=$item->id ?>/delete" class="delete">Удалить</a>
				<a href="/admin/specs/classes/<?=$spec_group->id ?>" class="left">Вернуться к списку</a>
			</td>
		</tr>
	</table>
</form>