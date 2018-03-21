<?
require_once LIBRARIES_PATH.'dbproxy.php';
require_once LIBRARIES_PATH.'dproc.php';
require_once CLASSES_PATH.'mailer.php';
require_once CLASSES_PATH.'hasher.php';

class setts {	
	static $obj=null;
	
	public static function get_obj() {
		if (is_null(static::$obj)) static::$obj=new settings;
		return static::$obj;
	}
	
	public static function get($name) {
		return static::get_obj()->get_value($name,'plain');
	}
	
	public static function get_ht($name) {
		return static::get_obj()->get_value($name,'html');
	}
}
	
class settings extends fieldset {	
	public function __construct(){
		parent::__construct(descriptors::xml_file(CLASSES_PATH.'descriptors/settings.xml'));
		
		$arr=DBP::ExecSingleRow('call get_settings()');
		if (is_null($arr)) return;
		
		foreach($this as $val){
			if (isset($arr[$val->descr->dbname])){
				$this->set_value($val->descr->name,$arr[$val->descr->dbname],true,'db');
			}
		}
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$this->inf_host=$_SERVER['SERVER_NAME'];
	
		$o=&$this;	
		DBP::Exec('call update_settings(:uid,
				:inf_shopname,
				:inf_shopurl,
				:inf_host,
				:inf_keywords,
				:inf_description,
				:inf_nameintitle,
				:img_max_width,
				:img_max_height,
				:img_small_width,
				:img_small_height,
				:img_middle_width,
				:img_middle_height,
				:img_small_width_cat,
				:img_small_height_cat,
				:img_middle_width_cat,
				:img_middle_height_cat,
				:img_art_width,
				:img_art_height,
				:num_onpage_prod,
				:num_box_rand,
				:num_box_new,
				:num_box_recomend,
				:num_onpage_news,
				:num_box_news,
				:num_onpage_articles,
				:num_box_articles,
				:num_onpage_specials,
				:num_box_specials,
				:num_onpage_orders,
				:ord_mail,
				:ord_initstatus,
				:num_xpd_rand,
				:name_crit1,
				:name_crit2,
				:name_crit3,
				:name_etab1,
				:name_etab2,
				:name_etab3,
				:name_etab4,
				:name_etab5
			)', // #XPD {num_xpd_rand}
			function ($q) use (&$o) {
				foreach ($o as $val) {
					if (!is_null($val->descr->dbname) && !is_null($val->descr->forupdate) && $val->descr->forupdate==1){
						//echo $val->descr->dbname."=".$val->descr->forupdate."<br/>";
						$val->pdo_bind($q);
					}
				}			
				$q->bindParam(':uid',$uid);
			}
		);
		
		$arr=DBP::ExecSingleRow('call gen_hashes');
		if ($arr['stt']==0) {
			$ml=mailer::get_mailer();
			$ml->addAddress($arr['dest']);
			$ml->setFrom($arr['esors'],$arr['nsors']);
			$ml->Subject=$arr['subj'];
			$ml->msgHTML($arr['cont']);
			if ($ml->send()) DBP::Exec('call update_hashes');
		}
	}
}

?>