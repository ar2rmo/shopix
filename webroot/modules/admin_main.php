<?
require_once CLASSES_PATH.'statistics.model.php';

class mod_admin_main extends module_admin {
	function body() {
		$this->mnu->setcurrent('main');
		
		$stats=statistics::calculate();
		
		$this->tpl->sub='summary';
		$this->tpl->stats=$stats;
	}
}
?>