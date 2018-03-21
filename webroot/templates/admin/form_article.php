<? if(isset($del)) { ?>
<div id="dbox">
	<p>Удалить статью &laquo;<?=$name?>&raquo;?</p>
	<form name="item" method="post" action="/admin/aedit/articles">
		<input type="submit" class= "ok" name="delete" value="Да">
		<input type="hidden" name="artn" value="<?=$artn?>">
		<a href="?" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
<form method="post"  enctype="multipart/form-data">
	<input type="submit" name="submt" value="Сохранить" class="save"><br/><br/>
	<table class="tablica1">
		<? if (isset($artn)) { ?>
		<tr>
			<th colspan=2>Статья &laquo;<?=$article['caption']?>&raquo; от <?=$article['date']?></th>
		</tr>
		<? } ?>
		<tr>
			<td><?=$frm['date']['label']?></td>
			<td><?=$frm['date']['input']?></td>
			<td rowspan="3"><? if ($art->ispict) { ?><img src="<?=$art->pict_uri?>" width="100px" height="100px"><?}?></td>
		</tr>
		<tr>
			<td><?=$frm['fshow']['label']?></td>
			<td><?=$frm['fshow']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['mpict']['label']?></td>
			<td><?=$frm['mpict']['input']?><br/><?=$frm['mpict_del']['input']?><?=$frm['mpict_del']['label']?></td>
		</tr>
		<tr>
			<td><?=$frm['caption']['label']?></td>
			<td colspan="2"><?=$frm['caption']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['short']['label']?></td>
			<td colspan="2"><?=$frm['short']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['full']['label']?></td>
			<td colspan="2"><?=$frm['full']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['link']['label']?></td>
			<td colspan="2"><?=$frm['link']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['href']['label']?></td>
			<td colspan="2"><?=$frm['href']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['css']['label']?></td>
			<td colspan="2"><?=$frm['css']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['uri_name']['label']?></td>
			<td colspan="2"><?=$frm['uri_name']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['title']['label']?></td>
			<td colspan="2"><?=$frm['title']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['keywords']['label']?></td>
			<td colspan="2"><?=$frm['keywords']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['description']['label']?></td>
			<td colspan="2"><?=$frm['description']['input']?></td>
		</tr>
		<tr>
			<td colspan=3 style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<? if (isset($artn)) { ?><a href="/admin/aedit/articles/<?=$artn?>?delete" class="delete">Удалить</a><? } ?>
				<a href="/admin/aedit/articles" class="left">Вернуться к статьям</a>
			</td>
		</tr>
	</table>
</form>