<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

class spec_class_value extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/spec_class_value.model.xml');
	}
	
	public function db_update(){
		$o=&$this;
		
		$qry='call spec_classes_value_set_by_product(:pid, :scid,
					:RefbookId, :ValueString, :ValueInteger, :ValueFloat,
					:ValueMoney, :ValueBoolean, :ValueDatetime
				)';
			
		DBP::Exec($qry,
			function ($q) use (&$o) {
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
	}
	
	private $_v=null;
	private $_vt=null;
	private $_v_s=false;
	
	protected function ovget_fld_v() {
		if (!$this->_v_s){
			$this->_v=$this->get_val_field();
			$this->_vt=$this->get_val_field(true);
		} 
		return $this->_v;
	}
	
	protected function ovget_fld_vt() {
		if (!$this->_v_s){
			$this->_v=$this->get_val_field();
			$this->_vt=$this->get_val_field(true);
		} 
		return $this->_vt;
	}
	
	protected function ovget_v() {
		return $this->fld_v->value('plain');
	}
	
	protected function ovget_tx_v() {
		return $this->fld_vt->value('txt');
	}
	
	protected function ovget_ht_v() {
		return $this->fld_vt->value('html');
	}
	
	public function get_val_field($forcetext=false) {
		switch ($this->DataType) {
			case 'REFBOOK':
				if ($forcetext) $fld=$this->get_field('RefbookName');
				else $fld=$this->get_field('RefbookId');
			break;
			case 'STRING':
				$fld=$this->get_field('ValueString');
			break;
			case 'INTEGER':
				$fld=$this->get_field('ValueInteger');
			break;
			case 'FLOAT':
				$fld=$this->get_field('ValueFloat');
			break;
			case 'MONEY':
				$fld=$this->get_field('ValueMoney');
			break;
			case 'BOOLEAN':
				$fld=$this->get_field('ValueBoolean');
			break;
			case 'DATETIME':
				$fld=$this->get_field('ValueDatetime');
			break;
			default:
				$fld=null;
		}
		
		return $fld;
	}
}

?>