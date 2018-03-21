<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class currency extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/currency.model.xml');
	}
}

class col_currencies extends mcollection {
    public function loadAll(){
		$items=&$this->_array;
		
		DBP::Exec('call get_currencies()',null,
			function ($arr) use (&$items) {
				$items[]=new currency($arr,'db');
			});

		//$this->_isFilled=1;
	}
}

?>