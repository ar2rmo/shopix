<?
require_once CLASSES_PATH.'cart.model.php';

class mod_ajax_cart extends module {
	private $auth;
	
	function body() {
		switch ($this->src->uri->uri_2) {
			case 'add':
				$this->addtocart();
			break;
			case 'info':
				$this->cartinfo();
			break;
		}
	}

	private function addtocart() {
		$auth=new auth_hash_cust;
		if (!$auth->check_priv(auth::PL_ANON)) $auth->authenticate_force();
		
		if (!$this->src->get->num('product')) return;
		else $pid=$this->src->get->product;
		if (!$this->src->get->num('count')) $count=1;
		else $count=$this->src->get->count;

		$crt=new cart_item;
		$crt->pid=$pid;
		$crt->qty=$count;
		$crt->db_add();
	}

	private function cartinfo() {
		$this->app->run('box_cart');
	}
	
}
?>