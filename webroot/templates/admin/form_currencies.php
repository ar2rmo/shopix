<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
<form name="settings" method="post">
	<table class="tablica1">
		<tr>
			<td><?=$frm['curr_base']['label']?></td>
			<td><?=$frm['curr_base']['input']?></td>
			<td><?=$frm['curr_base_show']['input']?> <?=$frm['curr_base_show']['label']?></td>
		</tr>
		<tr>
			<td><?=$frm['curr_1']['label']?></td>
			<td><?=$frm['curr_1']['input']?></td>
			<td><?=$frm['curr_1_ratio']['input']?> <?=$frm['curr_1_ratio_r']['input']?> <?=$frm['curr_1_ratio_r']['label']?></td>
		</tr>
		<tr>
			<td><?=$frm['curr_2']['label']?></td>
			<td><?=$frm['curr_2']['input']?></td>
			<td><?=$frm['curr_2_ratio']['input']?> <?=$frm['curr_2_ratio_r']['input']?> <?=$frm['curr_2_ratio_r']['label']?></td>
		</tr>
		<tr>
			<td colspan=3 style="text-align: center;"><input type="submit" name="submt" value="Сохранить" class="save"></td>
		</tr>
	</table>
</form>