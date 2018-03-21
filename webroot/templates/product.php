<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$title?></title>

<meta name="robots" content="index, follow">
<meta name="revisit-after" content="1 days">
<meta name="description" content="<?=$description?>">
<meta name="keywords" content="<?=$keywords?>">
<meta name="viewport" content="width=1280">

<link rel="icon" href="<?=TPL?>img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="<?=TPL?>css.css" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?=TPL?>jqcloud.css" type="text/css">
<link rel="stylesheet" href="<?=TPL?>fancybox.css" type="text/css">

<script language="javascript" type="text/javascript" src="<?=TPL?>scripts/jquery.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jqcloud-1.0.4.min.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/cart.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/modal.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/totop.js"></script>
<script type="text/javascript" src="/callme/js/callme.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/tabs.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.fancybox1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.fancybox2.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.mousewheel-3.0.4.pack.js"></script>

</head>

<body>
<img src="/templates/res/img/index.jpg" class="bg" />
<div id="content0">

	<? $this->sub('-top'); ?>

<div id="wrap">
	<div id="middle">
		<div id="container">
			<div id="content-page-left">

	<div id="cepka"><a href="/" class="home">Главная</a><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a href="/catalog">Продукция</a><? foreach ($breadcrumbs as $bc) { ?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a href="/catalog<?=$bc->uri ?>"><?=$bc->ht_name ?></a><? } ?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a class="current" href="/catalog<?=$prod->uri ?>"><?=$prod->ht_name ?></a></div>

<h1><?=$prod->ht_d_name ?></h1>


	<table width="100%" order="1">
	<tr>
	<td width="310">
		<div id="middle-foto-tovar-in">
		<? if ($prod->ispict) {?>
		<a href="<?=$prod->pict_uri_big ?>" rel="group" title="<?=$prod->nq_d_name ?><?if ($prod->price) {?> (<?=$prod->price_currency ?>)<?}?>"><img src="<?=$prod->pict_uri_medium ?>" width="<?=$setts->img_middle_width ?>" height="<?=$setts->img_middle_height ?>" alt="<?=$prod->nq_d_name ?>" class="middle-foto-tovar"></a>
		<? } else {?>
		<img src="<?=$prod->pict_uri_medium ?>" width="<?=$setts->img_middle_width ?>" height="<?=$setts->img_middle_height ?>" title="<?=$prod->nq_d_name ?><?if ($prod->price) {?> (<?=$prod->ht_price_currency?>)<?}?>" alt="<?=$prod->nq_d_name ?>">
		<? } ?>
		<? if ($prod->fspecial) {?><div id="akcia2"></div><?}?>
		<? if ($prod->fnew) {?><div id="novinka2"></div><?}?>
		</div>

	</td>
	<td width="30"><img src="<?=TPL?>img/0.gif" width="30" height="1" alt=""></td>
	<td>


		<? if ($prod->brand_id) { ?><div id="brend-in"><span>Бренд:&nbsp;</span><a href="/filter/brand-<?=$prod->brand_id ?>"><?=$prod->ht_brand_name ?></a></div><?}?>
		<? if ($prod->code) { ?><div id="brend-in"><span>Код товара:&nbsp;</span><?=$prod->ht_code ?></div><?}?>
		<? if ($prod->barcode) { ?><div id="brend-in"><span>Размер в упаковке, см / Вес:&nbsp;</span><?=$prod->ht_barcode ?></div><?}?>
		<? if ($prod->measure) { ?><div id="brend-in"><span>Ед.измерения:&nbsp;</span><?=$prod->ht_measure ?></div><?}?>
		<? if ($prod->size) { ?><div id="brend-in"><span>Вес в упаковке, кг:&nbsp;</span><?=$prod->ht_size ?></div><?}?>
		
		<? if ($prod->avail_id) { ?><div id="brend-in"><span>Наличие:&nbsp;</span><?=$prod->ht_avail_name ?></div><?}?>
		<? if ($prod->avail_num) { ?><div id="brend-in"><span>Остаток:&nbsp;</span><?=$prod->ht_avail_num ?></div><?}?>
		
		<? if (count($prod->col_crits1)>0) { ?><div id="brend-in"><span><? if ($setts->ht_name_crit1) { ?><?=$setts->ht_name_crit1 ?>:<? } else { ?>Критерий 1:<? } ?>&nbsp;</span><?=$this->cs($prod->col_crits1,function ($cr1) {?><a href="/filter/crit-<?=$cr1->id?>"><?=$cr1->ht_name?></a><?}) ?></div><? } ?>
		<? if (count($prod->col_crits2)>0) { ?><div id="brend-in"><span><? if ($setts->ht_name_crit2) { ?><?=$setts->ht_name_crit2 ?>:<? } else { ?>Критерий 2:<? } ?>&nbsp;</span><?=$this->cs($prod->col_crits2,function ($cr2) {?><a href="/filter/crit-<?=$cr2->id?>"><?=$cr2->ht_name?></a><?}) ?></div><? } ?>
		<? if (count($prod->col_crits3)>0) { ?><div id="brend-in"><span><? if ($setts->ht_name_crit3) { ?><?=$setts->ht_name_crit3 ?>:<? } else { ?>Критерий 3:<? } ?>&nbsp;</span><?=$this->cs($prod->col_crits3,function ($cr3) {?><a href="/filter/crit-<?=$cr3->id?>"><?=$cr3->ht_name?></a><?}) ?></div><? } ?>
		
		<? foreach ($prod->specs_values as $sv) { ?>
		<div id="brend-in"><span><?=$sv->ht_name ?>:&nbsp;</span><?=$sv->xvalue->ht_v ?></div>
		<? } ?>

		<? if ($prod->price) { ?><br><div id="tovar-price-in">Цена: <?=$prod->ht_full_price ?><? if ($prod->oprice) {?>&nbsp;&nbsp;<span style="text-decoration: line-through;"><?=$prod->ht_ud_price_currency ?></span><?}?></div>
		<? } elseif ($prod->is_price_range) { ?>
		<br><div id="tovar-price-in"><?=$prod->ht_price_range ?></div>
		<? } ?>
		
		<? if (count($prod->variants_vis)>0) { ?>
		<br>
		<div id="tovar-kupit-in">
			<select id="prodvar-<?=$prod->id?>" class="variant-sel modifikacia-in-tovar" onchange="return onChangeVariant.call(this);">
				<? $nofs=true; foreach ($prod->variants_vis as $pv) { ?>
				<option class="<?=$pv->forsale?'forsale':'not-forsale' ?>" value="<?=$pv->id?>"<? if ($nofs && $pv->forsale) { ?> selected="selected"<?}?>><?=$pv->ht_variant ?><? if ($pv->price) {?> - <?=$pv->ht_price_currency ?><?}?></option>
				<? if ($pv->forsale) {$nofs=false;} } ?>
			</select>
			&nbsp; &nbsp; <input type="text" value="1" id="cart_num"<? if ($nofs) {?> hidden="true"<?}?> class="input-kol-vo-in" /> &nbsp;
			&nbsp; <a href="" id="cart_add"<? if ($nofs) {?> hidden="true"<?}?> onclick="return AddToCart(document.getElementById('prodvar-<?=$prod->id?>').value,document.getElementById('cart_num').value)" class="vkorzinu-in" title="В корзину">Купить</a>
<!--
			&nbsp; &nbsp; <a href="" onclick="return AddToCmp(<?=$prod->id ?>)" class="sravnit-in" title="К&nbsp;сравнению">Добавить&nbsp;к&nbsp;сравнению</a>
-->
		</div>



		<? } elseif ($prod->sellable) { ?>
		<br>
		<div id="tovar-kupit-in">
			<input type="text" value="1" id="cart_num" class="input-kol-vo-in" /> &nbsp;
			<a href="" onclick="return AddToCart(<?=$prod->id ?>,document.getElementById('cart_num').value)" class="vkorzinu-in" title="В корзину">Купить</a>
<!--
			&nbsp; &nbsp; <a href="" onclick="return AddToCmp(<?=$prod->id ?>)" class="sravnit-in" title="К&nbsp;сравнению">Добавить&nbsp;к&nbsp;сравнению</a>
-->
		</div>
		<? } ?>


		<br><br>
		<div id="small-opisanie-in">
			<?=$prod->ht_descr_short ?>
		</div>
		
	</td></tr>
	</table>



<? if ($prod->descr_full || (count($prod->col_picts)>0) || $prod->descr_tech || true /* << поставь false, если каменты не нужны */ || $prod->extra1 || $prod->extra2 || $prod->extra3 || $prod->extra4 || $prod->extra5) { ?>
<br>
<div id="tabs">
<div class="section">
	<ul class="tabs">
	<? $notab=true; ?>
	<? if ($prod->descr_full) { ?><li<? if ($notab) { ?> class="current"<? } ?>>Описание товара</li><? $notab=false; } ?>
	<? if (count($prod->col_picts)>0) { ?><li<? if ($notab) { ?> class="current"<? } ?>>Фотографии&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sup><?=count($prod->col_picts) ?></sup></li><? $notab=false; } ?>
	<? if ($prod->descr_tech) { ?><li<? if ($notab) {?> class="current"<?}?>>Характеристика</li><? $notab=false; } ?>
	<? if (true) { /* << поставь false, если каменты не нужны */ ?><li<? if ($notab) {?> class="current"<?}?>>Комментарии</li><? $notab=false; } ?>
	<? if ($prod->extra1) { ?><li<? if ($notab) {?> class="current"<?}?>><? if ($setts->name_etab1) { ?><?=$setts->ht_name_etab1?><? } else { ?>Доп.&nbsp;1<? } ?></li><? $notab=false; } ?>
	<? if ($prod->extra2) { ?><li<? if ($notab) {?> class="current"<?}?>><? if ($setts->name_etab2) { ?><?=$setts->ht_name_etab2?><? } else { ?>Доп.&nbsp;2<? } ?></li><? $notab=false; } ?>
	<? if ($prod->extra3) { ?><li<? if ($notab) {?> class="current"<?}?>><? if ($setts->name_etab3) { ?><?=$setts->ht_name_etab3?><? } else { ?>Доп.&nbsp;3<? } ?></li><? $notab=false; } ?>
	<? if ($prod->extra4) { ?><li<? if ($notab) {?> class="current"<?}?>><? if ($setts->name_etab4) { ?><?=$setts->ht_name_etab4?><? } else { ?>Доп.&nbsp;4<? } ?></li><? $notab=false; } ?>
	<? if ($prod->extra5) { ?><li<? if ($notab) {?> class="current"<?}?>><? if ($setts->name_etab5) { ?><?=$setts->ht_name_etab5?><? } else { ?>Доп.&nbsp;5<? } ?></li><? $notab=false; } ?>
	</ul>

	
<? $notab=true; ?>

<? if ($prod->descr_full) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->ht_descr_full ?>
</div>
<? $notab=false; } ?>

<? if (count($prod->col_picts)>0) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
	<p>
	<? foreach ($prod->col_picts as $pic) {?>
	<div id="dop-foto"><div id="small-foto-tovar"><a href="<?=$pic->pict_uri_big ?>" rel="group" title="<?=$pic->pp_name?$pic->nq_pp_name:$prod->nq_d_name?><?if ($prod->price) {?> (<?=$prod->ht_price_currency?>)<?}?>"><img src="<?=$pic->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" class="small-foto-tovar" title="<?=$pic->pp_name?$pic->nq_pp_name:$prod->nq_d_name?><?if ($prod->price) {?> (<?=$prod->ht_price_currency?>)<?}?>" alt="<?=$pic->pp_name?$pic->nq_pp_name:$prod->nq_d_name?><?if ($prod->price) {?> (<?=$prod->ht_price_currency?>)<?}?>"></a></div></div>
	<? } ?>
	<div id="clear-left"></div>
</div>
<? $notab=false; } ?>

<? if ($prod->descr_tech) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->ht_descr_tech ?>
</div>
<? $notab=false; } ?>

<? if (true) { /* << поставь false, если каменты не нужны */ ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<!-- каменты тут -->

<div id="disqus_thread"></div>
<script>
    /**
     *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
     *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
     */
    /*
    var disqus_config = function () {
        this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    */
    (function() {  // DON'T EDIT BELOW THIS LINE
        var d = document, s = d.createElement('script');
        
        s.src = '//cvm2016.disqus.com/embed.js';
        
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>


</div>
<? $notab=false; } ?>

<? if ($prod->extra1) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->extra1 ?>
</div>
<? $notab=false; } ?>

<? if ($prod->extra2) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->extra2 ?>
</div>
<? $notab=false; } ?>

<? if ($prod->extra3) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->extra3 ?>
</div>
<? $notab=false; } ?>

<? if ($prod->extra4) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->extra4 ?>
</div>
<? $notab=false; } ?>

<? if ($prod->extra5) { ?>
<div class="box<?if ($notab) {?> visible<?}?>">
<p>
	<?=$prod->extra5 ?>
</div>
<? $notab=false; } ?>

</div>
</div>
<? } ?>

		<? if (count($prod->col_tags)>0) {?>
		<div id="metki">
			<span>Метки:&nbsp;</span>
			<? $i=count($prod->col_tags); foreach ($prod->col_tags as $tag) { $i--;?>
			<a href="/filter/tag-<?=urlencode($tag->name) ?>"><?=$tag->ht_name ?></a><? if ($i) {?>, <?}?>
			<?}?>
		</div><?}?>



<p>
<br>
<br>
<strong>Поделитесь ссылкой на товар с друзьями:</strong>
<br>
<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="transparent" data-options="small,round,line,horizontal,counter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,print"></div>






<br><br>

<? if (count($xpd)>0) { ?>

<br>
<h2>Вас также может заинтересовать</h2>
<p>
<ol class="tovari">
<? foreach ($xpd as $xp) { ?>
	<li>
			<div id="tovar-for-podsvetka">
					<? if ($xp->fspecial) {?><div id="akcia2"></div><?}?>
					<? if ($xp->fnew) {?><div id="novinka2"></div><?}?>
			<div id="tovar-podsvetka">
			<div id="tovar-podsvetka-content">
				<div id="small-foto-tovar">
					<a href="/catalog<?=$xp->uri ?>" title="<?=$xp->nq_d_name?>"><img src="<?=$xp->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" alt="<?=$xp->nq_d_name ?>" class="small-foto-tovar"></a>
				</div>

				<? if ($xp->price) {?><div id="tovar-price"><?=$xp->ht_full_price ?></div><?}?>
				<? if (!$xp->price && (count($xp->variants_vis)>0)) { $fv=null; foreach ($xp->variants_vis as $vv) {if ($vv->price) {$fv=$vv; break;}} ?>
				<div id="tovar-price"><?=$fv->ht_full_price ?></div>
				<?}?>

				<div id="tovar-nazvanie">
					<a href="/catalog<?=$xp->uri ?>" title="<?=$xp->nq_d_name?>"><?=$xp->ht_name?><? if ($xp->code) {?>&nbsp;<span><?=$xp->ht_code?></span><?}?></a>
				</div>


				<? if ($xp->brand_id) { ?><div id="brend"><span>Бренд:&nbsp;</span><a href="/filter/brand-<?=$xp->brand_id ?>"><?=$xp->ht_brand_name ?></a></div><?}?>
<? /*
				<? if ($xp->barcode) {?><div id="tovar-kod"><span>Штрихкод:&nbsp;</span><?=$xp->ht_barcode?></div><?}?>
*/ ?> 


					<? if (count($xp->variants_vis)>0) { ?>
					<div id="tovar-kupit">	
						<select id="prodvar-<?=$xp->id?>" class="modifikacia">
							<? foreach ($xp->variants_vis as $pv) { ?>
							<option value="<?=$pv->id?>"><?=$pv->ht_variant ?><? if ($pv->price) {?> - <?=$pv->ht_price_currency ?><?}?></option>
							<? } ?>
						</select><br/><br/>
						<input type="text" value="1" id="cart_num_<?=$xp->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(document.getElementById('prodvar-<?=$xp->id?>').value,document.getElementById('cart_num_<?=$xp->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
<!--
						<a href="" onclick="return AddToCmp(<?=$xp->id ?>)" class="sravnit" title="Добавить к сравнению">К&nbsp;сравнению</a>
-->
					</div>
					<? } elseif ($xp->sellable) { ?>
					<div id="tovar-kupit">	
						<input type="text" value="1" id="cart_num_<?=$xp->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(<?=$xp->id ?>,document.getElementById('cart_num_<?=$xp->id?>').value)" class="vkorzinu" title="В корзину">В корзину</a>
<!--
						<a href="" onclick="return AddToCmp(<?=$xp->id ?>)" class="sravnit" title="Добавить к сравнению">К&nbsp;сравнению</a>
-->
					</div>
					<? } ?>


			<div id="small-opisanie">
				<?=$xp->ht_descr_short ?>
			</div>

			</div>
			</div>
			</div>
	</li>
<? } ?>
</ol>


<? } ?>


<p>
<? if ($prod->created) { ?><div id="tovar-add"><span>Товар добавлен в каталог:&nbsp;</span><?=$prod->created->format('d.m.Y') ?> в <?=$prod->created->format('H:i') ?></div><? } ?>
<? if ($prod->modified_price) {?><div id="tovar-obnovili"><span>Стоимость товара обновлена:&nbsp;</span><?=$prod->modified_price->format('d.m.Y') ?> в <?=$prod->modified_price->format('H:i') ?></div><?}?>
<div id="counter"><span>Просмотров всего:&nbsp;</span><?=$prod->ht_log_all ?><span>&nbsp;&nbsp;|&nbsp;&nbsp;Сегодня:&nbsp;</span><?=$prod->ht_log_day ?></div>



<br><br>
<h2 class="rekomend">Рекомендуем</h2>
<br>
<?=$sub_box_recomend?>


<h2 class="rekomend">Недавно покупали</h2>
<br>
<?=$sub_box_random?>




			</div>
		</div>
		<? $this->sub('-left'); ?>
	</div>
</div>

	<? $this->sub('-bottom'); ?>

<a href="#" class="scrollToTop">Наверх</a>
</div>
</body>
</html>
