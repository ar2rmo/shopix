<ol class="tovari">
	<? foreach ($prods as $prod) { ?>
	<li>
			<div id="tovar-for-podsvetka">
					<? if ($prod->fspecial) {?><div id="akcia2"></div><?}?>
					<? if ($prod->fnew) {?><div id="novinka2"></div><?}?>
			<div id="tovar-podsvetka">
			<div id="tovar-podsvetka-content">
				<div id="small-foto-tovar">
					<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_d_name?>"><img src="<?=$prod->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" alt="<?=$prod->nq_d_name ?>" class="small-foto-tovar"></a>
				</div>

				<? if ($prod->price) {?><div id="tovar-price"><?=$prod->ht_full_price ?></div><?}?>
				<? if (!$prod->price && (count($prod->variants_vis)>0)) { $fv=null; foreach ($prod->variants_vis as $vv) {if ($vv->price) {$fv=$vv; break;}} ?>
				<div id="tovar-price"><?=$fv->ht_full_price ?></div>
				<?}?>

				<div id="tovar-nazvanie">
					<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_d_name?>"><?=$prod->ht_name?><? if ($prod->code) {?>&nbsp;<span><?=$prod->ht_code?></span><?}?></a>
				</div>


				<? if ($prod->brand_id) { ?><div id="brend"><span>Бренд:&nbsp;</span><a href="/filter/brand-<?=$prod->brand_id ?>"><?=$prod->ht_brand_name ?></a></div><?}?>
<? /*
				<? if ($prod->barcode) {?><div id="tovar-kod"><span>Штрихкод:&nbsp;</span><?=$prod->ht_barcode?></div><?}?>
*/ ?> 


					<? if (count($prod->variants_vis)>0) { ?>
					<div id="tovar-kupit">	
						<select id="prodvar-<?=$prod->id?>" class="modifikacia">
							<? foreach ($prod->variants_vis as $pv) { ?>
							<option value="<?=$pv->id?>"><?=$pv->ht_variant ?><? if ($pv->price) {?> - <?=$pv->ht_price_currency ?><?}?></option>
							<? } ?>
						</select><br/><br/>
						<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(document.getElementById('prodvar-<?=$prod->id?>').value,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
<? /*
						<a href="" onclick="return AddToCmp(<?=$prod->id ?>)" class="sravnit" title="Добавить к сравнению">К&nbsp;сравнению</a>
*/ ?> 
					</div>
					<? } elseif ($prod->sellable) { ?>
					<div id="tovar-kupit">	
						<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(<?=$prod->id ?>,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
<? /*
						<a href="" onclick="return AddToCmp(<?=$prod->id ?>)" class="sravnit" title="Добавить к сравнению">К&nbsp;сравнению</a>
*/ ?> 
					</div>
					<? } ?>


			<div id="small-opisanie">
				<?=$prod->ht_descr_short ?>
			</div>

			</div>
			</div>
			</div>
	</li>
	<? } ?>
</ol>
