<?
require_once CLASSES_PATH.'menu.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_page extends module_page {
	const mod_tpl='page';
	
	function body() {
		$page=page::getByUri($this->src->uri->uri_2,$this->src->uri->uri_3);
		if (is_null($page)||$page->mref) {
			$this->app->err404();
			$this->abort();
		} else {
			if ($page->pempty) {
				$mrel=menu::getFirstChild($page);
				if (is_null($mrel)) {
					$this->tpl->page=$page;
				} else {
					$this->app->http->relocate($mrel->uri);
					$this->abort();
				}
			} else {
				$this->tpl->page=$page;
			}
		}
	}
}
?>