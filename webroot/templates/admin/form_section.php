<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
	<form name="item" method="post" enctype="multipart/form-data">
		<?if(isset($del_confirm)){?>
			<div id="dbox">
				<p>Удалить <strong><?=$node_name?></strong>?</p>
				<input type="submit" class="ok" name="del_yes" value="Да">
				<input type="submit" class="cancel" name="del_no" value="Нет">
			</div>
		<?}
		elseif (isset($del_ready)){?>Запись удалена </a><?
		}
		elseif (isset($save_ready)){?>
		<a href="<?=$full_URL?>/edit_section.html"  class="left">Вернуться к редактированию Раздела </a>&nbsp;
		<a href="./new_section.html"  class="left">Добвить еще раздел</a><?
		}
		else{?>
				<table class="tablica1">
					<tr>
						<th colspan=3><?=$cat->ht_name ?></th>
					</tr>
					<tr>
						<td colspan=3>
							<input type="submit" name="submt" value="Сохранить" class="save">
							<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
							<a href="/admin/catalog" class="left">Вернуться к структуре каталога</a>
						</td>
					</tr>
					<tr>
						<td><?=$frm['name']['label']?></td>
						<td><?=$frm['name']['input']?></td>
						<td rowspan="6">
							<a href="<?=$cat->pict_uri_big ?>"  target="_blank" title="Клик для просмотра большого фото"><img src="<?=$cat->pict_uri_medium ?>"/></a>
						</td>
					</tr>
					<tr>
						<td><?=$frm['fullname']['label']?></td>
						<td><?=$frm['fullname']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['title']['label']?></td>
						<td><?=$frm['title']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['uri_name']['label']?></td>
						<td><?=$frm['uri_name']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['css']['label']?></td>
						<td><?=$frm['css']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['mpict']['label']?></td>
						<td><?=$frm['mpict']['input']?><br/><?=$frm['mpict_del']['input']?><?=$frm['mpict_del']['label']?></td>
					</tr>
					<tr>
						<td><?=$frm['keywords']['label']?></td>
						<td colspan="2"><?=$frm['keywords']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['description']['label']?></td>
						<td colspan="2"><?=$frm['description']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['text_top']['label']?></td>
						<td colspan="2"><?=$frm['text_top']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['text_bott']['label']?></td>
						<td colspan="2"><?=$frm['text_bott']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['fshow']['label']?></td>
						<td colspan="2"><?=$frm['fshow']['input']?></td>
					</tr>
					<tr>
						<td><?=$frm['parent_id']['label']?></td>
						<td colspan="2"><?=$frm['parent_id']['input']?></td>
					</tr>
					<? if (isset($frm['specs_t'])) { ?>
					<tr>
						<td>Спецификации</td>
						<td colspan="2"><b><?=$frm['specs_t']['label']?></b><br/><br/>
						<?=$frm['specs_t']['input']?></td>
					</tr>
					<? } ?>
					<tr>
						<td colspan=3>
							<input type="submit" name="submt" value="Сохранить" class="save">
							<a href="?delete" class="delete">Удалить</a></td>
						</td>
					</tr>
				</table>
	    <?}?>
		<?if(true){?><a class="left" href="/admin/catalog">Вернуться к структуре каталога</a> <?}?>
	</form>
