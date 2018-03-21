<?
require_once CLASSES_PATH.'xmlsitemap.php';

class mod_admin_xmlsitemap extends module_admin {
	function body() {
		$this->mnu->setcurrent('main');
		
		$cm=new xmlsitemap();
		
		if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
			if ($m[1]=='') $m[1]='http';
			$prefix=$m[1].'://'.$m[2];
		} else {
			$prefix=($this->http->is_secure()?'https://':'http://').$_SERVER['SERVER_NAME'];
		}
		
		if ($this->src->post->def('doit')) {
			$cm->wipe();
			$files=$cm->doit($prefix);
			$this->tpl->files=$files;
			$this->tpl->sub='xmlsitemap1';
		} else {
			$this->tpl->sub='xmlsitemap0';
		}
	}
}
?>