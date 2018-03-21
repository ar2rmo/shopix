<?
// #TMN=

require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'statistics.model.php';

class mod_box_catalog1 extends module_sub {
	function body() {
		if ($this->src->uri->uri_1=='catalog') {
			$uris=$this->src->uri->get_all();
			unset($uris['uri_1']);
			$uri='/'.implode('/',$uris);
			
			$cat=category::getByURI($uri);
			if (is_null($cat)) {
				$puri=array_pop($uris);
				$uri='/'.implode('/',$uris);
			}
			
			$cats=new col_categories;
			$cats->loadAllTree($uri,true,2);
		} else {
			$uri=null;
			$cats=new col_categories;
			$cats->loadAllTree(null,true,2);
		}
		
		$tpl=new template('boxes/catalog1');
		$tpl->urib=$this->app->urib_f;
		$tpl->cats=$cats;
		$tpl->uri=$uri;
		$tpl->output();
	}
}
?>