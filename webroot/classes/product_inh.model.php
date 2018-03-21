<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once LIBRARIES_PATH.'image.php';

class product_inh extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/product_inh.model.xml');
	}
	
	public static function getByID($id){
		$obj=null;
		DBP::Exec("call get_product_inheritance(:id)",
			function ($q) use ($id) {
				$q->bindParam(":id",$id,PDO::PARAM_INT);
			},
			function($arr) use (&$obj){
				$obj=new product_inh($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_product_inherit(:acl_uid, :pid, :inh_name, :inh_fullname, :inh_title, :inh_keywords, :inh_description,
									      :inh_css, :inh_code, :inh_barcode, :inh_brand, :inh_measure, :inh_size, :inh_descr_short,
									      :inh_descr_full, :inh_descr_tech, :inh_price, :inh_fnew, :inh_frecomend, :inh_fspecial, :inh_tags, :inh_crits)';
			
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
	}
}

?>