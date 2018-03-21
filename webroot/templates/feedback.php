<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title><?=$page->tx_caption?><? if ($title) { ?> | <?=$title?><?}?></title>

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


<? if ($conf) { ?>
<h1>Ваше сообщение отправлено!<br><br>Наши менеджеры свяжутся с Вами в ближайшее время!<br><br>Спасибо!</h1>

<script language="javascript">setTimeout(function(){window.location.href='/';},5000)</script>

<? } else { ?>

<? if (isset($errs)) { ?>

<h1>Допущены ошибки при заполнении контактной формы!</h1>

<ul>
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<li><b class="err"><?=$msg?>!</b><br/>
<? }}?>
</ul>

<br><br>
<? } ?>


<h1><?=$page->ht_caption?></h1>



<? if (!is_null($page)) { ?>
	<?=$page->ht_text?>
<? } ?>
	
<br>
<br>
<h2 class="center">Задайте свой вопрос:</h2>
<form method="POST" action="/feedback">
<table class="tablica1feedback" width="70%">
<tr>
	<th>
	<?=$frm['name']['label']?><br>
	<?=$frm['name']['input']?>
<br><br>
	<?=$frm['email']['label']?><br>
	<?=$frm['email']['input']?>
<br><br>
	<?=$frm['message']['label']?><br>
	<?=$frm['message']['input']?>
<br><br>
	<?=$frm['captcha']['label']?><br>
	<?=$frm['captcha_img']['input']?><br/><br/><?=$frm['captcha']['input']?>
</th>
</tr>
<tr>
	<td colspan="2" align="center"><input type="submit" class="btn" name="feedback" value="Отправить"></td>
</tr>
</table>
</form>

<?}?>


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
