<?
class mod_admin_logout extends module {
	function body() {
		$auth=new auth_lp_adm;
		$auth->logout();
		$this->app->http->relocate('/admin');
	}
}
?>