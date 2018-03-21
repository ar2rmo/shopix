<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
	<table class="tablica1">
		<tr>
			<th colspan=3><?=$prod->ht_fullname ?></th>
		</tr>
		<tr>
			<td colspan=2>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save">*/?>
				<a href="#" class="save" onclick="document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->base->uri ?>?tab=modifications" class="left">Вернуться к товару</a>
			</td>
			<td style="text-align: right">
				<?=$catsel ?>
			</td>
		</tr>
	</table>

	<div class="section">
		<ul class="tabs">
			<li class="current">Основное</li>
			<li>Описание</li>
			<li>Фотографии</li>
		</ul>
		<div class="brk">

	<form name="item" id="item_form" method="post"  enctype="multipart/form-data"><input type="hidden" name="submt" value="submt" />
		
		<div class="box visible">
			<table class="tablica1">
				<tr>
					<td><?=$frm['variant']['label']?></td>
					<td><?=$frm['variant']['input']?><br/><small>Например: Красная</small></td>
					<td rowspan=16 style="text-align: center;" valign="middle">
						<a href="<?=$prod->pict_uri_big ?>"  target="_blank" title="Клик для просмотра большого фото"><img style="max-width: 250px; max-height: 250px;" src="<?=$prod->pict_uri_medium ?>"/></a>
					</td>
				</tr>
				<tr>
					<td><?=$frm['name']['label']?><br/><?=$frm_inh['inh_name']['input']?><?=$frm_inh['inh_name']['label']?></td>
					<td><?=$frm['name']['input']?><br/><small>Например: Nokia 3310</small></td>
				</tr>
				<tr>
					<td><?=$frm['fullname']['label']?><br/><?=$frm_inh['inh_fullname']['input']?><?=$frm_inh['inh_fullname']['label']?></td>
					<td><?=$frm['fullname']['input']?><br/><small>Например: Мобильный телефон Nokia 3310</small></td>
				</tr>
				<tr>
					<td><?=$frm['title']['label']?><br/><?=$frm_inh['inh_title']['input']?><?=$frm_inh['inh_title']['label']?></td>
					<td><?=$frm['title']['input']?><br/><small>Например: Купить Мобильный телефон Nokia 3310</small></td>
				</tr>
				<tr>
					<td><?=$frm['css']['label']?><br/><?=$frm_inh['inh_css']['input']?><?=$frm_inh['inh_css']['label']?></td>
					<td><?=$frm['css']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['brand_id']['label']?><br/><?=$frm_inh['inh_brand']['input']?><?=$frm_inh['inh_brand']['label']?></td>
					<td><?=$frm['brand_id']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['uri_name']['label']?></td>
					<td>
						<?=$frm['uri_name']['input']?>
						<? if ($prod->id) {?><br/><small><a href="/catalog<?=$prod->uri ?>" target="_blank">Ссылка на товар</a></small><?}?>
					</td>
				</tr>
				<tr>
					<td><?=$frm['cid']['label']?></td>
					<td><?=$frm['cid']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['code']['label']?><br/><?=$frm_inh['inh_code']['input']?><?=$frm_inh['inh_code']['label']?></td>
					<td><?=$frm['code']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['barcode']['label']?><br/><?=$frm_inh['inh_barcode']['input']?><?=$frm_inh['inh_barcode']['label']?></td>
					<td><?=$frm['barcode']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['measure']['label']?><br/><?=$frm_inh['inh_measure']['input']?><?=$frm_inh['inh_measure']['label']?></td>
					<td><?=$frm['measure']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['size']['label']?><br/><?=$frm_inh['inh_size']['input']?><?=$frm_inh['inh_size']['label']?></td>
					<td><?=$frm['size']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['mpict']['label']?></td>
					<td><?=$frm['mpict']['input']?><br/><?=$frm['mpict_del']['input']?><?=$frm['mpict_del']['label']?></td>
				</tr>
				<tr>
					<td><?=$frm['price']['label']?>&nbsp;/&nbsp;<?=$frm['oprice']['label']?><br/><?=$frm_inh['inh_price']['input']?><?=$frm_inh['inh_price']['label']?></td>
					<td><?=$frm['price']['input']?>&nbsp;/&nbsp;<?=$frm['oprice']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['avail_id']['label']?>&nbsp;/&nbsp;<?=$frm['avail_num']['label']?></td>
					<td><?=$frm['avail_id']['input']?>&nbsp;/&nbsp;<?=$frm['avail_num']['input']?>&nbsp;/&nbsp;<?=$frm['forsale']['input']?><?=$frm['forsale']['label']?></td>
				</tr>
				<tr>
					<td>
						<?=$frm['fnew']['label']?>&nbsp;/&nbsp;<?=$frm['fshow']['label']?>&nbsp;/&nbsp;<br/><?=$frm['frecomend']['label']?>&nbsp;/&nbsp;<?=$frm['fspecial']['label']?><br/>
						<?=$frm_inh['inh_fnew']['input']?>&nbsp;/&nbsp;--&nbsp;/&nbsp;<?=$frm_inh['inh_frecomend']['input']?>&nbsp;/&nbsp;<?=$frm_inh['inh_fspecial']['input']?>
					</td>
					<td><?=$frm['fnew']['input']?>&nbsp;/&nbsp;<?=$frm['fshow']['input']?>&nbsp;/&nbsp;<?=$frm['frecomend']['input']?>&nbsp;/&nbsp;<?=$frm['fspecial']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['descr_short']['label']?><br/><?=$frm_inh['inh_descr_short']['input']?><?=$frm_inh['inh_descr_short']['label']?></td>
					<td colspan=2><?=$frm['descr_short']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['keywords']['label']?><br/><?=$frm_inh['inh_keywords']['input']?><?=$frm_inh['inh_keywords']['label']?></td>
					<td colspan=2><?=$frm['keywords']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['description']['label']?><br/><?=$frm_inh['inh_description']['input']?><?=$frm_inh['inh_description']['label']?></td>
					<td colspan=2><?=$frm['description']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['tags']['label']?><br/><?=$frm_inh['inh_tags']['input']?><?=$frm_inh['inh_tags']['label']?></td>
					<td colspan=2><?=$frm['tags']['input']?></td>
				</tr>
				<? if ($prod->id) { ?>
				<tr>
					<td>Создано</td>
					<td colspan=2><?=$prod->ht_created ?></td>
				</tr>
				<tr>
					<td>Изменено</td>
					<td colspan=2><?=$prod->ht_modified ?></td>
				</tr>
				<tr>
					<td>Посещения</td>
					<td colspan=2>Всего: <?=$prod->log_all ?>; Сегодня: <?=$prod->log_day ?>.</td>
				</tr>
				<? } ?>
				<tr>
					<td>Критерии<br/><?=$frm_inh['inh_crits']['input']?><?=$frm_inh['inh_crits']['label']?></td>
					<td><b><?=$frm['crits1']['label']?></b><br/><br/>
					<?=$frm['crits1']['input']?></td>
					<td><b><?=$frm['crits2']['label']?></b><br/><br/>
					<?=$frm['crits2']['input']?></td>
				</tr>
			</table>
		</div>

		<div class="box">
			<table class="tablica1">
				<tr>
					<td><b><?=$frm['descr_full']['label']?></b><br/><?=$frm_inh['inh_descr_full']['input']?><?=$frm_inh['inh_descr_full']['label']?><br/>
					<?=$frm['descr_full']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['descr_tech']['label']?></b><br/><?=$frm_inh['inh_descr_tech']['input']?><?=$frm_inh['inh_descr_tech']['label']?><br/>
					<?=$frm['descr_tech']['input']?></td>
				</tr>
			</table>
		</div>

		<div class="box">
			<table class="tablica1">
				<? foreach ($pfrms as $pn=>$pfrm) { ?>
				<tr>
					<td><?=$pfrm['pp_name']['label']?> <?=$pn ?><br/><br/>
					<?=$pfrm['pp_name']['input']?></td>
					<td><?=$pfrm['pp_pict']['input']?><br/><br/>
					<?=$pfrm['pp_pict_del']['input']?><?=$pfrm['pp_pict_del']['label']?></td>
					<td style="text-align: center;">
					<a href="<?=$pcol[$pn]->pict_uri_big ?>"  target="_blank" title="Клик для просмотра большого фото">
					<img src="<?=$pcol[$pn]->pict_uri_small ?>" width="70px"/></a></td>
				</tr>
				<? } ?>
			</table>
		</div>
		
</form>

	</div>

	<table class="tablica1">
		<tr>
			<td colspan=2>
				<a href="#" class="save" onclick="document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save" onclick="document.getElementById('item_form').submit(); return false;">*/?>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->cat->uri ?>" class="left">Вернуться к разделу</a>
			</td>
		</tr>
	</table>

</div>
