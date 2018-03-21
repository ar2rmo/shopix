	<table class="tablica1">
		<tr>
			<th colspan="2">Редактирование справочников для спецификаций</th>
		</tr>
		<? foreach ($collect as $itm) { ?>
		<tr>
			<td style="text-align: center;">
				<?=$itm->ht_name ?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/specs/refbooks/<?=$itm->id ?>" class="edit">Редактировать</a>
			</td>
		</tr>
		<? } ?>
	</table>