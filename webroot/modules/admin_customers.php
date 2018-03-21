<?
require_once CLASSES_PATH.'cust_phone.model.php';

class mod_admin_customers extends module_admin {
	function body() {
		$this->mnu->setcurrent('customers');

		$this->show_list();
	}
	
	private function show_list() {
		$pg=new paginator($this->src,50);
		
		$col=new col_cust_phone;
		$col->load($pg);
		
		$this->tpl->sub='customers';
		$this->tpl->collect=$col;
		$this->tpl->pages=$pg->get_parray();
	}
}
?>