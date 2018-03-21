<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'refbk.model.php';
require_once CLASSES_PATH.'catsel.model.php';
require_once CLASSES_PATH.'inherit.model.php';
require_once CLASSES_PATH.'spec_class.model.php';
require_once CLASSES_PATH.'spec_group.model.php';

class mod_admin_products extends module_admin {
	function body() {
		$this->mnu->setcurrent('catalog');
		$this->mnu->setcurrentsub('products');
		
		if ($this->src->get->def('reorder') && $this->src->post->def('sortord')) {
			$this->reorder($this->src->post->get('sortord'));
			exit;
		}
		
		if ($this->src->get->def('reorder-vars') && $this->src->post->def('sortord')) {
			$this->reorder_vars($this->src->post->get('sortord'));
			exit;
		}
	
		$uris=$this->src->uri->get_all();
		unset($uris['uri_1'],$uris['uri_2']);
		$uri='/'.implode('/',$uris);
				
		if ($uri=='/') {
			$this->set_catsel();
			$this->show_main();
		} else {
			$cat=category::getByURI($uri,false);
			if (is_null($cat)) {
				$prod=product::getByURI($uri,false,false);
				if (is_null($prod)) {
					$this->app->err404();
					$this->abort();
				} else {
					$this->set_catsel($prod->cat);
					if (!is_null($this->src->get->delete)) {
						if ($prod->inherited) {
							if (!is_null($this->src->post->del_conf)) {
								$puri=$prod->base->uri;
								$prod->db_delete();
								$this->app->http->relocate('/admin/products'.$puri.'?tab=modifications');
								$this->abort();
							} else {
								$this->show_form_edit($prod->base,$prod);
							}
						} else {
							if (!is_null($this->src->post->del_conf)) {
								$curi=$prod->cat->uri;
								$prod->db_delete();
								$this->app->http->relocate('/admin/products'.$curi);
								$this->abort();
							} else {
								$this->show_list($prod->cat,$prod);
							}
						}
					} elseif (!$prod->inherited && !is_null($this->src->get->clone)) {
						$this->show_form_create($prod->cat,$prod);
					} else {
						if ($prod->inherited) {
							$this->show_form_edit_inh($prod);
						} else {
							$this->show_form_edit($prod);
						}
					}
				}
			} else {
				$this->set_catsel($cat);
				if (!is_null($this->src->get->create)) {
					$this->show_form_create($cat);
				} else {
					$this->show_list($cat);
				}
			}
		}
	}
	
	private function reorder($sord) {
		$arr=explode(';',$sord);
		$ord_ids=array();
		foreach ($arr as $itm) {
			if (preg_match('/^p([0-9]+)$/',$itm,$mchs)) {
				$ord_ids[]=(int)$mchs[1];
			}
		}
		if (count($ord_ids)>1) {
			product::db_reorder($ord_ids);
		}
	}
	
	private function reorder_vars($sord) {
		$arr=explode(';',$sord);
		$ord_ids=array();
		foreach ($arr as $itm) {
			if (preg_match('/^p([0-9]+)$/',$itm,$mchs)) {
				$ord_ids[]=(int)$mchs[1];
			}
		}
		if (count($ord_ids)>1) {
			product::db_reorder_vars($ord_ids);
		}
	}
	
	private function set_catsel(category $current=null) {
		$dsc=descriptors::xml_file(MODULES_PATH.'descriptors/form_catsel.xml');
		$dsc['catsel']->set_val('tgattribs',array('onchange'=>'window.location.href=\'/admin/products\'+this.value;'));
		$cnt=control::create($dsc['catsel']);
		
		if (is_null($current)) {
			$this->tpl->catsel=$cnt->html_input();
		} else {
			$this->tpl->catsel=$cnt->html_input($current->fld_uri);
		}
	}
	
	private function show_main() {
		$this->tpl->sub='form_items';
		$this->tpl->collect=null;
	}
	
	private function show_list(category $cat,product $del_prod=null) {
		$this->tpl->set('jscripts',array('jquery-1.7.2.min.js','jquery.tablednd-0.5.js','table-dnd.js'));
		
		$pg=new paginator_overlap($this->src,50,1);
		
		$col=new col_products;
		$col->loadByCat($pg,null,$cat,false);
		
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_product_m.xml');
		$frms=array();
		foreach ($col as $itm) {
			$frm=new cform($dscs,$itm);
			$frm->post_suffix='_'.$itm->id;
			$frms[$itm->id]=$frm;
		}
		
		if ($this->src->post->def('msubmt')) {
			foreach ($frms as $frm) {
				$frm->validate_post();
			}
			foreach ($col as $itm) {
				$itm->db_update_part();
			}
			product::db_recalc();
        }
		
		$frms_ht=array();
		foreach ($frms as $id=>$frm) {
			$frms_ht[$id]=$frm->html_data();
		}
		$this->tpl->frms=$frms_ht;
		
		$this->tpl->sub='form_items';
		$this->tpl->collect=$col;
		$this->tpl->cat=$cat;
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->del_prod=$del_prod;
	}
	
	private function show_form_edit(product $prod, product $del_var=null) {
		$this->show_form($prod,$del_var);
	}
	
	private function show_form_create(category $cat=null, product $clone=null) {
		$itm=new product();
		$itm->set_value('fshow',true,true);
		$itm->set_value('forsale',true,true);
		if (!is_null($cat)) {
			$itm->set_value('cid',$cat->id,true);
		}
		if (!is_null($clone)) {
			$itm=clone $clone;
			$itm->uri_name=null;
			$itm->set_clone_picts($clone->pict_uri);
		}
		$itm->set_value('id',0,true);
		$this->show_form($itm);
	}
	
	private function show_form(product $prod, product $del_var=null) {
		$this->tpl->set('jquery',true);
		$this->tpl->set('ckeditor',true);
		$this->tpl->set('jscripts',array('jquery.tablednd-0.5.js','table-dnd-var.js','tabs.js','check_inherit.js'));
		
		$prod->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_product.xml'));
		$frm_dsc=descriptors::xml_file(MODULES_PATH.'descriptors/form_product.xml');
		if (!(defined('PRICE_ENABLED') && PRICE_ENABLED)) unset($frm_dsc['price_type']);
		if (!(defined('SPEC_ENABLED') && SPEC_ENABLED)) unset($frm_dsc['specs_inh']);
		$frm=new cform($frm_dsc,$prod);
		
		$col_scs=new col_spec_classes();
		$col_scs->loadByProductVal($prod);
		$frm_scs=$col_scs->get_form();
		
		$ih=new inherit;
		$ih->parent_id=$prod->id;
		$ih->fshow=true;
		$ih->forsale=true;
		$ih->inh_price=false;
		$ih->price=$prod->price;
		$ih->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_inherit.xml'));
		$ih_d=descriptors::xml_file(MODULES_PATH.'descriptors/form_inherit.xml');
		$ih_d['inh_price']->set_val('tgattribs',array('shp-prnt-value'=>$prod->ht_price));
		$ih_frm=new cform($ih_d,$ih);
		$ih_frm->post_prefix='ih_';
		
		$pcol=new col_product_pics;
		$pcol->loadByProductFixedNum($prod,10);
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_product_picts.xml');
		$pfrms=array();
		foreach ($pcol as $itm) {
			$frmi=new cform($dscs,$itm);
			$frmi->post_suffix='_'.str_pad($itm->pn,3,'0',STR_PAD_LEFT);
			$pfrms[$itm->pn]=$frmi;
		}
		
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_product_m.xml');
		$vfrms=array();
		foreach ($prod->variants as $vitm) {
			$vfrm=new cform($dscs,$vitm);
			$vfrm->post_suffix='_'.$vitm->id;
			$vfrms[$vitm->id]=$vfrm;
		}
		
		// #XPR [
		$xprods=new col_products;
		$xprods->loadByLinksMS(null,'PROM',$prod,false,false);
		// ] #XPD
		
		if ($this->src->post->def('submt')) {
			if ($frm->validate_post($errs)) {
				$oid=$prod->id;
				$prod->db_update();
				if ($prod->id != $oid) {
					$pcol=new col_product_pics;
					$pcol->loadByProductFixedNum($prod,10);
					$pfrms=array();
					foreach ($pcol as $itm) {
						$frmi=new cform($dscs,$itm);
						$frmi->post_suffix='_'.str_pad($itm->pn,3,'0',STR_PAD_LEFT);
						$pfrms[$itm->pn]=$frmi;
					}
				}
				foreach ($pfrms as $frmi) {
					$frmi->validate_post();
				}
				foreach ($pcol as $itm) {
					$itm->db_update();
				}
				if (!is_null($frm_scs) && $frm_scs->validate_post()) {
					$col_scs->storeValues();
				}
				$pnew=product::getByID($prod->id);
				$this->app->http->relocate('/admin/products'.$pnew->uri);
				$this->abort();
			} else {
				$this->rt_form($prod,$frm,$errs,$pcol,$pfrms,$ih_frm,null,$vfrms,null,$frm_scs,$xprods); // #XPD
			}
        } elseif ($this->src->post->def('submt_ih')) {
			if ($ih_frm->validate_post($ih_errs)) {
				$ih->db_inherit();
				
				//$this->app->http->relocate('/admin/products'.$ih->variant_uri);
				$this->app->http->relocate('/admin/products'.$prod->uri.'?tab=modifications');
				$this->abort();
			} else {
				$this->rt_form($prod,$frm,null,$pcol,$pfrms,$ih_frm,$ih_errs,$vfrms,null,$frm_scs,$xprods); // #CPD
			}
		} elseif ($this->src->post->def('msubmt_vars')) {
			foreach ($vfrms as $vfrm) {
				$vfrm->validate_post();
			}
			foreach ($prod->variants as $var) {
				$var->db_update_part();
			}
			product::db_recalc();
			$this->app->http->relocate('/admin/products'.$prod->uri.'?tab=modifications');
			$this->abort();
		} else {
			$this->rt_form($prod,$frm,null,$pcol,$pfrms,$ih_frm,null,$vfrms,$del_var,$frm_scs,$xprods); // #XPD
		}
	}
	
	private function rt_form($prod,$frm,$errs,$pcol,$pfrms,$ih_frm,$ih_errs,$vfrms,$del_var,$frm_scs,$xprods) { // #XPD
		if (!is_null($this->src->get->tab)) {
			switch ($this->src->get->tab) {
				case 'modifications':
					$this->tpl->acttab='mods';
				// #XPD [
				break;
				case 'linked':
					$this->tpl->acttab='xp';
				// ] #XPD
				break;
			}
		}
		
		$this->tpl->sub='form_item';
		$this->tpl->prod=$prod;
		$this->tpl->frm=$frm->html_data();
		if (!is_null($errs)) $this->tpl->errs=$errs;
		
		$frms_ht=array();
		foreach ($pfrms as $id=>$frmi) {
			$frms_ht[$id]=$frmi->html_data();
		}
		$this->tpl->pfrms=$frms_ht;
		$this->tpl->pcol=$pcol;
		
		if (!is_null($ih_errs)) $this->tpl->ih_errs=$ih_errs;
		$this->tpl->ihfrm=$ih_frm->html_data();
		
		if (!is_null($del_var)) $this->tpl->del_var=$del_var;
		
		$vfrms_ht=array();
		foreach ($vfrms as $vid=>$vfrm) {
			$vfrms_ht[$vid]=$vfrm->html_data();
		}
		$this->tpl->vfrms=$vfrms_ht;
		
		$this->tpl->frm_scs=is_null($frm_scs)?null:$frm_scs->html_data();
		
		$this->tpl->xprods=$xprods; // #XPD
	}
	
	private function show_form_edit_inh(product $prod) {
		$this->tpl->set('jquery',true);
		$this->tpl->set('ckeditor',true);
		$this->tpl->jscripts=array('tabs.js','check_inherit.js');
		
		$prod->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_product.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_product.xml'),$prod);
		
		$col_scs=new col_spec_classes();
		$col_scs->loadByProductVal($prod);
		$frm_scs=$col_scs->get_form();
		
		$pinh=$prod->inheritance;
		$ih_d=descriptors::xml_file(MODULES_PATH.'descriptors/form_product_inh.xml');
		$ih_d['inh_price']->set_val('tgattribs',array('shp-prnt-value'=>$prod->parent->ht_price));
		$ih_d['inh_code']->set_val('tgattribs',array('shp-prnt-value'=>$prod->parent->ht_code));
		$frm_inh=new cform($ih_d,$pinh);
		
		$pcol=new col_product_pics;
		$pcol->loadByProductFixedNum($prod,10);
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_product_picts.xml');
		$pfrms=array();
		foreach ($pcol as $itm) {
			$frmi=new cform($dscs,$itm);
			$frmi->post_suffix='_'.str_pad($itm->pn,3,'0',STR_PAD_LEFT);
			$pfrms[$itm->pn]=$frmi;
		}
		
		if ($this->src->post->def('submt')) {
			if ($frm->validate_post($errs) && $frm_inh->validate_post($errs2)) {
				$oid=$prod->id;
				$prod->db_update();
				if ($prod->id != $oid) {
					$pcol=new col_product_pics;
					$pcol->loadByProductFixedNum($prod,10);
					$pfrms=array();
					foreach ($pcol as $itm) {
						$frmi=new cform($dscs,$itm);
						$frmi->post_suffix='_'.str_pad($itm->pn,3,'0',STR_PAD_LEFT);
						$pfrms[$itm->pn]=$frmi;
					}
				}
				foreach ($pfrms as $frmi) {
					$frmi->validate_post();
				}
				foreach ($pcol as $itm) {
					$itm->db_update();
				}
				if (!is_null($frm_scs) && $frm_scs->validate_post()) {
					$col_scs->storeValues();
				}
				$pnew=product::getByID($prod->id);
				$this->app->http->relocate('/admin/products'.$pnew->base->uri.'?tab=modifications');
				$this->abort();
			} else {
				if (defined('INH_FORMTPL')) {
					switch (INH_FORMTPL) {
						case 'brief':
							$this->tpl->sub='form_item_inh_simp';
						break;
						case 'specs':
							$this->tpl->sub='form_item_inh_specs';
						break;
						case 'full':
							$this->tpl->sub='form_item_inh';
						break;
					}
				} else {
					$this->tpl->sub='form_item_inh';
				}
				
				$this->tpl->errs=$errs;
					
				$this->tpl->prod=$prod;
				$this->tpl->frm=$frm->html_data();
				$this->tpl->frm_inh=$frm_inh->html_data();
				
				$frms_ht=array();
				foreach ($pfrms as $id=>$frmi) {
					$frms_ht[$id]=$frmi->html_data();
				}
				$this->tpl->pfrms=$frms_ht;
				$this->tpl->pcol=$pcol;
				
				$this->tpl->frm_scs=$frm_scs->html_data();
			}
        } else {
			if (defined('INH_FORMTPL')) {
				switch (INH_FORMTPL) {
					case 'brief':
						$this->tpl->sub='form_item_inh_simp';
					break;
					case 'specs':
						$this->tpl->sub='form_item_inh_specs';
					break;
					case 'full':
						$this->tpl->sub='form_item_inh';
					break;
				}
			} else {
				$this->tpl->sub='form_item_inh';
			}
				
			$this->tpl->prod=$prod;
			$this->tpl->frm=$frm->html_data();
			$this->tpl->frm_inh=$frm_inh->html_data();
			
			$frms_ht=array();
			foreach ($pfrms as $id=>$frmi) {
				$frms_ht[$id]=$frmi->html_data();
			}
			$this->tpl->pfrms=$frms_ht;
			$this->tpl->pcol=$pcol;
			
			$this->tpl->frm_scs=is_null($frm_scs)?null:$frm_scs->html_data();
		}
	}
}
?>