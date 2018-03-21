<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'dproc_field_telephone.php';

class cust_phone extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/cust_phone.model.xml');
	}
}

class col_cust_phone extends mcollection {
	public function load(paginator $pg=null){
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_customers_by_phone(null,null)',
				null,
				function ($arr) use (&$items) {
					$items[]=new order($arr,'db');
				});
		} else {
			DBP::MExec('call get_customers_by_phone(:loffset,:lnum)',
				array('loffset'=>$pg->get_offset(),'lnum'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new cust_phone($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
}

?>