<?
require_once CLASSES_PATH.'subscriber.model.php';

class mod_admin_subscribers extends module_admin {
	private $akind;
	private $auri;
	
	function body() {
		$this->mnu->setcurrent('articles');
		$this->mnu->setcurrentsub('subscribers');
		
		if (is_null($this->src->uri->uri_3)) {
			$this->show_list();
		} else {
			$scr=subscriber::get_by_id($this->src->uri->uri_3);
			if (is_null($scr)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$scr->db_delete();
						$this->app->http->relocate('/admin/subscribers');
						$this->abort();
					} else {
						$this->show_list($scr);
					}
				} else {
					$this->show_form_edit($scr);
				}
			}
		}
	}
	
	private function show_list($del=null) {
		$pg=new paginator($this->src,50);
		
		$col=new col_subscribers;
		$col->loadAll($pg);
		
		$this->tpl->sub='subscribers';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->del=$del;
	}
}
?>