<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

require_once CLASSES_PATH.'spec_class_value.model.php';
require_once CLASSES_PATH.'spec_refbook.model.php';

class spec_class extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/spec_class.model.xml');
	}
	
	protected function ovget_is_refbook() {
		return $this->datatype=='REFBOOK';
	}
	
	private $_val=null;
	
	protected function ovget_xvalue() {
		return $this->_val;
	}
	
	public function set_xvalue(spec_class_value $val) {
		$this->_val=$val;
	}
	
	public function get_form_descriptor() {
		$descr=new descriptor;
		
		$descr->set_val('name',$this->icode);
		$descr->set_val('caption',$this->name);
		
		switch ($this->datatype) {
			case 'REFBOOK':
				$descr->set_val('type','list');
				$descr->set_val('source','dbproc');
				$descr->set_val('proc_name','spec_refbook_get_by_class(:scid,null,null)');
				$descr->set_val('proc_param',array('scid'=>$this->id));
				$descr->set_val('oclass','spec_refbook_item');
				$descr->set_val('keyfld','id');
				$descr->set_val('valfld','name');
				$descr->set_val('preamble','---');
				$descr->set_val('preamble_key','null');
			break;
			case 'STRING':
				$descr->set_val('type','text');
			break;
			case 'INTEGER':
				$descr->set_val('type','text');
			break;
			case 'FLOAT':
				$descr->set_val('type','text');
			break;
			case 'MONEY':
				$descr->set_val('type','text');
			break;
			case 'BOOLEAN':
				$descr->set_val('type','check');
			break;
			case 'DATETIME':
				$descr->set_val('type','calendar');
			break;
			default:
				return null;
		}
		
		return $descr;
	}
	
	public function storeValues(){
		$this->xvalue->db_update();
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call spec_class_get(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new spec_class($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call spec_class_update(:acl_uid, :scid, :code, :name, :is_multy, :datatype, :group_id)';
			
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
			DBP::Exec('call spec_class_delete(:uid,:id)',
				array('uid'=>$uid,'id'=>$this->id)
			);
		}
		
		$this->id=0;
	}
	
	public function db_move_up($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_class_move(:uid,"UPUP",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
	
	public function db_move_down($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		DBP::Exec('call spec_class_move(:uid,"DOWN",:id)',
			array('uid'=>$uid,'id'=>$this->id)
		);
	}
}

class col_spec_classes extends mcollection {
    public function get_form_descriptors() {
		$descrs=array();
		foreach ($this as $sc) {
			$d=$sc->get_form_descriptor();
			if (is_null($d)) continue;
			$descrs[$sc->icode]=$d;
		}
		return $descrs;
	}
	
	public function get_fieldset() {
		$flds=array();
		foreach ($this as $sc) {
			if (is_null($sc->xvalue)) continue;
			$flds[$sc->icode]=$sc->xvalue->get_val_field();
		}
		
		if (count($flds)==0) return null;
		
		$fs=new fieldset(array());
		foreach ($flds as $k=>$fld) {
			$fs[$k]=$fld;
		}

		return $fs;
	}
	
	public function get_form(&$fieldset=null) {
		$fieldset=$this->get_fieldset();
		$descrs=$this->get_form_descriptors();
		if (empty($descrs)) return null;
		$frm=new cform($descrs,$fieldset);
		return $frm;
	}
	
	public function get_by_icode($code) {
		foreach ($this as $itm) {
			if ($itm->icode==$code) return $itm;
		}
		return null;
	}
	
	public function loadAll(){
		$items=&$this->_array;
		
		DBP::Exec('call spec_classes_get()',
			null,
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadAllByGroups(){
		$items=&$this->_array;
		
		DBP::Exec('call spec_classes_get_all_by_groups()',
			null,
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadByGroup($group){
		$items=&$this->_array;
		
		if (is_object($group)) $sgid=$group->id;
		elseif (is_numeric($group)) $sgid=$group;
		else return;
		
		DBP::Exec('call spec_classes_get_by_group(:sgid)',
			array('sgid'=>$sgid),
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadRefBooks(){
		$items=&$this->_array;
		
		DBP::Exec('call spec_classes_get_rb()',
			null,
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadByProductVal($prod,$defonly=false,$inherit=false){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		if ($defonly) {
			if ($inherit) {
				$call='call spec_classes_values_get_by_product_defined_ih(:pid)';
			} else {
				$call='call spec_classes_values_get_by_product_defined(:pid)';
			}
		} else {
			if ($inherit) {
				$call='call spec_classes_values_get_by_product_ih(:pid)';
			} else {
				$call='call spec_classes_values_get_by_product(:pid)';
			}
		}
		
		$items=&$this->_array;
		
		DBP::Exec($call,
			array('pid'=>$pid),
			function ($arr) use (&$items) {
				$sc=new spec_class($arr,'db');
				$xv=new spec_class_value($arr,'db');
				$sc->set_xvalue($xv);
				$items[]=$sc;
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadByProduct($prod){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		$items=&$this->_array;
		
		DBP::Exec('call spec_classes_get_by_product(:pid)',
			array('pid'=>$pid),
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function loadByProducts($prods){
		$tpids=array();
		if (is_object($prods)) foreach ($prods as $prod) $tpids[]=$prod->id;
		elseif (is_array($prods)) $tpids=$prods;
		else $tpids=explode(',',$prods);
		
		$pids=array();
		foreach ($tpids as $pid) {
			if (!is_numeric($pid)) $pid=trim($pid);
			if (is_numeric($pid)) $pids[]=$pid;
		}
		
		$ppp=implode(',',$pids);
		
		$items=&$this->_array;
		
		DBP::Exec('call spec_classes_get_by_products(:ppp)',
			array('ppp'=>$ppp),
			function ($arr) use (&$items) {
				$items[]=new spec_class($arr,'db');
			},null,null);

		//$this->_isFilled=1;
	}
	
	public function storeValues(){
		foreach ($this as $sc) {
			$sc->storeValues();
		}
	}
}

?>