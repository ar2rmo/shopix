<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="<?=TPL?>css.css" type="text/css">
</head>
<body>
<? /* #CMP= */ ?>
<? if (count($prods)>0) { ?>
<table class="tablica-compare">
	<tr>
		<th>Фото</th>
		<th>Код</th>
		<th>Модель</th>
		<? foreach ($cs as $c) { ?>
		<th class="<?=$c->nq_icode ?>" title="<?=$c->ht_name ?>"><?=$c->ht_name ?></th>
		<? } ?>
		<th>Цена</th>
		<th>Действия</th>
	</tr>
	<? foreach ($prods as $p) { ?>
	<tr>
		<td><? $pi=$p->inherited?$p->parent:$p ?><img src="<?=$pi->pict_uri_small ?>" width="<?=$setts->img_small_width ?>" height="<?=$setts->img_small_height ?>" title="<?=$pi->nq_d_name ?><?if ($pi->price) {?> (<?=$pi->ht_price_currency?>)<?}?>" alt="<?=$pi->nq_d_name ?>"></td>
		<td><a href="<?=$p->inherited?$p->parent->uri:$p->uri ?>"><?=$p->ht_code ?></a></td>
		<td><?=$p->ht_variant ?></td>
		<? foreach ($cs as $c) { $sv=$p->specs_values_all->get_by_icode($c->icode);  ?>
		<? if (is_null($sv)) { ?>
		<td class="<?=$c->nq_icode ?>">-</td>
		<? } else { ?>
		<td class="<?=$c->nq_icode ?>"><?=$sv->xvalue->ht_v ?></td>
		<? } ?>
		<? } ?>
		<td><?=$p->ht_full_price ?></td>
		<td><a href="/compare?del=<?=$p->id ?>">Удалить</a></td>
	</tr>
	<? } ?>
</table>

<? } ?>
</body>
</html>