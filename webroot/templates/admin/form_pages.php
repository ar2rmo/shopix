<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
<form name="page" method="post">
	<? if (isset($del_conf)) { ?>
	<div id="dbox">
		<p>Вы действительно хотите удалить страницу?</p>
		<input type="submit" name="del_conf" class="ok" value="Удалить">
		<a href="?" class="cancel">Отмена</a>
	</div>
	<? } ?>
	<? if ($page->id) { ?>
	<a href="/admin/pages" class="add">Добавить</a>
	<? if (!$page->ro) { ?>
	<a href="?delete" class="delete">Удалить</a>
	<? }} ?>
	<p>
	<table class="tablica1">
		<tr>
			<td><?=$frm['caption']['label']?></td>
			<td>
				<?=$frm['caption']['input']?>&nbsp;
				<input type="submit" name="submt" value="Сохранить" class="save">
			</td>
		</tr>
		<tr>
			<td><?=$frm['title']['label']?></td>
			<td><?=$frm['title']['input']?></td>
		</tr>
		<? if (!$page->ro) { ?>
		<tr>
			<td><?=$frm['uri_name']['label']?></td>
			<td><?=$frm['uri_name']['input']?></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan=2><?=$frm['text']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['keywords']['label']?></td>
			<td><?=$frm['keywords']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['description']['label']?></td>
			<td><?=$frm['description']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['css']['label']?></td>
			<td><?=$frm['css']['input']?></td>
		</tr>
		<? if (!$page->ro) { ?>
		<tr>
			<td><?=$frm['pid']['label']?></td>
			<td><?=$frm['pid']['input']?></td>
		</tr>
		<? } ?>
		<tr>
			<td><?=$frm['mnu']['label']?></td>
			<td><?=$frm['mnu']['input']?></td>
		</tr>
		<? if (!$page->ro) { ?>
		<tr>
			<td><?=$frm['mref']['label']?> (Новости - news, Статьи - articles,<br/> Акции - specials,<br/> Каталог - catalog, Обратная связь - feedback,<br/> Прайс-лист - price)</td>
			<td><?=$frm['mref']['input']?></td>
		</tr>
		<? } ?>
		<tr>
			<td><?=$frm['after']['label']?></td>
			<td><?=$frm['after']['input']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;"><input type="submit" name="submt" value="Сохранить" class="save"></td>
		</tr>
	</table>
</form>