<?
class mod_admin_login extends module {
	function body() {
		$tpl=new template('admin/login');
		$tpl->setts=$this->app->setts;
		$tpl->hint=$this->data;
		$tpl->output();
	}
}
?>