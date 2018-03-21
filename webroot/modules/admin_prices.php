<?
require_once CLASSES_PATH.'price_type.model.php';

class mod_admin_prices extends module_admin {
	function body() {
		if (!(defined('PRICE_ENABLED') && PRICE_ENABLED)) {
			$this->tpl->content='Module is disabled';
			return;
		}
		
		$this->mnu->setcurrent('catalog');
		$this->mnu->setcurrentsub('prices');

		if (is_null($this->src->uri->uri_3)) {
			if (!is_null($this->src->get->create)) {
				$this->show_form_create();
			} else {
				$this->show_list();
			}
		} else {
			$itm=price_type::getByID($this->src->uri->uri_3);
			if (is_null($itm)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$itm->db_delete();
						$this->app->http->relocate('/admin/prices');
						$this->abort();
					} else {
						$this->show_list($itm);
					}
				} else {
					$this->show_form_edit($itm);
				}
			}
		}
	}
	
	private function show_list($todel=null) {
		$col=new col_price_types;
		$col->loadAll();
		
		$this->tpl->sub='prices';
		$this->tpl->collect=$col;
		$this->tpl->del=$todel;
	}
	
	private function show_form_edit(price_type $itm) {
		$this->show_form($itm);
	}
	
	private function show_form_create() {
		$itm=new price_type();
		$itm->set_value('id',0,true);
		$this->show_form($itm);
	}
	
	private function show_form(price_type $itm) {
		$itm->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_price_type.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_price_type.xml'),$itm);

		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$itm->db_update();
				$this->app->http->relocate('/admin/prices');
				$this->abort();
			} else {
				$this->tpl->sub='form_price_type';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->obj=$itm;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_price_type';
			$this->tpl->obj=$itm;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>