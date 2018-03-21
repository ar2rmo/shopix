<?
require_once CLASSES_PATH.'ymlexport.php';

class mod_admin_ymlexport extends module_admin {
	function body() {
		$this->mnu->setcurrent('main');
		
		$cy=new ymlexport();
		
		if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
			if ($m[1]=='') $m[1]='http';
			$prefix=$m[1].'://'.$m[2];
		} else {
			$prefix=($this->http->is_secure()?'https://':'http://').$_SERVER['SERVER_NAME'];
		}
		
		if ($this->src->post->def('doit')) {
			$cy->wipe();
			$file=$cy->doit($prefix);
			$this->tpl->file=$file;
			$this->tpl->sub='ymlexport1';
		} else {
			$this->tpl->sub='ymlexport0';
		}
	}
}
?>