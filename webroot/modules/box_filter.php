<?
require_once CLASSES_PATH.'category.model.php';

class mod_box_filter extends module_sub {
	function body() {
		$frm=new cform(descriptors::xml_file(MODULES_PATH.'descriptors/form_filter.xml'));
		
		$tpl=new template('boxes/filter');
		$tpl->urib=$this->app->urib_f;
		$tpl->frm=$frm->html_data();
		$tpl->output();
	}
}
?>