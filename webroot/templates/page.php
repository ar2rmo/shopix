<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$page->tx_d_title ?><? if ($title) { ?> | <?=$title?><?}?></title>

<meta name="robots" content="index, follow">
<meta name="revisit-after" content="1 days">
<meta name="description" content="<?=$page->nq_description?$page->nq_description:$description?>">
<meta name="keywords" content="<?=$page->nq_keywords?$page->nq_keywords:$keywords?>">
<meta name="viewport" content="width=1280">

<link rel="icon" href="<?=TPL?>img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="<?=TPL?>css.css" type="text/css">
<link rel="stylesheet" href="<?=TPL?>fancybox.css" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?=TPL?>jqcloud.css" type="text/css">

<script language="javascript" type="text/javascript" src="<?=TPL?>scripts/jquery.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jqcloud-1.0.4.min.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.fancybox1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.fancybox2.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/cart.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/compare.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/modal.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/totop.js"></script>
<script type="text/javascript" src="/callme/js/callme.js"></script>

</head>

<body>
<img src="/templates/res/img/index.jpg" class="bg" />
<div id="content0">

	<? $this->sub('-top'); ?>

<div id="wrap">
<!--
	<div id="ico-home"><a href="/" title="Home"><img src="<?=TPL?>img/home-icon-white.png" alt="Главная"></a></div>
	<div id="cepka"><a href="/" class="home">Главная</a><? if ($page->pid) {?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7" alt=""><a href="/page<?=$page->puri ?>"><?=$page->pcaption ?></a><?}?><img src="<?=TPL?>img/str-cepka.png" width="10" height="7"><a href="<?=$page->uri ?>" class="current"><?=$page->caption ?></a></div>
-->
	<div id="middle">
		<div id="container">
			<div id="content-page-left">

				<h1><?=$page->ht_caption ?></h1>
				<?=$page->ht_text ?>


			<br><br><br>
			<h2 class="novinki">Популярные товары</h2>
			<br><br>
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
