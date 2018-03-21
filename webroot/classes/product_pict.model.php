<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';

define('PROD_PICT_NUM',10);

class product_pict extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/product_pict.model.xml');
	}
	
	protected function ovget_ispict() {
		$ip=true;
		
		$fp='products/big/'.$this->pp_pict_uri;
		if (is_null(product::find_ext($fp))) $ip=false;
		
		return $ip;
	}
	
	protected function ovget_pict_uri_big() {
		$fp='products/big/'.$this->pp_pict_uri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_medium() {
		$fp='products/medium/'.$this->pp_pict_uri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	protected function ovget_pict_uri_small() {
		$fp='products/small/'.$this->pp_pict_uri;
		$file=product::find_ext($fp);
		if (!is_null($file)) {
			return '/media/'.$file;
		} else {
			return '/resources/img/no-image.jpg';
		}
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		if ($this->pp_pict_del) {
			$this->db_delete();
			return;
		}
		
		$qry='call update_product_pict(:acl_uid, :pid, :pn, :name)';
			
		DBP::Exec($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						$val->pdo_bind($q);
					}
				}
			}
		);
		
		if (!is_null($this->pp_pict)) {
			product::make_pics($this->pp_pict,$this->pp_pict_uri);		
		}
	}
	
	public function db_delete($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call delete_product_pict(:acl_uid, :pid, :pn)';
			
		DBP::Exec($qry,
			function ($q) use (&$o) {
				$q->bindParam(':acl_uid',$uid);
				$o->fld_pid->pdo_bind($q);
				$o->fld_pn->pdo_bind($q);
			}
		);
		
		product::drop_pic($this->pp_pict_uri);
		
		$this->pp_name='';
	}
}

class col_product_pics extends mcollection {
    public function loadByProduct($prod){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		$items=&$this->_array;
		
		DBP::Exec('call get_product_picts_fn(:pid,:num)',
			array('pid'=>$pid,'num'=>PROD_PICT_NUM),
			function ($arr) use (&$items) {
				$pp=new product_pict($arr,'db');
				if ($pp->ispict) {
					$items[]=$pp;
				}
			});

		//$this->_isFilled=1;
	}
	
	public function loadByProductFixedNum($prod,$num){
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		$items=&$this->_array;
		
		DBP::Exec('call get_product_picts_fn(:pid,:num)',
			array('pid'=>$pid,'num'=>PROD_PICT_NUM),
			function ($arr) use (&$items) {
				$items[$arr['pn']]=new product_pict($arr,'db');
			});

		//$this->_isFilled=1;
	}
}

?>