<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class page extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/page.model.xml');
	}
	
	public static function getById($id){
		$obj=null;
		DBP::Exec("call get_page_by_id(:id)",
			array('id'=>$id),
			function($arr) use (&$obj){
				$obj=new page($arr,'db');
			}
		);
		return $obj;
	}
	
	public static function getByUri($uri1,$uri2=null){
		$obj=null;
		DBP::Exec("call get_page_by_uri(:uri1,:uri2)",
			array('uri1'=>$uri1,'uri2'=>$uri2),
			function($arr) use (&$obj){
				$obj=new page($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_page(:acl_uid , :pid ,
					:caption ,:uri_name,:title,:keywords,
					:description,:css,:par_id,:ptext,:mnu,:mref,null,
					:after
					)';
			
		$id=DBP::ExecSingleVal($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		$o->id=$id;
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_page(:uid,:id)',array('uid'=>$uid,'id'=>$this->id));
		}
		
		$this->id=0;
	}
}

?>