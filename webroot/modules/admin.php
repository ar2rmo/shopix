<?
class mod_admin extends module {
	function body() {
		$mod=$this->src->uri->uri_2;
		if (is_null($mod) || ($mod=='')) $mod='main';
		$mod='admin_'.str_replace('-','_',$mod);
		
		if (!$this->app->run($mod,null,array('hidden'=>true,'admin'=>true))) {
			$this->app->err404();
		}
	}
}
?>