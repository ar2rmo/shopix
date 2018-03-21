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
		<th>Способ доставки</th>
		<th rowspan="2">Ф.И.О.</th>
		<th>Тел.</th>
		<th rowspan="2">Область<br/>Адрес</th>
		<th>Способ оплаты</th>
		<th>Сумма</th>
	</tr>
	<tr>
		<th>Номер заказа</th>
		<th>Город</th>
		<th colspan="2">Примечания</th>
	</tr>
	<? foreach ($data as $order) {?>
	<tr>
		<td><?=$order['shipping']?></td>
		<td rowspan="2"><?=$order['_html']['name']?></td>
		<td><?=$order['_html']['telephone']?></td>
		<td rowspan="2"><?=$order['_html']['region']?><br/><?=$order['_html']['address']?></td>
		<td><?=$order['payment']?></td>
		<td><?=$order['fsumm']?></td>
	</tr>
	<tr>
		<td><?=$order['ID']?></td>
		<td><?=$order['_html']['city']?></td>
		<td colspan="2"><?=$order['_html']['extra']?></td>
	</tr>
	<?}?>
</table>
</body>
</html>