<?
// #CMP=

require_once CLASSES_PATH.'compare.php';

class mod_box_compare extends module_sub {
	function body() {
		$col=compare::get_products();
		
		$tpl=new template('boxes/compare');
		$tpl->urib=$this->app->urib_f;
		
		$l=compare::get_list();
		$tpl->list=implode(',',$l->get_array());
		
		$tpl->prods=$col;
		$tpl->output();
	}
}
?>