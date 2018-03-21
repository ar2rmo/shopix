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


<h1>Прайс-лист</h1>


<p>
<table class="tablica-price" width="100%">
	<? foreach ($items as $cat) { ?>
	<tr>
		<th colspan="3"><a href="/catalog<?=$cat->uri ?>"><?=$cat->ht_name ?></a></th>
	</tr>
	
	<? if (!is_null($cat->col_products)) { foreach ($cat->col_products as $prod) { ?>
	<tr>
		<td align="center" width="70"><? if ($prod->ispict) {?><a href="/catalog<?=$prod->uri ?>"><img src="<?=$prod->pict_uri_small ?>" width="40" height="40" alt="<?=$prod->nq_d_name ?>" /></a><?}?></td>
		<td><a href="/catalog<?=$prod->uri ?>" class="name-product"><?=$prod->ht_name ?></a><? if ($prod->code) {?> (<?=$prod->code ?>)<?}?></td>
		<? if ($prod->price) {?>
		<td align="center"><b class="price-in-price"><?=$prod->ht_full_price ?></b><? if ($prod->oprice) {?> (<s><?=$prod->ht_oprice ?></s>)<?}?></td>
		<? } elseif ($prod->is_price_range) { ?>
		<td align="center"><b class="price-in-price"><?=$prod->ht_price_range ?></b></td>
		<?}else{?>
		<td align="center"><b class="price-in-price">&mdash;</b></td>
		<?}?>
	</tr>
	<?}}?>
	<?}?>
</table>

<p>
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
