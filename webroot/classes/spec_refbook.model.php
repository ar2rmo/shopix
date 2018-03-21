<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class spec_refbook_item extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/spec_refbook.model.xml');
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call spec_refbook_item_get(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new spec_refbook_item($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call spec_refbook_item_update(:acl_uid, :srid, :scid, :code, :name)';
			
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
			DBP::Exec('call spec_refbook_item_delete(:uid,:id)',
				array('uid'=>$uid,'id'=>$this->id)
			);
		}
		
		$this->id=0;
	}
	
	public function db_move_up($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_refbook_item_move(:uid,"UPUP",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
	
	public function db_move_down($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_refbook_item_move(:uid,"DOWN",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
}

class col_spec_refbook extends mcollection {
	public function loadByClass(paginator $pg=null, $sclass){
		if (is_object($sclass)) $scid=$sclass->id;
		elseif (is_numeric($sclass)) $scid=$sclass;
		else return;
		
		$items=&$this->_array;
		
		if (is_null($pg)) {
		DBP::Exec('call spec_refbook_get_by_class(:scid,null,null)',
			array('scid'=>$scid),
			function ($arr) use (&$items) {
				$items[]=new spec_refbook_item($arr,'db');
			},null,null);
		} else {
			DBP::MExec('call spec_refbook_get_by_class(:scid,:off,:onp)',
				array('scid'=>$scid,'off'=>$pg->get_offset(),'onp'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new spec_refbook_item($arr,'db');
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