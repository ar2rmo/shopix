<? if ($form) { ?>
<div id="dbox">
	<form name="editor" method="post">
		<p><?=$min['lable']?>
		<?=$min['cont']?>
		<?=$max['lable']?>
		<?=$max['cont']?></p>
		<input type="submit" name="<?=$submit?>" class="save" value="Сохранить">
		<a href="./" class="cancel">Отмена</a>
	</form>
</div>
<? }
elseif ($conf) { ?>
<div id="dbox">
	<form name="editor" method="post">
		<p>Вы действительно хотите удалить запись?</p>
		<input type="submit" name="<?=$submit?>" class="ok" value="Удалить">
		<a href="./" class="cancel">Отмена</a>
	</form>
</div>
<? } ?>
<?=$table?>