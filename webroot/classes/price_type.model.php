<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class price_type extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/price_type.model.xml');
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call price_type_get(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new price_type($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call price_type_update(:acl_uid, :ptid, :currency_code, :name, :mark, :rate, :incr)';
			
		$res=DBP::ExecSingleRow($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		$o->id=$res['aff_id'];
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call price_type_delete(:acl_uid,:id)',
				array('acl_uid'=>$uid,'id'=>$this->id)
			);
		}
		
		$this->id=0;
	}
}

class col_price_types extends mcollection {
    public function loadAll(){
		$items=&$this->_array;
		
		DBP::Exec('call price_types_get()',
			null,
			function ($arr) use (&$items) {
				$items[]=new price_type($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
}

?>