<html>
<head>
	<style>
		table {border-collapse: collapse;}
		th, td {border: 1px solid #000; padding: 2px 4px; vertical-align: top;}
	</style>
</head>
<body>
<table>
	<tr>
		<th>Номер</th>
		<th>Дата</th>
		<th>Заказчик</th>
		<th>E-mail</th>
		<th>Телефон</th>
		<th>Страна</th>
		<th>Город</th>
		<th>Область</th>
		<th>Адрес</th>
		<th>Оплата</th>
		<th>Доставка</th>
		<th>Статус</th>
		<th>Товары</th>
		<th>Сумма</th>
	</tr>
	<? foreach ($data as $order) {?>
	<tr>
		<td><?=$order['ID']?></td>
		<td><?=$order['date']?></td>
		<td><?=$order['_html']['name']?></td>
		<td><?=$order['email']?></td>
		<td><?=$order['_html']['telephone']?></td>
		<td><?=$order['_html']['country']?></td>
		<td><?=$order['_html']['city']?></td>
		<td><?=$order['_html']['region']?></td>
		<td><?=$order['_html']['address']?></td>
		<td><?=$order['payment']?></td>
		<td><?=$order['shipping']?></td>
		<td><?=$order['status']?></td>
		<td>
		<? foreach ($order['items'] as $item) {?>
			<nobr><?=$item['name']?> (код: <?=$item['PLU']?>, штрихкод: <?=$item['barcode']?>, бренд: <?=$item['brand_name']?>) &mdash; <?=$item['foprice']?> <sup><?=$item['count']?></sup></nobr><br/>
		<? }?>
		</td>
		<td><?=$order['fsumm']?></td>
	</tr>
	<?}?>
</table>
</body>
</html>