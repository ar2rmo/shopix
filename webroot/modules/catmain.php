<?
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_catmain extends module_page {
	const mod_hidden=true;
	const mod_tpl='catmain';
	
	function body() {
		$page=page::getByUri('catalog');
		$this->tpl->page=$page;
		if (!is_null($page)) {
			if (!is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
			if (!is_null($page->d_description)) $this->tpl->description=$page->tx_d_description;
		}
		
		/*$prods=new col_products();
		$prods->loadRand(array('visible'=>true,'new'=>true),$this->app->setts->num_box_new);
		$this->tpl->whatsnew=$prods;*/
	}
}
?>