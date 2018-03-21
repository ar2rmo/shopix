<a href="/admin/admusers" class="left">Вернуться к списку</a>
<br/><br/>
<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
<form name="settings" method="post">
	<table class="tablica1" border=1>
		<tr>
			<td><?=$frm['login']['label']?></td>
			<td><?=$frm['login']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['name']['label']?></td>
			<td><?=$frm['name']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['pword']['label']?></td>
			<td><?=$frm['pword']['input'][0]?><br/><?=$frm['pword']['input'][1]?></td>
		</tr>
		<tr>
			<td><?=$frm['role']['label']?></td>
			<td><?=$frm['role']['input']?></td>
		</tr>
		<tr>
			<td><?=$frm['banned']['label']?></td>
			<td><?=$frm['banned']['input']?></td>
		</tr>
		<tr>
			<td colspan=2 style="text-align: center;">
				<input type="submit" name="submt" value="Сохранить" class="save">
				<a href="/admin/admusers" class="left">Вернуться к списку</a>
			</td>
		</tr>
	</table>
</form>