<?
require_once CLASSES_PATH.'product.model.php';

class mod_box_random2 extends module_sub {
	function body() {
		$prods=new col_products();
		$prods->loadRand(array('visible'=>true),$this->app->setts->num_box_rand);
		
		$tpl=new template('boxes/random2');
		$tpl->setts=$this->app->setts;
		$tpl->urib=$this->app->urib_f;
		$tpl->prods=$prods;
		$tpl->output();
	}
}
?>