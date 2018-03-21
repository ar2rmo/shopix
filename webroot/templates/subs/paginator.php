<? if (count($pages)>0) { ?>
<div class="pagination">
<? $eg=isset($gets)?$this->pgets($gets,'&'):''; ?>
<? $eg2=isset($gets)?$this->pgets($gets,'?'):''; ?>
<? foreach ($pages as $p=>$pt) { ?>
	<? switch ($pt) { case 'page': if ($p==1) { ?>
	<a href="<?=($eg2=='')?$baseurl:$eg2 ?>"><?=$p ?></a>
	<? } else { ?>
	<a href="?p=<?=$p ?><?=$eg ?>"><?=$p ?></a>
	<? } break; case 'current': ?>
	<span class="current"><?=$p ?></span>
	<? break; case 'stub': ?>
	...
	<? } ?>
<? } ?>
</div>
<? } ?>