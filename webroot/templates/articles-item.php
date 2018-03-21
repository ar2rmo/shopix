<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$art->tx_caption?><? if ($title) { ?> | <?=$title?><?}?></title>

<meta name="robots" content="index, follow">
<meta name="revisit-after" content="1 days">
<meta name="description" content="<?=$description?>">
<meta name="keywords" content="<?=$keywords?>">
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
	<div id="middle">
		<div id="container">
			<div id="content-page-left">



			<h1><?=$art->ht_caption ?></h1>
			<p>
			<div id="date-article1"><?=$art->ht_date ?></div>
			<? if ($art->isfull) {?>
			<p>
			<?=$art->ht_short ?>
			<?=$art->ht_full ?></p>
			<? } else { ?>
			<div id="article2">
				<div id="small-text1">
					<? if ($art->ispict) {?><div id="img-article"><img src="<?=$art->pict_uri?>" width="150" height="150" alt="<?=$art->nq_caption?>"></div>
					<div id="small-text-article"><?}?>
						<p><?=$art->ht_short?></p>
						<? if ($art->ispict) {?>
					</div>
					<div id="clear"></div><?}?>
				</div>
			</div>
			<?}?>
			<? if ($art->link) { ?><br/>Источник: <? if ($art->href) { ?><a href="<?=$art->href ?>" target="_blank"><?=$art->link ?></a><? } else { ?><?=$art->link ?><? }} ?>

<br>
<br>
<strong>Поделитесь этой записью с друзьями:</strong>
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

<div id="arhive-news-in"><a href="/articles">Все статьи</a></div>


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
