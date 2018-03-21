<?
require_once CLASSES_PATH.'product.model.php';

class mod_box_pspecials extends module_sub {
	function body() {
		$prods=new col_products();
		$prods->loadRand(array('visible'=>true,'special'=>true),100);
		
		$tpl=new template('boxes/pspecials');
		$tpl->setts=$this->app->setts;
		$tpl->urib=$this->app->urib_f;
		$tpl->prods=$prods;
		$tpl->output();
	}
}
?>