<?
require_once CLASSES_PATH.'product.model.php';

class mod_box_whatsnew extends module_sub {
	function body() {
		$prods=new col_products();
		$prods->loadRand(array('visible'=>true,'new'=>true),$this->app->setts->num_box_new);
		
		$tpl=new template('boxes/whatsnew');
		$tpl->setts=$this->app->setts;
		$tpl->urib=$this->app->urib_f;
		$tpl->prods=$prods;
		$tpl->output();
	}
}
?>