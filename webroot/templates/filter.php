<?
	$nms=array();
	if (isset($fos['brand'])) {
		$nms[]=$fos['brand']->ht_name;
	}
	foreach ($fos['crits'] as $oc) {
		$nms[]=$oc->ht_name;
	}
	if (isset($fos['tag'])) {
		$nms[]=$fos['tag'];
	}
	if (isset($fos['prices']['low']) && isset($fos['prices']['hi'])) {
		$nms[]='Цена от '.$fos['prices']['low'].' до '.$fos['prices']['hi'];
	} elseif (isset($fos['prices']['low'])) {
		$nms[]='Цена от '.$fos['prices']['low'];
	} elseif (isset($fos['prices']['hi'])) {
		$nms[]='Цена до '.$fos['prices']['hi'];
	}
	if ($fos['fnew']) {
		$nms[]='Новинки';
	}
	if ($fos['fspecial']) {
		$nms[]='Акционные товары';
	}
	if ($fos['frecomend']) {
		$nms[]='Рекомендуемые товары';
	}
	
	$name=implode(', ',$nms);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$name?><? if ($title) { ?> | <?=$title?><?}?></title>

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
<script type="text/javascript" src="<?=TPL?>scripts/cart.js"></script>
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


<? if (count($prods)>0) {?>

<h1>Отобранные товары: <?=$name ?></h1>

	<div id="sort-kolvo">
		<div id="kolvo-v-razdele">Товаров отобрано:&nbsp;<?=$num?>&nbsp;&nbsp;<?=$fbrands ?></div>
		<div id="sortirovka">
			Сортировка:&nbsp;&nbsp;<?=$fsort?>
		</div>
		<div id="clear"></div>
	</div>

<br>
<div id="products">
<? $this->sub('prodlist'); ?>
</div>

<? $this->sub('paginator'); ?>

<? } else { ?>
<h1>По заданным критериям продукции нет!</h1>
<? } ?>



<br>
<h2 class="rekomend">Рекомендуем</h2>
<br>
<?=$sub_box_recomend?>


<br>
<h2 class="rekomend">Популярное</h2>
<br>
<?=$sub_box_random ?>



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
