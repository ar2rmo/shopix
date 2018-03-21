<?
require_once CLASSES_PATH.'category.model.php';

class mod_box_fullcount extends module_sub {
	function body() {
		$rt=category::getRoot();
		echo $rt->ht_vfullcount;
	}
}
?>