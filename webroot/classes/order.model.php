<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'dproc_field_telephone.php';

require_once CLASSES_PATH.'order_item.model.php';
require_once CLASSES_PATH.'mailer.php';

class order extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/order.model.xml');
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
	
	public static function get_order_by_id($id,$uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$obj=null;
		DBP::Exec("call get_order_by_id(:uid,:id)",
			array('uid'=>$uid,'id'=>$id),
			function($arr) use (&$obj){
				$obj=new order($arr,'db');
			}
		);
		return $obj;
	}
	
	private $_items=null;
	
	protected function ovget_col_items() {
		if (is_null($this->_items) && !is_null($this->num) && ($this->num!=0)){
			$this->_items=new col_order_items;
			$this->_items->loadByOrder($this->num);
		} 
		return $this->_items;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_order(:acl_uid,:num,
						:c_name,:c_email,:c_telephone,:c_country,:c_city,
						:c_region ,:c_address,:shipping_id,:payment_id,:status_id,
						:message,:coupon )';
			
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
		
		$o->num=$id;
	}
	
	public function db_make($uid=null) {
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$o=&$this;
		
		$qry='call order_make(:acl_uid,:num,
						:c_name,:c_email,:c_telephone,:c_country,:c_city,
						:c_region ,:c_address,:shipping_id,:payment_id,:status_id,
						:message,:coupon )';
			
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
		
		$o->num=$id;
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (!is_null($this->num) && ($this->num!=0)) {
			DBP::Exec('call delete_order(:uid,:id)',array('uid'=>$uid,'id'=>$this->num));
		}
		
		$this->num=0;
	}
	
	public function send_admin() {
		mailer::order_admin($this);
	}
	
	public function send_customer() {
		if ($this->c_email) mailer::order_customer($this);
	}
	
	public function send_supervisor($email) {
		mailer::order_supervisor($this,$email);
	}
}

class col_orders extends mcollection {
	public function load(paginator $pg=null,$fstatus=null,$fcustphone=null,$fcustemail=null,$dtrange=null,$sort=null,$uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$items=&$this->_array;
		
		if (is_null($dtrange)) $dtrange=array();
		
		if (is_null($sort)) $sort='DTTD';
		
		if (is_null($pg)) {
			DBP::Exec('call get_orders(:acl_uid,:fstatus,:fcustphone,:fcustemail,
							from_unixtime(:fdtfrom),from_unixtime(:fdtto),:loffset,:lnum,:sort)',
				array('acl_uid'=>$uid,'fstatus'=>$fstatus,'fcustphone'=>$fcustphone,'fcustemail'=>$fcustemail,
					'fdtfrom'=>isset($dtrange['from'])?$dtrange['from']:null,'fdtto'=>isset($dtrange['to'])?$dtrange['to']:null,
					'loffset'=>null,'lnum'=>null,'sort'=>$sort
				),
				function ($arr) use (&$items) {
					$items[]=new order($arr,'db');
				});
		} else {
			DBP::MExec('call get_orders(:acl_uid,:fstatus,:fcustphone,:fcustemail,
							from_unixtime(:fdtfrom),from_unixtime(:fdtto),:loffset,:lnum,:sort)',
				array('acl_uid'=>$uid,'fstatus'=>$fstatus,'fcustphone'=>$fcustphone,'fcustemail'=>$fcustemail,
					'fdtfrom'=>isset($dtrange['from'])?$dtrange['from']:null,'fdtto'=>isset($dtrange['to'])?$dtrange['to']:null,
					'loffset'=>$pg->get_offset(),'lnum'=>$pg->get_onpage(),'sort'=>$sort
				),
				array(
					function ($arr) use (&$items) {
						$items[]=new order($arr,'db');
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