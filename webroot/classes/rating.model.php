<?
class Vote extends mobject
{
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/vote.model.xml');
	}
	
	public function dbAdd($uid=null) {
		if (is_null($uid)) $uid=auth_hash_cust::get_uid();
		
		$o=&$this;
		
		$qry='call products_rating_add_vote(:acl_uid,:pid,:rate,:ip)';
			
		$err=false;
			
		DBP::Exec($qry,
			function ($q) use (&$o,$uid) {
				$q->bindParam(':acl_uid',$uid);
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//var_dump($val->descr->dbname); var_dump($val->value('plain'));
						$val->pdo_bind($q);
					}
				}
			},
			null,null,
			function ($e) use (&$err)  {
				$err=true;
				return true;
			}
		);
		
		return !$err;
	}
}

class Rating extends mobject
{
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_file(CLASSES_PATH.'descriptors/rating.model.xml');
	}
	
	public static function getByProduct($prod, $uid=null){
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		if (is_object($prod)) $pid=$prod->id;
		elseif (is_numeric($prod)) $pid=$prod;
		else return;
		
		$obj=null;
		DBP::Exec("call product_rating_get(:uid,:pid,null)",
			array('uid'=>$uid,'pid'=>$pid),
			function($arr) use (&$obj){
				$obj=new Rating($arr,'db');
			}
		);
		return $obj;
	}
}

?>