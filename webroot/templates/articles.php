<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><? if (is_null($page)) { ?>Статьи<? if ($title) { ?> | <?=$title?><?}} else {?><?=$title?><?}?></title>

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


			<h1>Статьи (<?=$num?>)</h1>
			
			<? if (!is_null($page)) { ?>
				<?=$page->ht_text?>
			<? } ?>

			<? foreach ($arts as $art) { ?>
			<div id="article1">
				<a href="/articles/<?=$art->uid ?>" class="article1"><?=$art->ht_caption ?></a>
				<div id="date-article1"><?=$art->ht_date ?></div>
				<div id="small-text1">
					<? if ($art->ispict) {?><div id="img-article"><a href="/articles/<?=$art->uid ?>" title="<?=$art->nq_caption ?>"><img src="<?=$art->pict_uri?>" width="150" height="150"></a></div>
					<div id="small-text-article"><?}?>
						<?=$art->ht_short ?>
						<? if ($art->ispict) {?>
					</div>
					<div id="clear"></div><?}?>
				</div>
			</div>
			<? } ?>

			<? $this->sub('paginator'); ?>


<p>
<img src="<?=TPL?>img/0.gif" id="podpiska-metka" width="1" height="10" alt="">
<h2>Оформление подписки на рассылку новостей, статей и акций</h2>
<p>
Для оформления подписки на рассылку наших новостей и статей, введите в поле ниже свой E-mail.<br>
Вы всегда сможете отказаться от рассылки нажав в письме на ссылку &laquo;Отписаться&raquo;.
<div id="podpiska">
	<form method="post" action="/subscribe">
	<input type="text" class="forma-podpiska" name="email" value="Подписаться на E-mail рассылку" onfocus="if(this.value=='Подписаться на E-mail рассылку') this.value='';" onblur="if(this.value.trim()=='') this.value='Подписаться на E-mail рассылку'">
	<input type="image" class="button" src="<?=TPL?>img/podpiska.png" name="sa" value="Подписаться" title="Подписаться">
	</form>
</div>
<img src="<?=TPL?>img/0.gif" width="1" height="50" alt="">



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
