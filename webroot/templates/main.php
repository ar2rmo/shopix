<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$title?></title>

<meta name="revisit-after" content="1 days">
<meta name="description" content="<?=$description?>">
<meta name="keywords" content="<?=$keywords?>">
<meta name="viewport" content="width=1280">

<link rel="shortcut icon" href="<?=TPL?>img/favicon.ico">
<link rel="stylesheet" href="<?=TPL?>css.css" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?=TPL?>jqcloud.css" type="text/css">

<script language="javascript" type="text/javascript" src="<?=TPL?>scripts/jquery.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript" src="<?=TPL?>scripts/jquery.nivo.slider.pack1.js"></script>
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

<div id="wrap-banners">
<div id="wrap">
<div id="banners">
	<table width="100%">
	<tr>
	<td width="800">
		<div id="slider0">
		<div id="slider">
		<a href=""><img src="/media/images/sl3.jpg" width="788" height="350"></a>
		<a href=""><img src="/media/images/sl1.jpg" width="788" height="350"></a>
		<a href=""><img src="/media/images/sl2.jpg" width="788" height="350"></a>
		</div>
		</div>
	</td>
	<td width="30">&nbsp;</td>
	<td width="370">
		<div id="banners2small">
			<a href=""><img src="/media/images/small1.jpg" width="360" height="100"></a>
			<a href=""><img src="/media/images/small2.jpg" width="360" height="100"></a>
			<a href=""><img src="/media/images/small3.jpg" width="360" height="100"></a>
		</div>
	</td></tr>
	</table>
</div>
</div>
</div>


<div id="wrap">
	<div id="middle">
		<div id="container">
			<div id="content-page-left">

			<?=$page->ht_text ?>

<p>
<br>
<strong>Поделитесь ссылкой на сайт с друзьями:</strong>
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
<div class="pluso" data-background="transparent" data-options="medium,round,line,horizontal,counter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir,email,print"></div>


			<br>
			<br>

			<div id="home-news">
			<h2 class="harticles">Акции</h2>
			<ol class="home-news">
				<?=$sub_box_specials?>
			</ol>
			</div>


			<h2 class="rekomend">Рекомендуем!</h2>
			<br><br>
			<?=$sub_box_recomend ?>


			<br>
			<div id="home-news">
			<h2 class="harticles">Статьи</h2>
			<ol class="home-news">
				<?=$sub_box_articles?>
			</ol>
			<div id="arhive-news"><a href="/articles">Читать все статьи</a></div>
			</div>


			<br>
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
