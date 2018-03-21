<?
require_once CLASSES_PATH.'order.model.php';
require_once CLASSES_PATH.'refbk.model.php';

require_once CLASSES_PATH.'spec_group.model.php';
require_once CLASSES_PATH.'spec_class.model.php';
require_once CLASSES_PATH.'spec_refbook.model.php';

class mod_admin_specs extends module_admin {
	function body() {
		if (!(defined('SPEC_ENABLED') && SPEC_ENABLED)) {
			$this->tpl->content='Module is disabled';
			return;
		}
		
		switch ($this->src->uri->uri_3) {
			case 'refbooks':
				$this->mnu->setcurrent('refbooks');
				$this->mnu->setcurrentsub('specs');
				if ($this->src->uri->num('uri_4')) {
					$spec_class=spec_class::getById((int)$this->src->uri->uri_4);
					if (is_null($spec_class)) {
						$this->app->err404();
						$this->abort();
						return;
					} else {
						if (is_null($this->src->uri->uri_5)) {
							$this->show_rb_items_list($spec_class);
						} elseif ($this->src->uri->num('uri_5')) {
							$rb_item=spec_refbook_item::getById((int)$this->src->uri->uri_5);
							if (is_null($rb_item)) {
								$this->app->err404();
								$this->abort();
								return;
							} else {
								switch ($this->src->uri->uri_6) {
									case 'edit':
										$this->show_rb_item_form($spec_class,$rb_item);
									break;
									case 'delete':
										if (!is_null($this->src->post->del_conf)) {
											$rb_item->db_delete();
											$this->app->http->relocate('/admin/specs/refbooks/'.$spec_class->id);
											$this->abort();
										} else {
											$this->show_rb_items_list($spec_class,$rb_item);
										}
									break;
									case 'move':
										if ($this->src->get->dir=='up') $rb_item->db_move_up();
										if ($this->src->get->dir=='down') $rb_item->db_move_down();
										$this->app->http->relocate('/admin/specs/refbooks/'.$spec_class->id);
										$this->abort();
									break;
									default:
										$this->app->err404();
										$this->abort();
										return;
								}
							}
						} elseif ($this->src->uri->uri_5='add') {
							$this->show_rb_item_form($spec_class);
						} else {
							$this->app->err404();
							$this->abort();
							return;
						}
					}
				} elseif (is_null($this->src->uri->uri_5)) {
					$this->show_rb_spec_classes_list();
				} else {
					$this->app->err404();
					$this->abort();
					return;
				}
			break;
			case 'classes':
				$this->mnu->setcurrent('catalog');
				$this->mnu->setcurrentsub('specs');
				if ($this->src->uri->num('uri_4')) {
					$spec_group=spec_group::getById((int)$this->src->uri->uri_4);
					if (is_null($spec_group)) {
						$this->app->err404();
						$this->abort();
						return;
					} else {
						if (is_null($this->src->uri->uri_5)) {
							$this->show_spec_classes_list($spec_group);
						} elseif ($this->src->uri->num('uri_5')) {
							$spec_class=spec_class::getById((int)$this->src->uri->uri_5);
							if (is_null($spec_class)) {
								$this->app->err404();
								$this->abort();
								return;
							} else {
								switch ($this->src->uri->uri_6) {
									case 'edit':
										$this->show_spec_class_form($spec_group,$spec_class);
									break;
									case 'delete':
										if (!is_null($this->src->post->del_conf)) {
											$spec_class->db_delete();
											$this->app->http->relocate('/admin/specs/classes/'.$spec_group->id);
											$this->abort();
										} else {
											$this->show_spec_classes_list($spec_group,$spec_class);
										}
									break;
									case 'move':
										if ($this->src->get->dir=='up') $spec_class->db_move_up();
										if ($this->src->get->dir=='down') $spec_class->db_move_down();
										$this->app->http->relocate('/admin/specs/classes/'.$spec_group->id);
										$this->abort();
									break;
									default:
										$this->app->err404();
										$this->abort();
										return;
								}
							}
						} elseif ($this->src->uri->uri_5=='add') {
							$this->show_spec_class_form($spec_group);
						} else {
							switch ($this->src->uri->uri_5) {
								case 'edit':
									$this->show_spec_group_form($spec_group);
								break;
								case 'delete':
									if (!is_null($this->src->post->del_conf)) {
										$spec_group->db_delete();
										$this->app->http->relocate('/admin/specs/classes');
										$this->abort();
									} else {
										$this->show_spec_groups_list($spec_group);
									}
								break;
								case 'move':
									if ($this->src->get->dir=='up') $spec_group->db_move_up();
									if ($this->src->get->dir=='down') $spec_group->db_move_down();
									$this->app->http->relocate('/admin/specs/classes');
									$this->abort();
								break;
								default:
									$this->app->err404();
									$this->abort();
									return;
							}
						}
					}
				} elseif ($this->src->uri->uri_4=='add') {
					$this->show_spec_group_form();
				} elseif (is_null($this->src->uri->uri_4)) {
					$this->show_spec_groups_list();
				} else {
					$this->app->err404();
					$this->abort();
					return;
				}
			break;
		}
	}
	
	private function show_rb_spec_classes_list() {
		//$pg=new paginator($this->src,50);
		
		$col=new col_spec_classes;
		$col->loadRefBooks();
		
		$this->tpl->sub='spec_rb_classes_list';
		$this->tpl->collect=$col;
		//$this->tpl->pages=$pg->get_parray();
	}
	
	private function show_rb_items_list(spec_class $spec_class, spec_refbook_item $del=null) {
		$pg=new paginator($this->src,50);
		
		$col=new col_spec_refbook;
		$col->loadByClass($pg,$spec_class);
		
		$this->tpl->sclass=$spec_class;
		$this->tpl->sub='spec_rb_items_list';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->del=$del;
	}
	
	private function show_rb_item_form(spec_class $spec_class, spec_refbook_item $item = null) {
		if (is_null($item)) {
			$item = new spec_refbook_item();
			$item->set_value('id',0,true);
			$item->set_value('scid',$spec_class->id,true);
		}
		
		$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_spec_rb_item.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_spec_rb_item.xml');
		$frm=new cform($dscs,$item);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$item->db_update();
				$this->app->http->relocate('/admin/specs/refbooks/'.$spec_class->id);
				$this->abort();
			} else {
				$this->tpl->sub='form_spec_rb_item';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->sclass=$spec_class;
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_spec_rb_item';
			$this->tpl->sclass=$spec_class;
			$this->tpl->item=$item;
			$this->tpl->frm=$frm->html_data();
		}
	}
	
	private function show_spec_groups_list(spec_group $todel = null) {
		//$pg=new paginator($this->src,50);
		
		$col=new col_spec_groups;
		$col->loadAll();
		
		$this->tpl->del=$todel;
		
		$this->tpl->sub='spec_groups_list';
		$this->tpl->collect=$col;
		//$this->tpl->pages=$pg->get_parray();
	}
	
	private function show_spec_group_form(spec_group $item = null) {
		if (is_null($item)) {
			$item = new spec_group();
			$item->set_value('id',0,true);
		}
		
		$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_spec_group.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_spec_group.xml');
		$frm=new cform($dscs,$item);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$item->db_update();
				$this->app->http->relocate('/admin/specs/classes/'.$item->id);
				$this->abort();
			} else {
				$this->tpl->sub='form_spec_group';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_spec_group';
			$this->tpl->item=$item;
			$this->tpl->frm=$frm->html_data();
		}
	}
	
	private function show_spec_classes_list(spec_group $spec_group, spec_class $todel = null) {
		//$pg=new paginator($this->src,50);
		
		$col=new col_spec_classes;
		$col->loadByGroup($spec_group);
		
		$this->tpl->spec_group=$spec_group;
		$this->tpl->del=$todel;
		
		$this->tpl->sub='spec_classes_list';
		$this->tpl->collect=$col;
		//$this->tpl->pages=$pg->get_parray();
	}
	
	private function show_spec_class_form(spec_group $spec_group, spec_class $item = null) {
		if (is_null($item)) {
			$item = new spec_class();
			$item->set_value('id',0,true);
			$item->set_value('is_multy',false,true);
			$item->set_value('group_id',$spec_group->id,true);
		}
		
		$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_spec_class.xml'));
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_spec_class.xml');
		$frm=new cform($dscs,$item);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$item->db_update();
				$this->app->http->relocate('/admin/specs/classes/'.$spec_group->id);
				$this->abort();
			} else {
				$this->tpl->sub='form_spec_class';
				
				$this->tpl->errs=$errs;
					
				$this->tpl->spec_group=$spec_group;
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
        } else {
			$this->tpl->sub='form_spec_class';
			$this->tpl->spec_group=$spec_group;
			$this->tpl->item=$item;
			$this->tpl->frm=$frm->html_data();
		}
	}
}
?>