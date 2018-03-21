<?
require_once CLASSES_PATH.'article.model.php';

class mod_box_news extends module_sub {
	function body() {
		$arts=new col_article();
		$arts->loadTop('NEWS',$this->app->setts->num_box_news);
		
		$tpl=new template('boxes/news');
		$tpl->urib=$this->app->urib_f;
		$tpl->arts=$arts;
		$tpl->output();
	}
}
?>