<?
require_once CLASSES_PATH.'cart.model.php';
require_once CLASSES_PATH.'order.model.php';

class mod_cart extends module_page {
	const mod_tpl='cart';
	
	function body() {
		$auth=new auth_hash_cust;
		$empty=false;
		if ($auth->check_priv(auth::PL_ANON)) {
			$cs=cart_summ::db_get();
			if ($cs->qty==0) {
				$empty=true;
			} else { 
				$cart=new cart;
				$cart->load();
			}
		} else {
			$empty=true;
		}
		
		$this->tpl->cempty=$empty;
		if (!$empty) {
			if (!is_null($this->src->get->del)) {
				foreach ($cart as $itm) {
					if ($itm->pid==$this->src->get->del) {
						$itm->db_delete();
						$this->app->http->relocate('/cart');
						$this->abort();
						return;
					}
				}
			}
			$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_cart_m.xml');
			$frms=array();
			foreach ($cart as $itm) {
				$frm=new cform($dscs,$itm);
				$frm->post_suffix='-'.$itm->pid;
				$frms[$itm->pid]=$frm;
			}
			
			if ($this->src->post->def('recalc')) {
				$allvalid=true;
				foreach ($frms as $frm) {
					if (!$frm->validate_post()) $allvalid=false;
				}
				foreach ($cart as $itm) {
					$itm->db_update();
				}
				$cs=cart_summ::db_get();
				$cart=new cart;
				$cart->load();
			}
			
			$frms_ht=array();
			foreach ($frms as $id=>$frm) {
				$frms_ht[$id]=$frm->html_data();
			}
			$this->tpl->frms=$frms_ht;
			
			
			$omade=false;
			
			$ord=new order();
			$ord->set_value('num',0,true);
			$ord->set_value('status_id',1,true);
			$ord->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_order_make.xml'));
			$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_order_make.xml'),$ord);
			
			if ($this->src->post->def('order')) {
				if ($frm->validate_post($errs)) {
					$ord->db_make();
					$ord2=order::get_order_by_id($ord->num);
					$ord2->send_admin();
					$ord2->send_customer();
					if (defined('SUPERVISE_ORDERS')) $ord2->send_supervisor(SUPERVISE_ORDERS);
					
					$omade=true;
				} else {
					$this->tpl->errs=$errs;
						
					$this->tpl->order=$ord;
					$this->tpl->frm=$frm->html_data();
				}
			} else {
				$this->tpl->order=$ord;
				$this->tpl->frm=$frm->html_data();
			}
			
			$this->tpl->omade=$omade;
			
			$this->tpl->cs=$cs;
			$this->tpl->cart=$cart;
		} else {
			$this->tpl->omade=false;
		}
	}
}
?>