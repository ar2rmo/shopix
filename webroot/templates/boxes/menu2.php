<? if (count($mnu)>0) { ?>
<!--
<div id="header-level2"><?=$mnu[0]->pcaption ?></div>
-->

<div id="menu-level2">
<ul class="level2">
	<? foreach ($mnu as $itm) { ?>
	<li<?=(is_null($itm->css)?($itm->selected?' class="selected"':''):(' class="'.($itm->selected?'selected ':'').$itm->css.'"')) ?>><a href="<?=$itm->uri ?>" title="<?=$itm->nq_caption ?>"><?=$itm->caption ?></a></li>
	<? } ?>
</ul>
<div id="clear-left"></div>
</div>
<? } ?>
