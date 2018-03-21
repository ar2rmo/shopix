<?
require_once CLASSES_PATH.'menu.model.php';

class mod_box_menu2 extends module_sub {
	function body() {
		$mnu=new col_menu;
		$mnu->loadLev2($this->src->uri->uri_2,$this->src->uri->uri_3);
		
		$tpl=new template('boxes/menu2');
		$tpl->urib=$this->app->urib_f;
		$tpl->mnu=$mnu;
		$tpl->output();
	}
}
?>