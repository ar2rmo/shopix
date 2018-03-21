<?
require_once MODULES_PATH.'sitemap.php';

class mod_err404 extends mod_sitemap {
	const mod_hidden=true;
	const mod_tpl='error';
	
	function body() {
		header("HTTP/1.0 404 Not Found");
		$this->tpl->errnum=404;
		parent::body();
	}
}
?>