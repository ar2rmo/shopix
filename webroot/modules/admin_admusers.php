<?
require_once CLASSES_PATH.'admuser.model.php';

class mod_admin_admusers extends module_admin {
	function body() {
		$this->mnu->setcurrent('settings');
		$this->mnu->setcurrentsub('admusers');
		
		if (is_null($this->src->uri->uri_3)) {
			if (!is_null($this->src->get->create)) {
				$this->show_form_create();
			} else {
				$this->show_list();
			}
		} else {
			$adm=admuser::getByID($this->src->uri->uri_3);
			if (is_null($adm)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$adm->db_delete();
						$this->app->http->relocate('/admin/admusers');
						$this->abort();
					} else {
						$this->show_list($adm);
					}
				} else {
					$this->show_form_edit($adm);
				}
			}
		}
	}
	
	private function show_list($del=null) {
		$col=new col_admusers;
		$col->loadAll();
		
		$this->tpl->sub='admins';
		$this->tpl->collect=$col;
		$this->tpl->del=$del;
	}
	
	private function show_form_edit(admuser $adm) {
		$this->show_form($adm);
	}
	
	private function show_form_create() {
		$itm=new admuser();
		$itm->fld_pword->descr->set_val('null',false);
		$itm->set_value('id',0,true);
		$itm->set_value('banned',false,true);
		$itm->set_value('role','PL_ADMIN',true);
		$this->show_form($itm);
	}
	
	private function show_form(admuser $adm) {
		$adm->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_admuser.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_admuser.xml'),$adm);

		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				if ($adm->db_update()) {
					$this->app->http->relocate('/admin/admusers');
					$this->abort();
				} else {
					$this->tpl->sub='form_admin';
					
					$this->tpl->errs=array(array('msg'=>array('Вы пытаетесь влезть куда не следует или отстрелить себе ногу')));
					
					$this->tpl->adm=$adm;
					$this->tpl->frm=$frm->html_data();
				}
			} else {
				$this->tpl->sub='form_admin';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->adm=$adm;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_admin';
			$this->tpl->adm=$adm;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>