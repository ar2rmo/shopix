<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'dproc_field_telephone.php';

require_once CLASSES_PATH.'product.model.php';

class inherit extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/inherit.model.xml');
	}
	
	public function db_inherit($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		$qry='call create_product_variant(:acl_uid, :pid, :variant, :fshow, :price, :inh_price, :forsale)';
			
		$row=DBP::ExecSingleRow($qry,
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
		
		$o->variant_id=$row['pid'];
		$o->variant_uri=$row['uri'];
	}
}

?>