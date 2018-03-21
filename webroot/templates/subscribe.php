<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title>Подписка на рассылку<? if ($title) { ?> | <?=$title?><?}?></title>

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


<h1>Подписка на рассылку по E-mail (Новости и Статьи)</h1>


			<? if (isset($success)&&$success) { ?>
			<script language="javascript">setTimeout(function(){window.location.href='/';},5000)</script>
			<h3>Ваш E-mail успешно добавлен в нашу базу для рассылки новостей!</h3>
			<? } elseif (isset($unsuc)&&$unsuc) { ?>
			<script language="javascript">setTimeout(function(){window.location.href='/';},5000)</script>
			<h3>Ваш E-mail успешно удален из нашей базы рассылки!</h3>
			<? } elseif (isset($already)&&$already) { ?>
			<script language="javascript">setTimeout(function(){window.history.back();},5000)</script>
			<h3>Введеный Вами E-mail адрес уже участвует в рассылке!</h3>
			<? } else { ?>
			<? if (isset($errs)) { ?>
			<div id="error">
			<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
			<h3><?=$msg?><br>Повторите ввод!</h3>
			<?}}?>
			</div>
			<?}?>
			<script language="javascript">setTimeout(function(){window.history.back();},5000)</script>
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
