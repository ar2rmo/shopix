<?/* #TMN= */?>

<? $count=true; ?>
<? $plv=0; $ui=0; $di=0; foreach ($cats as $cat) { ?>
	<? if ($cat->level > $plv) { ?>
		<? if ($ui>0) { ?></ul><? $ui--; } ?>

		<div id="menu-left<?=$cat->level ?>"><? $di++; ?>
			<ul id="leftm"><? $ui++; ?>
	<? } ?>
	<? if ($cat->level < $plv) { ?>
			<? if ($ui>0) { ?></ul><? $ui--; } ?>
		<? if ($di>0) { ?></div><? $di--; } ?>
		<ul><? $ui++; ?>
	<? } ?>

	<li<?=(is_null($cat->css)?($cat->selected?' class="current"':''):(' class="'.($cat->selected?'current ':'').$cat->css.'"')) ?>><a href="/catalog<?=$cat->uri ?>"><?=$cat->ht_name ?><? if ($count) {?>&nbsp;<span class="kol"><?=$cat->vfullcount ?></span><?}?></a></li>
<? $plv=$cat->level; } ?>
<? while ($ui>0) { ?></ul><? $ui--; } ?>
<? while ($di>0) { ?></div><? $di--; } ?>
