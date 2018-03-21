<?
require_once CLASSES_PATH.'category.model.php';
require_once CLASSES_PATH.'product.model.php';

require_once CLASSES_PATH.'ymlexport.php';

class mod_ymlexport extends module {
	const mod_cli=true;
	
	function body() {
		$cy=new ymlexport();
		
		if ($this->data=='CLI') {
			if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
				if ($m[1]=='') $m[1]='http';
				$prefix=$m[1].'://'.$m[2];
			} else {
				$prefix='http://'.$this->setts->tx_inf_shopurl;
			}
			
			$cy->wipe();
			$cy->doit($prefix);
		} else {
			if (defined("CANONICAL_DOMAIN") && preg_match('/^(?:(https?):\/\/)?([-A-Za-z0-9\.]+)\/?$/',CANONICAL_DOMAIN,$m)) {
				if ($m[1]=='') $m[1]='http';
				$prefix=$m[1].'://'.$m[2];
			} else {
				$prefix=($this->http->is_secure()?'https://':'http://').$_SERVER['SERVER_NAME'];
			}
			
			if ($this->src->post->def('doit')) {
				$cy->wipe();
				$file=$cy->doit($prefix);
				echo '<h3>YML файл сгенерирован</h3>'."\n";
				echo '<p><a href="/'.$file.'" target="_blank">'.$file.'</a></p>'."\n";
			} else {
			   echo '<h3>Генерация YML файла</h3>'."\n";
			   echo '<form method="POST"><input type="submit" name="doit" value="Генерировать"></form>';
			}
		}
	}
}
?>