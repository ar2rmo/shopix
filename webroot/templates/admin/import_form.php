	<form method="post" action="/admin/import" enctype="multipart/form-data">
		<?=$frm['file']['label'] ?>: <?=$frm['file']['input'] ?><br/>
		<? /*<?=$frm['method']['label'] ?>: <?=$frm['method']['input'] ?><br/>*/ ?>
		<input type="submit" class= "ok" name="submt" value="Импорт">
	</form>