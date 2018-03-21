<ul>
	<? foreach ($mnu as $itm) { ?>
	<li<?=(is_null($itm->css)?($itm->selected?' class="selected"':''):(' class="'.($itm->selected?'selected ':'').$itm->css.'"')) ?>><a href="<?=$itm->uri ?>" title="<?=$itm->nq_caption ?>"><?=$itm->caption ?></a></li>
	<? } ?>
</ul>