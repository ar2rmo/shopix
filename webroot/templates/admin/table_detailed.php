<html>
<head>
	<style>
		table {border-collapse: collapse;}
		th, td {border: 1px solid #000; padding: 2px 4px;}
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
		<th>Товар</th>
		<th>Количество</th>
		<th>Код</th>
		<th>Штрихкод</th>
		<th>Бренд</th>
		<th>Цена</th>
	</tr>
	<? foreach ($data as $order) {?>
	<tr>
		<td><nobr><?=$order['o_num']?></nobr></td>
		<td><nobr><?=$order['o_date']?></nobr></td>
		<td><nobr><?=$order['_html']['o_name']?></nobr></td>
		<td><nobr><?=$order['o_email']?></nobr></td>
		<td><nobr><?=$order['_html']['o_telephone']?></nobr></td>
		<td><nobr><?=$order['_html']['o_country']?></nobr></td>
		<td><nobr><?=$order['_html']['o_city']?></nobr></td>
		<td><nobr><?=$order['_html']['o_region']?></nobr></td>
		<td><nobr><?=$order['_html']['o_address']?></nobr></td>
		<td><nobr><?=$order['x_payment']?></nobr></td>
		<td><nobr><?=$order['x_shipping']?></nobr></td>
		<td><nobr><?=$order['x_status']?></nobr></td>
		<td><nobr><?=$order['_html']['i_name']?></nobr></td>
		<td><nobr><?=$order['_html']['i_count']?></nobr></td>
		<td><nobr><?=$order['i_PLU']?></nobr></td>
		<td><nobr><?=$order['i_barcode']?></nobr></td>
		<td><nobr><?=$order['x_brand']?></nobr></td>
		<td><nobr><?=$order['_html']['i_price']?></nobr></td>
	</tr>
	<?}?>
</table>
</body>
</html>