<?
require_once CLASSES_PATH.'category.model.php';

class mod_box_filter_incat extends module_sub {
	function body() {
		$cat=$this->data['cat'];
		
		$dscs=descriptors::xml_file(MODULES_PATH.'descriptors/form_filter_incat.xml');
		
		$dscs['brand']->set_val('proc_name','cmb_fbrand_incat(:cat,1,0)');
		$dscs['brand']->set_val('proc_param',array('cat'=>$cat->id));
		
		$dscs['crit1']->set_val('proc_name','cmb_fcrit_incat("FRST",:cat,null,null,1,0)');
		$dscs['crit1']->set_val('proc_param',array('cat'=>$cat->id));
		
		$dscs['crit2']->set_val('proc_name','cmb_fcrit_incat("SCND",:cat,null,null,1,0)');
		$dscs['crit2']->set_val('proc_param',array('cat'=>$cat->id));
		
		$frm=new cform($dscs);
		
		$tpl=new template('boxes/filter-incat');
		$tpl->urib=$this->app->urib_f;
		$tpl->frm=$frm->html_data();
		$tpl->output();
	}
}
?>