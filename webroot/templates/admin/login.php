<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?=$setts->tx_inf_shopname ?></title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name="description" content="@@description">
<meta name="keywords" content="@@keywords">
<link rel="stylesheet" href="<?=TPL?>adm/css.css" type="text/css">
<? if (isset($styles) && $styles) { foreach($styles as $style) {?>
<link rel="stylesheet" href="<?=TPL?>adm/<?=$style?>" type="text/css">
<? }} ?>
<? if (isset($jscripts) && $jscripts) { foreach($jscripts as $jscript) {?>
<script type="text/javascript" src="<?=TPL?>adm/scripts/<?=$jscript?>"></script>
<? }} ?>
<!--[if lte IE 6]><script type="text/javascript" src="<?=TPL?>adm/scripts/png-fix.js"></script><![endif]-->
</head>

<body>

<div id="container">

	<div id="logo-ml">
		<a href="http://www.medialine.com.ua/" target="_blank"><img width="400" height="109" src="<?=TPL ?>adm/img/logo-ml.png"></a><br><br>
		Административная система сайта<br>
		<a href="http://<?=$setts->tx_inf_shopurl?>/" target="_blank"><?=$setts->ht_inf_shopurl?></a>
	</div>

	<div id="forma-login">
        <?
        	if (isset($hint)) {
				switch ($hint) {
					case 'bad':
        ?>
        <div id="error">Вы ввели не верный Логин или Пароль.</div>
        <?
					break;
					case 'out':
		?>
		<div id="info">Вы вышли из системы.</div>
		<?
					break;
					case 'inv':
		?>
		
		<?
				}
        	}
        ?>

        <?
        	if (isset($remember) && $remember) {
        ?>
        <form method="POST" action="">
        Логин (e-mail):<br><img src="<?=TPL?>adm/img/0.gif" width=1 height=10><br>
			<div style="padding-right: 16px">
			<input type="text" name="remember" id="remember" class="login" value="">
			</div>
		<img src="<?=TPL?>adm/img/0.gif" width=1 height=30><br>
		<input type="submit" value="Отправить мне пароль" class="big_button">
		<a href="/admin/">Логин / Пароль</a>
        </form>
        <?
        	} else {
        ?>
        <form method="POST" action="/admin">
		Логин (e-mail):<br><img src="<?=TPL?>adm/img/0.gif" width=1 height=10><br>
			<div style="padding-right: 16px">
			<input type="text" name="adm_login" id="login" class="login" value="">
			</div>
		<img src="<?=TPL?>adm/img/0.gif" width=1 height=15><br>
		Пароль:<br><img src="<?=TPL?>adm/img/0.gif" width=1 height=10><br>
			<div style="padding-right: 16px">
			<input type="password" name="adm_passwd" class="login">
			</div>
		<img src="<?=TPL?>adm/img/0.gif" width=1 height=30><br>
		<a href="?remember">Забыли пароль?</a>
		<input type="submit" value="Войти" class="big_button">
        </form>
        <?
        	}
        ?>
	</div>
	<p align=center><img src="<?=TPL?>adm/img/ten-login.png"></p>

</div>

</body>
</html>