<?
require_once CLASSES_PATH.'currency.model.php';

class mod_admin_currencies extends module_admin {
	function body() {
		$this->mnu->setcurrent('settings');
		$this->mnu->setcurrentsub('currencies');
		$this->tpl->sub='form_currencies';
		
		$itm=currs_upd::Load();
		
		$itm->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_currencies.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_currencies.xml'),$itm);
		
		if ($this->src->post->def('submt')) {
        	if ($frm->validate_post($errs)) {
				$itm->db_update();
			} else {
				$this->tpl->errs=$errs;
			}
        }
		$this->tpl->frm=$frm->html_data();
	}
}

class currs_upd extends mobject {
	protected static $descrs=null;
	
	protected static function load_descrs() {
		return descriptors::xml_str('
			<fieldset>
				<field dtype="string" dbname="curr_base" name="curr_base" forupdate="1" />
				<field dtype="boolean" dbname="curr_base_show" name="curr_base_show" forupdate="1" />
				
				<field dtype="string" dbname="curr_1" name="curr_1" forupdate="1" />
				<field dtype="real" dbname="curr_1_ratio" name="curr_1_ratio" forupdate="1">
					<flag name="null" />
					<flag name="inv_null" />
					<param name="min" val="1" />
					<param name="format">0,00</param>
				</field>
				<field dtype="boolean" dbname="curr_1_ratio_r" name="curr_1_ratio_r" forupdate="1" />
				
				<field dtype="string" dbname="curr_2" name="curr_2" forupdate="1" />
				<field dtype="real" dbname="curr_2_ratio" name="curr_2_ratio" forupdate="1">
					<flag name="null" />
					<flag name="inv_null" />
					<param name="min" val="1" />
					<param name="format">0,00</param>
				</field>
				<field dtype="boolean" dbname="curr_2_ratio_r" name="curr_2_ratio_r" forupdate="1" />
			</fieldset>');
	}
	
	public static function Load(){
		$obj=null;
		DBP::Exec("call get_dcurrencies()",
			null,
			function($arr) use (&$obj){
				$obj=new currs_upd($arr,'db');
			}
		);
		return $obj;
	}
	
	public function db_update($uid=null) {
		if (is_null($uid)) $uid=auth_lp_adm::get_uid();
		
		$o=&$this;
		
		$qry='call update_dcurrencies(:acl_uid, :curr_base, :curr_base_show,
			:curr_1, :curr_1_ratio, :curr_1_ratio_r,
			:curr_2, :curr_2_ratio, :curr_2_ratio_r)';
			
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