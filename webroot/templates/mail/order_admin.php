Поступил новый заказ!
<br/><br/>
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
<br/>
Товары:<br/>
<?foreach ($order->col_items as $sitm){?>
- <?=$sitm->ht_p_name ?><? if ($sitm->p_variant) {?> - <?=$sitm->ht_p_variant?><?}?><? if ($sitm->p_code) { ?> (<?=$sitm->ht_p_code ?>)<?}?> &mdash; <?=$sitm->ht_price_currency ?>, <?=$sitm->ht_qty ?> шт.<br/>
<?}?>
<br/><br>
Общая сумма по заказу: <?=$order->ht_summ_currency ?>
<br/><br>
<? if ($order->message) {?>Примечания: <?=$order->ht_message?><br/><?}?>

<br/>
<br/>
<?=$shopname?><br/>
<a href="http://<?=$shopurl?>"><?=$shopurl?></a>
