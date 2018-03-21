<?
require_once CLASSES_PATH.'refbk.model.php';

class mod_admin_refbooks extends module_admin {
	private $bk=null;
	private $order=false;
	
	function body() {
		$this->mnu->setcurrent('refbooks');
	
		if (is_null($this->src->uri->uri_3)) {
			$this->show_main();
		} else {
			$bk=$this->src->uri->uri_3;
			switch ($bk) {
				case 'shipping':
					$this->mnu->setcurrentsub('shipping');
				break;
				case 'payment':
					$this->mnu->setcurrentsub('payment');
				break;
				case 'avail':
					$this->mnu->setcurrentsub('avail');
				break;
				case 'status':
					$this->mnu->setcurrentsub('status');
				break;
				case 'brands':
					$this->mnu->setcurrentsub('brands');
				break;
				case 'criterias1':
					$this->mnu->setcurrentsub('criterias');
					$this->mnu->setcurrentsub2('criterias1');
					$this->order=true;
				break;
				case 'criterias2':
					$this->mnu->setcurrentsub('criterias');
					$this->mnu->setcurrentsub2('criterias2');
					$this->order=true;
				break;
				case 'criterias3':
					$this->mnu->setcurrentsub('criterias');
					$this->mnu->setcurrentsub2('criterias3');
					$this->order=true;
				break;
				case 'prices':
					$this->mnu->setcurrentsub('prices');
				break;
				default:
					$this->app->err404();
					$this->abort();
			}
			$this->bk=$bk;
			
			
			
			if (is_null($this->src->uri->uri_4)) {
				$this->make_list();
				if (!is_null($this->src->get->create)) {
					$this->show_form_create();
				}
			} else {
				$itm=refbk::get($bk,$this->src->uri->uri_4);
				if (is_null($itm)) {
					$this->app->err404();
					$this->abort();
				} else {
					$this->make_list();
					if (!is_null($this->src->get->delete)) {
						if (!is_null($this->src->post->del_conf)) {
							$itm->db_delete($bk);
							$this->app->http->relocate('/admin/refbooks/'.$bk);
							$this->abort();
						} else {
							$this->show_delete($itm);
						}
					} elseif (!is_null($this->src->get->up)) {
						$itm->db_move_up($bk);
						$this->app->http->relocate('/admin/refbooks/'.$bk);
						$this->abort();
					} elseif (!is_null($this->src->get->down)) {
						$itm->db_move_down($bk);
						$this->app->http->relocate('/admin/refbooks/'.$bk);
						$this->abort();
					} else {
						$this->show_form_edit($itm);
					}
				}
			}
		}
	}
	
	private function show_main() {
		
	}
	
	private function make_list() {
		$col=new col_refbk;
		$col->Load($this->bk);
		
		$this->tpl->sub='form_editor';
		$this->tpl->ord=$this->order;
		$this->tpl->collect=$col;
		$this->tpl->bk=$this->bk;
	}
	
	private function show_delete(refbk $del) {
		$this->tpl->del=$del;
	}
	
	private function show_form_edit(refbk $rbk) {
		$this->show_form($rbk);
	}
	
	private function show_form_create() {
		$rbk=new refbk();
		$rbk->set_value('id',0,true);
		$this->show_form($rbk);
	}
	
	private function show_form(refbk $rbk) {
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_refbook.xml'),$rbk);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$rbk->db_update($this->bk);
				$this->app->http->relocate('/admin/refbooks/'.$this->bk);
				$this->abort();
			} else {
				$this->tpl->errs=$errs;
				
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>