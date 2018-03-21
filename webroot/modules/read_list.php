<?
require_once CLASSES_PATH.'article.model.php';
require_once CLASSES_PATH.'page.model.php';

class mod_read_list extends module_page {
	const mod_hidden=true;
	
	function body() {
		switch ($this->data) {
			case 'articles':
				$kind='ARTI';
				$pg=new paginator($this->src,$this->app->setts->num_onpage_articles);
				$this->tpl->set_template('articles');
				$page=page::getByUri('articles');
			break;
			case 'news':
				$kind='NEWS';
				$pg=new paginator($this->src,$this->app->setts->num_onpage_news);
				$this->tpl->set_template('news');
				$page=page::getByUri('news');
			break;
			case 'specials':
				$kind='SPEC';
				$pg=new paginator($this->src,$this->app->setts->num_onpage_specials);
				$this->tpl->set_template('specials');
				$page=page::getByUri('specials');
			break;
		}
		
		$this->tpl->page=$page;
		
		if (!is_null($page)) {
			$this->tpl->title=$page->tx_d_title.($this->app->setts->inf_nameintitle?(' | '.$this->app->setts->inf_shopname):'');
			if (!is_null($page->d_keywords)) $this->tpl->keywords=$page->tx_d_keywords;
			if (!is_null($page->d_description)) $this->tpl->description=str_replace('"','\'',strip_tags($page->tx_d_description));
		}
		
		$arts=new col_article;
		$arts->loadAll($pg,$kind);
		
		$this->tpl->pages=$pg->get_parray();
		$this->tpl->num=$pg->get_items();
		$this->tpl->arts=$arts;
	}
}
?>