<?
require_once CLASSES_PATH.'article.model.php';

class mod_articles extends module {
	function body() {
		if ($this->src->uri->uri_2) {
			$this->app->run('read_item','articles');
		} else {
			$this->app->run('read_list','articles');
		}
	}
}
?>