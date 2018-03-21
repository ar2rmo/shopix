<? if (isset($sv_msg)) { ?>
<? if ($sv_msg=='PW') { ?><p>Удалено <?=$sv_cnt ?> изображений</p><? } ?>
<? } ?>
<form method="POST">
	<input type="submit" name="wipe_pict" value="Очистить картинки">
</form>