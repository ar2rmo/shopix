<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'statistics.model.php';

class mod_box_catalog extends module_sub {
	function body() {
		$stat=statistics::calculate_vis_nsr();
		$curr_new=(($this->src->uri->uri_1=='filter')&&($this->src->uri->uri_2=='new'));
		$curr_special=(($this->src->uri->uri_1=='filter')&&($this->src->uri->uri_2=='special'));
		$curr_recomend=(($this->src->uri->uri_1=='filter')&&($this->src->uri->uri_2=='recomend'));
		
		$cats=new col_categories;
		
		if ($this->src->uri->uri_1=='catalog') {
			$uris=$this->src->uri->get_all();
			unset($uris['uri_1']);
			$uri='/'.implode('/',$uris);
			
			$cat=category::getByURI($uri);
			if (is_null($cat)) {
				$puri=array_pop($uris);
				$uri='/'.implode('/',$uris);
			}
			
			$cats->loadBranchTree($uri,3);
		} else {
			$cats->loadByParent(0);
		}
		
		$tpl=new template('boxes/catalog');
		$tpl->urib=$this->app->urib_f;
		$tpl->cats=$cats;
		$tpl->stat=$stat;
		$tpl->curr_new=$curr_new;
		$tpl->curr_special=$curr_special;
		$tpl->curr_recomend=$curr_recomend;
		$tpl->output();
	}
}
?>