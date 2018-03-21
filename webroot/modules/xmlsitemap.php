<?
require_once CLASSES_PATH.'xmlsitemap.php';

class mod_xmlsitemap extends module {
	const mod_cli=true;
	
	function body() {
		$cm=new xmlsitemap();
		
		if ($this->data=='CLI') {
			if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
				if ($m[1]=='') $m[1]='http';
				$prefix=$m[1].'://'.$m[2];
			} else {
				$prefix='http://'.$this->setts->tx_inf_shopurl;
			}
			
			$cm->wipe();
			$cm->doit($prefix);
		} else {
			if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
				if ($m[1]=='') $m[1]='http';
				$prefix=$m[1].'://'.$m[2];
			} else {
				$prefix=($this->http->is_secure()?'https://':'http://').$_SERVER['SERVER_NAME'];
			}
			
			if ($this->src->post->def('doit')) {
				$cm->wipe();
				$files=$cm->doit($prefix);
				echo '<h3>Карта сайта XML сгенерирована</h3>'."\n";
				foreach ($files as $file) {
					echo '<p><a href="/sitemaps/'.$file.'" target="_blank">'.$file.'</a></p>'."\n";
				}
			} else {
			   echo '<h3>Генерация карты сайта XML</h3>'."\n";
			   echo '<form method="POST"><input type="submit" name="doit" value="Генерировать"></form>';
			}
		}
	}
}
?>