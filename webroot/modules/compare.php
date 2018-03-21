<?
// #CMP=

require_once CLASSES_PATH.'compare.php';

class mod_compare extends module_page {
	const mod_tpl='compare';
	
	function body() {
		if (!is_null($this->src->get->del)) {
			compare::del_item($this->src->get->del);
			$this->app->http->relocate('/compare');
			$this->abort();
			return;
		}
		
		$arr=null;
		$sl=$this->src->get->list;
		if (is_null($sl)) {
			$this->app->http->relocate('/compare?list='.implode(',',compare::get_array()));
			$this->abort();
		} else {
			$arr=explode(',',$sl);
			
			$col=compare::get_products($arr);
			$col_cs=new col_spec_classes();
			$col_cs->loadByProducts($col);
			
			$this->tpl->prods=$col;
			$this->tpl->cs=$col_cs;
		}
	}
}
?>