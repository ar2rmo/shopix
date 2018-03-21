<p>Товаров в каталоге - <?=$stats->ht_num_all?></p>
<p>Показыаются на сайте - <?=$stats->ht_num_show?></p>
<p>Новинок - <?=$stats->ht_num_new?></p>
<p>Рекомендуемых - <?=$stats->ht_num_recomend?></p>
<p>Акционных - <?=$stats->ht_num_special?></p>
<p></p>
<p>Новостей на сайте - <?=$stats->ht_news?></p>
<p>Статей на сайте - <?=$stats->ht_articles?></p>
<p>Акций на сайте - <?=$stats->ht_specs?></p>
<p></p>
<p>Заказов новых - <?=$stats->ht_ord_new?></p>
<p>Всего заказов - <?=$stats->ht_ord_all?> на сумму <?=currencies::ht_format($stats->ord_summ)?></p>
<p></p>
<p>Подписчиков на рассылку  - <?=$stats->ht_subscribers?></p>
<p><a href="/admin/xmlsitemap" class="right">Сгенерировать карту сайта XML</a></p>
<p><a href="/admin/ymlexport" class="right">Сгенерировать YML файл</a></p>