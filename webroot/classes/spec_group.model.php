<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class spec_group extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/spec_group.model.xml');
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call spec_group_get(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new spec_group($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call spec_group_update(:acl_uid, :sgid, :code, :name)';
			
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
			//var_dump(array('uid'=>$uid,'id'=>$this->id));
			DBP::Exec('call spec_group_delete(:uid,:id)',
				array('uid'=>$uid,'id'=>$this->id)
			);
		}
		
		$this->id=0;
	}
	
	public function db_move_up($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_group_move(:uid,"UPUP",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
	
	public function db_move_down($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_group_move(:uid,"DOWN",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
}

class col_spec_groups extends mcollection {
	public function loadAll(){
		$items=&$this->_array;
		
		DBP::Exec('call spec_groups_get()',
			null,
			function ($arr) use (&$items) {
				$items[]=new spec_group($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
}

?>