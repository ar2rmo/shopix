<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title>Поиск по сайту<? if ($title) { ?> | <?=$title?><?}?></title>

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


<h1>Поиск по каталогу товаров</h1>
<p>

<? if (!$nos) { ?>

<? if (count($prods)>0) {?>

<h2>Результаты поиска:</h2>

	<div id="sort-kolvo">
		<div id="kolvo-v-razdele">Товаров найдено:&nbsp;<?=$num?></div>
		<div id="clear"></div>
	</div>

<br><br>
<div id="products">
<? $this->sub('prodlist'); ?>
</div>

<? $this->sub('paginator'); ?>

<? } else { ?>
<h2>К сожалению по Вашему запросу ничего не найдено!</h2>

<h3>Попробуйте другие поисковые фразы (не пустые).<br><br>
Используйте для поиска не менее четырех букв в запросе.<br><br>
Поиск работает по &laquo;вхождению&raquo; фразы или ее отрывка в наименовании товара или его кодировке (Артикулу).</h3>


			<br><br>
			<h2 class="rekomend">Рекомендуем!</h2>
			<br>
			<?=$sub_box_recomend ?>


<? } ?>

<? } else { ?>

<h2>Введите поисковый запрос!</h2>

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
