<?
require_once CLASSES_PATH.'page.model.php';
require_once CLASSES_PATH.'menu.model.php';

class mod_admin_pages extends module_admin {
	function body() {
		$this->mnu->setcurrent('pages');
		
		$this->make_menu();
		
		if (is_null($this->src->uri->uri_3)) {
			$this->show_form_create();
		} else {
			$page=page::getByUri($this->src->uri->uri_3,$this->src->uri->uri_4);
			if (is_null($page)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$page->db_delete();
						$this->app->http->relocate('/admin/pages/main');
						$this->abort();
					} else {
						$this->show_form_delete($page);
					}
				} else {
					$this->show_form_edit($page);
				}
			}
		}
	}
	
	private function make_menu() {
		$mnu=new col_menu;
		$mnu->loadLev1($this->src->uri->uri_3,false);
		foreach ($mnu as $itm) {
			$sub=false;
			if ($itm->selected) {
				$mnu2=new col_menu;
				$mnu2->loadLev2($itm->uri_name,$this->src->uri->uri_4,false);
				foreach ($mnu2 as $itm2) {
					$aitm2=array(
						'capt'=>$itm2->ht_caption,
						'href'=>'/admin/pages/'.$itm->uri_name.'/'.$itm2->uri_name,
						'selected'=>$itm2->selected,
						'submenu'=>false
					);
					$sub[]=$aitm2;
				}
			}
			$aitm=array(
				'capt'=>$itm->ht_caption,
				'href'=>'/admin/pages/'.$itm->uri_name,
				'selected'=>$itm->selected,
				'submenu'=>$sub
			);
			$this->mnu->add_submenu_item($aitm);
		}
	}
	
	private function show_form_edit(page $page) {
		$this->show_form($page);
	}
	
	private function show_form_create() {
		$itm=new page();
		$itm->set_value('id',0,true);
		$itm->set_value('ro',0,false);
		$this->show_form($itm);
	}
	
	private function show_form_delete(page $page) {
		$this->tpl->del_conf=true;
		$this->show_form($page);
	}
	
	private function show_form(page $page) {
		$this->tpl->set('jquery',true);
		$this->tpl->set('ckeditor',true);
	
		$page->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_page.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_page.xml');
		if ($page->ro) {
			unset($dscs['uri_name'],$dscs['pid'],$dscs['mref']);
		}
		$frm=new cform($dscs,$page);
		if (!$page->ro) $frm->pid->descr->set_val('proc_param',array('id'=>$page->id));
		$frm->after->descr->set_val('proc_param',array('pid'=>$page->pid,'id'=>$page->id));
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$page->db_update();
				$page2=$page->getById($page->id);
				$this->app->http->relocate('/admin/pages/'.(is_null($page2->puri_name)?'':($page2->puri_name.'/')).$page2->uri_name);
				$this->abort();
			} else {
				$this->tpl->sub='form_pages';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->page=$page;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_pages';
			$this->tpl->page=$page;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>