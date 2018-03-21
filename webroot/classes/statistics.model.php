<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class statistics extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/statistics.model.xml');
	}
	
	public static function calculate(){
		$obj=null;
		DBP::Exec("call get_statistics",
			null,
			function($arr) use (&$obj){
				$obj=new statistics($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function calculate_vis_nsr(){
		$obj=null;
		DBP::Exec("call get_products_nums_vis_nsr",
			null,
			function($arr) use (&$obj){
				$obj=new statistics($arr,'db');
			}
		);
		return $obj;
	}
}

?>