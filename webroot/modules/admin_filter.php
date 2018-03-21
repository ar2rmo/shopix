<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';

class mod_admin_filter extends module_admin {
	function body() {
		$this->mnu->setcurrent('catalog');
	
		switch ($this->src->uri->uri_3) {
			case 'hidden':
				$pg=new paginator($this->src,50);
				$col=new col_products;
				$col->loadHidden($pg);
				$this->show_list($col,$pg);
				
				$this->mnu->setcurrentsub('hidden');
			break;
			case 'new':
				$pg=new paginator($this->src,50);
				$col=new col_products;
				$col->loadByFilter($pg,'NAME',null,null,null,null,null,true,false,false,false);
				$this->show_list($col,$pg);
				
				$this->mnu->setcurrentsub('new');
			break;
			case 'recommend':
				$pg=new paginator($this->src,50);
				$col=new col_products;
				$col->loadByFilter($pg,'NAME',null,null,null,null,null,false,true,false,false);
				$this->show_list($col,$pg);
				
				$this->mnu->setcurrentsub('recommend');
			break;
			case 'specials':
				$pg=new paginator($this->src,50);
				$col=new col_products;
				$col->loadByFilter($pg,'NAME',null,null,null,null,null,false,false,true,false);
				$this->show_list($col,$pg);
				
				$this->mnu->setcurrentsub('specials');
			break;
			default:
				$this->app->err404();
				$this->abort();
		}
	}
	
	private function show_list(col_products $col, paginator $pg) {
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
		
		$this->tpl->sub='form_items_nc';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
	}
}
?>