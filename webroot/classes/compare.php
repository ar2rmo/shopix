<?
// #CMP=

require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

require_once CLASSES_PATH.'product.model.php';

class ss_cmplst {
	protected $lst;
	
	public function __construct() {
		if (session_status()!=PHP_SESSION_ACTIVE) session_start();
		if (!isset($_SESSION['compare_list'])) $_SESSION['compare_list']=array();
		
		$this->lst=&$_SESSION['compare_list'];
	}
	
	public function is_empty() {
		return empty($this->lst);
	}
	
	public function get_array() {
		return $this->lst;
	}
	
	public function append($itm) {
		if (!in_array($itm,$this->lst)) {
			$this->lst[]=$itm;
		}
	}
	
	public function remove($itm) {
		$this->lst=array_filter($this->lst, function ($v, $k) use ($itm) {return $v != $itm;});
	}
}

class compare {
	static $lst=null;
	
	public static function get_list() {
		if (is_null(static::$lst)) static::$lst=new ss_cmplst;
		return static::$lst;
	}
	
	public static function add_item($itm) {
		if (is_object($itm) and ($itm instanceof product)) $pid=$itm->id;
		elseif (is_numeric($itm)) $pid=$itm;
		else return;
		
		$l=static::get_list();
		$l->append($pid);
	}
	
	public static function del_item($itm) {
		if (is_object($itm) and ($itm instanceof product)) $pid=$itm->id;
		elseif (is_numeric($itm)) $pid=$itm;
		else return;
		
		$l=static::get_list();
		$l->remove($pid);
	}
	
	public static function get_array() {
		$l=static::get_list();
		$arr=$l->get_array();
		
		return $arr;
	}
	
	public static function get_products($ov_lst=null) {
		if (is_null($ov_lst)) {
			$l=static::get_list();
			$arr=$l->get_array();
		} else {
			$arr=$ov_lst;
		}
		
		$col=new col_products();
		$col->loadByIdList($arr,true,true);
		
		return $col;
	}
}


?>