<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta charset="UTF-8">

<title>Ваша корзина<? if ($title) { ?> | <?=$title?><?}?></title>

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


<? if ($omade) { ?>
<h1>Спасибо за Ваш заказ!</h1>
<h2>Менеджеры нашей компании свяжутся с Вами<br>для его подтверждения в ближайшее время!</h2>
<script language="javascript">setTimeout(function(){window.location.href='/';},5000)</script>

<? } elseif ($cempty) { ?>
<h1>У Вас в корзине пока нет отобранной продукции!</h1>
<p>
Чтобы купить заинтересовавшую вас продукцию, ее нужно добавить в корзину.<br>
Товар помещается в корзину с помощью нажатия кнопки <b>&laquo;Купить&raquo;</b>.<br>
Далее вы можете перейти в корзину для оформления заказа или продолжить выбор других товаров.
<p>
Количество товаров в корзине и их общая стоимость отображается на всех страницах (если корзина не пуста) вверху.
Посмотреть содержимое вашей корзины вы можете в любой момент нажатием иконки и ссылки на общей стоимости продукции.

<h2>Важно!</h2>
<p>
Размещение товара в корзине не обязывает вас покупать этот товар.<br>
В любой момент вы сможете удалить товар из корзины нажатием кнопки <b>&laquo;Удалить&raquo;</b> напротив стоимости товара и выбрать другой товар.
Содержимое корзины сохранится, если вы закроете окно браузера и вернетесь в магазин в следующий раз.
<h2>Хотите изменить количество экземпляров товаров?</h2>
<p>
Напишите нужное количество экземпляров в столбце <b>&laquo;Количество&raquo;</b> напротив нужного товара и нажмите кнопку <b>&laquo;Пересчитать&raquo;</b>.

<? } else { ?>


<? if (isset($errs)) { ?>
<h1>При заполнении формы заказа допущены ошибки!</h1>
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<p><b><font class="err"><?=$msg ?></font></b><br/></p>
<?}}?>

<? } ?>

<h1>Оформление заказа</h1>

<br>
<form method="POST" action="/cart">
<table width=100% class="tablica1">
<tr>
<th>Фотография</th>
<th>Наименование</th>
<th>Цена</th>
<th>Кол-во</th>
<th>Сумма</th>
<th>Операция</th></tr>
<? foreach ($cart as $item) { ?>
<tr>
<td align="center">
	<? if ($item->prod->inherited) { ?>
	<a href="/catalog<?=$item->prod->base->uri ?>"><img src="<?=$item->prod->base->pict_uri_small ?>" width="100" height="100" class="small-foto-tovar-in-cart" title="<?=$item->prod->nq_d_name ?>"></a>
	<? } else { ?>
	<a href="/catalog<?=$item->prod->uri ?>"><img src="<?=$item->prod->pict_uri_small ?>" width="100" height="100" class="small-foto-tovar-in-cart" title="<?=$item->prod->nq_d_name ?>"></a>
	<? } ?>
</td>
<td>
	<br>
	<? if ($item->prod->inherited) { ?>
	<div id="tovar-nazvanie" style="text-align: left;"><a href="/catalog<?=$item->prod->base->uri ?>"><?=$item->prod->ht_d_name ?></a></div>
	<? } else { ?>
	<div id="tovar-nazvanie" style="text-align: left;"><a href="/catalog<?=$item->prod->uri ?>"><?=$item->prod->ht_d_name ?></a></div>
	<? } ?>
	<? if ($item->prod->variant) { ?><div id="tovar-nazvanie" style="text-align: left;"><?=$item->prod->ht_variant ?></div><?}?>
	<? if ($item->prod->code) {?><div id="tovar-kod"><span>Код товара:</span>&nbsp;<?=$item->prod->ht_code ?></div><?}?>
</td>
<td align="center"><div id="tovar-price"><?=$item->ht_full_price ?><? /*<br><span class="dopvalut">(<nobr><?=$item['price2']?></nobr>&nbsp;/ <nobr><?=$item['price3']?></nobr>)</span>*/ ?></div></td>
<td align="center"><?=$frms[$item->pid]['qty']['input'] ?></td>
<td align="center"><div id="tovar-price"><?=$item->ht_full_summ ?><? /*<br><span class="dopvalut">(<nobr><?=$item['summ2']?></nobr>&nbsp;/ <nobr><?=$item['summ3']?></nobr>)</span>*/ ?></div></td>

<td align="center"><a href="/cart?del=<?=$item->pid ?>"><img src="<?=TPL?>img/delete.png" width="16" height="16" alt="Удалить"><br>Удалить</a></td></tr>
<? } ?>
<tr>
<td colspan="2" align="right"><big><b>Общая сумма по заказу:</b></big></td>
<td colspan="4"><div id="tovar-price" style="padding: 10px 20px 10px 20px;"><?=$cs->ht_full_summ ?> <? /*<span class="dopvalut">(<?=$currency2?><?=$summ2?>&nbsp;/&nbsp;<?=$currency3?><?=$summ3?>)</span>*/ ?></div></td></tr>
<tr>
<th colspan="6">
	<input name="recalc" type="submit" class="btn" value="Пересчитать">
	&nbsp;<input type="button" value="Продолжить покупки" class="btn" onClick="history.go(-1);">
</th></tr>
</table>
</form>




<br>
<h2 class="center">Уточняющие данные</h2>

<!--
<p>
Для оформления заказа заполните контактную информацию ниже (Имя и Контактный телефон достаточно).
<p>
В случае если Вы сделали заказ товара, которого сейчас нет в наличии&nbsp;&mdash; Вы оформляете предварительный заказ
как и для обычной покупки, но при этом Ваш заказ попадает на рассмотрение менеджеру и будет сохранен в нашей базе до получения товара на склад.
Как только товар попадает на склад, он автоматически резервируется для Вас, а мы ставим Вас в известность о том, что товар можно оплачивать и получать.
-->
<br><br>

<form method="POST">
<table width=80% class="tablica1">
<tr>
<th colspan="2">Обязательные поля для заказа отмечены: * (звездочкой)</th></tr>
<tr>
<td align="right"><font color="red">*</font>&nbsp;<b><?=$frm['c_name']['label']?>:</b></td>
<td><?=$frm['c_name']['input']?></td></tr>
<tr>
<td align="right"><font color="red">*</font>&nbsp;<b><?=$frm['c_telephone']['label']?>:</b></td>
<td><?=$frm['c_telephone']['input']?><br><font color="red"><small>например: +380623450315</small></font></td></tr>
<tr>
<td align="right"><b><?=$frm['c_email']['label']?>:</b></td>
<td><?=$frm['c_email']['input']?></td></tr>
<tr>
<td align="right"><b><?=$frm['message']['label']?>:</b></td>
<td><?=$frm['message']['input']?></td></tr>

<? /*<tr>
<td align="right"><b><?=$frm['c_country']['label']?>:</b></td>
<td><?=$frm['c_country']['input']?></td></tr>
<tr>
<td align="right"><b><?=$frm['c_city']['label']?>:</b></td>
<td><?=$frm['c_city']['input']?></td></tr>
<tr>
<td align="right"><b><?=$frm['c_region']['label']?>:</b></td>
<td><?=$frm['c_region']['input']?></td></tr>*/ ?>

<tr>
<td align="right"><b><?=$frm['c_address']['label']?>:</b></td>
<td><?=$frm['c_address']['input']?></td></tr>
<tr>
<td align="right"><b><?=$frm['payment_id']['label']?>:</b></td>
<td>
	<?=$frm['payment_id']['input']?>
</td></tr>
<tr>
<td align="right"><b><?=$frm['shipping_id']['label']?>:</b></td>
<td>
	<?=$frm['shipping_id']['input']?>
</td></tr>
<tr>
<td align="right"><b><?=$frm['coupon']['label']?>:</b></td>
<td><?=$frm['coupon']['input']?></td></tr>
<tr>
	<th colspan=2>
		<input type="submit" style="font-weight: bold; font-size: 120%;" class="btn-zakaz" value="Оформить заказ" name="order">
	</th>
</tr>
</table>
</form>
<?}?>


<h3>Приятных покупок :)</h3>


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
