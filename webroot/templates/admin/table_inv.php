<html>
<head>
	<style>
		body { background: #EEE; font-family: arial; font-size: 13px; }
		#wrap { width: 800px; margin: 0 auto; }
		#nakl { padding: 20px 25px; background: #FFF; margin-bottom: 30px;
			-webkit-box-shadow: #717171 0px 2px 3px; -moz-box-shadow: #717171 0px 2px 3px; box-shadow: #717171 0px 2px 3px; }
		table { border-collapse: collapse; border: none; font-size: 13px; }
		table.nakl { border-collapse: collapse; border: none; }
		th, td { padding: 2px 4px; vertical-align: top; }
		table.nakl th, table.nakl td { border: 1px solid #000; padding: 2px 4px; vertical-align: top; }
	</style>
</head>
<body>
<div id="wrap">


<? foreach ($data as $order) {?>
<div id="nakl">
<table width="100%">
<tr>
<td width="50%">
<b>Получатель:</b> <?=$order['_html']['name']?><br/>
<b>Телефон:</b> <?=$order['_html']['telephone']?><br/>
<b>Адрес доставки:</b> <?=$order['_html']['region']?> <?=$order['_html']['city']?> <?=$order['_html']['address']?><br/>
<b>Оплата:</b> <?=$order['payment']?><br/>
<b>Дата доставки:</b> _______________________ <br>
<b>Номер заказа:</b> <?=$order['ID']?><br/>
<b>Комментарий:</b> <?=$order['_html']['extra']?><br/>
</td>
<td width="30">&nbsp;</td>
<td align="right">
Поставщик: Интернет-Магазин "Дочки Матери"<br>
ФОП Маншилин А.О. / ИНН  2613410317<br>
Адрес: Донецк, бул. Шахтостроителей 16<br>
Не является плательщиком налога на прибыль на общих основаниях
<br>
<b>Телефоны:</b> (095) 108-03-97, (067) 620-08-90<br>
<b>E-mail:</b> dochkimateridn@mail.ru<br>
<b>Сайт:</b> www.dm.donetsk.ua
</td></tr>
</table>

<p>
<h2 style="text-align:center">Копия чека  от __________</h2>
<p>
<table width="100%" class="nakl">
	<tr>
		<th>№</th>
		<th>Арт. пост.</th>
		<th>Товар</th>
		<th>Ед.</th>
		<th>К-во</th>
		<th>Цена</th>
		<th>Сумма, грн.</th>
	</tr>
	<? $i=0; foreach ($order['items'] as $item) { $i++; ?>
	<tr>
		<td align="center"><?=$i?></td>
		<td align="center"><?=$item['PLU']?></td>
		<td><?=$item['name']?></td>
		<td align="center"><?=$item['measure']?></td>
		<td align="center"><?=$item['count']?></td>
		<td align="center"><?=$item['foprice']?></td>
		<td align="center"><?=$item['fosumm']?></td>
	</tr>
	<?}?>
	<tr>
		<td colspan="6" align="right">Всего сумма заказа:</td>
		<td align="center"><?=$order['fsumm']?></td>
	</tr>
	<tr>
		<td colspan="6" align="right">Скидка:</td>
		<td align="center">__________</td>
	</tr>
	<tr>
		<td colspan="6" align="right">К оплате:</td>
		<td align="center">__________</td>
	</tr>
</table>
<p><b>Всего на сумму: ___________________________________________________ Без НДС.
<br><br>
Сдал(а): ____________________________
<p align="right">Принял(а):  _________________________________</p>

</div>
<?}?>


</div>
</body>
</html>