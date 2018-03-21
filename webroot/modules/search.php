<?
require_once CLASSES_PATH.'product.model.php';

class mod_search extends module_page {
	const mod_tpl='search';
	
	function body() {
		$pg=new paginator($this->src,$this->app->setts->num_onpage_prod);
		
		$sa='';
		if ($this->src->get->def('sa')) $sa=$this->src->get->search;
		$this->tpl->srch=htmlspecialchars($sa);
		
		if ($sa=='') {
			$this->tpl->nos=true;
			$this->tpl->num=0;
		} else {
			$this->tpl->nos=false;
			
			$prods=new col_products();
			$prods->loadBySearch($pg,$sa);
			
			$this->tpl->num=$pg->get_items();
			$this->tpl->pages=$pg->get_parray();
			
			$this->tpl->prods=$prods;
		}
	}
}
?>