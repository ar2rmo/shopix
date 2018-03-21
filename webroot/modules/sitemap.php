<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'menu.model.php';
require_once CLASSES_PATH.'article.model.php';


class mod_sitemap extends module_page {
	const mod_tpl='sitemap';
	
	function body() {
		$cats=new col_categories;
		$cats->loadAllTree();
		$this->tpl->cats=$cats;
		
		$menu=new col_menu;
		$menu->loadTree();
		$this->tpl->menu=$menu;
		
		$arts=new col_article();
		$arts->loadAll(null,'ARTI');
		$this->tpl->arts=$arts;
		
		$news=new col_article();
		$news->loadAll(null,'NEWS');
		$this->tpl->news=$news;
		
		$specs=new col_article();
		$specs->loadAll(null,'SPEC');
		$this->tpl->specs=$specs;
	}
}
?>