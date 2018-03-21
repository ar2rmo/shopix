<? if($del_confirm) { ?>
<div id="dbox">
	Удалить <?=$node_name?>?
	<form name="item" method="post" enctype="multipart/form-data">
		<input type="submit" class= "ok" name="del_yes" value="Да">
		<a href="./struct.htm" class= "cancel">Нет</a>
	</form>
</div>
<?}?>
<?=$tree?>