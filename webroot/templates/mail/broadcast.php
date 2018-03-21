<body style="font-family: Arial; font-size: 13px; color: #333; ">

<img src="http://<?=$baseurl?>/resources/img/logo.png">

<p>
<h3><a href="http://<?=$baseurl?>/"><?=$shopname?></a> - Новостная рассылка</h3>


<? if (count($specials)>0) {?>
<h3>Акции</h3>
<? foreach ($specials as $itm) { ?>
<big><b><a href="http://<?=$baseurl?>/specials/<?=$itm->uid?>"><?=$itm->ht_caption?></a></b></big><br>
<small><?=$itm->ht_date?></small>
<br><br>
<? if ($itm->ispict) {?><a href="http://<?=$baseurl?>/specials/<?=$itm->uid?>"><img src="http://<?=$baseurl?><?=$itm->pict_uri?>" width="100" height="100"></a><br><br><?}?>
<?=$itm->ht_short?>
<br><br>
<? }} ?>

<? if (count($news)>0) {?>
<h3>Новости</h3>
<? foreach ($news as $itm) { ?>
<big><b><a href="http://<?=$baseurl?>/news/<?=$itm->uid?>"><?=$itm->ht_caption?></a></b></big><br>
<small><?=$itm->ht_date?></small>
<br><br>
<? if ($itm->ispict) {?><a href="http://<?=$baseurl?>/news/<?=$itm->uid?>"><img src="http://<?=$baseurl?><?=$itm->pict_uri?>" width="100" height="100"></a><br><br><?}?>
<?=$itm->ht_short?>
<br><br>
<? }} ?>


<? if (count($articles)>0) {?>
<h3>Статьи</h3>
<? foreach ($articles as $itm) { ?>
<big><b><a href="http://<?=$baseurl?>/articles/<?=$itm->uid?>"><?=$itm->ht_caption?></a></b></big><br>
<small><?=$itm->ht_date?></small>
<br><br>
<? if ($itm->ispict) {?><a href="http://<?=$baseurl?>/articles/<?=$itm->uid?>"><img src="http://<?=$baseurl?><?=$itm->pict_uri?>" width="100" height="100"></a><br><br><?}?>
<?=$itm->ht_short?>
<? }} ?>

<br><br><br><br>
***<br>
Вы являетсь подписчиком новостной рассылки.<br>
Если Вы желаете отписаться от рассылки, нажмите <a href="<?=$unsubscr?>">сюда</a>.
<br><br>
<b><?=$shopname?>: <a href="http://<?=$baseurl?>/"><?=$baseurl?></a></b>
<br><br>

</body>
