<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_price extends module_page {
	const mod_tpl='price';
	
	function body() {
		$page=page::getByUri('price');
		$this->tpl->page=$page;
		
		if (!is_null($page)) {
			$this->tpl->title=$page->tx_d_title.($this->app->setts->inf_nameintitle?(' | '.$this->app->setts->inf_shopname):'');
			if (!is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
			if (!is_null($page->d_description)) $this->tpl->description=str_replace('"','\'',strip_tags($page->tx_d_description));
		}		
		
		$pg=new paginator($this->src,500);
		
		$col=new col_categories;
		$col->loadAllTreeVProducts($pg);
		$this->tpl->items=$col;
		
		$this->tpl->pages=$pg->get_parray();
	}
}
?>