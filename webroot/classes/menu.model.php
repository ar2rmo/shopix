<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class menu extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/menu.model.xml');
	}
	
	public static function getFirstChild($mnu){
		if (is_object($mnu)) $pid=$mnu->id;
		elseif (is_numeric($mnu)) $pid=$mnu;
		else return;

		$obj=null;
		DBP::Exec("call get_menu_fch(:pid)",
			function ($q) use ($pid) {
				$q->bindParam(":pid",$pid,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new menu($arr,'db');
			}
		);
		return $obj;
	}
}

class col_menu extends mcollection {
    public function loadLev1($curi,$vis=true){
		$items=&$this->_array;
		
		DBP::Exec('call get_menu_l1(:curi,:vis)',
			array('curi'=>$curi,'vis'=>$vis),
			function ($arr) use (&$items) {
				$items[]=new menu($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadLev2($puri,$curi,$vis=true){
		$items=&$this->_array;
		
		DBP::Exec('call get_menu_l2(:puri,:curi,:vis)',
			array('puri'=>$puri,'curi'=>$curi,'vis'=>$vis),
			function ($arr) use (&$items) {
				$items[]=new menu($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadTree($vis=true){
		$items=&$this->_array;
		
		DBP::Exec('call get_menu_tree(:vis)',
			array('vis'=>$vis),
			function ($arr) use (&$items) {
				$items[]=new menu($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
}

?>