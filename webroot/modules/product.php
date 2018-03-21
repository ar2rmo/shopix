<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';

class mod_product extends module_page {
	const mod_hidden=true;
	const mod_tpl='product';
	
	function body() {
		$prod=$this->data;
		
		DBP::Exec('call prod_view_log(:uid,:pid)',array('uid'=>0,'pid'=>$prod->id));
		
		$this->tpl->title=$prod->tx_d_title.($this->app->setts->inf_nameintitle?(' | '.$this->app->setts->inf_shopname):'');
		//if (!is_null($prod->d_keywords)) $this->tpl->keywords=$prod->tx_d_keywords;
		//if (!is_null($prod->d_description)) $this->tpl->description=$prod->tx_d_description;
		if ($prod->keywords) {
			$this->tpl->keywords=$prod->nq_keywords;
		} elseif ($prod->cat->r_keywords) {
			$this->tpl->keywords=$prod->cat->nq_r_keywords;
		}
		if ($prod->description) {
			$this->tpl->description=$prod->nq_description;
		} elseif ($prod->descr_short) {
			$this->tpl->description=$prod->nq_descr_short;
		} elseif ($prod->cat->r_description) {
			$this->tpl->description=$prod->cat->nq_r_description;
		}
		
		$this->tpl->prod=$prod;
		
		// #XPD [
		$xpd = new col_products;
		$xpd->loadByLinksMSRnd($this->app->setts->num_xpd_rand,'PROM',$prod);
		$this->tpl->xpd=$xpd;
		// ] #XPD
		
		$bc=new col_categories;
		$bc->loadBreadCrumbs($prod->cid,true);
		$this->tpl->breadcrumbs=$bc;
	}
}
?>