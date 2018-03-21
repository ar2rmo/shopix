<?
// #XPD=

require_once CLASSES_PATH.'plink.model.php';
require_once CLASSES_PATH.'product.model.php';
require_once CLASSES_PATH.'category.model.php';

class mod_admin_plinks extends module_admin {
	function body() {
		$this->mnu->setcurrent('catalog');
		$this->mnu->setcurrentsub('products');
		
		//$this->make_menu();
		
		if ($this->src->uri->num('uri_3')) {
			$prod=product::getByID($this->src->uri->uri_3);
			if (is_null($prod)) {
				$this->app->err404();
				$this->abort();
			} else {
				if (is_null($this->src->uri->uri_4)) {
					$this->show_items_list($prod);
				} elseif ($this->src->uri->num('uri_4')) {
					$item=new plink;
					$item->master=$prod->id;
					$item->slave=(int)$this->src->uri->uri_4;
					$item->kind='PROM';
					$item->db_delete();
					$this->app->http->relocate('/admin/products'.$prod->uri.'?tab=linked');
					$this->abort();
				} else {
					switch ($this->src->uri->uri_4) {
						case 'add':
							$this->show_additem_form($prod);
						break;
						default:
							$this->app->err404();
							$this->abort();
					}
				}
			}
		} else {
			$this->app->err404();
			$this->abort();
		}
	}
	
	private function show_items_list(product $prod, product $del=null) {
		$col=new col_products;
		$col->loadByLinksMS(null,'PROM',$prod,false,false);
		
		$this->tpl->sub='productlinks';
		$this->tpl->collect=$col;
		$this->tpl->prod=$prod;
		$this->tpl->del=$del;
	}
	
	private function show_additem_form(product $prod) {
		$cat=null;
		if ($this->src->get->num('cat')) $cat=category::getById($this->src->get->cat);
		else $cat=$prod->cat;
		
		$dsc=descriptors::xml_file(MODULES_PATH.'descriptors/form_catsel_id.xml');
		$dsc['catsel']->set_val('tgattribs',array('onchange'=>'window.location.href=\'/admin/plinks/'.$prod->id.'/add?cat=\'+this.value;'));
		$cnt=control::create($dsc['catsel']);
		
		if (is_null($cat)) {
			$this->tpl->catsel=$cnt->html_input();
		} else {
			$this->tpl->catsel=$cnt->html_input($cat->fld_id);
		}
		
		if (!is_null($cat)) {
			$item=new plink;
			$item->master=$prod->id;
			$item->kind='PROM';
			
			$item->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_plinks_item_add.xml'));
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_plinks_item_add.xml');
			$dscs['slave']->set_val('proc_param',array(
				'nid'=>$cat->id
			));
			$frm=new cform($dscs,$item);
			
			if ($this->src->post->def('item_add')) {
				if ($frm->validate_post($errs)) {
					$item->db_add();
					$this->app->http->relocate('/admin/products'.$prod->uri.'?tab=linked');
					$this->abort();
				} else {
					$this->tpl->sub='form_prodaddlink';
					
					$this->tpl->errs=$errs;
						
					$this->tpl->prod=$prod;
					$this->tpl->item=$item;
					$this->tpl->frm=$frm->html_data();
				}
			} else {
				$this->tpl->sub='form_prodaddlink';
				$this->tpl->prod=$prod;
				$this->tpl->item=$item;
				$this->tpl->frm=$frm->html_data();
			}
		} else {
			$this->tpl->sub='form_prodaddlink';
			$this->tpl->prod=$prod;
		}
	}
}
?>