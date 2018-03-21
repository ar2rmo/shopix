<?/* #TMN= */?>
<?

$l1=array();
foreach ($cats as $cat) {
	if ($cat->level==1) {
		unset($chld);
		$chld=array();
		$l1[]=array('item'=>$cat, 'children'=>&$chld);
	}
	if ($cat->level==2) {
		$chld[]=$cat;
	}
}

?>
<ul>
	<? foreach ($l1 as $l) {  $p=$l['item'] ?>
	<? if (empty($l['children'])) { ?>
	<li<?=(is_null($p->css)?($p->selected?' class="current"':''):(' class="'.($p->selected?'current ':'').$p->css.'"')) ?>><a href="/catalog<?=$p->uri ?>" title="<?=$p->ht_name ?>"><?=$p->ht_name ?></a></li>
	<? } else { ?>
	<li<?=(is_null($p->css)?($p->selected?' class="current arr"':' class="arr"'):(' class="arr '.($p->selected?'current ':'').$p->css.'"')) ?>><a href="/catalog<?=$p->uri ?>" class="arr" title="<?=$p->ht_name ?>"><?=$p->ht_name ?><span></span></a>
		<ul>
			<? foreach ($l['children'] as $c) { ?>
			<li<?=(is_null($c->css)?($c->selected?' class="current2"':''):(' class="'.($c->selected?'current2 ':'').$c->css.'"')) ?>><a href="/catalog<?=$c->uri ?>"><?=$c->ht_name ?></a></li>
			<? } ?>
		</ul>
	</li>
	<? } ?>
	<? } ?>
</ul>
