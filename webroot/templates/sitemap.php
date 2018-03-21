<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title>Карта сайта<? if ($title) { ?> | <?=$title?><?}?></title>

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

<h1>Карта сайта</h1>

<table width="100%">
<tr>
<td width="60%">

<h2>Каталог</h2>

	<? $plv=0; foreach ($cats as $cat) { $dl=$cat->level-$plv; ?>
		<? while ($dl>0) { ?><ul><? $dl--; } ?>
		<? while ($dl<0) { ?></ul><? $dl++; } ?>
		<li><a href="/catalog<?=$cat->uri ?>"><b><?=$cat->ht_name ?></b></a></li>
	<? $plv=$cat->level; } ?>
	<? while ($plv>0) { ?></ul><? $plv--; } ?>

</td>
<td width="50">&nbsp;</td>
<td>

<h2>Страницы</h2>

	<? $plv=0; foreach ($menu as $mnu) { $dl=$mnu->level-$plv; ?>
		<? while ($dl>0) { ?><ul><? $dl--; } ?>
		<? while ($dl<0) { ?></ul><? $dl++; } ?>
		<li><a href="<?=$mnu->uri ?>"><? if ($mnu->level==1) {?><b><?}?><?=$mnu->ht_caption ?><? if ($mnu->level==1) {?></b><?}?></a></li>
	<? $plv=$mnu->level; } ?>
	<? while ($plv>0) { ?></ul><? $plv--; } ?>

</td></tr>
</table>






<br>
<table width="100%">
<tr>
<td width="45%">

<h2>Новости</h2>

	<ul>
	<? foreach ($news as $itm) { ?>
		<li><a href="/news/<?=$itm->uid ?>"><?=$itm->ht_caption ?></a></li>
	<? } ?>
	</ul>
	
<h2>Акции</h2>

	<ul>
	<? foreach ($specs as $itm) { ?>
		<li><a href="/specials/<?=$itm->uid ?>"><?=$itm->ht_caption ?></a></li>
	<? } ?>
	</ul>

</td>
<td width="50">&nbsp;</td>
<td>

<h2>Полезные статьи</h2>

	<ul>
	<? foreach ($arts as $itm) { ?>
		<li><a href="/articles/<?=$itm->uid ?>"><?=$itm->ht_caption ?></a></li>
	<? } ?>
	</ul>

</td></tr>
</table>



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
