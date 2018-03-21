<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';

class mod_products extends module_page {
	const mod_tpl='products';
	
	function body() {
		$pg=new paginator($this->src,300);
		
		$col=new col_categories;
		$col->loadAllTreeVProducts($pg);
		$this->tpl->items=$col;
		
		$this->tpl->pages=$pg->get_parray();
	}
}
?>