<form name="multy_items" method="post">
	<div style="text-align: right; float: right;">
		<input type="submit" name="multy_items_send" value="Сохранить" class="save">
	</div>
	<br/><br/>
	<table id="dnd-table" class="tablica1">
		<tr>
			<th>Фото</th>
			<th>Название</th>
			<th>Управление</th>
			<?/*<th>Сортировка</th>*/?>
			<th>Цена (<?=$currency?>)</th>
			<th><img src="/admin/templates/img/star.png" title="Новинка" alt="Новинка"></th>
			<th><img src="/admin/templates/img/redstar.png" title="Акция" alt="Акция"></th>
			<th><img src="/admin/templates/img/eye.png" title="Показывать" alt="Показывать"></th>
		</tr>
		<?
		if ($inputs)
			foreach($inputs as $key=>&$inputset){ ?>
		<tr id="<?=$URL_names[$key]?>">
			<td style="text-align: center;"><a href="/admin/<?=$baseurls[$key]?>/<?=$URL_names[$key]?>"><img width=70 src="<?=$picts[$key]['small']?>"></a></td>
			<td>
				<b><a class="tovar" href="/admin/<?=$baseurls[$key]?>/<?=$URL_names[$key]?>"><?=$names[$key]?></a></b>
				<? if ($PLUs[$key]) {?><br/>Код: <?=$PLUs[$key]?><?}?>
				<? if ($barcodes[$key]) {?><br/>Штрихкод: <?=$barcodes[$key]?><?}?>
				<? if ($brands[$key]) {?><br/>Бренд: <?=$brands[$key]['val']?><?}?>
			</td>
			<td style="text-align: center;">
				<a href="/admin/<?=$baseurls[$key]?>/<?=$URL_names[$key]?>" class="edit">Редактировать</a><br/><br/>
				<a href="/admin/<?=$baseurls[$key]?>/items.html?item_del=<?=$URL_names[$key]?>" class="delete">Удалить</a><br/><br/>
				<a href="/admin/<?=$baseurls[$key]?>/new_item?templ=<?=$URL_names[$key]?>" class="add">Дублировать</a>
			</td>
			<?foreach($inputset as &$input){?>
				<td style="text-align: center;"><?=$input['cont']?></td>
			<?} ?>
		</tr>
		<?}?>
		<tr>
			<td colspan=8>
				<input type="submit" name="multy_items_send" value="Сохранить" class="save">
			</td>
		</tr>
	</table>
</form>