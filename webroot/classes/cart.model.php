<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

require_once CLASSES_PATH.'product.model.php';

class cart_item extends mobject {
	protected static $descrs=null;
	
	private $_prod=null;
	
	public function set_prod(product $prod) {
		$this->_prod=$prod;
	}
	
	protected function ovget_prod() {
		return $this->_prod;
	}
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/cart_item.model.xml');
	}
	
	protected function ovget_price_currency() {
		$val=currencies::format($this->price);
		if (is_null($val)) $val=$this->price;
		return $val;
	}
	
	protected function ovget_ht_price_currency() {
		$val=currencies::ht_format($this->price);
		if (is_null($val)) $val=$this->ht_price;
		return $val;
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
			case 2: return $htp[0].' <span>('.$htp[1].')</span>';
			case 3: return $htp[0].' <span>('.$htp[1].',&nbsp;'.$htp[2].')</span>';
			default: return 'NP';
		}
	}
	
	protected function ovget_summ_currency() {
		$val=currencies::format($this->summ);
		if (is_null($val)) $val=$this->summ;
		return $val;
	}
	
	protected function ovget_ht_summ_currency() {
		$val=currencies::ht_format($this->summ);
		if (is_null($val)) $val=$this->ht_summ;
		return $val;
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
			case 2: return $htp[0].' <span>('.$htp[1].')</span>';
			case 3: return $htp[0].' <span>('.$htp[1].',&nbsp;'.$htp[2].')</span>';
			default: return 'NS';
		}
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$o=&$this;
		
		$qry='call cart_upd(:acl_uid,:acid,:pid,:qty)';
			
		$dt=DBP::ExecSingleRow($qry,
			function ($q) use (&$o,$uid) {
				$q->bindParam(':acl_uid',$uid);
				$q->bindParam(':acid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			}
		);
	}
	
	public function db_add($uid=null) {
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$o=&$this;
		
		$qry='call cart_add(:acl_uid, :acid, :pid, :qty)';
			
		$dt=DBP::ExecSingleRow($qry,
			function ($q) use (&$o,$uid) {
				//var_dump($uid);
				$q->bindParam(':acl_uid',$uid);
				$q->bindParam(':acid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			}
		);
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		if (!is_null($this->pid) && ($this->pid!=0)) {
			DBP::Exec('call cart_del(:uid,:acid,:id)',array('uid'=>$uid,'acid'=>$uid,'id'=>$this->pid));
		}
	}
}

class cart extends mcollection {
    public function load($uid=null){
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$items=&$this->_array;
		$prods=array();
		
		DBP::MExec('call get_cart(:uid)',
			array('uid'=>$uid),
			array(
				function ($arr) use (&$items) {
					$items[]=new cart_item($arr,'db');
				},
				function ($arr) use (&$prods) {
					$prods[]=new product($arr,'db');
				},
			),null,null);

		//$this->_isFilled=1;
		
		
		foreach ($items as $cr) {
			foreach ($prods as $pr) {
				if ($cr->pid==$pr->id) {
					$cr->set_prod($pr);
					break;
				}
			}
		}
	}
}

class cart_summ extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/cart_summ.model.xml');
	}
	
	protected function ovget_summ_currency() {
		$val=currencies::format($this->summ);
		if (is_null($val)) $val=$this->summ;
		return $val;
	}
	
	protected function ovget_ht_summ_currency() {
		$val=currencies::ht_format($this->summ);
		if (is_null($val)) $val=$this->ht_summ;
		return $val;
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
	
	public static function db_get($uid=null){
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$obj=null;
		DBP::Exec("call get_cart_sum(:uid)",
			array('uid'=>$uid),
			function($arr) use (&$obj){
				$obj=new cart_summ($arr,'db');
			}
		);
		return $obj;
	}
}

?>