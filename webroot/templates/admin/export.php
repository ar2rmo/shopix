<form method="post" action="/admin/export">
	<? foreach ($rows as $row) { ?>
	<a href="/admin/export?getfile=<?=urlencode($row['filename'])?>" target="_blank"><?=$row['dt']->format('d.m.Y H:i:s')?></a><br/>
	<? } ?>
	<br/><br/>
	<input type="submit" class= "add" name="submt" value="Выгрузить">
</form>