	<? foreach ($brands as $brand) { ?>
	<div id="brandlist"><a href="/filter/brand-<?=$brand->id ?>" title="Продукция по производителю <?=$brand->ht_name ?>"><?=$brand->ht_name ?></a></div>
	<? } ?>
