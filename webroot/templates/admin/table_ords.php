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
		<th>Код товара</th>
		<th>К-во</th>
		<th>Наименование</th>
		<th>Производитель</th>
		<th>Цена</th>
	</tr>
	<? foreach ($data as $item) {?>
	<tr>
		<td><?=$item['PLU']?></td>
		<td><?=$item['_html']['count']?></td>
		<td><?=$item['name']?></td>
		<td><?=$item['brand']?></td>
		<td><?=$item['_html']['price']?></td>
	</tr>
	<?}?>
</table>
</body>
</html>