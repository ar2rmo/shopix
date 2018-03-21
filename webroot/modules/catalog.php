<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_catalog extends module {
	function body() {
		$uris=$this->src->uri->get_all();
		unset($uris['uri_1']);
		$uri='/'.implode('/',$uris);
		
		if ($uri=='/') {
			$this->app->run('catmain');
		} else {
			$cat=category::getByURI($uri);
			if (is_null($cat)) {
				$chld=defined('INH_VARPAGE')&&INH_VARPAGE;
				$prod=product::getByURI($uri,true,!$chld);
				if (is_null($prod)) {
					$this->app->err404();
				} else {
					if ($uri!=$prod->uri) {
						$this->app->http->relocate($this->app->urib_f->bo($prod),true);
					}
					$this->app->run('product',$prod);
				}
			} else {
				if ($uri!=$cat->uri) {
					$this->app->http->relocate($this->app->urib_f->bo($cat),true);
				}
				$this->app->run('category',$cat);
			}
		}
	}
}
?>