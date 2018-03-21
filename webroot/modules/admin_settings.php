<?
class mod_admin_settings extends module_admin {
	function body() {
		$this->mnu->setcurrent('settings');
		$this->mnu->setcurrentsub('settings');
		$this->tpl->sub='form_settings';
		
		$itm=$this->app->setts;
		
		$itm->make_edescrs(descriptors::xml_file(MODULES_PATH.'descriptors/messages_settings.xml'));
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_settings.xml'),$itm);
		
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
?>