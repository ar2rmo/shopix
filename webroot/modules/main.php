<?
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_main extends module_page {
	const mod_tpl='main';
	
	function body() {
		$page=page::getByUri('main');
		$this->tpl->page=$page;
		
		$this->tpl->title=$page->tx_d_title.($this->app->setts->inf_nameintitle?(' | '.$this->app->setts->inf_shopname):'');
		if (!is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
		if (!is_null($page->d_description)) $this->tpl->description=str_replace('"','\'',strip_tags($page->tx_d_description));
		
		$prods=new col_products();
		$prods->loadRand(array('visible'=>true,'new'=>true),$this->app->setts->num_box_new);
		$this->tpl->prods=$prods;
	}
}
?>