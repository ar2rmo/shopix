<?
require_once CLASSES_PATH.'category.model.php';

class mod_box_nextlev extends module_sub {
	function body() {
		$cats=new col_categories;
		$cats->loadByParent($this->data);
		
		if ((count($cats)==0) && ($this->data->level>1) && ($this->data->level<(defined('NEXLEV_MAXLEV')?NEXLEV_MAXLEV:2))) {
			$cats->loadByParent($this->data->parent_id,$this->data->id);
		}
		
		if (count($cats)>0) {
			$tpl=new template('boxes/nextlev');
			$tpl->urib=$this->app->urib_f;
			$tpl->cats=$cats;
			$tpl->output();
		}
	}
}
?>