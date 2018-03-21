<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'spec_group.model.php';

class mod_admin_catalog extends module_admin {
	function body() {
		$this->mnu->setcurrent('catalog');
		$this->mnu->setcurrentsub('catalog');
		
		$uris=$this->src->uri->get_all();
		unset($uris['uri_1'],$uris['uri_2']);
		$uri='/'.implode('/',$uris);
		
		if ($uri=='/') {
			if (!is_null($this->src->get->addchild)) {
				$this->show_form_create();
			} else {
				$this->show_tree();
			}
		} else {
			$cat=category::getByURI($uri,false);
			if (is_null($cat)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (!is_null($this->src->get->delete)) {
					if (!is_null($this->src->post->del_conf)) {
						$cat->db_delete();
						$this->app->http->relocate('/admin/catalog');
						$this->abort();
					} else {
						$this->show_tree($cat);
					}
				} elseif (!is_null($this->src->get->addchild)) {
					$this->show_form_create($cat);
				} elseif (!is_null($this->src->get->move_up)) {
					$cat->db_move_up();
					$this->app->http->relocate('/admin/catalog');
				} elseif (!is_null($this->src->get->move_down)) {
					$cat->db_move_down();
					$this->app->http->relocate('/admin/catalog');
				} else {
					$this->show_form_edit($cat);
				}
			}
		}
	}
	
	private function show_tree(category $del_cat=null) {
		$col=new col_categories;
		$col->loadAllTree(null,false);
		
		$this->tpl->sub='section_tree';
		$this->tpl->del_cat=$del_cat;
		$this->tpl->tree=$col;
	}
	
	private function show_form_edit(category $cat) {
		$this->show_form($cat);
	}
	
	private function show_form_create(category $par=null) {
		$itm=new category();
		$itm->set_value('id',0,true);
		$itm->set_value('parent_id',is_null($par)?0:$par->id,true);
		$itm->set_value('fshow',true,true);

		$this->show_form($itm);
	}
	
	private function show_form(category $cat) {
		$this->tpl->set('jquery',true);
		$this->tpl->set('ckeditor',true);
		
		$cat->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_category.xml'));
		$frm_dsc=descriptors::xml_file(MODULES_PATH.'descriptors/form_category.xml');
		if (!(defined('SPEC_ENABLED') && SPEC_ENABLED)) unset($frm_dsc['specs_t']);
		$frm=new cform($frm_dsc,$cat);
		$frm->parent_id->descr->set_val('proc_param',array('nid'=>$cat->id));
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$cat->db_update();
				$this->app->http->relocate('/admin/catalog');
				$this->abort();
			} else {
				$this->tpl->sub='form_section';
				
				$this->tpl->errs=$errs;
				
				$this->tpl->cat=$cat;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_section';
			$this->tpl->cat=$cat;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>