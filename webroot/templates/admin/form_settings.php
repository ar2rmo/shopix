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
			<td colspan=3><input type="submit" name="submt" value="Сохранить" class="save"></td>
		</tr>
		<tr>
			<td><?=$frm['inf_shopname']['label']?></td>
			<td colspan=2><?=$frm['inf_shopname']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['inf_shopurl']['label']?></td>
			<td colspan=2><?=$frm['inf_shopurl']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['inf_keywords']['label']?></td>
			<td colspan=2><?=$frm['inf_keywords']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['inf_description']['label']?></td>
			<td colspan=2><?=$frm['inf_description']['input']?></td>
		</tr>
		<tr>
			<td></td>
			<td colspan=2><?=$frm['inf_nameintitle']['input']?> <?=$frm['inf_nameintitle']['label']?></td>
		</tr>
		<tr>
			<td><?=$frm['ord_mail']['label']?></td>
			<td colspan=2><?=$frm['ord_mail']['input']?></td>
		</tr>
		<tr>
			<td>Максимальный размер изображения</td>
			<td colspan=2><?=$frm['img_max_width']['input']?> x <?=$frm['img_max_height']['input']?></td>
		</tr>
		<tr>
			<td>Размер среднего изображения</td>
			<td><?=$frm['img_middle_width']['input']?> x <?=$frm['img_middle_height']['input']?> для товара</td>
			<td><?=$frm['img_middle_width_cat']['input']?> x <?=$frm['img_middle_height_cat']['input']?> для раздела</td>
		</tr>
		<tr>
			<td>Размер маленького изображения</td>
			<td><?=$frm['img_small_width']['input']?> x <?=$frm['img_small_height']['input']?> для товара</td>
			<td><?=$frm['img_small_width_cat']['input']?> x <?=$frm['img_small_height_cat']['input']?> для раздела</td>
		</tr>
		<tr>
			<td>Размер изображения для статей</td>
			<td colspan=2><?=$frm['img_art_width']['input']?> x <?=$frm['img_art_height']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_onpage_prod']['label']?></td>
			<td colspan=2><?=$frm['num_onpage_prod']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_recomend']['label']?></td>
			<td colspan=2><?=$frm['num_box_recomend']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_new']['label']?></td>
			<td colspan=2><?=$frm['num_box_new']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_rand']['label']?></td>
			<td colspan=2><?=$frm['num_box_rand']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_onpage_news']['label']?></td>
			<td colspan=2><?=$frm['num_onpage_news']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_news']['label']?></td>
			<td colspan=2><?=$frm['num_box_news']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_onpage_articles']['label']?></td>
			<td colspan=2><?=$frm['num_onpage_articles']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_articles']['label']?></td>
			<td colspan=2><?=$frm['num_box_articles']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_onpage_specials']['label']?></td>
			<td colspan=2><?=$frm['num_onpage_specials']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_box_specials']['label']?></td>
			<td colspan=2><?=$frm['num_box_specials']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['num_onpage_orders']['label']?></td>
			<td colspan=2><?=$frm['num_onpage_orders']['input']?></td>
		</tr>
		<? /* #XPD [ */ ?>
		<tr>
			<td><?=$frm['num_xpd_rand']['label']?></td>
			<td colspan=2><?=$frm['num_xpd_rand']['input']?></td>
		</tr>
		<? /* ] #XPD */ ?>
		<tr>
			<td><?=$frm['name_crit1']['label']?></td>
			<td colspan=2><?=$frm['name_crit1']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_crit2']['label']?></td>
			<td colspan=2><?=$frm['name_crit2']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_crit3']['label']?></td>
			<td colspan=2><?=$frm['name_crit3']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_etab1']['label']?></td>
			<td colspan=2><?=$frm['name_etab1']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_etab2']['label']?></td>
			<td colspan=2><?=$frm['name_etab2']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_etab3']['label']?></td>
			<td colspan=2><?=$frm['name_etab3']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_etab4']['label']?></td>
			<td colspan=2><?=$frm['name_etab4']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name_etab5']['label']?></td>
			<td colspan=2><?=$frm['name_etab5']['input']?></td>
		</tr>
		<tr>
			<td colspan=3 style="text-align: center;"><input type="submit" name="submt" value="Сохранить" class="save"></td>
		</tr>
	</table>
</form>