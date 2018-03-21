<? if (isset($errs)) { ?>
<div id="error">
<? foreach ($errs as $err) { foreach ($err['msg'] as $msg) { ?>
<?=$msg ?><br/>
<?}}?>
</div>
<?}?>
	<table class="tablica1">
		<tr>
			<th colspan=3><?=$prod->ht_fullname ?></th>
		</tr>
		<tr>
			<td colspan=2>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save">*/?>
				<a href="#" class="save" onclick="document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->base->uri ?>?tab=modifications" class="left">Вернуться к товару</a>
			</td>
			<td style="text-align: right">
				<?=$catsel ?>
			</td>
		</tr>
	</table>
	<br/>
	<form name="item" id="item_form" method="post"  enctype="multipart/form-data"><input type="hidden" name="submt" value="submt" />
		<table class="tablica1">
			<tr>
				<td><?=$frm['variant']['label']?></td>
				<td><?=$frm['variant']['input']?><br/><small>Например: Красная</small></td>
			</tr>
			<tr>
				<td><?=$frm['code']['label']?><br/><?=$frm_inh['inh_code']['input']?><?=$frm_inh['inh_code']['label']?></td>
				<td><?=$frm['code']['input']?></td>
			</tr>
			<tr>
				<td><?=$frm['price']['label']?>&nbsp;/&nbsp;<?=$frm['oprice']['label']?><br/><?=$frm_inh['inh_price']['input']?><?=$frm_inh['inh_price']['label']?></td>
				<td><?=$frm['price']['input']?>&nbsp;/&nbsp;<?=$frm['oprice']['input']?></td>
			</tr>
			<tr>
				<td><?=$frm['avail_id']['label']?>&nbsp;/&nbsp;<?=$frm['avail_num']['label']?></td>
				<td><?=$frm['avail_id']['input']?>&nbsp;/&nbsp;<?=$frm['avail_num']['input']?>&nbsp;/&nbsp;<?=$frm['forsale']['input']?><?=$frm['forsale']['label']?></td>
			</tr>
			<tr>
				<td><?=$frm['fshow']['label']?></td>
				<td><?=$frm['fshow']['input']?></td>
			</tr>
		</table>		
	</form>
	<script type="text/javascript">
		$(function(){
			var checker=check_inherit("shp",{
				price:{check:"#inh_price",edit:"#price"},
				code:{check:"#inh_code",edit:"#code"},
			})
			checker.editBegin=function(){
				$(this).parent().addClass("ih-self").removeClass("ih-parent");
			};
			checker.editParentSet=function(){
				$(this).parent().addClass("ih-parent").removeClass("ih-self");
			};
			checker.editRestore=function(){
				$(this).parent().addClass("ih-self").removeClass("ih-parent");
			};
		})
	</script>
	<br/>
	<table class="tablica1">
		<tr>
			<td colspan=2>
				<a href="#" class="save" onclick="document.getElementById('item_form').submit(); return false;">Сохранить</a>
				<?/*<input type="submit" name="submt" value="Сохранить" class="save" onclick="document.getElementById('item_form').submit(); return false;">*/?>
				<?/*<input type="submit" name="item_del" value="Удалить" class="delete">*/?>
				<a href="/admin/products<?=$prod->base->uri ?>" class="left">Вернуться к товару</a>
			</td>
		</tr>
	</table>

