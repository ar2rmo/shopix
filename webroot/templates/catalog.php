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
<link rel="stylesheet" href="<?=TPL?>ne-moy.css" type="text/css">
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

			<div id="cepka"><a href="/" class="home">Главная</a><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a href="/catalog">Продукция</a><? foreach ($breadcrumbs as $bc) { ?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a href="/catalog<?=$bc->uri ?>"><?=$bc->ht_name ?></a><? } ?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a class="current" href="/catalog<?=$cat->uri ?>"><?=$cat->ht_name ?></a></div>

			<h1><?=$cat->ht_d_name ?> <span class="kol-vo">товаров: <?=$num?></span></h1>

			<?=$sub_box_nextlev ?>

			<? if ($num>0) { ?>

			<? if ($firstpage && $cat->text_top) { ?>
			<p>
			<?=$cat->ht_text_top ?>
			<br><br>
			<? } ?>

<!--			
			<div id="sort-kolvo">
				<div id="kolvo-v-razdele">Товаров отобрано:&nbsp;<?=$num?>&nbsp;&nbsp;<br><img src="<?=TPL?>img/0.gif" width="1" height="7" alt=""><br><?=$fbrands ?>&nbsp;&nbsp;<?=$fcrit1 ?>&nbsp;&nbsp;<?=$fcrit2 ?></div>
				<? if ($num>0) {?>
				<div id="sortirovka">Сортировка:&nbsp;&nbsp;<?=$fsort ?></div>
				<?}?>
				<div id="clear"></div>
			</div>
-->

			<? $this->sub('prodlist'); ?>

			<? $this->sub('paginator'); ?>

			<p>
			<? } ?>



<? if ($firstpage && $cat->text_bott) { ?>
<br><br>
<?=$cat->ht_text_bott ?>
<? } ?>



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
