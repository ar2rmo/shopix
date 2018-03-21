<?
require_once CLASSES_PATH.'refbk.model.php';

class mod_box_brands extends module_sub {
	function body() {
		$brands=new col_refbk;
		$brands->LoadBrands();
		
		$tpl=new template('boxes/brands');
		$tpl->urib=$this->app->urib_f;
		$tpl->brands=$brands;
		$tpl->output();
	}
}
?>