<? if (isset($del_cat) && !is_null($del_cat)) { ?>
<div id="dbox">
	Удалить <strong><?=$del_cat->ht_name ?></strong>?
	<form name="fdel" method="post" enctype="multipart/form-data">
		<input type="submit" class= "ok" name="del_conf" value="Да">
		<a href="/admin/catalog" class= "cancel">Нет</a>
	</form>
</div>
<? } ?>
<div id="admin_tree">
<div class="tree">
	<div class="control">
		<a href="/admin/catalog?addchild" class="add">Добавить подраздел</a>
	</div>
	<span>Каталог</span>
</div>
<? foreach($tree as $cat){ ?>
	<div class="tree" style="margin-left:<?=($cat->level-1)*35?>px;">
		<div class="control">
			<a href="/admin/catalog<?=$cat->uri ?>" class="edit">Редактировать</a>
			<a href="/admin/catalog<?=$cat->uri ?>?delete" class="delete">Удалить</a>
			<a href="/admin/catalog<?=$cat->uri ?>?addchild" class="add">Добавить подраздел</a>
		</div>
		<a href="/admin/catalog<?=$cat->uri ?>?move_up"><img src="<?=TPL?>adm/buttons/arrow_up.png"></a>
		<a href="/admin/catalog<?=$cat->uri ?>?move_down"><img src="<?=TPL?>adm/buttons/arrow_down.png"></a>
		<a href="/admin/products<?=$cat->uri ?>" class="name level<?=$cat->level+1 ?>"><?=$cat->ht_name ?>&nbsp;(<?=$cat->count ?>&nbsp;/&nbsp;<small><?=$cat->fullcount ?></small>)</a>
	</div>
<? } ?>
</div>

