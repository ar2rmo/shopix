<? /* #CMP= */ ?>

<div id="cmp">
<? if (count($prods)>0) { ?>
	<div id="left-header-sravnenie">Сравнение товаров</div>
	<div id="compare">

<? foreach ($prods as $p) { ?>
	<a href="/catalog<?=$p->uri ?>"><?if (true) {?><img src="<?=$p->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" /> <?}?><?=$p->variant?$p->ht_variant:$p->ht_name ?> (<?=$p->ht_code ?>) <?=$p->ht_id ?></a>&nbsp;&nbsp;<a href="#" onclick="return DelFromCmp(<?=$p->id ?>)"><img src="<?=TPL?>img/delete.png" width="16" height="16" alt="Убрать из сравнения">убрать</a><br><br>
<? } ?>

	<div id="btn-filter"><a href="/compare?list=<?=$list?>" target="_blank">СРАВНИТЬ</a></div>
	</div>
<? } ?>
</div>

