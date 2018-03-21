<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

require_once CLASSES_PATH.'product.model.php';

class order_item extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/order_item.model.xml');
	}
	
	private $_prod=null;
	
	protected function ovget_prod() {
		if (is_null($this->_prod) && !is_null($this->p_id)){
			$this->_prod=product::getById($this->p_id);
		} 
		return $this->_prod;
	}
	
	protected function ovget_price_currency() {
		return currencies::format($this->price);
	}
	
	protected function ovget_ht_price_currency() {
		return currencies::ht_format($this->price);
	}
	
	private $_pc=null;
	
	protected function ovget_price_currencies() {
		if (is_null($this->_pc)) {
			if ($this->price) $this->_pc=currencies::convert($this->price);
			else $this->_pc=array();
		}
		return $this->_pc;
	}
	
	private $_pcht=null;
	
	protected function ovget_ht_price_currencies() {
		if (is_null($this->_pcht)) {
			if ($this->price) $this->_pcht=currencies::ht_convert($this->price);
			else $this->_pcht=array();
		}
		return $this->_pcht;
	}
	
	protected function ovget_tx_full_price() {
		$htp=$this->price_currencies;
		switch (count($htp)) {
			case 0: return $this->tx_price;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].', '.$htp[2].')';
			default: return 'NP';
		}
	}
	
	protected function ovget_ht_full_price() {
		$htp=$this->ht_price_currencies;
		switch (count($htp)) {
			case 0: return $this->ht_price;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].',&nbsp;'.$htp[2].')';
			default: return 'NP';
		}
	}
	
	protected function ovget_summ_currency() {
		return currencies::format($this->summ);
	}
	
	protected function ovget_ht_summ_currency() {
		return currencies::ht_format($this->summ);
	}
	
	private $_sc=null;
	
	protected function ovget_summ_currencies() {
		if (is_null($this->_sc)) {
			if ($this->summ) $this->_sc=currencies::convert($this->summ);
			else $this->_sc=array();
		}
		return $this->_sc;
	}
	
	private $_scht=null;
	
	protected function ovget_ht_summ_currencies() {
		if (is_null($this->_scht)) {
			if ($this->summ) $this->_scht=currencies::ht_convert($this->summ);
			else $this->_scht=array();
		}
		return $this->_scht;
	}
	
	protected function ovget_tx_full_summ() {
		$htp=$this->summ_currencies;
		switch (count($htp)) {
			case 0: return $this->tx_summ;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].', '.$htp[2].')';
			default: return 'NS';
		}
	}
	
	protected function ovget_ht_full_summ() {
		$htp=$this->ht_summ_currencies;
		switch (count($htp)) {
			case 0: return $this->ht_summ;
			case 1: return $htp[0];
			case 2: return $htp[0].' ('.$htp[1].')';
			case 3: return $htp[0].' ('.$htp[1].',&nbsp;'.$htp[2].')';
			default: return 'NS';
		}
	}
	
	public static function get_order_item_by_id($id,$uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$obj=null;
		DBP::Exec("call get_order_item_by_id(:uid,:id)",
			array('uid'=>$uid,'id'=>$id),
			function($arr) use (&$obj){
				$obj=new order_item($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if ($this->id==0) return;
		
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_order_item(:acl_uid, :ord_id, :qty, :price)';
			
		$id=DBP::ExecSingleVal($qry,
			function ($q) use (&$o,$uid) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		$o->id=$id;
	}
	
	public function db_add($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call add_order_item(:acl_uid, :or_id, :p_id, :qty)';
			
		$id=DBP::ExecSingleVal($qry,
			function ($q) use (&$o,$uid) {
				$q->bindParam(':acl_uid',$uid);
				
				$o->fld_o_num->pdo_bind($q);
				$o->fld_p_id->pdo_bind($q);
				$o->fld_qty->pdo_bind($q);
			}
		);
		
		$o->id=$id;
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->id) && ($this->id!=0)) {
			DBP::Exec('call delete_order_item(:acl_uid, :id)',array('acl_uid'=>$uid,'id'=>$this->id));
		}
		
		$this->id=0;
	}
}

class col_order_items extends mcollection {
	public function loadByOrder($order,$uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (is_object($order)) $oid=$order->num;
		elseif (is_numeric($order)) $oid=$order;
		else return;
		
		$items=&$this->_array;
		
		DBP::Exec('call get_order_item_by_orderid(:acl_uid,:id)',
			array('acl_uid'=>$uid,'id'=>$oid),
			function ($arr) use (&$items) {
				$items[]=new order_item($arr,'db');
			});
		
		//$this->_isFilled=1;
	}
}

?>