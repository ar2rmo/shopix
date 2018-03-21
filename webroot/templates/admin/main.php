<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?=$title ?></title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta name="description" content="<?=$description?>">
<meta name="keywords" content="<?=$keywords?>">
<link rel="stylesheet" href="<?=TPL?>adm/css.css" type="text/css">
<? if (isset($ckeditor)&&$ckeditor || isset($jquery)&&$jquery) { ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<? } ?>
<? if (isset($ckeditor)&&$ckeditor) { ?>
<script type="text/javascript" src="/resources/scripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/resources/scripts/ckeditor/adapters/jquery.js"></script>
<? } ?>
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

	<div id="name-site">
		Административная система сайта &mdash; <a href="http://<?=$setts->tx_inf_shopurl ?>/" target="_blank"><?=$setts->ht_inf_shopurl?></a>
	</div>

	<div id="clear"></div>

	<div id="level1">
		<ul>
		<?
			foreach ($menu as $itm) {
				if ($itm['selected']) {
		?>
			<li class="selected"><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				} else {
		?>
			<li><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				}
			}
		?>
		</ul>
	</div>

	<div id="clear"></div>
    <?
    	if (isset($submenu) && is_array($submenu)) {
    ?>
	<div id="level2">
		<ul>
		<?
			foreach ($submenu as $itm) {
				if ($itm['selected']) {
		?>
			<li class="selected"><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				} else {
		?>
			<li><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				}
			}
		?>
		</ul>
	</div>
	<?
    	}
    ?>
    <?
    	if (isset($submenu2) && is_array($submenu2)) {
    ?>
	<div id="level2">
		<ul>
		<?
			foreach ($submenu2 as $itm) {
				if ($itm['selected']) {
		?>
			<li class="selected"><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				} else {
		?>
			<li><a href="<?=$itm['href']?>"><?=$itm['capt']?></a></li>
		<?
				}
			}
		?>
		</ul>
	</div>
	<?
    	}
    ?>
        <div id="clear"></div>

	<div id="content">

    <? if (isset($content)) echo $content; elseif (isset($sub)) $this->inc('admin/'.$sub); ?>

	</div>

	<div id="bottom">
		Разработка сайтов, CMS, хостинг, домены, SEO-раскрутка &mdash; <a href="http://www.MediaLine.com.ua" target="_blank">"MediaLine"</a><br>
		Письмо в службу поддержки: <a href="mailto: info@medialine.com.ua">info@medialine.com.ua</a>
	</div>

</div>


</body>
</html>
