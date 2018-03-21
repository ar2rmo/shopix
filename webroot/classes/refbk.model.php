<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class refbk extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="integer" dbname="id" name="id" forupdate="1" />
				<field dtype="string" dbname="name" name="name" forupdate="1" />
			</fieldset>');
	}
	
	public static function get($bk,$id){
		$obj=null;
		DBP::Exec("call rb_get_itm_".$bk."(:id)",
			array('id'=>$id),
			function($arr) use (&$obj){
				$obj=new refbk($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($bk,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$id=DBP::ExecSingleVal('call update_rb_'.$bk.'(:uid,:id,:name)',
			function ($q) use (&$o) {
				$q->bindParam(':uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		$o->id=$id;
	}
	
	public function db_move_up($bk,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call update_rb_'.$bk.'_move(:uid,"UPUP",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
	
	public function db_move_down($bk,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call update_rb_'.$bk.'_move(:uid,"DOWN",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
	
	public function db_delete($bk,$uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_rb_'.$bk.'(:uid,:id)',array('uid'=>$uid,'id'=>$this->id));
		}
		
		$this->id=0;
	}
}

class col_refbk extends mcollection {
	public function Load($bk){
		$items=&$this->_array;
		
		DBP::Exec('call rb_get_list_'.$bk.'()',
			null,
			function ($arr) use (&$items) {
				$items[]=new refbk($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function LoadBrands(){
		$items=&$this->_array;
		
		DBP::Exec('call rb_get_list_brands()',
			null,
			function ($arr) use (&$items) {
				$items[]=new refbk($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function LoadCrits($kind){
		$items=&$this->_array;
		
		DBP::Exec('call rb_get_list_criterias(:kind)',
			array('kind'=>$kind),
			function ($arr) use (&$items) {
				$items[]=new refbk($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function LoadProdCrits($pid,$kind){
		$items=&$this->_array;
		
		DBP::Exec('call rb_get_prod_criterias(:pid,:kind)',
			array('pid'=>$pid,'kind'=>$kind),
			function ($arr) use (&$items) {
				$items[]=new refbk($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function LoadProdTags($pid){
		$items=&$this->_array;
		
		DBP::Exec('call rb_get_prod_tags(:pid)',
			array('pid'=>$pid),
			function ($arr) use (&$items) {
				$items[]=new refbk($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
}

?>