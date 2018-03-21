<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class admuser extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/admuser.model.xml');
	}
	
	public static function getByID($id,$uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
	
		$obj=null;
		DBP::Exec("call get_admuser_by_id(:acl_uid,:id)",
			array ('acl_uid'=>$uid,'id'=>$id),
			function($arr) use (&$obj){
				$obj=new admuser($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_admuser(:acl_uid,:uid,:banned,:login,:name,:pword,:role)';
			
		$error=null;
		$id=DBP::ExecSingleVal($qry,
			function ($q) use (&$o,$uid) {
				$q->bindParam(':acl_uid',$uid);
				//var_dump($uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			},
			function ($err) use (&$error) {
				switch ($err[2]) {
					case '@acl_update_denied':
					case '@acl_create_denied':
						$error='ACL';
						return true;
					break;
					default:
						return false;
				}
			}
		);
		
		if (is_null($error)) {
			$o->id=$id;
			return true;
		} else {
			return false;
		}
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_admuser(:uid,:id)',array('uid'=>$uid,'id'=>$this->id));
		}
		
		$this->id=0;
	}
}

class col_admusers extends mcollection {
	public function loadAll($uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$items=&$this->_array;
		
		DBP::Exec('call get_admusers(:acl_uid)',
			array('acl_uid'=>$uid),
			function ($arr) use (&$items) {
				$items[]=new admuser($arr,'db');
			});
		
		//$this->_isFilled=1;
	}
}

?>