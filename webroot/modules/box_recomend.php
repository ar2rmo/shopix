<?
require_once CLASSES_PATH.'product.model.php';

class mod_box_recomend extends module_sub {
	function body() {
		$prods=new col_products();
		$prods->loadRand(array('visible'=>true,'recomend'=>true),$this->app->setts->num_box_recomend);
		
		$tpl=new template('boxes/recomend');
		$tpl->setts=$this->app->setts;
		$tpl->urib=$this->app->urib_f;
		$tpl->prods=$prods;
		$tpl->output();
	}
}
?>