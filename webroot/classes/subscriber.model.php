<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class subscriber extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/subscriber.model.xml');
	}
	
	public static function get_by_id($id){
		$obj=null;
		DBP::Exec("call get_subscriber_by_id(:id)",
			array('id'=>$id),
			function($arr) use (&$obj){
				$obj=new subscriber($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_subscribe() {
		$o=&$this;
		
		$qry='call add_subscribe(:email,:ip)';
			
		$res=DBP::ExecSingleRow($qry,
			function ($q) use (&$o) {
				$o->fld_email->pdo_bind($q);
				$o->fld_ip->pdo_bind($q);
			}
		);
		
		$this->unsuc=$res['unsuc'];
	}
	
	public function db_unsubscribe() {
		$o=&$this;
		
		$qry='call un_subscribe(:email,:unsuc)';
			
		$res=DBP::ExecSingleRow($qry,
			function ($q) use (&$o) {
				$o->fld_email->pdo_bind($q);
				$o->fld_unsuc->pdo_bind($q);
			}
		);
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_subscribe(:uid,:id)',array('uid'=>$uid,'id'=>$this->id));
		}
		
		$this->id=0;
	}
	
	public function broadcast(col_article $articles,col_article $news,col_article $specials) {
		mailer::broadcast($this,$articles,$news,$specials);
	}
}

class col_subscribers extends mcollection {
	public function loadAll(paginator $pg=null){
		$items=&$this->_array;
		
		if (is_null($pg)) {
			DBP::Exec('call get_subscribers(null,null)',
				null,
				function ($arr) use (&$items) {
					$items[]=new subscriber($arr,'db');
				});
		} else {
			DBP::MExec('call get_subscribers(:loffset,:lnum)',
				array('loffset'=>$pg->get_offset(),'lnum'=>$pg->get_onpage()),
				array(
					function ($arr) use (&$items) {
						$items[]=new subscriber($arr,'db');
					},
					function ($arr) use (&$pg) {
						$pg->set_items($arr['cnt']);
					}
				));
		}
		
		//$this->_isFilled=1;
	}
	
	public function loadBroadcast($limit){
		$items=&$this->_array;
		
		DBP::Exec('call get_subscribers_4broadcast(:lim)',
			array('lim'=>$limit),
			function ($arr) use (&$items) {
				$items[]=new subscriber($arr,'db');
			});
		
		//$this->_isFilled=1;
	}
}
?>