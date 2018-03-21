<ul id="flexiselDemo3"> 
	<? foreach ($prods as $prod) { ?>
	<li>
		<div id="tovar-for-podsvetka-prozr">
		<div id="tovar-for-podsvetka">
		<? if ($prod->fspecial) {?><div id="akcia2"></div><?}?>
		<? if ($prod->fnew) {?><div id="novinka2"></div><?}?>

		<div id="small-foto-tovar0">
		<div id="small-foto-tovar">
			<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_name ?>"><img src="<?=$prod->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" alt="<?=$prod->nq_name ?>" class="small-foto-tovar"></a>
		</div>
		</div>

		<? if ($prod->price) { ?>
		<div id="tovar-price"><?=$prod->ht_full_price ?><? if ($prod->oprice) {?> <span style="text-decoration: line-through;"><?=$prod->ht_oprice ?></span><?}?></div>
		<? } else { ?>
		<div id="tovar-price"><?=$prod->ht_price_range ?></div>
		<? } ?>

		<div id="tovar-podsvetka-content">
			<div id="tovar-nazvanie">
				<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_name ?>"><?=$prod->ht_name ?></a>
			</div>
			<? if (count($prod->variants_vis)>0) { ?>
			<div id="tovar-kupit">	
				<select id="prodvar-<?=$prod->id?>" class="modifikacia">
					<? foreach ($prod->variants_vis as $pv) { ?>
					<option value="<?=$pv->id?>"><?=$pv->ht_variant ?><? if (false && $pv->price) {?> - <?=$pv->ht_price_currency ?><?}?></option>
					<? } ?>
				</select><br/><br/>
				<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-small3" />&nbsp;
				<a href="" onclick="return AddToCart(document.getElementById('prodvar-<?=$prod->id?>').value,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
			</div>
			<? } elseif ($prod->sellable) { ?>
			<div id="tovar-kupit">
				<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-small3" />&nbsp;
				<a href="" onclick="return AddToCart(<?=$prod->id ?>,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
			</div>
			<? } ?>
		</div>
	</div>
	</div>
	</li>
	<? } ?>
</ul>
