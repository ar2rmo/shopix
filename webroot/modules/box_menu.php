<?
require_once CLASSES_PATH.'menu.model.php';

class mod_box_menu extends module_sub {
	function body() {
		$mnu=new col_menu;
		$mnu->loadLev1((is_null($this->src->uri->uri_1)||($this->src->uri->uri_1==''))?'main':(($this->src->uri->uri_1=='page')?$this->src->uri->uri_2:$this->src->uri->uri_1));
		
		$tpl=new template('boxes/menu');
		$tpl->urib=$this->app->urib_f;
		$tpl->mnu=$mnu;
		$tpl->output();
	}
}
?>