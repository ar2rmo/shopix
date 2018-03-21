<?
require_once CLASSES_PATH.'article.model.php';

class mod_read_item extends module_page {
	const mod_hidden=true;
	
	function body() {
		switch ($this->data) {
			case 'articles':
				$kind='ARTI';
				$this->tpl->set_template('articles-item');
			break;
			case 'news':
				$kind='NEWS';
				$this->tpl->set_template('news-item');
			break;
			case 'specials':
				$kind='SPEC';
				$this->tpl->set_template('specials-item');
			break;
		}
		
		if ($this->src->uri->num('uri_2')) {
			$art=article::getByID($kind,$this->src->uri->uri_2);
		} else {
			$art=article::getByURI($kind,$this->src->uri->uri_2);
		}
		
		if (is_null($art)) {
			$this->app->err404();
			$this->abort();
		} else {
			//$this->tpl->title=$art->tx_d_title.' | '.$this->app->setts->inf_shopname;
			if (!is_null($art->d_keywords)) $this->tpl->keywords=$art->tx_d_keywords;
			if (!is_null($art->d_description)) $this->tpl->description=str_replace('"','\'',strip_tags($art->tx_d_description));
			
			$this->tpl->art=$art;
		}
	}
}
?>