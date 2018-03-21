<?
class mod_order extends module_page {
	const mod_tpl='order';
	
	function body() {
		/*$page=page::getByUri('order');
		$this->tpl->page=$page;
		
		if (!is_null($page) && !is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
		if (!is_null($page) && !is_null($page->d_description)) $this->tpl->description=$page->tx_d_description;
		*/
		
		$ord=order::get_order_by_id((int)$this->src->uri->uri_2);
		if (is_null($ord)) {
			$this->app->err404();
			$this->abort();
		} else {
			
		}
	}
}
?>