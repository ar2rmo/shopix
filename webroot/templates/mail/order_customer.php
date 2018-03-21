Здравствуйте, <?=$order->ht_c_name?>!<br/>

Вы сделали заказ продукции в <?=$shopname?><br/><br/>


***<br>
Контактное лицо: <?=$order->ht_c_name?><br/>
Телефон: <?=$order->ht_c_telephone?><br/>
<? if ($order->c_email) {?>E-mail: <a href="mailto:<?=$order->tx_c_email?>"><?=$order->ht_c_email?></a><br/><?}?>
<? if ($order->c_country) {?>Страна: <?=$order->ht_c_country?><br/><?}?>
<? if ($order->c_city) {?>Город: <?=$order->ht_c_city?><br/><?}?>
<? if ($order->c_region) {?>Область: <?=$order->ht_c_region?><br/><?}?>
<? if ($order->c_address) {?>Адрес доставки: <?=$order->ht_c_address?><br/><?}?>
<? if ($order->payment_id) {?>Форма оплаты: <?=$order->ht_payment?><br/><?}?>
<? if ($order->shipping_id) {?>Способ доставки: <?=$order->ht_shipping?><br/><?}?>
<? if ($order->coupon) {?>Секретный код для скидки или номер карты клиента: <?=$order->ht_coupon?><br/><?}?>
<br><br>
***

<br/><br/>

Вы отобрали для покупки:<br/>
<?foreach ($order->col_items as $sitm){?>
- <?=$sitm->ht_p_name ?><? if ($sitm->p_variant) {?> - <?=$sitm->ht_p_variant?><?}?><? if ($sitm->p_code) { ?> (<?=$sitm->ht_p_code ?>)<?}?> &mdash; <?=$sitm->ht_price_currency ?>, <?=$sitm->ht_qty ?> шт.<br/>
<?}?>
<br/>
Общая сумма по заказу: <b><?=$order->ht_summ_currency ?></b>
<br/><br>
<? if ($order->message) {?>Примечания:<br>
<?=$order->ht_message?><br/><?}?>

<br/>
<br/>
<br/>

Спасибо за Ваш заказ.<br/>
Мы свяжемся с Вами для уточнения деталей в ближайшее время.

<br/>
<br/>
<br/>

<?=$shopname?><br/>
<a href="http://<?=$shopurl?>"><?=$shopurl?></a>

<br/>
<br/>

- - - - -<br/>
Если данное письмо попало к Вам случайно, просто проигнорируйте его.

