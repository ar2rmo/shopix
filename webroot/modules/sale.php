<?
require_once CLASSES_PATH.'page.model.php';

class mod_sale extends module_page {
	const mod_tpl='sale';
	
	function body() {
		$page=page::getByUri('sale');
		$this->tpl->page=$page;
		
		if (!is_null($page) && !is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
		if (!is_null($page) && !is_null($page->d_description)) $this->tpl->description=$page->tx_d_description;
		
		$this->add_sub('box_pspecials');
	}
}
?>