<? foreach ($arts as $art) { ?>
		<li><div id="article">
			<? if ($art->ispict) {?><a href="/specials/<?=$art->uid ?>" title="<?=$art->nq_caption ?>"><img src="<?=$art->pict_uri?>" width="100" height="100" alt="<?=$art->nq_caption ?>" class="small-foto-news-home"></a><?}?>
			<a href="/specials/<?=$art->uid ?>" class="article"><?=$art->ht_caption ?></a>
			<div id="date-article"><?=$art->ht_date ?></div>
			<div id="category-small-text">
				<?=$art->ht_short ?>
			</div>
			<div id="clear"></div>
		</div></li>
<? } ?>
