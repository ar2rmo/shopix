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

<script language="javascript" type="text/javascript" src="<?=TPL?>scripts/jquery.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jqcloud-1.0.4.min.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/modal.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/totop.js"></script>
<script type="text/javascript" src="/callme/js/callme.js"></script>

</head>

<body>
<img src="/templates/res/img/index.jpg" class="bg" />
<div id="content0">

	<? $this->sub('-top'); ?>

<div id="wrap">
	<div id="middle">
		<div id="container">
			<div id="content-page-left">



<h1>Весь каталог продукции</h1>


<p>
<ol class="tovari">
	<? foreach ($items as $cat) { ?>
		<? if ($cat->level==1) { ?><h1><a href="/catalog<?=$cat->uri?>"><?=$cat->ht_name?></a></h1><?}?>
		<? if ($cat->level==2) { ?><h2 class="inallcatalog"><a href="/catalog<?=$cat->uri?>"><?=$cat->ht_name?></a></h2><br><?}?>
		<? if ($cat->level==3) { ?><h3 class="inallcatalog"><a href="/catalog<?=$cat->uri?>"><?=$cat->ht_name?></a></h3><br><?}?>
<!--
	<tr>
		<th colspan="3"><a href="/catalog<?=$cat->uri ?>"><?=$cat->ht_name ?></a></th>
	</tr>
-->
	<? if (!is_null($cat->col_products)) { foreach ($cat->col_products as $prod) { ?>
			<li>
			<div id="tovar-for-podsvetka">
					<? if ($prod->fspecial) {?><div id="akcia2"></div><?}?>
					<? if ($prod->fnew) {?><div id="novinka2"></div><?}?>
			<div id="tovar-podsvetka">
			<div id="tovar-podsvetka-content">
				<div id="small-foto-tovar">
					<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_d_name?>"><img src="<?=$prod->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" alt="<?=$prod->nq_d_name ?>" class="small-foto-tovar"></a>
				</div>

				<div id="tovar-nazvanie">
					<a href="/catalog<?=$prod->uri ?>" title="<?=$prod->nq_d_name?>"><?=$prod->ht_name?><? if ($prod->code) {?>&nbsp;<span><?=$prod->ht_code?></span><?}?></a>
				</div>

				<? if ($prod->brand_id) { ?><div id="brend"><span>Бренд:&nbsp;</span><a href="/filter/brand-<?=$prod->brand_id ?>"><?=$prod->ht_brand_name ?></a></div><?}?>

				<? if ($prod->price) { ?>
				<div id="tovar-price"><?=$prod->ht_full_price ?><? if ($prod->oprice) {?> <span style="text-decoration: line-through;"><?=$prod->ht_oprice ?></span><?}?></div>
				<? } else { ?>
				<div id="tovar-price"><?=$prod->ht_price_range ?></div>
				<? } ?>

					<? if (count($prod->variants_vis)>0) { ?>
					<div id="tovar-kupit">	
						<select id="prodvar-<?=$prod->id?>" class="modifikacia">
							<? foreach ($prod->variants_vis as $pv) { ?>
							<option value="<?=$pv->id?>"><?=$pv->ht_variant ?><? if ($pv->price) {?> - <?=$pv->ht_price_currency ?><?}?></option>
							<? } ?>
						</select><br/><br/>
						<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(document.getElementById('prodvar-<?=$prod->id?>').value,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="Купить">Купить</a>
					</div>
					<? } elseif ($prod->sellable) { ?>
					<div id="tovar-kupit">
						<input type="text" value="1" id="cart_num_<?=$prod->id?>" class="input-kol-vo" />&nbsp;
						<a href="" onclick="return AddToCart(<?=$prod->id ?>,document.getElementById('cart_num_<?=$prod->id?>').value)" class="vkorzinu" title="Купить">Купить</a>
					</div>
					<? } ?>

			<div id="small-opisanie">
				<?=$prod->ht_descr_short ?>
			</div>

			</div>
			</div>
			</div>
			</li>
	<?}}?>
	<?}?>
</ol>


<? $this->sub('paginator'); ?>



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
