<?
require_once CLASSES_PATH.'article.model.php';

class mod_box_articles extends module_sub {
	function body() {
		$arts=new col_article();
		$arts->loadRand('ARTI',$this->app->setts->num_box_articles);
		
		$tpl=new template('boxes/articles');
		$tpl->urib=$this->app->urib_f;
		$tpl->arts=$arts;
		$tpl->output();
	}
}
?>