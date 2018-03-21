<?
require_once CLASSES_PATH.'cart.model.php';

class mod_box_cart extends module_sub {
	function body() {
		$auth=new auth_hash_cust;
		$empty=false;
		if ($auth->check_priv(auth::PL_ANON)) {
			$cs=cart_summ::db_get();
			if ($cs->qty==0) $empty=true;
		} else {
			$empty=true;
		}

		if ($empty) {
			$tpl=new template('boxes/cart_empty');
			$tpl->output();
		} else {
			$tpl=new template('boxes/cart');
			$tpl->urib=$this->app->urib_f;
			$tpl->cart_sum=$cs;
			$tpl->output();
		}
	}
}
?>