<?
require_once CLASSES_PATH.'article.model.php';

class mod_box_specials extends module_sub {
	function body() {
		$arts=new col_article();
		$arts->loadTop('SPEC',$this->app->setts->num_box_specials);
		
		$tpl=new template('boxes/specials');
		$tpl->urib=$this->app->urib_f;
		$tpl->arts=$arts;
		$tpl->output();
	}
}
?>