<script type='text/javascript'>
function CKupdate() {
    for (instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
}
</script>

<? if (isset($del_var) && !is_null($del_var)) { ?>
<div id="dbox">
	<p>Удалить модификацию <strong><?=$del_var->ht_variant ?></strong>?</p>
	<form name="fdel" method="post" enctype="multipart/form-data">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/products<?=$prod->uri ?>?tab=modifications" class= "cancel">Нет</a>
	</form>
</div>
<?}?>

<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>

<? if (isset($ih_errs)) { ?>
<div id="error">
<? foreach ($ih_errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
	<table class="tablica1">
		<tr>
			<th colspan=3><?=$prod->fullname?$prod->ht_fullname:$prod->ht_name?></th>
		</tr>
		<tr>
			<td colspan=2>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save">*/?>
				<a href="#" class="save" onclick="CKupdate(); document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->cat->uri ?>" class="left">Вернуться к разделу</a>
			</td>
			<td style="text-align: right">
				<?=$catsel ?>
			</td>
		</tr>
	</table>

	<div class="section">
		<ul class="tabs">
			<li<? if (!isset($acttab)) {?> class="current"<?}?>>Основное</li>
			<li>Описание</li>
			<li>Дополнительно</li>
			<li<? if (isset($acttab) && ($acttab=='xp')) {?> class="current"<?}?>>Связанные товары</li><? /* #XPD */ ?>
			<? if (!is_null($frm_scs)) { ?><li<? if (isset($acttab) && ($acttab=='specs')) {?> class="current"<?}?>>Спецификации</li><?}?>
			<li>Фотографии</li>
			<li<? if (isset($acttab) && ($acttab=='mods')) {?> class="current"<?}?>>Модификации</li>
		</ul>
		<div class="brk">

	<form name="item" id="item_form" method="post"  enctype="multipart/form-data"><input type="hidden" name="submt" value="submt" />
		
		<div class="box<? if (!isset($acttab)) {?> visible<?}?>">
			<table class="tablica1">
				<tr>
					<td><?=$frm['name']['label']?></td>
					<td><?=$frm['name']['input']?><br/><small>Например: Nokia 3310</small></td>
					<td rowspan=15 style="text-align: center;" valign="middle">
						<a href="<?=$prod->pict_uri_big ?>"  target="_blank" title="Клик для просмотра большого фото"><img style="max-width: 250px; max-height: 250px;" src="<?=$prod->pict_uri_medium ?>"/></a>
					</td>
				</tr>
				<tr>
					<td><?=$frm['fullname']['label']?></td>
					<td><?=$frm['fullname']['input']?><br/><small>Например: Мобильный телефон Nokia 3310</small></td>
				</tr>
				<tr>
					<td><?=$frm['title']['label']?></td>
					<td><?=$frm['title']['input']?><br/><small>Например: Купить Мобильный телефон Nokia 3310</small></td>
				</tr>
				<tr>
					<td><?=$frm['css']['label']?></td>
					<td><?=$frm['css']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['brand_id']['label']?></td>
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
					<td><?=$frm['code']['label']?></td>
					<td><?=$frm['code']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['barcode']['label']?></td>
					<td><?=$frm['barcode']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['measure']['label']?></td>
					<td><?=$frm['measure']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['size']['label']?></td>
					<td><?=$frm['size']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['mpict']['label']?></td>
					<td><?=$frm['mpict']['input']?><br/><?=$frm['mpict_del']['input']?><?=$frm['mpict_del']['label']?></td>
				</tr>
				<tr>
					<td><? if (isset($frm['price_type'])) { ?><?=$frm['price_type']['label']?>&nbsp;/&nbsp;<? } ?><?=$frm['price']['label']?>&nbsp;/&nbsp;<?=$frm['oprice']['label']?></td>
					<td><? if (isset($frm['price_type'])) { ?><?=$frm['price_type']['input']?>&nbsp;/&nbsp;<? } ?><?=$frm['price']['input']?>&nbsp;/&nbsp;<?=$frm['oprice']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['avail_id']['label']?>&nbsp;/&nbsp;<?=$frm['avail_num']['label']?></td>
					<td><?=$frm['avail_id']['input']?>&nbsp;/&nbsp;<?=$frm['avail_num']['input']?>&nbsp;/&nbsp;<?=$frm['forsale']['input']?><?=$frm['forsale']['label']?></td>
				</tr>
				<tr>
					<td><?=$frm['fnew']['label']?>&nbsp;/&nbsp;<?=$frm['fshow']['label']?>&nbsp;/&nbsp;<br/><?=$frm['frecomend']['label']?>&nbsp;/&nbsp;<?=$frm['fspecial']['label']?></td>
					<td><?=$frm['fnew']['input']?>&nbsp;/&nbsp;<?=$frm['fshow']['input']?>&nbsp;/&nbsp;<?=$frm['frecomend']['input']?>&nbsp;/&nbsp;<?=$frm['fspecial']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['descr_short']['label']?></td>
					<td colspan=2><?=$frm['descr_short']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['keywords']['label']?></td>
					<td colspan=2><?=$frm['keywords']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['description']['label']?></td>
					<td colspan=2><?=$frm['description']['input']?></td>
				</tr>
				<tr>
					<td><?=$frm['tags']['label']?></td>
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
					<td rowspan="2">Критерии</td>
					<td><b><?=$frm['crits1']['label']?></b><br/><br/>
					<?=$frm['crits1']['input']?></td>
					<td><b><?=$frm['crits2']['label']?></b><br/><br/>
					<?=$frm['crits2']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['crits3']['label']?></b><br/><br/>
					<?=$frm['crits3']['input']?></td>
					<td></td>
				</tr>
				<? if (isset($frm['specs_inh'])) { ?>
				<tr>
					<td>Спецификации</td>
					<td colspan="2"><b><?=$frm['specs_inh']['label']?></b><br/><br/>
					<?=$frm['specs_inh']['input']?></td>
				</tr>
				<? } ?>
			</table>
		</div>

		<div class="box">
			<table class="tablica1">
				<tr>
					<td><b><?=$frm['descr_full']['label']?></b><br/><br/>
					<?=$frm['descr_full']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['descr_tech']['label']?></b><br/><br/>
					<?=$frm['descr_tech']['input']?></td>
				</tr>
			</table>
		</div>
		
		<div class="box">
			<table class="tablica1">
				<tr>
					<td><b><?=$frm['extra1']['label']?></b><br/><br/>
					<?=$frm['extra1']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['extra2']['label']?></b><br/><br/>
					<?=$frm['extra2']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['extra3']['label']?></b><br/><br/>
					<?=$frm['extra3']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['extra4']['label']?></b><br/><br/>
					<?=$frm['extra4']['input']?></td>
				</tr>
				<tr>
					<td><b><?=$frm['extra5']['label']?></b><br/><br/>
					<?=$frm['extra5']['input']?></td>
				</tr>
			</table>
		</div>
		
		<? /* #XPD */ ?>
		<div class="box<? if (isset($acttab) && ($acttab=='xp')) {?> visible<?}?>">
			<table class="tablica1">
				<tr>
					<th>Фото</th>
					<th>Название</th>
					<th>Управление</th>
				</tr>
				<? foreach ($xprods as $itm) { ?>
				<tr id="p<?=$itm->id ?>">
					<td style="text-align: center;"><a href="/admin/products<?=$itm->uri ?>"><img width=70 src="<?=$itm->pict_uri_small ?>"></a></td>
					<td>
						<b><a class="tovar" href="/admin/products<?=$itm->uri ?>"><?=$itm->ht_name ?></a></b>
						<? if ($itm->code) {?><br/>Код: <?=$itm->ht_code ?><?}?>
						<? if ($itm->barcode) {?><br/>Штрихкод: <?=$itm->ht_barcode ?><?}?>
						<? if ($itm->brand_id) {?><br/>Бренд: <?=$itm->ht_brand_name ?><?}?>
					</td>
					<td style="text-align: center;">
						<a href="/admin/plinks/<?=$prod->id ?>/<?=$itm->id ?>/del" class="delete">Отвязать</a><br/><br/>
					</td>
				</tr>
				<?}?>
				<? /* if (count($pages)>1) { ?>
				<tr>
					<td colspan="7">
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
				<? } */ ?>
				<tr>
					<td colspan=3>
						<a href="/admin/plinks/<?=$prod->id ?>/add" class="add">Привязать товар</a>
					</td>
				</tr>
			</table>
		</div>
		<? /* ] #XPD */ ?>
		
		<? if (!is_null($frm_scs)) { ?>
		<div class="box">
			<table class="tablica1">
				<? foreach ($frm_scs as $sfld) { ?>
				<tr>
					<td><b><?=$sfld['label']?></b></td>
					<td><?=$sfld['input']?></td>
				</tr>
				<? } ?>
			</table>
		</div>
		<? } ?>

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

		
		<div class="box<? if (isset($acttab) && ($acttab=='mods')) {?> visible<?}?>">
			<br/>
			<form name="inherit" method="post">
			<table class="tablica1">
				<tr>
					<td><?=$ihfrm['variant']['label']?></td>
					<td colspan="2"><?=$ihfrm['variant']['input']?></td>
				</tr>
				<tr>
					<td><?=$ihfrm['price']['label']?><br/><?=$ihfrm['inh_price']['input']?><?=$ihfrm['inh_price']['label']?></td>
					<td><?=$ihfrm['price']['input']?></td>
					<td><input type="submit" name="submt_ih" value="Создать модификацию" class="add" /></td>
				</tr>
			</table>
			</form>
			<script type="text/javascript">
				$(function(){
					var checker=check_inherit("shp",{
						price:{check:"#ih_inh_price",edit:"#ih_price"}
					})
					checker.editBegin=function(){
						$(this).parent().addClass("ih-self").removeClass("ih-parent");
					};
					checker.editParentSet=function(){
						$(this).parent().addClass("ih-parent").removeClass("ih-self");
					};
					checker.editRestore=function(){
						$(this).parent().addClass("ih-self").removeClass("ih-parent");
					};
				})
			</script>
			<br/>
			<form name="mvar" method="post">
			<table id="dnd-table-var" class="tablica1">
				<tr>
					<th>Название</th>
					<th>Управление</th>
					<th>Цена</th>
					<th><img src="/resources/adm/img/eye.png" title="Показывать" alt="Показывать"></th>
				</tr>
			<? foreach ($prod->variants as $var) { $vfrm=$vfrms[$var->id]; ?>
				<tr id="p<?=$var->id ?>">
					<td><?=$var->ht_variant ?></td>
					<td>
						<a href="/admin/products<?=$var->uri ?>" class="edit">Редактировать</a><br/><br/>
						<a href="/admin/products<?=$var->uri ?>?tab=modifications&delete" class="delete">Удалить</a>
					</td>
					<td style="text-align: center;">
						<?=$vfrm['price']['input'] ?>
					</td>
					<td style="text-align: center;">
						<?=$vfrm['fshow']['input'] ?>
					</td>
				</tr>
			<? } ?>
				<tr>
					<td colspan=4>
						<input type="submit" name="msubmt_vars" value="Сохранить модификации" class="save">
					</td>
				</tr>
			</table>
			</form>
			<br/>
		</div>
		
	</div>

	<table class="tablica1">
		<tr>
			<td colspan=2>
				<a href="#" class="save" onclick="CKupdate(); document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save" onclick="document.getElementById('item_form').submit(); return false;">*/?>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->cat->uri ?>" class="left">Вернуться к разделу</a>
			</td>
		</tr>
	</table>

</div>
